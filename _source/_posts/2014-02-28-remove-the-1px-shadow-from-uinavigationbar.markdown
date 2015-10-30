---
layout: post
title: "Remove the 1px shadow from UINavigationBar"
date: 2014-02-28 21:25:15 +0800
comments: true
categories: 
- ios
---

There is a shadow _(only 1 pixel)_ below the UINavigationBar

![With shadow](http://jslim89.github.com/images/posts/2014-02-28-remove-the-1px-shadow-from-uinavigationbar/with-shadow.png)

May be you can't see clearly, zoom it see

![Shadow zoom](http://jslim89.github.com/images/posts/2014-02-28-remove-the-1px-shadow-from-uinavigationbar/shadow-zoom.png)

We can see 1 pixel darkness. Where the gray area is what I want, the code is

```obj-c
self.navigationController.navigationBar.layer.shadowRadius = 0;
self.navigationController.navigationBar.layer.shadowOffset = CGSizeMake(0, 3);
self.navigationController.navigationBar.layer.shadowOpacity = 0.5;
self.navigationController.navigationBar.layer.shadowColor = [UIColor grayColor].CGColor;
UIBezierPath *path = [UIBezierPath bezierPathWithRect:self.navigationController.navigationBar.bounds];
self.navigationController.navigationBar.layer.shadowPath = path.CGPath;
```

**NOTE: The code above is to draw the gray shadow only**

The desired result will be

![Without shadow](http://jslim89.github.com/images/posts/2014-02-28-remove-the-1px-shadow-from-uinavigationbar/without-shadow.png)

After zoom

![After zoom: Without shadow](http://jslim89.github.com/images/posts/2014-02-28-remove-the-1px-shadow-from-uinavigationbar/no-shadow-zoom.png)

In order to achieve the result

```obj-c MyRootViewController.m
...
@implementation ViewController {
    UIImageView *navBarHairlineImageView;
}
...

- (void)viewDidLoad
{
    [super viewDidLoad];
    navBarHairlineImageView = [self findHairlineImageViewUnder:self.navigationController.navigationBar];
    ...
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    navBarHairlineImageView.hidden = YES;
}

- (UIImageView *)findHairlineImageViewUnder:(UIView *)view {
    if ([view isKindOfClass:UIImageView.class] && view.bounds.size.height <= 1.0) {
            return (UIImageView *)view;
    }
    for (UIView *subview in view.subviews) {
        UIImageView *imageView = [self findHairlineImageViewUnder:subview];
        if (imageView) {
            return imageView;
        }
    }
    return nil;
}
```

You only do this in the rootViewController of the navigationController, then the rest will be the same result.

_Source:_

* _[How to hide iOS7 UINavigationBar 1px bottom line](http://stackoverflow.com/questions/19226965/how-to-hide-ios7-uinavigationbar-1px-bottom-line/19227158#19227158)_
