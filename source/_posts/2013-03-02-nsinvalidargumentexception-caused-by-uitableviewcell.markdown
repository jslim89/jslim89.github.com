---
layout: post
title: "NSInvalidArgumentException caused by UITableViewCell"
date: 2013-03-02 18:36
comments: true
categories: 
- ios
- programming
---

I keep hitting an error like this
```
2013-03-02 18:34:52.488 StoreSearch[84541:c07] -[UITableViewCell nameLabel]: unrecognized selector sent to instance 0x6a7d880
2013-03-02 18:34:52.489 StoreSearch[84541:c07] *** Terminating app due to uncaught exception 'NSInvalidArgumentException', reason: '-[UITableViewCell nameLabel]: unrecognized selector sent to instance 0x6a7d880'
*** First throw call stack:
(0x14ab052 0xeabd0a 0x14acced 0x1411f00 0x1411ce2 0x3717 0xb4e0f 0xb5589 0xa0dfd 0xaf851 0x5a322 0x14ace72 0x1d6e92d 0x1d78827 0x1cfefa7 0x1d00ea6 0x1d00580 0x147f9ce 0x1416670 0x13e24f6 0x13e1db4 0x13e1ccb 0x1394879 0x139493e 0x1ba9b 0x20bd 0x1fe5)
terminate called throwing an exception(lldb)
```

{% img http://jslim89.github.com/images/posts/2013-03-02-nsinvalidargumentexception-caused-by-uitableviewcell/uitableviewcell-error.png Error thread %}

This took me few hours to figure out the problem. The problem is just make use of wrong **cell identifier**.

Finally I change this
```obj-c
static NSString *const SearchResultCellIdentifier = @"NothingFoundCell";
static NSString *const NothingFoundCellIdentifier = @"NothingFoundCell";
```
to this
```obj-c
static NSString *const SearchResultCellIdentifier = @"SearchResultCell";
static NSString *const NothingFoundCellIdentifier = @"NothingFoundCell";
```

Problem solved :)
