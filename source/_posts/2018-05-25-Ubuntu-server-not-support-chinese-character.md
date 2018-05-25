---
title: Ubuntu server not support chinese character
date: 2018-05-25 18:02:43
tags:
- linux
- ubuntu
- utf-8
---

I'm using Amazon EC2, Ubuntu 16.04.

I noticed this problem when I try to generate a pdf file from php, the filename was not the right chinese character.

![Chinese character not able to input to console](/images/posts/2018-05-25-Ubuntu-server-not-support-chinese-character/chinese-input-not-working.gif)

### Solved

```
$ sudo apt-get install language-pack-zh*
$ sudo apt-get install chinese*
```

_(If you need korean & japanese language)_

**Japanese language**

```
$ sudo apt-get install language-pack-ja*
$ sudo apt-get install japan*
```

**Korean language**

```
$ sudo apt-get install language-pack-ko*
$ sudo apt-get install korean*
```

Lastly

```
$ sudo apt-get install fonts-arphic-ukai fonts-arphic-uming fonts-ipafont-mincho fonts-ipafont-gothic fonts-unfonts-core
```

After installed everything, reboot the server.

```
$ sudo reboot
```

References:

- [Installing Asian Fonts on Ubuntu & Debian](http://help.accusoft.com/PCC/v11.2/HTML/Installing%20Asian%20Fonts%20on%20Ubuntu%20and%20Debian.html)
