---
layout: post
title: "Vagrant for PHP development environment"
date: 2015-10-31 20:02:46 +0800
comments: true
categories: 
- php
- vagrant
---

## What is Vagrant?

I asked this question before, I think is about 1 year+ ago. Many times, I search for it, but couldn't understand how it work.

Until recently, I found that Laravel also recommended it. I tried and eventually I found it really useful!

## Configure now

First of all, you will need a **ssh key**, [Vagrant](https://www.vagrantup.com/) & [VirtualBox](https://www.virtualbox.org/).

Generate ssh key

```
$ ssh-keygen -t rsa -b 4096 -C "username@example.com"
```

Navigate to your development folder, e.g. `cd ~/dev`

Clone the Laravel configuration for Vagrant _(we're refering to Laravel's vagrant file)_

```
$ vagrant box add laravel/homestead
$ git clone https://github.com/laravel/homestead.git homestead
$ cd homestead

# pre configure
$ sh init.sh
```

Assuming that all projects are hosted on the same VM.

The configuration files stored in `~/.homestead`

```
$ cd ~/.homestead/
$ vim Homestead.yaml
```

Edit the file **Homestead.yaml**, refer to [Laravel doc](http://laravel.com/docs/5.1/homestead) for complete information.

There are few things you need to change

```
folders:
    - map: ~/dev/web
      to: /home/vagrant/dev

sites:
    - map: laravel.homestead.com
      to: /home/vagrant/dev/laravel/public

databases:
    - laravel
```

Just an example here, look at the section **folders**, it actually created a shared folder in the VM. `~/dev/web` is the local folder which keep all the web project _(make sure it exists before you run `vagrant up`)_, whereas the `/home/vagrant/dev` will be inside the VM

For **sites**, is actually the virtual hosts for a particular project, in this example is to map `laravel.homestead.com` to the project named `laravel`

Now you can create the VM

```
$ cd ~/dev/homestead
$ vagrant up
```

Now edit your **/etc/hosts** file, by adding the following line

```
192.168.10.10  laravel.homestead.com
```

_(the ip address was in your **~/.homestead/Homestead.yaml**)_

Create a new laravel project _(if you don't know how to install laravel, refer http://laravel.com/docs/5.0)_

```
$ cd ~/dev/web
$ laravel new laravel
```

Now open up your browser, and type in the address [laravel.homestead.com](http://laravel.homestead.com), and you will see the page!

You can remain your development in the host OS, but host the project in VM.

You can actually verify this by **open a new terminal**

```
$ cd ~/dev/homestead
$ vagrant ssh
```

Now you logged in to the VM

```
$ cd ~/dev
$ ls -l
drwxr-xr-x 1 vagrant vagrant  816 Oct 21 08:55 laravel/
```

Go back to the previous terminal, create a dummy file

```
$ touch ~/dev/web/new-file
```

Switch back to the second terminal

```
$ ll
drwxr-xr-x 1 vagrant vagrant  816 Oct 21 08:55 laravel/
-rw-r--r-- 1 vagrant vagrant  816 Oct 21 08:57 new-file
```

Means now you can create any other PHP projects beside Laravel in this environment as you like.

Enjoy yourself :)
