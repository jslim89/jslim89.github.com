---
layout: post
title: "PHP Laravel 4 - before &amp; after action"
date: 2013-12-20 09:07
comments: true
categories: 
- php
- laravel
---

We may need to perform some actions before the controller function get called.

For example

## Check user authentication before access to certain page.

```php AccountController.php
<?php
class AccountController extends BaseController
{
    ...

    public function __construct()
    {
        parent::__construct();

        // before goes into certain function, check for authentication
        $this->beforeFilter('auth');
    }
}
```

The filter function is in

```php app/filters.php
<?php
...
Route::filter('auth', function()
{
    if (Auth::guest()) return Redirect::to('login');
}
```

_References:_

* _[Laravel 4 - Controller Filters](http://laravel.com/docs/controllers#controller-filters)_
* _[Laravel 4 Controller Before and After function](http://stackoverflow.com/questions/16317784/laravel-4-controller-before-and-after-function/16317851#16317851)_
