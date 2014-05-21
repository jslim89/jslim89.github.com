---
layout: post
title: "Restructure a series of numbers"
date: 2014-05-21 20:40:16 +0800
comments: true
categories: 
- php
---

Example:

```
+-----------------+----------------------------------------+
| Input           | 2, 4, 2, 5, 8, 7, 7, 6, 13, 11, 11, 12 |
+-----------------+----------------------------------------+
| Expected result | 1, 2, 1, 3, 6, 5, 5, 4,  9,  7,  7,  8 |
+-----------------+----------------------------------------+
```

The **input** contains missing numbers which are `1`, `3`, `9`, `10`. Now the mission is to remove the gap in missing numbers.

{% img http://jslim89.github.com/images/posts/2014-05-21-restructure-a-series-of-numbers/mapping.png Input - result mapping %}

See the mapping above:

- infront of `2` there is **ONE** missing number
- infront of `4`, `5`, `6`, `7`, `8` there are **TWO** missing number
- infront of `11`, `12`, `13` there are **FOUR** missing number

Can see the pattern?

Now, lets implement it in PHP

```php
<?php
$input = [2,4,2,5,8,7,7,6,13,11,11,12];

$max_input = max($input);
// find the missing value in ascending order & avoid duplication
$missing_values = [];
for ($i = 1; $i <= $max_input; $i++) {
    // skip if current value is not missing OR is already inside the $missing_values container
    if (in_array($i, $input) || in_array($i, $missing_values)) continue;
    $missing_values[] = $i;
}

$results = [];
foreach ($input as $i) { // goes through all input values
    $diff = 0; // this is the `-1`, `-2`, `-4` (green color) on the mapping there
    foreach ($missing_values as $mv) {
        if ($i < $mv) break;
        $diff++; // count the number of missing values infront of current value
    }
    // finally minus the difference will get the desired result
    $results[] = $i - $diff;
}
```

The output `$results` will be

```
Array
(
    [0] => 1
    [1] => 2
    [2] => 1
    [3] => 3
    [4] => 6
    [5] => 5
    [6] => 5
    [7] => 4
    [8] => 9
    [9] => 7
    [10] => 7
    [11] => 8
)
```

Done :)
