---
layout: post
title: "Setup wildcard subdomain in Laravel Homestead"
date: 2015-12-01 20:36:50 +0800
comments: true
categories: 
- vagrant
- setup
---

Before start, you need to [setup vagrant](http://jslim.net/blog/2015/10/31/vagrant-for-php-development-environment/) and ssh into the virtual machine

```
$ vagrant up
$ vagrant ssh
```

## 1. Edit your Nginx config

```
$ sudo vim /etc/nginx/sites-available/laravel.app
```

In the **server_name** there

```
server_name laravel.app *.laravel.app;
```

## 2. Install `dnsmasq` & configure

```
$ sudo apt-get install dnsmasq
```

Then edit the config file

```
$ sudo vim /etc/dnsmasq.conf
```

Add in the following content

```
local=/laravel.app/
domain=laravel.app

address=/laravel.app/127.0.0.1
```

Restart **dnsmasq**

```
$ sudo /etc/init.d/dnsmasq restart
```

## 3. Go back to host machine _(I'm using OS X here)_

![open network preference](http://jslim89.github.com/images/posts/2015-12-01-setup-wildcard-subdomain-in-laravel-homestead/network-preference-1.png)

Open up the **Network Preferences**

![Network preference - overview](http://jslim89.github.com/images/posts/2015-12-01-setup-wildcard-subdomain-in-laravel-homestead/network-preference-2.png)

Click on the **Advanced...** tab

![Network preference - DNS settings](http://jslim89.github.com/images/posts/2015-12-01-setup-wildcard-subdomain-in-laravel-homestead/network-preference-dns.png)

Now add these DNS to the settings, in the **DNS** tab

**NOTE: for 192.168.1.1, this you should follow your router address, because some may be 192.168.0.1 or others**

The **/etc/hosts** value would be

```
127.0.0.1      laravel.app
```

## 4. Verify

Before that, double check your **/etc/hosts** in the host OS

```
127.0.0.1      laravel.app
```

Now you try to **dig**

```
$ dig subdomain.laravel.app @192.168.10.10

; <<>> DiG 9.8.3-P1 <<>> subdomain.laravel.app @192.168.10.10
;; global options: +cmd
;; Got answer:
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 62725
;; flags: qr aa rd ra; QUERY: 1, ANSWER: 1, AUTHORITY: 0, ADDITIONAL: 0

;; QUESTION SECTION:
;subdomain.laravel.app.  IN  A

;; ANSWER SECTION:
subdomain.laravel.app. 0 IN  A   127.0.0.1

;; Query time: 11 msec
;; SERVER: 192.168.10.10#53(192.168.10.10)
;; WHEN: Mon Nov 30 12:14:00 2015
;; MSG SIZE  rcvd: 62
```

Now try in your browser, type in the address [subdomain.laravel.app:8000](subdomain.laravel.app:8000)

**NOTE:**

Sometime you may encounter a problem which it doesn't work, so in this case you have to clear your DNS cache _(for OSX, [refer here](https://support.apple.com/en-us/HT202516))_


References:

- [Local Dns Server With Vagrant Homestead](http://www.gufran.me/post/local-dns-server-with-vagrant-homestead/)
- [OpenWrt: Dnsmasq](https://wiki.openwrt.org/doc/howto/dhcp.dnsmasq)
- [Using Dnsmasq for local development on OS X](http://passingcuriosity.com/2013/dnsmasq-dev-osx/)
