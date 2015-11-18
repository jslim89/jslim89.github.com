---
layout: post
title: "MySQL search order by relevance column using FULLTEXT"
date: 2014-01-23 10:39:03 +0800
comments: true
categories: 
- mysql
---

I've blamed by one of the client and says that the keyword that match the title shown at bottom. So want me to order by column match.

Finally I found the solution which is [Full-Text Search](http://dev.mysql.com/doc/refman/5.5/en/fulltext-search.html).

**NOTE: In MySQL 5.5, full text search only applicable on MyISAM, only MySQL 5.6 onward can used in InnoDB**

Let say I have 2 tables: `book` & `category`

```sql
CREATE TABLE `book` (
    `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(100) NOT NULL,
    `description` varchar(1000) NOT NULL,
    `keywords` varchar(200) NOT NULL,
    `category_id` int(8) unsigned,
    PRIMARY KEY (`id`)
)  ENGINE=MyISAM;

CREATE TABLE `category` (
    `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` varchar(1000) NOT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=MyISAM;
```

Before using the full text search, index the columns

```sql
ALTER TABLE book ADD FULLTEXT(title, keywords, description);
ALTER TABLE category ADD FULLTEXT(name, description);
```

Let's verify that it already in Full Text _(e.g. shows `book` table's column)_

```sql
SELECT index_name, group_concat(column_name) as columns
FROM information_Schema.STATISTICS 
WHERE table_schema = 'my_db_name' 
AND table_name = 'book'
AND index_type = 'FULLTEXT'
GROUP BY index_name
```

**Objective: search result must shows the result that match the `title` first, then book's `keywords` & `description`, followed by category's `name` & `description`**

Let see how the sql look like

```sql
SELECT book.*
, MATCH (book.title) AGAINST ('PHP MySQL' IN BOOLEAN MODE) AS relevance_1
, MATCH (book.keywords, book.description) AGAINST ('PHP MySQL' IN BOOLEAN MODE) AS relevance_2
, MATCH (category.name, category.description) AGAINST ('PHP MySQL' IN BOOLEAN MODE) AS relevance_3
FROM book
LEFT JOIN category ON category.id = book.category_id
WHERE MATCH (book.title, book.keywords, book.description, category.name, category.description) AGAINST ('PHP MySQL' IN BOOLEAN MODE)
ORDER BY (relevance_1 * 3) + (relevance_2 * 2) + relevance_3 DESC
```

The `IN BOOLEAN MODE` will return result either **1** or **0**.

In the last row (`ORDER BY`), there is multiplication, that is weightage

* `relevance_1 * 3` - the most important
* `relevance_2 * 2` - the second important
* `relevance_3` - the least important (no multiply anything)

So order them descendingly will give the result most important first _(higher the number, the more important)_

_References:_

* _[How can I manipulate MySQL fulltext search relevance to make one field more 'valuable' than another?](http://stackoverflow.com/questions/547542/how-can-i-manipulate-mysql-fulltext-search-relevance-to-make-one-field-more-val/600915#600915)_
* _[Show a tables FULLTEXT indexed columns](http://stackoverflow.com/questions/4107599/show-a-tables-fulltext-indexed-columns/4107794#4107794)_
