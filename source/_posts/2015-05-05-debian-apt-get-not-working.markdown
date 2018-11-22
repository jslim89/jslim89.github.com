---
layout: post
title: "Debian apt-get not working"
date: 2015-05-05 22:47:19 +0800
comments: true
tags: 
- linux
- debian
---

I'm facing this when I fresh install Debian 7.

No matter what I trying to install, it doesn't works.

Keep showing **E: Unable to locate package ...**

```
root@debian:~# apt-get install git
Reading package lists... Done
Building dependency tree       
Reading state information... Done
E: Unable to locate package git
root@debian:~# apt-get upgrade
Reading package lists... Done
Building dependency tree       
Reading state information... Done
0 upgraded, 0 newly installed, 0 to remove and 0 not upgraded.
```

Then I found the solution. Edit the source list

```
# vi /etc/apt/sources.list
```

to this content

```
deb http://ftp.at.debian.org/debian wheezy main contrib

# Line commented out by installer because it failed to verify:
deb http://security.debian.org/ wheezy/updates main
# Line commented out by installer because it failed to verify:
deb-src http://security.debian.org/ wheezy/updates main
```

Then run

```
# apt-get update
# apt-get upgrade
```

Now can install other packages

References:

- _[Install Proxmox VE on Debian Wheezy](https://pve.proxmox.com/wiki/Install_Proxmox_VE_on_Debian_Wheezy)_
