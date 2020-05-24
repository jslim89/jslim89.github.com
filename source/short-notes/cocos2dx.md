---
layout: page
title: Cocos2d-x
permalink: /short-notes/cocos2dx/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

https://www.cocos2d-x.org/download

#### Change `Label` font size

```cpp
auto label = Label::createWithTTF("Your text", "helvetica.ttf", 28);

// later on
auto ttfConfig = label->getTTFConfig();
ttfConfig.fontSize = 40; // change to 40 point
label->setTTFConfig(ttfConfig);
```
