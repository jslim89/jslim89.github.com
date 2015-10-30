---
layout: post
title: "ZendFramework 2 - Construct SQL with subquery"
date: 2013-04-24 15:22
comments: true
categories: 
- php
- zend
- programming
---

A scenario here:

A **Q & A** website that allow user to ask question and put several **tags** to that question.

Database tables:

**tag**  
```
+--------------+-------------+------+-----+---------+----------------+
| Field        | Type        | Null | Key | Default | Extra          |
+--------------+-------------+------+-----+---------+----------------+
| id           | int(11)     | NO   | PRI | NULL    | auto_increment |
| title        | varchar(30) | NO   |     | NULL    |                |
| description  | varchar(100)| NO   |     | NULL    |                |
+--------------+-------------+------+-----+---------+----------------+
```

**tag_rel**  
```
+--------------+-------------+------+-----+---------+----------------+
| Field        | Type        | Null | Key | Default | Extra          |
+--------------+-------------+------+-----+---------+----------------+
| id           | int(11)     | NO   | PRI | NULL    | auto_increment |
| tag_id       | varchar(30) | NO   |     | NULL    |                |
| question_id  | varchar(100)| NO   |     | NULL    |                |
+--------------+-------------+------+-----+---------+----------------+
```

**question**  
```
+--------------+-------------+------+-----+---------+----------------+
| Field        | Type        | Null | Key | Default | Extra          |
+--------------+-------------+------+-----+---------+----------------+
| id           | int(11)     | NO   | PRI | NULL    | auto_increment |
| description  | varchar(100)| NO   |     | NULL    |                |
+--------------+-------------+------+-----+---------+----------------+
```

Now want to get those tags related to a tag with ID **100**, the query could be
```sql
SELECT tag.*
FROM tag
INNER JOIN tag_rel ON tag.id = tag_rel.tag_id
WHERE tag.id <> 100
AND tag_rel.question_id IN
(
    SELECT tag_rel.question_id
    FROM tag
    INNER JOIN tag_rel ON tag.id = tag_rel.tag_id
    WHERE tag.id = 100
)
```

Now want to construct this query in Zend Frameword 2

In your model table, says **./module/Application/src/Application/Model/TagTable.php**
```php
<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class TagTable
{
    protected $table_gateway;
    protected $sql;

    public function __construct(TableGateway $table_gateway)
    {
        $this->table_gateway = $table_gateway;
        $this->sql = new Sql($this->table_gateway->adapter);
    }
    ...
    public function getRelatedTagsByTag($id)
    {
        $result_set = $this->table_gateway->select(function(Select $select) use ($id) {
            $subselect = $this->sql->select();
            $subselect
                ->columns(array())
                ->from('tag')
                ->join(
                    'tag_rel',
                    'tag.id = tag_rel.tag_id',
                    array(
                        'question_id',
                    )
                )
                ->where(array('tag_rel.tag_id = ?' => $id,))
            ;

            $select
                ->join(
                    'tag_rel',
                    'tag.id = tag_rel.tag_id',
                    array(
                    )
                )
                ->where(array('tag.id <> ?' => $id))
                ->where->in('tag_rel.question_id', $subselect)
            ;
            // uncomment the next line to see your query
            // echo $select->getSqlString();
        });

        // don't use DISTINCT in sql due to performance issue
        // Get unique records
        $distincted_result = array();
        foreach ($result_set as $rowset) {
            if (!in_array($rowset, $distincted_result)) {
                $distincted_result[] = $rowset;
            }
        }
        return $distincted_result;
    }
    ...
}
```

The performance may be sux, in order to solve this, just index `tag_rel.tag_id` and `tag_rel.question_id`.
```sql
mysql > ALTER TABLE `tag_rel` ADD INDEX (`tag_id`);
mysql > ALTER TABLE `tag_rel` ADD INDEX (`question_id`);
```

_References:_

* _[Zend\Db\Sql](http://zf2.readthedocs.org/en/latest/modules/zend.db.sql.html#in-identifier-array-valueset-array)_
* _[Zend Framework 2: sql subquery](http://stackoverflow.com/questions/13110257/zend-framework-2-sql-subquery)_
