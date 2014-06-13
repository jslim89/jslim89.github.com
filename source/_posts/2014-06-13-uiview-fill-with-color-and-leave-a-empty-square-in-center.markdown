---
layout: post
title: "UIView fill with color and leave a empty square in center"
date: 2014-06-13 22:37:38 +0800
comments: true
categories: 
- ios
---

{% img http://jslim89.github.com/images/posts/2014-06-13-uiview-fill-with-color-and-leave-a-empty-square-in-center/uiview.png UIView %}

Create a UIView that fill with color, and leave an empty in center.

This can be accomplish by using `CAShapeLayer`.

```obj-c
// 1
UIBezierPath *overlayPath = [UIBezierPath bezierPathWithRect:self.view.bounds];

// 2
UIBezierPath *transparentPath = [UIBezierPath bezierPathWithRect:CGRectMake(60, 120, 200, 200)];
[overlayPath appendPath:transparentPath];
[overlayPath setUsesEvenOddFillRule:YES];

// 3
CAShapeLayer *fillLayer = [CAShapeLayer layer];
fillLayer.path = overlayPath.CGPath;
fillLayer.fillRule = kCAFillRuleEvenOdd;
fillLayer.fillColor = [UIColor colorWithRed:255/255.0 green:20/255.0 blue:147/255.0 alpha:1].CGColor;

// 4
[self.view.layer addSublayer:fillLayer];
```

1. Initialize a bezier path filled with the whole UIView
2. Create another bezier path to represent the rectangle inside. Then merge the inner rectangle to the outer rectangle.
For [usesEvenOddFillRule](https://developer.apple.com/library/ios/documentation/uikit/reference/UIBezierPath_class/Reference/Reference.html#//apple_ref/occ/instp/UIBezierPath/usesEvenOddFillRule) you can read more in apple doc
3. Create a layer _(`CAShapeLayer` is subclass of `CALayer`)_, set the path create just now to the layer, then fill it with a color _(put whatever color that you like)_
4. Add the layer to the UIView layer _(by default, all `UIView`s come with a layer)_

_References:_

* _[UIView with transparent in middle](http://stackoverflow.com/questions/24196784/uiview-with-transparent-in-middle/24197290#24197290)_
* _[CALayer with transparent hole in it](http://stackoverflow.com/questions/16512761/calayer-with-transparent-hole-in-it/16518739#16518739)_
