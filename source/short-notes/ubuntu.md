---
layout: page
title: Linux - Ubuntu
permalink: /short-notes/ubuntu/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

https://ubuntu.com/

#### Settings icon missing in Ubuntu 19.10

![No settings icon](/images/short-notes/linux/no-settings-icon.png "Ubuntu settings icon missing")

```
$ sudo apt remove --purge gnome-control-center
$ sudo apt install gnome-control-center
```

##### Reference:

- [The settings icon has disappeared and i can't find a way to get to it](https://www.reddit.com/r/Ubuntu/comments/bkjckp/the_settings_icon_has_disappeared_and_i_cant_find/emhvidu/)
