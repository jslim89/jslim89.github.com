---
layout: post
title: "How iPhone communicate with server in PHP"
date: 2013-09-22 15:16
comments: true
tags: 
- ios
- php
---

I have few friends as me about this.

-- **How iOS can retrieve/update from/to MySQL/MSSQL database?** --

There are 2 parts:- **Server side** and **Client side**

In this example the **Server side** will refer to `Apache`, `MySQL` and `PHP`. While **Client side** is `iPhone App` in Objective-C.

## Server side
Assume that your **Base URL** is `http://api.yoursite.com`

#### Retrieve a list of users
`GET http://api.yoursite.com/users`

```php
<?php
mysql_connect(localhost, 'username', 'password');
mysql_select_db('database_name');
$sql = 'SELECT * FROM user';

$result = mysql_query($sql);
$count = mysql_numrows($result);

// store the result in array form
$result_set = array();
$i = 0;
while ($i < $count) {
    $result_set[$i] = array();
    $result_set[$i]['username'] = mysql_result($result, $i, 'username');
    $result_set[$i]['first_name'] = mysql_result($result, $i, 'first_name');
    $result_set[$i]['last_name'] = mysql_result($result, $i, 'last_name');
    $i++;
}

mysql_close();

echo json_encode($result_set);

// set the header to JSON
header('Content-Type: application/json');
exit;
```

#### Add a new user
`POST http://api.yoursite.com/user/add`

```php
<?php
// only process it if it is POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO user VALUES ($_POST['username'], $_POST['first_name'], $_POST['last_name'])";
    mysql_query($sql);

    echo json_encode(array(
        'message' => 'John Smith has been inserted'
    ));
} else { // if not POST, then return 404
    echo json_encode(array(
        'message' => 'Endpoint not found'
    ));
    header('HTTP/1.1 404 Not Found');
}

// set the header to JSON
header('Content-Type: application/json');
exit;
```

## Client side
In this example will using this library - [AFNetworking](https://github.com/AFNetworking/AFNetworking). It has been simplify a lot of code from iOS SDK (or just call it as a wrapper)

```obj-c
#import "AFNetworking.h"

#define BASE_URL @"http://api.yoursite.com"

...
/* Retrieve a list of users */
- (void)loadUserList
{
    static NSString *relativePath = @"/users";

    NSURL *url = [NSURL URLWithString:relativePath relativeToURL:[NSURL URLWithString:BASE_URL]];
    AFHTTPClient *httpClient = [[AFHTTPClient alloc] initWithBaseURL:url];
    [httpClient setParameterEncoding:AFFormURLParameterEncoding];

    NSURLRequest *request = [httpClient requestWithMethod:@"GET"
                                                     path:relativePath
                                               parameters:nil];

    [[AFJSONRequestOperation JSONRequestOperationWithRequest:request success:^(NSURLRequest *request, NSHTTPURLResponse *response, id json) {

        // success
        NSLog(@"Success %@", json);
        
    } failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id json) {

        // failed
        NSLog(@"Failed %@", json);

    }] start];
}

/* Add a new user */
- (void)addUser
{
    static NSString *relativePath = @"/user/add";

    NSURL *url = [NSURL URLWithString:relativePath relativeToURL:[NSURL URLWithString:BASE_URL]];
    AFHTTPClient *httpClient = [[AFHTTPClient alloc] initWithBaseURL:url];
    [httpClient setParameterEncoding:AFFormURLParameterEncoding];

    NSURLRequest *request = [httpClient requestWithMethod:@"POST"
                                                     path:relativePath
                                               parameters:[NSDictionary dictionaryWithObjectsAndKeys:
                                                             self.usernameTextField.text, @"username",
                                                             self.firstNameTextField.text, @"first_name",
                                                             self.lastNameTextField.text, @"last_name",
                                                            nil]];

    [[AFJSONRequestOperation JSONRequestOperationWithRequest:request success:^(NSURLRequest *request, NSHTTPURLResponse *response, id json) {

        // success
        NSLog(@"Success %@", json);
        
    } failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id json) {

        // failed
        NSLog(@"Failed %@", json);

    }] start];
}
```

Simple :)
