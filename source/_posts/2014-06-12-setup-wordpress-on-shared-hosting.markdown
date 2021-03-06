---
layout: post
title: "Setup Wordpress on shared hosting"
date: 2014-06-12 21:38:06 +0800
comments: true
tags: 
- php
- wordpress
---

# Description
Now we want to setup WordPress manually on shared hosting.

## 1. Download WordPress
First of all, [download the WordPress source files](http://wordpress.org/download/). See the screenshot below

![Download WordPress](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/download-wp.png)

Once completed download, unzip it, and you will see all the source files

![WordPress source files](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/wp-files.png)

## 2. Access to your hosting server via FTP

[FTP](http://en.wikipedia.org/wiki/File_Transfer_Protocol) is File Transfer Protocol. Means that you need to upload the souce files
to server via FTP.

You will need a FTP client. You can get a free 1, [FileZilla](https://filezilla-project.org/) is one of the most widely use
FTP client. But in this tutorial I'm using [ForkLift](http://www.binarynights.com/forklift/).

### i. Login

![FTP login](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/ftp-login.png)

You can see the pink box there, **Server** typically is your URL _(or more precise is domain name)_. You will have this
**Username** & **Password** after you subscribed the hosting plan. The **Port** just leave it `21`.

### ii. Copy wordpress to the hosting server

![Copy wordpress over](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/copy-wordpress-over.png)

Make sure is inside **public_html** folder. Then edit the file **.htaccess** _(if you don't have, create it)_ with the following content.

```apache
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{HTTP_HOST} ^www.jslim89.com
RewriteRule ^(.*)$ http://jslim89.com/$1 [R=301,L]
RewriteRule ^$ wordpress/    [L]
RewriteRule (.*) wordpress/$1 [L]
</IfModule>
```

Remember change it to your domain name _(e.g. replace `jslim89.com` to yourdomain.com)_. The line 3 & 4 is when user type in the url
[www.jslim89.com](http://jslim89.com), it will redirect to [jslim89.com](http://jslim89.com) _(without `www`)_

Now go to your browser, type in your URL...

![Internal Server Error](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/internal-server-error.png)

Oopsss!!! What is this? You're not setup properly.

## 3. Configure in cPanel or DirectAdmin
[cPanel](http://cpanel.net/) & [DirectAdmin](http://www.directadmin.com/) are kind of web control panel for your hosting.

I'm showing all these in cPanel.

### i. Login to cPanel
You will get the login info after you subscribe the hosting plan.

![cPanel login](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/cpanel-login.png)

### ii. Create a new database for WordPress

First of all, look for this

![MySql database](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/mysql-database.png)

Then create a database _(there is a prefix over there, we cannot change it)_, make sure its name is unique.

![Create MySQL database](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/create-db.png)

You have to create a database user for this particular database for security purpose, **ONE** user for **ONE** database.

![Create MySQL user](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/create-user.png)

Assign the user for the database you've just created.

![Assign user to database](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/assign-user-to-db.png)

At the end you will see this

![Database creation complete](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/mysqldb-result.png)

## 4. Configure WordPress
Go back to FTP client, look for the file named **wp-config-sample.php** in your **wordpress** folder. Rename the file to **wp-config.php**

Edit the file

### i. Change database settings

![Database config](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/config-db-settings.png)

Change the settings to what you set in cPanel.

### ii. Auth secret keys

Go to [https://api.wordpress.org/secret-key/1.1/salt/](https://api.wordpress.org/secret-key/1.1/salt/), you will get a bunch of "dummy text"

![Replace secret key](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/config-secret-key.png)

Replace with the text you get from the URL

### iii. Add another .htaccess file

Add another **.htaccess** file with the following content to your wordpress folder.

```apache
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]

# add a trailing slash to /wp-admin
RewriteRule ^wp-admin$ wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^(wp-(content|admin|includes).*) $1 [L]
RewriteRule ^(.*\.php)$ $1 [L]
RewriteRule . index.php [L]
```

## 5. Setup your site
Now you refresh your browser, you will see a setup page.

![WordPress setup page](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/wp-welcome.png)

The username name is for you to login and post your blog content, so make sure you remember the username & password well.

Make sure you check _Allow search engines to index this site._ if you want to have more people reach you.

Now click on **Install WordPress** button, then login with the username you set just now.

## 6. Done
You will see the admin panel once successful login.

![WordPress admin panel](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/wp-admin.png)

By default, it comes with 1 post `Hello World!`. So if you want to know what end user see, click on **View site** button on top left corner.

![WordPress frontend](/images/posts/2014-06-12-setup-wordpress-on-shared-hosting/wp-frontend.png)

You are done, you can start posting your content now.

_References:_

* _[WordPress htaccess](http://codex.wordpress.org/htaccess)_
