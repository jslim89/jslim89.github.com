---
layout: post
title: "Setup PHP environment in Mavericks using XAMPP"
date: 2014-01-17 05:38
comments: true
tags: 
- mac
---

I've been using [MAMP](http://www.mamp.info/) for some time, unfortunately, it seems like outdated _(seldom update)_, now I've switched to [XAMPP](http://www.apachefriends.org/en/xampp.html) and it is up to date.

To setup a PHP environment in Mac OS X 10.9:

## 1. Download XAMPP
You can download the latest version [here](http://www.apachefriends.org/en/xampp-macosx.html). Then install it.

**Bare in mind that by default, MySQL root user has no password, but will set it later**

### Installation

![Install XAMPP](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/installation.png)

Once completed, launch it

![Complete install XAMPP](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/complete-installation.png)

### Start MySQL server

![Start MySQL server](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/start-mysql-server.png)

1. Select **Manage Servers** tab
2. Select **MySQL Database**
3. Start it

Then close it.

## 2. Add some PHP projects

Hit **Command `⌘`** + **Space** key, type in `terminal` then hit **Enter** key.

![Search for Terminal](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/open-terminal.png)

### Create project A

Create a directory to keep all projects _(e.g. public_html)_

```
$ mkdir ~/public_html
```

Create a project to that directory

```
$ mkdir ~/public_html/project_a
```

Create a home page for it

```
$ touch ~/public_html/project_a/index.php
```

then add the following content

**index.php**

```php
<!DOCTYPE html>
<html>
<body>

    <h1>Welcome to Foo Site</h1>

    <p><?php echo 'Here you can put dynamic content.'; ?></p>

</body>
</html>
```

### Create project B

For testing purpose, just duplicate the project A

```
$ cp -r ~/public_html/project_a ~/public_html/project_b
```

Edit the file **~/public_html/project_b/index.php**

```php
<!DOCTYPE html>
<html>
<body>

    <h1>Welcome to Bar Site</h1> <!-- change the header to differentiate them -->

    <p><?php echo 'Here you can put dynamic content.'; ?></p>

</body>
</html>
```

Done.

## 3. Setup virtual host

Now we have 2 projects, so we use virtual host _(setup different domains)_ to differentiate them

Navigate to XAMPP directory

```
$ cd /Applications/XAMPP/etc/
```

Edit the file named **httpd.conf**, search for `httpd-vhosts`, you will see the line

```apache
...

# Virtual hosts
#Include etc/extra/httpd-vhosts.conf

...
```

uncomment the line, i.e.

```apache
...

# Virtual hosts
Include etc/extra/httpd-vhosts.conf

...
```

Now will look like

![Uncommented vhosts](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/uncomment-vhost.png)

Navigate to deeper directory

```
$ cd extra/
```

Then edit the file named **httpd-vhosts.conf**, and it already come with this content

![Default virtual hosts](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/default-vhosts.png)

Remove it and change to

```apache
<VirtualHost *:80>
    ServerAdmin webmaster@dummy-host.example.com
    ServerName local.foosite.com
    ServerAlias local.foosite.com
    DocumentRoot "/Users/username/public_html/project_a"
    <Directory "/Users/username/public_html/project_a/">
        DirectoryIndex index.php
        Options All
        AllowOverride All
        Order allow,deny
        allow from all
        Require all granted
    </Directory>
    ErrorLog "logs/local.foosite.com-error_log"
    CustomLog "logs/local.foosite.com-access_log" common
</VirtualHost>

<VirtualHost *:80>
    ServerAdmin webmaster@dummy-host.example.com
    ServerName local.barsite.com
    ServerAlias local.barsite.com
    DocumentRoot "/Users/username/public_html/project_b"
    <Directory "/Users/username/public_html/project_b/">
        DirectoryIndex index.php
        Options All
        AllowOverride All
        Order allow,deny
        allow from all
        Require all granted
    </Directory>
    ErrorLog "logs/local.barsite.com-error_log"
    CustomLog "logs/local.barsite.com-access_log" common
</VirtualHost>
```

Restart apache server

```
$ sudo /Applications/XAMPP/xamppfiles/xampp restart
```

Edit the **/etc/hosts** file

```
$ sudo vi /etc/hosts
```

Add the following content to bottom

```
# Virtual hosts
127.0.0.1        local.foosite.com
127.0.0.1        local.barsite.com
```

Save it.

## 4. Test it in browser

Open your browser _(e.g. Safari)_, type in the URL **local.foosite.com**, then you will see

![Foo Site](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/foo-site.png)

and then change the URL to **local.barsite.com**, then you will see

![Bar Site](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/bar-site.png)

## 5. Add MySQL password for root user

In your browser, type in the URL **localhost/phpmyadmin**, select a user

![Edit root user](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/edit-user.png)

1. Select **Users** tab
2. Click on the link

Scroll to **Change password** section

![Change root user's password](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/change-password.png)

Then type in the password you want _(e.g. password)_.

Once completed, when you simply click on any link above, error appear.

![phpMyAdmin error](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/phpmyadmin-error.png)

To solve this, just have to edit the file located in **/Applications/XAMPP/xamppfiles/phpmyadmin/config.inc.php**

```
$ sudo vi /Applications/XAMPP/xamppfiles/phpmyadmin/config.inc.php
```

Add in the password you just set

![phpMyAdmin new password](/images/posts/2014-01-17-setup-php-environment-in-mavericks-using-xampp/new-password.png)

Save it and exit.

In your browser, refresh the phpmyadmin page. It should work now.

## 6. (Add on) Auto start XAMPP on machine boot up

```
$ cd /Library/StartupItems
$ sudo mkdir xampp # create xampp directory
$ cd xampp/
$ sudo touch xampp # add a file named `xampp`
$ sudo touch StartupParameters.plist # add a file named `StartupParameters.plist`
```

Add the content to the files

### xampp

```
$ sudo vi xampp
```

```
#!/bin/bash

/Applications/XAMPP/xamppfiles/xampp start
```

_(By default, XAMPP will install in the path above, if yours is different, just modify it.)_

### StartupParameters.plist

```
$ sudo vi StartupParameters.plist
```

```xml
<?xml version=”1.0″ encoding=”UTF-8″?>
<!DOCTYPE plist SYSTEM “file://localhost/System/Library/DTDs/PropertyList.dtd”>
<plist version=”0.9″>
    <dict>
        <key>Description</key>
        <string>XAMPP</string>
        <key>OrderPreference</key>
        <string>Late</string>
        <key>Provides</key>
        <array>
            <string>Starts Apache and MySQL</string>
        </array>
        <key>Uses</key>
        <array>
            <string>SystemLog</string>
        </array>
    </dict>
</plist>
```

Change the ownership

```
$ cd .. # go back 1 level up (directory)
$ sudo chown -R root xampp # change the owner of `xampp` directory
$ sudo chgrp -R wheel xampp # change the group of `xampp` directory
$ sudo chmod 755 xampp/xampp # change the permission of `xampp` file to -rwxr-xr-x
```

Done :)

_References:_

* [Automatically starting XAMPP on MAC OSX boot up](http://www.kharysharpe.com/2011/04/automatically-starting-xampp-on-mac-osx-boot-up/)
