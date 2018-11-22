---
layout: post
title: "Draw a shape using UIBezierPath"
date: 2014-06-13 22:59:27 +0800
comments: true
tags: 
- ios
---

![Shape wanted](http://jslim89.github.com/images/posts/2014-06-13-draw-a-shape-using-uibezierpath/shape.png)

To draw the shape above by `UIBezierPath`

```obj-c
// 1
static CGFloat thickness = 13;
static CGFloat shapeSize = 43;

// 2
CGPoint startingPoint = CGPointMake(50, 50); // top-left corner
CGPoint points[6];
points[0] = startingPoint;
points[1] = CGPointMake(points[0].x + shapeSize, points[0].y);
points[2] = CGPointMake(points[0].x + shapeSize, points[0].y + thickness);
points[3] = CGPointMake(points[0].x + thickness, points[0].y + thickness);
points[4] = CGPointMake(points[0].x + thickness, points[0].y + shapeSize);
points[5] = CGPointMake(points[0].x, points[0].y + shapeSize);

// 3
CGMutablePathRef cgPath = CGPathCreateMutable();
CGPathAddLines(cgPath, &CGAffineTransformIdentity, points, sizeof points / sizeof *points);
CGPathCloseSubpath(cgPath);

UIBezierPath *path = [UIBezierPath bezierPathWithCGPath:cgPath];

// 4
CAShapeLayer *shape = [CAShapeLayer layer];
shape.path = path.CGPath;
shape.fillColor = [UIColor colorWithRed:255/255.0 green:20/255.0 blue:147/255.0 alpha:1].CGColor;

// 5
[self.view.layer addSublayer:shape];
```

1. Define the _thickness_ & it's _size_ _(see the description on that image)_
2. Let say your starting point is in position `{50, 50}` and you have 6 points in this shape _(you can map the index with the image above)_
3. Convert the points to `CGPath` then add to bezier path
4. Then set the bezier path to the `CAShapeLayer` and fill it with color
5. Add to your `UIView`'s layer as sublayer

_Reference:_

* _[How can I draw an arrow using Core Graphics?](http://stackoverflow.com/questions/13528898/how-can-i-draw-an-arrow-using-core-graphics/13559449#13559449)_
