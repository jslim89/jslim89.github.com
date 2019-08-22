---
title: How to prevent duplicated join of table in Laravel Query Builder
date: 2019-08-22 22:07:30
tags:
- laravel
- php
- query-builder
- eloquent
---

Laravel [Query Builder](https://laravel.com/docs/5.8/queries) is an awesome feature, really.

When comes to generate a complicated report, with complicated filter/sort, the code logic can be very confusing.

I've encouter a problem with 1 of the complex report, duplicated table join.
The controller method has over 1,500 lines.

Thus, I figure out that we can get the joined tables from Laravel Query.

The helper function as below, we can check if we have joined the table before.

```php
<?php
/**
 * Check if the table has already been joined in the query

 * @param \Illuminate\Database\Query\Builder $query
 * @param string $table
 * @return bool
 */
function has_table_joined(\Illuminate\Database\Query\Builder $query, $table)
{
    if (empty($query->joins)) return false;
	foreach ($query->joins as $join) {
		if (is_string($join->table) && $join->table == $table) {
			return true;
		}
		if (!is_string($join->table)) {
			preg_match('(\w+)', (string)$join->table, $matches);
			if (count($matches) > 0 && $matches[0] == $table) {
				return true;
			}
		}
	}
	return false;
}
```

Usage

```php
<?php
$query = \DB::table('table_name')->select(...);
...
if ($some_filter) {
    if (!has_table_joined($query, 'table_foo')) {
        $query->join('table_foo', ...);
    }
    $query->where(...);
}
...
```
