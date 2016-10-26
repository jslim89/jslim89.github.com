---
layout: post
title: "Objective C macro - logging wrapper"
date: 2014-09-24 13:28:31 +0800
comments: true
categories: 
- ios
---

As we know logging in Objective C is using `NSLog`. The problem here
is I want to enable & disable the logging by changing a flag, NOT
remove `NSLog` when don't want to see the log; add `NSLog` when want to
see the log.

There is a solution, which is [CocoaLumberjack](https://github.com/CocoaLumberjack/CocoaLumberjack). However, I just want to see the message in console,
don't want such a huch package.

Thus I choose to use macro.

Let's add the macro in **Prefix.pch**

```obj-c
...

// 1. the debug flag
#define kDebugEnabled YES

// 2. the NSLog wrapper
#define JSLog(message, ...) if (kDebugEnabled) NSLog(message, ##__VA_ARGS__)
```

1. I can now turn on or off _(by changing the value to `YES` or `NO`)_ at anytime
2. The usage is exactly same as `NSLog`. e.g. `JSLog(@"error %@", error);`
