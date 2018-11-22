---
layout: post
title: "Apache Virtual Host accessed by other machines in LAN"
date: 2013-12-14 20:29
comments: true
tags: 
- linux
- configuration
- apache
---

## Environment

This is tested on Ubuntu 13.04 that running on VMWare Fusion 5. Host machine is MacBookPro that running Mavericks.

Most of the time in our development, we have to work on different projects.

We have to setup virtual host for every project.

## Scenario
In this case is we have 2 projects that used the same port number. **foo** project & **bar** project.

Later a software tester will need to test the project on his own machine.

`Machine A` has the IP **192.168.1.12**. `Machine B` has the IP **192.168.1.13**.

### In `Machine A` (server machine that hosts all projects)
i.e. Add files to `/etc/apache2/sites-available/`

**foo.com**

```apache
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    ServerName local.foo.com
    ServerAlias local.foo.com
    DocumentRoot /home/user/public_html/foo
    SetEnv APPLICATION_ENV "development"

    <Directory "/home/user/public_html/foo/">
        DirectoryIndex index.php
        Options All
        AllowOverride All
        Order allow,deny
        allow from all
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log

    LogLevel warn

    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

and

**bar.com**

```apache
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    ServerName local.bar.com
    ServerAlias local.bar.com
    DocumentRoot /home/user/public_html/bar
    SetEnv APPLICATION_ENV "development"

    <Directory "/home/user/public_html/bar/">
        DirectoryIndex index.php
        Options All
        AllowOverride All
        Order allow,deny
        allow from all
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log

    LogLevel warn

    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Activate the virtual host

```
$ sudo a2ensite foo.com
$ sudo a2ensite bar.com
# Reload apache service
$ sudo service apache2 reload
```

Edit the hosts file `/etc/hosts`, add the following lines

```text
127.0.0.1 local.foo.com
127.0.0.1 local.bar.com
```

### In `Machine B` (client that want to test the projects)

Edit the hosts file `/etc/hosts`, add the following lines

```text
192.168.1.12 local.foo.com
192.168.1.12 local.bar.com
```

_(`local.foo.com` and `local.bar.com` is `ServerAlias`)_

## Testing
For `Machine B`, open up the browser, and type in the URL **local.foo.com** will see the `foo` site; and **local.bar.com** will see the `bar` site.

Reference: [Apache Virtual Host (Subdomain) access with different computer on LAN](http://stackoverflow.com/questions/7141634/apache-virtual-host-subdomain-access-with-different-computer-on-lan/7146132#7146132)
