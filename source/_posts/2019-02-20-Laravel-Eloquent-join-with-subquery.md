---
title: Laravel Eloquent join with subquery
date: 2019-02-20 00:03:52
tags:
  - laravel
  - laravel5
  - eloquent
  - subquery
  - mysql
---

Let's take the query from [this post](/blog/2019/02/19/MySQL-a-trick-to-filter-multi-values-column/) as example

```sql
SELECT books.id AS book_id
  , books.isbn
  , books.title
  , t_borrowers.user_ids
FROM books
  LEFT JOIN (
    SELECT user_books.book_id
      , CONCAT('#', GROUP_CONCAT(user_books.user_id SEPARATOR '#,#'), '#') AS user_ids
    FROM user_books
    GROUP BY user_books.book_id
  ) AS t_borrowers ON t_borrowers.book_id = books.id
WHERE (t_borrowers.user_ids LIKE '%#1#%' OR t_borrowers.user_ids LIKE '%#3#%');
```

See there's a sub-select inside? Let's see how to construct this query in Laravel

`\App\Models\Book` class map to the table `books`

```php
<?php

// inner select
$subquery = \DB::table('user_books')
    ->select([
        'user_books.book_id',
        \DB::raw('CONCAT(\'#\', GROUP_CONCAT(user_books.user_id SEPARATOR \'#,#\'), \'#\') AS user_ids'),
    ])->groupBy('user_books.book_id');

$query = \App\Models\Book::select([
    \DB::raw('books.id AS book_id'),
    'books.isbn',
    'books.title',
    't_borrowers.user_ids',
])
    ->leftJoinSub($subquery, 't_borrowers', function ($join) {
        $join->on('t_borrowers.book_id', '=', 'books.id');
    })
    ->where(function ($q) {
        $q->orWhere('t_borrowers.user_ids', 'LIKE', '#' . 1 . '#')
            ->orWhere('t_borrowers.user_ids', 'LIKE', '#' . 3 . '#');
    });
```

By using the `leftJoinSub` to construct the query
