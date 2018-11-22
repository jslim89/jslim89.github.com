---
layout: post
title: "ZendFramework 2 - Construct AND OR SQL"
date: 2013-04-23 18:32
comments: true
tags: 
- php
- programming
- zend
---

I believe that almost all the web applications have a function **Search**, thus you might need to construct a query like:

```sql
SELECT *
FROM foo INNER JOIN bar ON foo.id = bar.foo_id
WHERE (foo.attr_1 LIKE '%abc%' OR foo.attr_2 LIKE '%abc%')
AND (bar.attr_1 LIKE '%xyz%' OR bar.attr_2 LIKE '%xyz%')
```

The **OR** is grouped together with **AND**, I'll show how to construct this query in ZF2

In your model table, says **./module/Application/src/Application/Model/FooTable.php**

```php
<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate;

class FooTable
{
    protected $table_gateway;
    protected $sql;

    public function __construct(TableGateway $table_gateway)
    {
        $this->table_gateway = $table_gateway;
        $this->sql = new Sql($this->table_gateway->adapter);
    }
    ...

    public function searchByKeyword($keyword_foo, $keyword_bar)
    {
        $result_set = $this->table_gateway->select(function(Select $select) use ($keyword_foo, $keyword_bar) {
            $select
                ->join(
                    'bar',
                    'foo.id = bar.foo_id',
                    array(
                        'bar_attr' => 'attr',
                        ...
                    )
                )
                ->where(array(
                    new Predicate\PredicateSet(
                        array(
                            new Predicate\Like('foo.attr_1', '%' . $keyword_foo . '%'),
                            new Predicate\Like('foo.attr_2', '%' . $keyword_foo . '%'),
                        ),
                        Predicate\PredicateSet::COMBINED_BY_OR
                    ),
                ));
            ;
            if ($keyword_bar) {
                $select->where(array(
                    new Predicate\PredicateSet(
                        array(
                            new Predicate\Like('bar.attr_1', '%' . $keyword_bar . '%'),
                            new Predicate\Like('bar.attr_2', '%' . $keyword_bar . '%'),
                        ),
                        Predicate\PredicateSet::COMBINED_BY_OR
                    ),
                ));
            }
            // print out the search string for verification
            echo $this->sql->getSqlStringForSqlObject($select);
        });
        return $result_set;
    }
    ...
}
```

In your controller

```php
<?php
...
public function searchAction()
{
    $result = $this->getFooTable()->searchByKeyword('abc', 'xyz');
    print_r($result); // verify the results
}
...
```

_References:_

* _[ZF2 How to orWhere()](http://stackoverflow.com/questions/13056820/zf2-how-to-orwhere#answers)_
* _[Fetching results from a mysql query in ZF2](http://stackoverflow.com/questions/15097328/fetching-results-from-a-mysql-query-in-zf2)_
