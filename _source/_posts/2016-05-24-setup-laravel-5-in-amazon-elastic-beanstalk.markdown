---
layout: post
title: "Setup Laravel 5 in Amazon Elastic Beanstalk"
date: 2016-05-24 15:42:55 +0800
comments: true
categories: 
- laravel
- aws
---

## Problem

This is my first time dealing with AWS. Initially, I setup my Laravel project in a normal EC2 instance, and manually install mysql, web server, etc.

Then a friend of mine told me that this kind of infrastructure is not scalable _(horizontal scaling)_. Eventually I go for Elastic Beanstalk.

## Create an [AWS account](https://aws.amazon.com/)

You need a valid credit card to perform this action. Just follow the instruction will do.

## Create a new app in Elastic Beanstalk

![AWS services](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/aws-services-eb.png)

Then choose **Web server environment**.

![AWS Elastic Beanstalk environment](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/eb-environment.png)

Platform select `PHP`.

![AWS Elastic Beanstalk - environment config](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/eb-environment-config.png)

You can temporary choose **Sample application** first.

![AWS Elastic Beanstalk - overview](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/eb-environment-overview.png)

Now you can click on the link above to see your sample page

## Install `eb` in your local

[Follow the instruction here](https://docs.aws.amazon.com/console/elasticbeanstalk/eb-cli-install)

Once you have installed. Run the following command

```
$ eb init
```

You will be prompted to key in AWS credential. [Refer the instruction here](http://docs.aws.amazon.com/general/latest/gr/getting-aws-sec-creds.html)


### Enable ssh login

Go to **Configuration** on your left side, then select **Instances**

![AWS Elastic Beanstalk - ssh login](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/choose-your-ssh-key.png)

If you don't have a key, create it. Download it and put into your `~/.ssh/` directory.

## Setup auto-deployment with [Rocketeer](http://rocketeer.autopergamene.eu/)

```
$ eb ssh
```

You can find the the document root is on `/var/www/html`, where the **html** is symlinked to `/var/app/current`.

Now what you need to do is, to change the `current` directory owner

```
$ sudo mv current current.bck
$ sudo mkdir current
$ sudo chown ec2-user:ec2-user current
```

### Configure rocketeer

In your local

```
$ mkdir deploy-app
$ cd deploy-app
$ composer require anahkiasen/rocketeer
$ php vendor/bin/rocketeer ignite
$ cd .rocketeer
$ ll

-rw-r--r-- 1 user user 3.8K May 24 14:52 config.php
-rw-r--r-- 1 user user 1.1K May 24 12:20 hooks.php
-rw-r--r-- 1 user user  637 May 24 14:50 paths.php
-rw-r--r-- 1 user user 2.4K May 24 15:27 remote.php
-rw-r--r-- 1 user user 1.2K May 24 12:20 scm.php
-rw-r--r-- 1 user user  523 May 24 12:20 stages.php
-rw-r--r-- 1 user user 1.8K May 24 12:20 strategies.php
```

Update the **config.php**

```php
<?php

'production' => [
    'host'      => 'zzzzzzzzzz.xxxxxxxxxx.ap-southeast-1.elasticbeanstalk.com',
    'username'  => 'ec2-user',
    'password'  => '', 
    'key'       => '/path/to/.ssh/ec2-privatekey.pem',
    'keyphrase' => '', 
    'agent'     => '', 
    'db_role'   => true,
],
```

Update the **remote.php**

```php
<?php

'root_directory' => '/var/app/',

'app_directory'  => 'current',

'shared' => [
    'storage',
    '.env',
],

...
```

And the rest you set yourself.

### Deploy

```
$ cd ..
$ php vendor/bin/rocketeer deploy
```

Now go back to your eb server.

```
$ cd /var/app

$ ll
total 12
lrwxrwxrwx 1 ec2-user ec2-user   40 May 24 07:30 current -> /var/app/current/releases/20160524072928
drwxrwxr-x 6 ec2-user ec2-user 4096 May 24 07:29 releases
drwxrwxr-x 3 ec2-user ec2-user 4096 May 25 06:42 shared
-rw-rw-r-- 1 ec2-user ec2-user   89 May 24 07:30 state.json
```

See? All belongs to `ec2-user`. Now change the `shared/storage` ownership

```
$ sudo chown -R webapp:ec2-user shared/storage
$ ll shared/
total 4
drwxr-sr-x 7 webapp ec2-user 4096 May 24 07:29 storage
```

Check back the screenshot above, you are required to change the document root

![AWS Elastic Beanstalk - change document root](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/change-docroot.png)

Set the value to `/current/public`

### Set .env file

Don't forget, your .env file won't be available for the first time. Now create it

```
$ cd /var/app/current/current
$ cp .env.sample /var/app/current/shared/.env
$ ln -s /var/app/current/shared/.env .
```

Generate key

```
$ php artisan key:generate
```

Now you test it again, it should show your page accordingly

## Configure RDS for your project

Go to **Services** _(in the top left)_, and choose `RDS`, then follow the instruction.

Now go to your instances

![AWS Elastic Beanstalk - RDS instances](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/rds-instances.png)

Get the link and put it in your **.env** file

```
DB_HOST=zzzzz.xxxxxxxxxxxx.ap-southeast-1.rds.amazonaws.com
DB_DATABASE=db_name
DB_USERNAME=db_user
DB_PASSWORD=db_pass
```

#### Mission Completed!!!


## References:

- [Laravel on AWS Elastic Beanstalk – Dev Guide](http://blog.goforyt.com/laravel-aws-elastic-beanstalk-dev-guide/)
