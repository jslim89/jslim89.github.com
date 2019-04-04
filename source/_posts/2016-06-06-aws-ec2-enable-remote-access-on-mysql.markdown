---
layout: post
title: "AWS EC2 - Enable remote access on mysql"
date: 2016-06-06 12:33:00 +0800
comments: true
tags: 
- aws
- mysql
---

I think most of us know that enable remote access, need to create a new user with `%` host.

But in EC2, there are some security config need to be done.

### Update the security group of the EC2 instance

![EC2 instance list](/images/posts/2016-06-06-aws-ec2-enable-remote-access-on-mysql/instance-list.png)

Go to your AWS console, select the instance where you host your database _(MySQL)_.

![EC2 security group](/images/posts/2016-06-06-aws-ec2-enable-remote-access-on-mysql/security-group-inbound-list.png)

Then select the security group

![EC2 security group add rule](/images/posts/2016-06-06-aws-ec2-enable-remote-access-on-mysql/security-group-add-rule.png)

Make sure you add a rule in the **In bound** there, for MySQL, and set the IP to `0.0.0.0`

### Update the mysql binding address

Edit the file `/etc/mysql/my.cnf`, and change the binding address to `0.0.0.0`

_(EDIT: 2019-04-04, you may also update the file `/etc/mysql/conf.d/mysql.cnf`
, for newer version of MySQL. Thanks for Dawood pointing out.)_

```
bind-address = 0.0.0.0
```

then restart mysql server

```
$ sudo /etc/init.d/mysql restart
```

### Create a new user for any host in MySQL

```mysql
CREATE USER 'foo'@'%' IDENTIFIED BY 'your-awesome-pass';

# grant privileges to table(s)
GRANT ALL PRIVILEGES ON db_name.* TO 'foo'@'%' WITH GRANT OPTION;
```

**NOTE: bare in mind that `'foo'@'localhost'` & `'foo'@'%'` are consider as different user, you may have 2 different passwords for each of them**

References:

- [Connect to mysql on Amazon EC2 from a remote server](http://stackoverflow.com/questions/9766014/connect-to-mysql-on-amazon-ec2-from-a-remote-server/9983461#9983461)
