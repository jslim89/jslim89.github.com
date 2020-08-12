---
layout: post
title: "Happyfox API - Create ticket with attachment using PHP"
date: 2014-07-27 14:43:12 +0800
comments: true
tags: 
- happyfox
- php
---

[Happyfox](https://www.happyfox.com/) is a ticketing service provider. Recently I'm dealing with it's API and took me quite some time to figure out _how to create a ticket with attachment_. You can see the [official documentation here](http://www.happyfox.com/developers/api/1.1/).

## 1. Create an account and Generate API key
You can [sign up an account](https://www.happyfox.com/help-desk-signup/)


Now you can create api key

![Create API key step 1](/images/posts/2014-07-27-happyfox-api-create-ticket-with-attachment-using-php/api-1.png)

![Create API key step 2](/images/posts/2014-07-27-happyfox-api-create-ticket-with-attachment-using-php/api-2.png)

![Create API key step 3](/images/posts/2014-07-27-happyfox-api-create-ticket-with-attachment-using-php/api-3.png)

Enter **Identification Name** and save it

![Create API key step 4](/images/posts/2014-07-27-happyfox-api-create-ticket-with-attachment-using-php/api-4.png)

## 2. Get your `username` & `password`

As the requirement mention that HTTP Basic Authentication is required, thus you need to have a `username` & `password`

![Get credential](/images/posts/2014-07-27-happyfox-api-create-ticket-with-attachment-using-php/get-credential-1.png)

**Note: you must move your mouse over only you can see the link `see auth code`**

![See auth code](/images/posts/2014-07-27-happyfox-api-create-ticket-with-attachment-using-php/get-credential-2.png)

The **API key** served as `username` and **Auth code** served as `password`.

## 3. Make a POST request thru PHP cURL

```php
<?php
// get your data ready
$data = array(
    'subject' => 'How to change the DNS server',
    'text' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor ...',
    'category' => 1, // set your category (if you not sure about that, just put 1)
    'email' => 'john.smith@example.com', // web user's email
    'name' => 'John Smith', // web user's name

    // Optional. Note that there is an '@' infront
    'attachments' => '@/home/user/Desktop/screenshot.png', // attachment's path
);

$ch = curl_init('https://<youraccount>.happyfox.com/api/1.1/json/tickets/');

curl_setopt($ch, CURLOPT_POST, true); // this is POST request

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, sprintf('%s:%s', <Happyfox api key>, <Happyfox auth code>)); // here is the HTTP basic auth
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// don't forget to set your form data
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
$status = curl_getinfo($ch);
curl_close($ch);

if ($status['http_code] == 200) {
    $result = json_decode($response, true);
    print_r($result);
} else {
    echo 'Error occurred';
}
```

_References:_

* _[how to upload file using curl with php](https://stackoverflow.com/questions/15200632/how-to-upload-file-using-curl-with-php/15200804#15200804)_
