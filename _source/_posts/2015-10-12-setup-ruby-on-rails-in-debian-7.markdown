---
layout: post
title: "Setup Ruby on Rails in Debian 7"
date: 2015-10-12 22:46:27 +0800
comments: true
categories: 
- ruby-on-rails
- debian
---

## 1. Update the source list

Assumed that ruby & gem are not installed. Need to install [rvm](https://rvm.io/) first.

Before that, rvm require some packages which are not available on the default debian repo. Thus update the `apt` list first

Remember to switch to root user

```
$ su -
```

Edit the list file

```
$ vi /etc/apt/sources.list
```

Make sure all these are inside

```
deb http://security.debian.org/ wheezy/updates main
deb-src http://security.debian.org/ wheezy/updates main

deb http://http.kali.org/kali kali main non-free contrib
deb-src http://http.kali.org/kali kali main non-free contrib
```

Then run a command _(to prevent GPG error)_

```
$ gpg --keyserver pgpkeys.mit.edu --recv-key  ED444FF07D8D0BF6
$ gpg -a --export ED444FF07D8D0BF6 | sudo apt-key add -
```

Then update the `apt`

```
$ apt-get update
```

## 2. Install necessary programs

First you need `curl`

```
$  apt-get install curl
```

## 3. Add yourself to sudoers

This is because later the rvm script will need sudo

```
$ adduser username sudo
$ exit // exit from root user
```

## 4. Install rvm

Make sure this is run by yourself _(not **root**)_
```
$ gpg --keyserver hkp://keys.gnupg.net --recv-keys 409B6B1796C275462A1703113804BB82D39DC0E3
$ \curl -sSL https://get.rvm.io | bash -s stable
$ source ~/.rvm/scripts/rvm
```

Install latest ruby

```
$ rvm install current
```

## 5. Rails app

```
$ rails new yourapp
$ cd yourapp
```

Execute it

```
$ bin/rails server
```

Now you can open up your browser, and type in [localhost:3000](localhost:3000)

## 6. Test by other peers (optional)

Example if the project is run inside VM _(I'm using VMWare Fusion)_, then

```
$ bin/rails server -b 192.168.1.xx
```

Later on, in your host OS's browser, type in [192.168.1.xx:3000](192.168.1.xx:3000)

References:

- [Error running 'requirements_debian_libs_install gawk libyaml-dev libsqlite3-dev autoconf libgdbm-dev libncurses5-dev automake libtool'](https://github.com/rvm/rvm/issues/2358)
- [Accessing Rails Server From VirtualBox](http://stackoverflow.com/questions/11111219/accessing-rails-server-from-virtualbox/27576210#27576210)
