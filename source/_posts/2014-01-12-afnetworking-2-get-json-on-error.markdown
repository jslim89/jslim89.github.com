---
layout: post
title: "AFNetworking 2 - get JSON on error"
date: 2014-01-12 13:07
comments: true
tags: 
- ios
---

I've just switched from **AFNetworking 1.x** to **2.x**.

In **AFNetworking 1.x**, I make a request using `JSONRequestOperationWithRequest` method. i.e.

**ViewController.m**

```obj-c
NSURL *url = [NSURL URLWithString:@"/login" relativeToURL:@"http://api.example.com"];
AFHTTPClient *httpClient = [[AFHTTPClient alloc] initWithBaseURL:url];
[httpClient setParameterEncoding:AFFormURLParameterEncoding];
NSURLRequest *request = [httpClient requestWithMethod:@"POST"
                                                 path:@"login"
                                           parameters:[NSDictionary dictionaryWithObjectsAndKeys:
                                                       @"value1", @"key1",
                                                       @"value2", @"key2",
                                                       nil]];

[[AFJSONRequestOperation JSONRequestOperationWithRequest:request success:^(NSURLRequest *request, NSHTTPURLResponse *response, id json) {
    NSLog(@"success %@", json);
} failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id json) {
    // get the json easily
    NSLog(@"failure %@", json);
}] start];
```

The method above I can get the **json** _(error message return from server)_ on failure.

However, in **AFNetworking 2.x**, I dig for quite some time only found the solution.

### Create a subclass of `AFJSONResponseSerializer`

**JSONResponseSerializerWithData.h**

```obj-c
#import "AFURLResponseSerialization.h"

/// NSError userInfo key that will contain response data
static NSString * const JSONResponseSerializerWithDataKey = @"JSONResponseSerializerWithDataKey";

@interface JSONResponseSerializerWithData : AFJSONResponseSerializer

@end
```

**JSONResponseSerializerWithData.m**

```obj-c
#import "JSONResponseSerializerWithData.h"

@implementation JSONResponseSerializerWithData


- (id)responseObjectForResponse:(NSURLResponse *)response
                           data:(NSData *)data
                          error:(NSError *__autoreleasing *)error
{
    if (![self validateResponse:(NSHTTPURLResponse *)response data:data error:error]) {
        if (*error != nil) {
            NSMutableDictionary *userInfo = [(*error).userInfo mutableCopy];
            NSError *jsonError;
            // parse to json
            id json = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingAllowFragments error:&jsonError];
            // store the value in userInfo if JSON has no error
            if (jsonError == nil) userInfo[JSONResponseSerializerWithDataKey] = json;
            NSError *newError = [NSError errorWithDomain:(*error).domain code:(*error).code userInfo:userInfo];
            (*error) = newError;
        }
        return (nil);
    }
    return ([super responseObjectForResponse:response data:data error:error]);
}

@end
```

### Usage

**ViewController.m**

```obj-c
AFHTTPRequestOperationManager *manager = [AFHTTPRequestOperationManager manager];
// LOOK AT THIS LINE, change to the serializer you've just created
manager.responseSerializer = [JSONResponseSerializerWithData serializer];
[manager POST:@"http://api.example.com/login" parameters:@{@"key1": @"value1", @"key2": @"value2"} success:^(AFHTTPRequestOperation *operation, id responseObject) {
    NSLog(@"success %@", responseObject);
} failure:^(AFHTTPRequestOperation *operation, NSError *error) {
    // get the json here
    id json = error.userInfo[JSONResponseSerializerWithDataKey];
    NSLog(@"failure %@", json);
}];
```

_References:_

* _[AFNetworking 500 response body](http://stackoverflow.com/questions/19325235/afnetworking-500-response-body/19383500#19383500)_
* _[HTTPClient -> AFHTTPSessionManager | Response body on error?](https://github.com/AFNetworking/AFNetworking/issues/1397#issuecomment-26139898)_
