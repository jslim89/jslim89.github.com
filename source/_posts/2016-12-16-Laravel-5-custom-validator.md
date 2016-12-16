---
title: Laravel 5 custom validator
date: 2016-12-16 15:17:44
tags:
- php
- laravel
---

As the official documentation doesn't specify how exactly to create a custom
validation class.

### 1. Create custom validator class

You can create in any where as you like. For my example, I will create in
**app/Libraries/CustomValidator.php**

```php
<?php namespace App\Libraries;

class CustomValidator
{
    /**
     * custom_rule:$param1,$param2,...
     * 
     * @param mixed $attribute 
     * @param mixed $value 
     * @param mixed $parameters 
     * @return bool
     */
    public function validateYourCustomRule($attribute, $value, $parameters)
    {
        // validate
        return false;
    }
}
```

### 2. Update to provider

Edit the file **app/Providers/AppServiceProvider.php**

```php
<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('custom_rule', 'App\Libraries\CustomValidator@validateYourCustomRule');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
```

### 3. Add the error message to language file

Edit **resources/lang/en/validation.php** and add this

```php
'custom_rule' => 'The :attribute has custom error.',
```

## Test it

```php
<?php
...
$rules = [
    'attr' => 'custom_rule:param1,param2',
];
```

References:

- [Custom Validation Function in Laravel 5](https://laracasts.com/discuss/channels/general-discussion/custom-validation-function-in-laravel-5?page=1)
- [“Unresolvable dependency resolving” error custom validation in laravel 5.1](http://stackoverflow.com/questions/34873101/unresolvable-dependency-resolving-error-custom-validation-in-laravel-5-1/34873877#34873877)
