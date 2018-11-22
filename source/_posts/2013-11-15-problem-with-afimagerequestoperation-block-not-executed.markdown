---
layout: post
title: "Problem with AFImageRequestOperation block not executed"
date: 2013-11-15 11:59
comments: true
tags: 
- ios
---

Recently I've faced a weird bug about displaying image from URL to `UIImageView`. Finally I found out is the **header** issue.

Let's take an example, there are 2 images

* http://example.com/image1.jpg
* http://example.com/image2.jpg

**Image1** has the header

```
HTTP 200 No Error

Server: AmazonS3
Content-Type: binary/octet-stream
Last-Modified: Mon, 01 Jul 2013 03:06:03 GMT
x-amz-meta-type: image/jpeg
x-amz-request-id: 8845F67C90997FF6
Date: Fri, 15 Nov 2013 04:02:30 GMT
x-amz-id-2: F9crPjTya1RNjUeaNLkE2cQTOet8WnAk72idgXeXBIglwwxHLVBvWbL67IC4Brkm
Accept-Ranges: bytes
Content-Length: 127456
Etag: "545ccc75ee7fc045ed201092742af52e"
```

**Image2** has the header

```
HTTP 200 No Error

Server: Microsoft-IIS/8.0
Content-Type: image/jpeg
X-Powered-By: ASP.NET
Last-Modified: Thu, 24 Oct 2013 21:41:42 GMT
Accept-Ranges: bytes
Date: Fri, 15 Nov 2013 04:07:30 GMT
Content-Length: 60585
Etag: "16e91dd41d1ce1:0"
```

Look at the `Content-Type`, **Image1** with the `Content-Type` **binary/octet-stream** _(if you type the URL in browser it will force download)_, while `Content-Type` of **Image2** is **image/jpeg** _(it will display in browser if you type in the URL)_.

## Solution

I make a plain HTTP request

```obj-c
NSString *url = @"http://example.com/image1.jpg";
AFHTTPClient *httpClient = [[AFHTTPClient alloc] initWithBaseURL:[NSURL URLWithString:url]];
[httpClient setParameterEncoding:AFFormURLParameterEncoding];

NSURLRequest *request = [httpClient requestWithMethod:@"GET"
                                                 path:url
                                           parameters:nil];

AFHTTPRequestOperation *operation = [[AFHTTPRequestOperation alloc] initWithRequest:request];
[operation setCompletionBlockWithSuccess:^(AFHTTPRequestOperation *operation, id responseObject) {
    UIImage *image = [UIImage imageWithData:responseObject];
    imageView.image = image;
} failure:^(AFHTTPRequestOperation *operation, NSError *error) {
    NSLog(@"Error: %@", error.description);
}];
[operation start];
```

instead of _(this will only work for `Content-Type: image/jpeg`)_

```obj-c
NSString *url = @"http://example.com/image1.jpg";
[[AFImageRequestOperation imageRequestOperationWithRequest:[NSURLRequest requestWithURL:[NSURL URLWithString:url]] success:^(UIImage *image) {
    imageView.image = image;
}] start];
```
