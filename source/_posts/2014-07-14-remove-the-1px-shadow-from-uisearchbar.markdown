---
layout: post
title: "Remove the 1px Shadow From UISearchBar"
date: 2014-07-14 11:56:04 +0800
comments: true
categories: 
- ios
---

When I added a `UISearchBar` to `UITableView.tableHeaderView`, it shows a border

{% img http://jslim89.github.com/images/posts/2014-07-14-remove-the-1px-shadow-from-uisearchbar/with-shadow.png With shadow %}

{% img http://jslim89.github.com/images/posts/2014-07-14-remove-the-1px-shadow-from-uisearchbar/with-shadow-zoom.png With shadow zoom %}

So in order to remove the **1px**, just

```obj-c
self.searchBar.layer.borderColor = [UIColor yourColor].CGColor;
self.searchBar.layer.borderWidth = 1;
```

The final result will be

{% img http://jslim89.github.com/images/posts/2014-07-14-remove-the-1px-shadow-from-uisearchbar/without-shadow.png Without shadow %}

{% img http://jslim89.github.com/images/posts/2014-07-14-remove-the-1px-shadow-from-uisearchbar/without-shadow-zoom.png Without shadow zoom %}

_References:_

- [Remove the 1px border under UISearchBar](https://stackoverflow.com/questions/6868214/remove-the-1px-border-under-uisearchbar/6868227#6868227)
