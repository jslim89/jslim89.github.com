---
layout: post
title: "Integrate GoogleAds API to Laravel 5"
date: 2015-11-05 20:02:46 +0800
comments: true
tags: 
- laravel
- google-api
---

This may be too simple for you.
At the beginning, I was thinking like how the PSR works, what class should I add to **config/app.php**'s provider _(actually no need)_, etc.

Eventually I realised that, it was quite simple.

### 1. Edit the `composer.json`

Add `"googleads/googleads-php-lib": "6.*"` to the **require** section.

e.g.

```json
"require": {
    "php": ">=5.5.9",
    "laravel/framework": "5.1.*",
    "googleads/googleads-php-lib": "6.*"   <----------- this line
}
```

Then run the command:

```
$ composer update
```

### 2. Create config file

By follow [Laravel 5 standard](http://laravel.com/docs/5.1/packages), create a config file in the **config** folder

```
project
├── config
│   ├── googleleads
│       └── adwords.ini
```

The **adwords.ini** contains:

```ini
; Detailed descriptions of these properties can be found at:
; https://developers.google.com/adwords/api/docs/headers

developerToken = "INSERT_DEVELOPER_TOKEN_HERE"
userAgent = "INSERT_COMPANY_NAME_HERE"

; Uncomment clientCustomerId to make requests against a single AdWords account,
; such as when you run the examples.
; If you don't set it here, you can set the client customer ID dynamically:
;  $user = new AdWordsUser();
;  $user->SetClientCustomerId(...);

; clientCustomerId = "INSERT_CLIENT_CUSTOMER_ID_HERE"

[OAUTH2]

; If you do not have a client ID or secret, please create one of type
; "installed application" in the Google API console:
; https://cloud.google.com/console
client_id = "INSERT_OAUTH2_CLIENT_ID_HERE"
client_secret = "INSERT_OAUTH2_CLIENT_SECRET_HERE"

; If you already have a refresh token, enter it below. Otherwise run
; GetRefreshToken.php.
refresh_token = "INSERT_OAUTH2_REFRESH_TOKEN_HERE"
```

### 3. How to use

```php
<?php
Route::get('adwords', function () {
    $user = new AdWordsUser(config_path('googleads/adwords.ini'));
    print_r($user); // for debug only
    // other actions here
    return view('adwords');
});
```

That's all~
