---
layout: post
title: "Show chinese subtitle on VLC player in OS X"
date: 2013-05-05 14:32
comments: true
categories: 
- setup-configuration
---

I've facing an issue on display the chinese character in not readable form.

I googled and found [this blog](http://www.fanhow.com/knowhow:Chinese_Subtitles_Doesn't_Work_on_VLC_Player_84561534) to show in windows platform.

First, open VLC and in top menu click on **VLC** -> **Preferences...**

{% img http://jslim89.github.com/images/posts/2013-05-05-show-chinese-subtitle-on-vlc-player-in-os-x/top-menu.png Top menu %}

Then

1. click on **Subtitles & OSD** tab
2. change the **Default Encoding** to `Universal, Chinese (GB18030)`
3. change the **Font** to `GB18030Bitmap`

{% img http://jslim89.github.com/images/posts/2013-05-05-show-chinese-subtitle-on-vlc-player-in-os-x/config.png Preferences... %}

Reopen the program and it should work. :)
