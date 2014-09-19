---
layout: post
title: "Effectively debug UIView in Objective-C"
date: 2014-06-23 07:55:06 +0800
comments: true
categories: 
- ios
---

Usually I'm using `NSLog` to debug all sort of objects.
When I encounter `UINavigationBar` background color issue,
I loop through it's `subviews` one by one. e.g.

```obj-c
for (id subview in navBar.subviews) {
    NSLog(@"subview %@", subview);
}
```

Then I realize I need to go 1 level deeper, I add one more loop inside

```obj-c
for (id subview in navBar.subviews) {
    NSLog(@"subview %@", subview);
    for (id subsubview in subview.subviews) {
        NSLog(@"subsubview %@", subsubview);
    }
}
```

which is not an effective way

Finally I found this `recursiveDescription` magic function to show all subviews recursively

It can't be invoked by `[view recursiveDescription]`, must be using `performSelector`. e.g.

```obj-c
NSLog(@"\nNav bar %@",[navBar performSelector:@selector(recursiveDescription)]);
```

Result:

```
Nav bar <CustomNavigationBar: 0x1369bad0; baseClass = UINavigationBar; frame = (0 8; 320 56); transform = [1, 0, 0, 1, 0, -12]; autoresize = W; userInteractionEnabled = NO; gestureRecognizers = <NSArray: 0x1369bff0>; layer = <CALayer: 0x1369bbf0>>
   | <_UINavigationBarBackground: 0x1369bd40; frame = (0 -8; 320 76); autoresize = W; userInteractionEnabled = NO; layer = <CALayer: 0x1369b950>> - (null)
   |    | <UIImageView: 0x1369be10; frame = (0 76; 320 0.5); userInteractionEnabled = NO; layer = <CALayer: 0x1369bea0>> - (null)
   | <UINavigationButton: 0x1369e3b0; frame = (5 18; 44 30); opaque = NO; layer = <CALayer: 0x1369e4d0>>
   |    | <UIImageView: 0x1369f470; frame = (11 4; 22 22); clipsToBounds = YES; opaque = NO; userInteractionEnabled = NO; layer = <CALayer: 0x1369f500>> - (null)
   | <UIView: 0x1369fe60; frame = (55 5; 224 58); layer = <CALayer: 0x1369fec0>>
   |    | <UIImageView: 0x1369ff20; frame = (2 15; 36 36); clipsToBounds = YES; opaque = NO; userInteractionEnabled = NO; layer = <CALayer: 0x1369ffb0>> - (null)
   |    | <UILabel: 0x136a0000; frame = (43 18; 156 14); text = '11 Cards'; clipsToBounds = YES; userInteractionEnabled = NO; layer = <CALayer: 0x136a02f0>>
   |    | <UILabel: 0x136a03c0; frame = (45 33; 154 13); text = '2 weeks ago'; clipsToBounds = YES; userInteractionEnabled = NO; layer = <CALayer: 0x136a0460>>
   | <_UINavigationBarBackIndicatorView: 0x12a711a0; frame = (8 24; 12.5 20.5); alpha = 0; opaque = NO; userInteractionEnabled = NO; layer = <CALayer: 0x12a71280>> - Back
```

**P/S: Please remove this method before you submit the app to AppStore. This is private API, so there is high chances that your app will be rejected if you don't remove this method.**

_References:_

* _[recursiveDescription on p. 361](http://forums.bignerdranch.com/viewtopic.php?f=96&t=3247#p7175)_
