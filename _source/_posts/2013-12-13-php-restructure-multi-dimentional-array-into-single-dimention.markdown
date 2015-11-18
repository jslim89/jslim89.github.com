---
layout: post
title: "PHP - Restructure multi-dimentional array into single dimention"
date: 2013-12-13 17:08
comments: true
categories: 
- php
---

## Source

For example, we have array like this

```php
<?php
array(
    'test_1' => array(
        'msg_1' => 'this is the first desc',
        'msg_2' => 'this is the second desc',
    ),
    'test_2' => 'this is the third desc',
    'test_3' => array(
        'msg_1' => array(
            'foo_1' => 'this is the fourth desc',
        ),
        'msg_2' => 'this is the fifth desc',
    ),
    'this is the sixth desc',
);
```

## Desired result

What we want the output is

```
Array
(
    [0] => this is the first desc
    [1] => this is the second desc
    [2] => this is the third desc
    [3] => this is the fourth desc
    [4] => this is the fifth desc
    [5] => this is the sixth desc
)
```

## Source code

```php
<?php
function restructure_array($obj) {
    if (!is_array($obj)) {
        return array($obj);
    }

    $single_array = array();
    foreach ($obj as $k => $sub_obj) {
        $tmp = restructure_array($sub_obj);
        $single_array = array_merge($single_array, $tmp);
    }
    return $single_array;
}
```
