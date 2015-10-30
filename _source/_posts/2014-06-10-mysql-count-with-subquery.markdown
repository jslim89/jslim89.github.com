---
layout: post
title: "MySQL count with subquery"
date: 2014-06-10 08:35:30 +0800
comments: true
categories: 
- mysql
---

# Example

Below shows a forum web app. A `post` can only post by 1 `user`, its `reply` can reply by many `users`

## Tables:

**post**

```
+-------------------+------------------+------+-----+---------+----------------+
| Field             | Type             | Null | Key | Default | Extra          |
+-------------------+------------------+------+-----+---------+----------------+
| id                | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| title             | varchar(128)     | NO   |     | NULL    |                |
| user_id           | int(10) unsigned | NO   |     | 0       |                |
| published_date    | datetime         | YES  |     | NULL    |                |
+-------------------+------------------+------+-----+---------+----------------+
```

**reply**

```
+-------------------+------------------+------+-----+---------+----------------+
| Field             | Type             | Null | Key | Default | Extra          |
+-------------------+------------------+------+-----+---------+----------------+
| id                | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| message           | varchar(128)     | NO   |     | NULL    |                |
| post_id           | int(10) unsigned | NO   |     | 0       |                |
| user_id           | int(10) unsigned | NO   |     | 0       |                |
| published_date    | datetime         | YES  |     | NULL    |                |
+-------------------+------------------+------+-----+---------+----------------+
```

**user**

```
+-------------------+------------------+------+-----+---------+----------------+
| Field             | Type             | Null | Key | Default | Extra          |
+-------------------+------------------+------+-----+---------+----------------+
| id                | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| name              | varchar(128)     | NO   |     | NULL    |                |
| email             | varchar(128)     | NO   |     | NULL    |                |
+-------------------+------------------+------+-----+---------+----------------+
```

## Desired result

We want to know all topics `total_users`, `unique_users`, i.e.

```
+-------------------+------------------+--------------+----------------+
| Topic             | Total Users      | Unique Users | Published Date |
+-------------------+------------------+--------------+----------------+
```

```sql query.sql
SELECT topic.name AS topic_name
    , COUNT(reply.user_id) AS total_users
    , (SELECT COUNT(DISTINCT user_id) FROM reply WHERE topic_id = topic.id) AS unique_users
    , topic.published_date
FROM topic INNER JOIN reply ON topic.id = reply.topic_id
GROUP BY topic.id
ORDER BY unique_users
```

See the _3rd line_ of the query, we can select item from inner query and passing the value from outer query to it.
