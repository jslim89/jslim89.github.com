---
layout: post
title: "List of possible PHP errors"
date: 2015-07-29 15:51:39 +0800
comments: true
categories: 
- php
---

## What is this?

Many times, we struggled on finding out the cause of error.
Especially, it works on local environment, but failed in live server.

For some hosting, especially shared hosting, we have no freedom to change
the php settings and we have to guess which section caused the error
and what is it about.

This usually caused us hours & hours to figure it out.

Thus, I wrote this post.

Please comment on below OR drop me an email at [me@jslim.co](mailto:me@jslim.co), if you want to contribute to this post.

Thanks

## Errors

### 1. Can't use method return value in write context

**Failed example** 

```php
<?php
if (empty($obj->method())) {
    // do something
}
```

**Correct way**

```php
<?php
$result = $obj->method();

if (empty($result)) {
    // do something
}
```

Move the result out as a separate variable, even though it may not be use
in the section below.

Reference: [Can't use method return value in write context](http://stackoverflow.com/questions/1075534/cant-use-method-return-value-in-write-context/1075559#1075559)

## Contributors

- Js Lim
