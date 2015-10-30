---
layout: post
title: "SQL - SELECT SUM joined with another table"
date: 2013-02-06 15:44
comments: true
categories: 
- sql
---

I had always face a scenerio like this:

There are 2 tables
```
user(id, first_name, last_name)
transaction(id, qty, user_id)
```

Now I want to SUM all the quantity based on **user**, typically what I did is
```sql
SELECT user_id, SUM(qty) as total
FROM transaction
GROUP BY user_id
ORDER BY total DESC
```

But the problem is, I want the **first_name** and **last_name** as well. How to get this in one query?

I had found a solution [here](http://stackoverflow.com/questions/4276785/how-to-get-sum-from-joined-table-b-with-multiple-results-againts-one-row-in-tabl#answers):
```sql
SELECT id, first_name, last_name, total
FROM user u
JOIN (
  SELECT user_id, SUM(qty) as total
  FROM transaction
  GROUP BY user_id
) AS t ON u.id = t.user_id
ORDER BY total DESC
```
