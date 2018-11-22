---
layout: post
title: "Enable display_errors in MAMP"
date: 2013-01-30 16:50
comments: true
tags: 
- php
- mac
- setup-configuration
---

As we all know that to enable `display_errors` was an easy task. Unfortunately I've made more than 10 attempts to configure this.

Initially I was edit **/Applications/MAMP/conf/php5.4.4/php.ini**, it doesn't solve my problem. Then I've tried to edit all **php.ini** in each php version. Sadly, it failed again.

Finally, I echo out `phpInfo()` to see it's attributes, and the value of `display_errors` was **`Off`**. The path for **php.ini** was located in **/Applications/MAMP/bin/php/php5.4.4/conf/php.ini**. :|

Damn!
