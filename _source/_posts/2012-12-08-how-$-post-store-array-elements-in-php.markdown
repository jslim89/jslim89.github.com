---
layout: post
title: "How $_POST store array elements in PHP"
date: 2012-12-08 10:17
comments: true
categories: 
- php
- programming
---

Sometime we need an array form elements like this

```html
<input type="text" name="foo[]" value="a" />
<input type="text" name="foo[3]" value="b" />
<input type="text" name="foo[]" value="c" />
<input type="text" name="foo[7]" value="d" />
```

But some of that contain key some didn't. What will we actually get in php `$_POST`?

According to example above,

```php
<?php
echo '<pre>';
print_r($_POST['foo']);
echo '</pre>';
```

will output:

```
Array
(
    [0] => a
    [3] => b
    [4] => c
    [7] => d
)
```

But let say the index is in **string** form

```html
<input type="text" name="foo[]" value="a" />
<input type="text" name="foo['3']" value="b" />
<input type="text" name="foo[]" value="c" />
<input type="text" name="foo['7']" value="d" />
```

will output:

```
Array
(
    [0] => a
    ['3'] => b
    [1] => c
    ['7'] => d
)
```
