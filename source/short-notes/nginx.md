---
layout: page
title: Nginx
permalink: /short-notes/nginx/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

https://www.nginx.com/

#### Laragon setup laravel always enforce no trailing slash

To allow optional trailing slash, change the nginx settings

```
rewrite ^([^.]*[^/])$ $1/ permanent;
```
