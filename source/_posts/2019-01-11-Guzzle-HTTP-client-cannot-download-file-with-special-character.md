---
title: Guzzle HTTP client cannot download file with special character
date: 2019-01-11 08:16:09
tags:
- php
- http
- guzzle
---

I'm using [Guzzle](http://docs.guzzlephp.org/en/stable/) to download image file, the code as below

```php
<?php

$normal_url = 'http://example.com/path/to/image.png';
$special_char_url = 'http://example.com/path/to/ครีมอาบน้ำ.jpg';

function download($url) {
    $output = '/tmp/' . basename($url);

    $client = new \GuzzleHttp\Client();
    $res = $client->get($url, [
        'verify' => false,
        'sink' => $output,
    ]);
}

download($normal_url); // this 1 works
download($special_char_url); // this throw 404 error
```

The thai characters file name throw error

```
GuzzleHttp\Exception\ClientException: Client error: `GET http://example.com/path/to/%E0%B8_%E0%B8%A3%E0%B8%B5%E0%B8%A1%E0%B8%AD%E0%B8%B2%E0%B8_%E0%B8_%E0%B9_%E0%B8%B3.jpg` resulted in a `404 Not Found` response:
```

The URL encoding is actually wrong, because I paste the URL to Chrome address bar, and then copy again, it gives

http://example.com/path/to/%E0%B8%84%E0%B8%A3%E0%B8%B5%E0%B8%A1%E0%B8%AD%E0%B8%B2%E0%B8%9A%E0%B8%99%E0%B9%89%E0%B8%B3.jpg

The Guzzle encoding has problem, so how I solve this is, use php curl, the raw php code...

```php
function download($url) {
    $output = '/tmp/' . basename($url);

    $fp = fopen($output, 'w+');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}
```

[Get the code from here](https://stackoverflow.com/questions/6409462/downloading-a-large-file-using-curl/6409531#6409531)

Simple & working solution :)
