---
layout: post
title: "Make use of Laravel 5 default login"
date: 2015-11-12 22:02:46 +0800
comments: true
categories: 
- laravel
---

If you see the default **AuthController.php**, you can actually see this `use AuthenticatesAndRegistersUsers`.
They called this as _[traits](http://php.net/manual/en/language.oop5.traits.php)_, which was introduced in PHP 5.4.0

By using their default login, you have to create your own _view_, e.g.

```html
<form class="form-signin" method="POST">
    {!! csrf_field() !!}
    <input type="text" name="username" class="form-control" placeholder="Username">
    <input type="password" name="password" class="form-control" placeholder="Password">
    <button class="btn btn-lg btn-primary" type="submit">Sign in</button>
</form>
```

In the **app/Http/Controllers/Auth/AuthController.php**, you can specify your _username_ in the table
_(some of people will use **email** instead)_. The `$redirectPath` is refer to after you successful
login, where should it redirect to?

```php
<?php
// ...

use App\Models\User;

// ...

protected $redirectPath = '/dashboard';
protected $username = 'username';
```

As my example, I actually move the model class to a **Models** folder, thus I need to change this

**config/auth.php**

```php
<?php
//
'model' => App\Models\User::class, // <------- change this
```

If you want to change the login error message, originally is _"These credentials do not match our records."_

Then you can edit **resources/lang/en/auth.php**

```php
<?php
// ...
'failed' => 'Your custom error message',
```

Basically is done now. Try it yourself...

References:

- [Laravel 5 User Model not found](https://stackoverflow.com/questions/28516454/laravel-5-user-model-not-found/28516582#28516582)
- [Log in with username or email in Laravel 5...](https://laracasts.com/discuss/channels/general-discussion/log-in-with-username-or-email-in-laravel-5)
