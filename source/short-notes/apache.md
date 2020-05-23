---
layout: page
title: Apache
permalink: /short-notes/apache/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

https://httpd.apache.org/

#### Update memory limit

```
<IfModule mod_rewrite.c>
    # ...

    # php.ini override
    php_value max_execution_time 300
    php_value memory_limit 512M
</IfModule>
```
