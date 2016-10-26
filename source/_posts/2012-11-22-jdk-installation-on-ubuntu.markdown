---
layout: post
title: "JDK installation on Ubuntu"
date: 2012-11-22 22:25
comments: true
categories:
- linux
- setup-configuration
---

There may have many ways to install Java on Ubuntu. However, in this example I'll show the manual way which is download the [JDK](http://www.oracle.com/technetwork/java/javase/downloads/index.html) from the official website.

There are few steps here:

Extract it and move to `/opt` directory.

```
$ tar -zxvf jdk_<version>.tar.gz
$ sudo mv jdk_<version> /opt/ # NOTE: /opt is a directory like `Program file` in Windows
```

Add **Java** to [Environment Variable](http://en.wikipedia.org/wiki/Environment_variable).

```
$ vi ~/.bash_profile
# Append this two lines:
#   export JAVA_HOME='/opt/jdk_<version>/bin'
#   export PATH=$PATH:${JAVA_HOME}
# Press :wq to save it
```

Activate the bash profile for immediate effect.

```
$ source ~/.bash_profile
```

Re-open terminal (i.e. `CTRL + ALT + T` for shortcut)

```
$ java -showversion
```

It should work. Have fun :)
