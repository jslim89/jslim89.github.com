---
layout: post
title: "Ubuntu server allow login via ssh with key"
date: 2013-04-08 17:47
comments: true
tags: 
- setup-configuration
- linux
---

The environment:

* Ubuntu server 12.04 LTS is running on VMWare Fusion with IP address 192.168.1.101 as **Host**
* Mac OS X Mountain Lion as **Client**

### In your ubuntu server
Install ssh server

```
$ sudo apt-get install openssh-server
```

### In OS X

Generate a key-pair

```
$ mkdir ~/.ssh
$ chmod 700 ~/.ssh
$ ssh-keygen -t rsa

Generating public/private rsa key pair.
Enter file in which to save the key (/home/b/.ssh/id_rsa):
Enter passphrase (empty for no passphrase):
Enter same passphrase again:
Your identification has been saved in /home/b/.ssh/id_rsa.
Your public key has been saved in /home/b/.ssh/id_rsa.pub.
```

Copy the public key **id_rsa.pub** to Ubuntu server

```
$ scp ~/.ssh/id_rsa.pub user@192.168.1.101:/home/user/.ssh/authorized_keys
```

If everything OK, now you can access via SSH without password

```
$ ssh user@192.168.1.101
```

Enjoy :)

Reference: [SSH/OpenSSH/Keys](https://help.ubuntu.com/community/SSH/OpenSSH/Keys)
