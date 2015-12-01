---
layout: post
title: 'Laravel 5 with Facebook error - Cross-site request forgery validation failed. Required param "state" missing'
date: 2015-12-01 20:41:10 +0800
comments: true
categories: 
- laravel
- facebook-php-sdk
---

The reason of this error is due to Laravel doesn't use native PHP session store.

## 1. Create a custom session handler class

```
$ mkdir /path/to/project/app/Libraries/Facebook
$ vim /path/to/project/app/Libraries/Facebook/FacebookPersistentDataHandler.php
```

paste the following content

```php
<?php
namespace App\Libraries\Facebook;

class FacebookPersistentDataHandler implements \Facebook\PersistentData\PersistentDataInterface
{
    public function get($key)
    {   
      return \Session::get('facebook.' . $key);
    }   

    public function set($key, $value)
    {   
      \Session::put('facebook.' . $key, $value);
    }   

}
```

## 2. Pass in to Facebook instance

```php
<?php
$facebook = new \Facebook\Facebook([
    'app_id' => config('facebook.app_id'),
    'app_secret' => config('facebook.app_secret'),
    'persistent_data_handler' => new \App\Libraries\Facebook\FacebookPersistentDataHandler()
]);
```

This should solve the issue

References:

- [GitHub: validateCsrf produces Error](https://github.com/facebook/facebook-php-sdk-v4/issues/292)

