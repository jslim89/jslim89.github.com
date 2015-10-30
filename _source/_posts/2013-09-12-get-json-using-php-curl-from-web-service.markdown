---
layout: post
title: "Get JSON using PHP cURL from web service"
date: 2013-09-12 16:41
comments: true
categories: 
- php
---

This example is to show how to detect user's country

For **GET** request
```php
<?php

// set HTTP header
$headers = array(
    'Content-Type: application/json',
);

// query string
$fields = array(
    'key' => '<your_api_key>',
    'format' => 'json',
    'ip' => $_SERVER['REMOTE_ADDR'],
);
$url = 'http://api.ipinfodb.com/v3/ip-country?' . http_build_query($fields);

// Open connection
$ch = curl_init();

// Set the url, number of GET vars, GET data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Execute request
$result = curl_exec($ch);

// Close connection
curl_close($ch);

// get the result and parse to JSON
$result_arr = json_decode($result, true);

print_r($result_arr);
/*
 *  output:
 *  Array
 *  (
 *      [statusCode] => "OK",
 *      [statusMessage] => "",
 *      [ipAddress] => "123.13.123.12",
 *      [countryCode] => "MY",
 *      [countryName] => "MALAYSIA",
 *  )
 */
 ?>
```

For **POST** request
```php 
<?php

// set HTTP header
$headers = array(
    'Content-Type: application/json'
);

// set POST params
$fields = array(
    'key' => '<your_api_key>',
    'format' => 'json',
    'ip' => $_SERVER['REMOTE_ADDR'],
);
$url = 'http://api.ipinfodb.com/v3/ip-country';

// Open connection
$ch = curl_init();

// Set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

// Execute post
$result = curl_exec($ch);

// Close connection
curl_close($ch);
$result_arr = json_decode($result, true);
?>
```
