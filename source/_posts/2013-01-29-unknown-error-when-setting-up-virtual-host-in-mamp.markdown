---
layout: post
title: "Unknown error when setting up virtual host in MAMP"
date: 2013-01-29 15:49
comments: true
categories: 
- setup-configuration
- mac
---

I'm struggling for half day just to setup virtual host in MAMP.

**/Applications/MAMP/conf/apache/extra/httpd-vhosts.conf**
```
NameVirtualHost *:80

<VirtualHost *:80>
    ServerAdmin webmaster@dummy-host.example.com
    DocumentRoot "/path/to/project"
    ServerName www-yourproject.com
    <Directory "/path/to/project">
        Options All
        AllowOverride All
        Order allow,deny
        allow from all
    </Directory>
</VirtualHost>
```
and for the file **/Applications/MAMP/conf/apache/httpd.conf**

Change from
```
# Virtual hosts
#Include /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
```
to
```
# Virtual hosts
Include /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
```
to uncomment the httpd-vhosts.conf

In **/etc/hosts**, add a line below
```
127.0.0.1	www-yourproject.com
```

I've done all these setting, but the MAMP still not able to start.

At the end, I figured out that the error was occurred in
```
<Directory "/path/to/project">
    Options All
    AllowOverride All
    Order allow,deny
    allow from all
</Directory>
```
this portion. It look correct and no mistake at all.

After struggling for half day, I re-type that portion of code, finally it works.

Damnnnn, it was those special characters error which we cannot see.

May be I'm too lucky to meet such a situation :)
