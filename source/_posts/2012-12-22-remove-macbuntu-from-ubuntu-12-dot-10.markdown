---
layout: post
title: "Remove Macbuntu from Ubuntu 12.10"
date: 2012-12-22 08:23
comments: true
categories: 
- linux
- uninstallation
---

About a month ago, I follow the instruction from [here](http://www.noobslab.com/2012/11/install-mac-os-x-theme-on-ubuntu-1210.html) installed some components to make my desktop like OS X.

Unfortunately, I realize that the [Cairo-Dock](http://glx-dock.org/) is damn annoying. When I want to click on it, it un-focus; when I don't want to use it, it focus again. wtf!!!

Thus I decided to remove it.

```
# change the left dock icon back to ubuntu icon
$ wget -O ubuntu-logo.zip http://goo.gl/mU42p

$ sudo unzip ubuntu-logo.zip -d /usr/share/unity/6/
# It will ask to replace file, Type "A" and Press enter

$ rm ubuntu-logo.zip

# Uninstall Mac cursor
$ cd /usr/share/icons/mac-cursors
$ ./uninstall-mac-cursors.sh

# Enable back the crash report
$ cd
$ sudo sed -i "s/enabled=0/enabled=1/g" '/etc/default/apport'

# Finally, remove the cairo-dock
$ sudo apt-get purge cairo-dock cairo-dock-plug-ins && sudo apt-get autoremove
```
Lastly, change back to Ubuntu original theme.  
Search for **appearance**

![Search for appearance](http://jslim89.github.com/images/posts/2012-12-22-remove-macbuntu-from-ubuntu-12-dot-10/search_for_appearance.png)

Change the **theme**

![Change Theme](http://jslim89.github.com/images/posts/2012-12-22-remove-macbuntu-from-ubuntu-12-dot-10/change_theme.png)

_References:_

* _[INSTALL MAC OS X THEME ON UBUNTU 12.10 QUANTAL/UBUNTU 12.04/LINUX MINT 14](http://www.noobslab.com/2012/11/install-mac-os-x-theme-on-ubuntu-1210.html)_
* _[cairo dock cant find plugins?](http://askubuntu.com/questions/128698/cairo-dock-cant-find-plugins#answers)_
