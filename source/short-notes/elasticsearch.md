---
layout: page
title: Elasticsearch   
permalink: /short-notes/elasticsearch/
date: 2020-05-22 21:13:51
comments: false
sharing: true
footer: true
---

https://www.elastic.co/

### Retrieve

#### Multiple `where` condition

```
GET /index/type/_search
{
   "query": {
      "bool": {
         "must": [
            {
               "term": {
                  "field1": {
                     "value": "foo"
                  }
               }
            },
            {
               "term": {
                  "field2": {
                     "value": "bar"
                  }
               }
            }
         ]
      }
   },
   "from": 0,
   "size": 20
}
```

equivalent to

```sql
SELECT *
FROM index
WHERE field1 = 'foo'
  AND field2 = 'bar'
LIMIT 20 OFFSET 0;
```

---

### Update

#### Bulk update

```
POST /index/type/_update_by_query
{
  "conflicts": "proceed",
  "script": {
    "source": "ctx._source['field'] = 'some value'"
  },
  "query": {
    "term": {
      "user": "kimchy"
    }
  }
```

equivalent to

```sql
UPDATE index
SET field = 'some value'
WHERE user = 'kimchy';
```

OR if want to add json value

```
POST /index/type/_update_by_query
{
  "conflicts": "proceed",
  "script": {
    "source": "ctx._source.field = params.some_json_field",
    "params": {
      "some_json_field": {
        "sub_field_1": "Foo",
        "sub_field_2": "Bar"
      }
    }
  },
  "query": {
   "term": {
     "user": "kimchy"
   }
  }
}
```

After run update, must flush it

```
GET /index/type/_flush
```

##### References:

- [Update By Query API](https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-update-by-query.html)
- [How to add a json object to multiple documents in a Elastic index using _update_by_query?](https://stackoverflow.com/questions/46927871/how-to-add-a-json-object-to-multiple-documents-in-a-elastic-index-using-update/46930821#46930821)

---

#### GROUP BY query

Translate

```
SELECT COUNT(*) as doc_count
FROM index
GROUP BY field_name
ORDER BY doc_count DESC
LIMIT 10
```

equivalent to

```
GET /index/type/_mapping
{
   "aggregations": {
      "some_name": {
         "terms": {
            "field": "field_name",
            "size": 10
         }
      }
   },
   "size": 0
}
```

##### References:

- [ElasticSearch group by documents field and count occurences](https://stackoverflow.com/questions/58733898/elasticsearch-group-by-documents-field-and-count-occurences/58734262#58734262)

---

#### Add new field

Update the mapping first

```
PUT /index/type/_mapping
{
  "properties": {
    "new_field": {
      "type": "keyword"
    }
  }
}
```

----

#### Copy data from source index to dest index

```
POST _reindex
{
   "source": {
      "index": "source_idx",
      "type": "product"
   },
   "dest": {
      "index": "destination_idx",
      "type": "product"
   }
}
```

##### References:

- [ElasticSearch - Reindex API](https://www.elastic.co/guide/en/elasticsearch/reference/6.4/docs-reindex.html)

----

#### Sort by nested object count

```
GET index_name/_doc/_search
{
    "sort" : {
        "_script" : {
            "script": "params['_source']['some_nested_objects'].size()",
            "order": "desc",
            "type" : "number"
        }
    }
}
```
