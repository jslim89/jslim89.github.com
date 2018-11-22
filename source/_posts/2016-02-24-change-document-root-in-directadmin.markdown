---
layout: post
title: "Change document root in DirectAdmin"
date: 2016-02-24 11:46:03 +0800
comments: true
tags: 
- DirectAdmin
- apache
---

For those of you developing [Laravel](https://laravel.com/) application, sure you have face this issue if you're hosted in DirectAdmin hosting.

Besides, if you want to setup auto deployment like [Rocketeer](http://rocketeer.autopergamene.eu/#/docs/rocketeer/README), also you will need to change the document root.

Let's take an example in my scenario, Laravel + Rocketeer auto deployment

## 1. Login to your DirectAdmin, and go to "Custom HTTPD Configurations"

![DirectAdmin home page](/images/posts/2016-02-24-change-document-root-in-directadmin/direct-admin-home.png)

## 2. Choose the site that you want to edit

![DirectAdmin select domain](/images/posts/2016-02-24-change-document-root-in-directadmin/select-domain.png)

## 3. Update the vhost config

![DirectAdmin change virtual host](/images/posts/2016-02-24-change-document-root-in-directadmin/change-docroot.png)

```
|*if !SUB|
ServerAlias *.|DOMAIN|
|?DOCROOT=/home/admin/domains/yourdomain.com/public_html/yourdomain.com/current/public|
|*endif|
```

As I mention just now, using Rocketeer in this case, thus it will point to **current** directory _(softlink)_, and **public** is because of laravel project.

## 4. Restart web server

Login with **root** user, then restart

```sh
$ service httpd restart
```

Now it should works.
