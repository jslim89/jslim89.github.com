---
layout: post
title: "Embed GVIM into Eclipse with Eclim on Ubuntu"
date: 2012-11-24 15:55
comments: true
categories:
- linux
- setup-configuration
- vim
---

[Eclim](http://eclim.org/) allowed us to embed [Vim](http://www.vim.org/) into [Eclipse](http://www.eclipse.org/).

Install [JDK](http://www.oracle.com/technetwork/java/javase/downloads/index.html) and [Eclipse](http://www.eclipse.org/).  
**NOTE: Installation of Eclipse is same as [Installation of JDK](http://jslim89.github.com/blog/2012/11/22/jdk-installation-on-ubuntu/). Just have to append `export ECLIPSE_HOME='/opt/eclipse'` after the `export JAVA_HOME` line and append `:${ECLIPSE_HOME}` to the end of `export PATH`.**

To make it on Gnome desktop

```
$ sudo touch /usr/share/applications/eclipse.desktop
```

Add the content below to the file just created

```
[Desktop Entry]
Name=Eclipse IDE
Comment=Eclipse
Exec=/opt/eclipse/eclipse
StartupNotify=true
Terminal=false
Type=Application
Icon=/opt/eclipse/icon.xpm
Category=Development;IDE;
```

Edit `/opt/eclipse/eclipse.ini` add

```ini
-vm
/opt/jdk<version>/bin/java
```

right before `-vmargs`

Next is to install [Eclim](http://eclim.org/). ([see more](http://eclim.org/install.html))

```
$ java -jar eclim_<version>.jar
```

Then start Eclipse

* at the eclipse menu bar: **Window** -> **Preferences** -> **Vimplugin** there, make sure the **Path to GVIM** is not blank _(usually should be `/usr/bin/gvim`)_
* open **Window** -> **Show View** -> **Other** -> **Eclim** -> **eclimd**
* Right-click any project source code, select **Open with** -> **Vim**

Happy viming ^^

_Reference: [eclipse not working - No java virtual machine was found](http://stackoverflow.com/questions/5898111/eclipse-not-working-no-java-virtual-machine-was-found)_
