---
layout: post
title: "Python: Make a GET request to get JSON data"
date: 2013-04-04 16:04
comments: true
tags: 
- python
---

Make a HTTP request to get JSON content from website.

Create a file **json_helper.py**

```py
import urllib2
import json

def get_json_by_user_id(user_id):
    header = {'Content-Type': 'application/json',}
    req = urllib2.Request('http://www.example.com/getmyjson?user_id=' + user_id)
    website = urllib2.urlopen(req)

    return json.loads(website.read())
```

Usage:

```py
from json_helper import get_json_by_user_id

print get_json_by_user_id(123)
```

_Reference: [Extract Hyperlinks Using Python and PHP](http://kianmeng.org/blog/2013/03/11/extract-hyperlinks-using-python-and-php/)_
