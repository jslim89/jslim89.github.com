---
title: MySQL - a trick to filter multi-values column
date: 2019-02-19 22:22:19
tags:
  - mysql
---

For certain reason, sometime we want to filter the data in a multi-values column.

e.g.

`books`

| id  | isbn          | title            |
| --- | ------------- | ---------------- |
| 1   | 8-230185-1321 | The Secret C++   |
| 2   | 23801-23815-9 | MySQL for tummy  |
| 3   | 78-923722-223 | Programmer Bible |

`users`

| id  | username | name   |
| --- | -------- | ------ |
| 1   | js       | JS     |
| 2   | foo      | Mr Foo |
| 3   | bar      | Ms Bar |

`user_books`

| book_id | user_id | date                |
| ------- | ------- | ------------------- |
| 2       | 1       | 2019-01-03 12:38:29 |
| 1       | 3       | 2019-01-08 18:08:09 |
| 3       | 2       | 2019-01-13 22:37:12 |

Let say now want to filter the books has borrowed by user ID 1 & 3

If normal select

```sql
SELECT books.id AS book_id
  , books.isbn
  , books.title
  , t_borrowers.user_ids
FROM books
  LEFT JOIN user_books ON user_books.book_id = books.id
WHERE user_books.user_id IN (1, 3);
```

Filter in single column

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

The trick is, to add a symbol _(here I use #)_ to wrap the ID when concat the values,
then in bottom there use LIKE & OR to filter, remember to wrap around the ID when filter.

This example may not exactly shows up the purpose of using sub-query & wrap the value with symbol.
It look complicated & uncessary, but, in some cases may need to do in this way.
