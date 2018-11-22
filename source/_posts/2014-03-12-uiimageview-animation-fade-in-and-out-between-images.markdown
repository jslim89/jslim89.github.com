---
layout: post
title: "UIImageView animation fade in &amp; out between images"
date: 2014-03-12 07:06:46 +0800
comments: true
tags: 
- ios
---

To animate between images _(not .gif)_ in a UIImageView like the example below

**Original images**

![First image](http://jslim89.github.com/images/posts/2014-03-12-uiimageview-animation-fade-in-and-out-between-images/Image1.png)

![Second image](http://jslim89.github.com/images/posts/2014-03-12-uiimageview-animation-fade-in-and-out-between-images/Image2.png)

**Result**

![Animate between 2 images](http://jslim89.github.com/images/posts/2014-03-12-uiimageview-animation-fade-in-and-out-between-images/animation.gif)

The existing method in `UIImageView` simply won't work.

To get the result like this

**MyViewController.m**

```obj-c
- (void)viewDidLoad
{
    [super viewDidLoad];

    // make the first call
    [self animateImages];
}

- (void)animateImages
{
    static int count = 0;
    NSArray *animationImages = @[[UIImage imageNamed:@"Image1"], [UIImage imageNamed:@"Image2"]];
    UIImage *image = [animationImages objectAtIndex:(count % [animationImages count])];
    
    [UIView transitionWithView:self.animationImageView
                      duration:1.0f // animation duration
                       options:UIViewAnimationOptionTransitionCrossDissolve
                    animations:^{
                        self.animationImageView.image = image; // change to other image
                    } completion:^(BOOL finished) {
                        [self animateImages]; // once finished, repeat again
                        count++; // this is to keep the reference of which image should be loaded next
                    }];
}

```

Reference: [How to animate the change of image in an UIImageView?](http://stackoverflow.com/questions/2834573/how-to-animate-the-change-of-image-in-an-uiimageview/12778881#12778881)
