---
layout: post
title: "How to use Apache .htpasswd"
date: 2014-05-06 08:52:18 +0800
comments: true
tags: 
- setup-configuration
- apache
---

In order to prevent your site from outsider, you may need to add a password prompt so that only authenticated user can access.

![Password prompt](http://jslim89.github.com/images/posts/2014-05-06-how-to-use-apache-htpasswd/password-prompt.png)

To achieve this, [Apache .htpasswd](http://httpd.apache.org/docs/2.0/en/programs/htpasswd.html) helps.

Before that, create a **.htaccess** to project's document root _(e.g. if the document root is in project/public, then it should located project/public/.htaccess)_ contains the following content

**.htaccess**

```apache
AuthUserFile /absolute/path/to/your/project/document_root/.htpasswd
AuthGroupFile /dev/null
AuthName "Js's protected files"
AuthType Basic

<Limit GET>
require valid-user
</Limit>
```

The file is using absolute path, if relative path, it will point to **apache** document root.

Create a .htpasswd with some users

```
$ cd /absolute/path/to/your/project/document_root
# -c option is used when the .htpasswd file doesn't exists, so it will create a new file
$ htpasswd -c .htpasswd admin
New password:
Re-type new password:
Adding password for user admin

# add another user
$ htpasswd .htpasswd jslim
New password:
Re-type new password:
Adding password for user jslim

# verify that 2 users are in the .htpasswd
$ tail .htpasswd
admin:$apr1$5OC.YPUn$sM17ex8wLCKZADIUx8J.6/
jslim:$apr1$z6OQQjv9$8mGc3Vw6a8fLfHiuTTqkw.
```

**Note that the password here is encrypted.**

Now you open up your browser, type in the URL, you will see the prompt before you actually see the content

_Reference:_ _[Password Tutorial](http://www.colostate.edu/~ric/htpass.html)_
