---
layout: post
title: "iOS - Working with block handler"
date: 2014-08-30 14:36:12 +0800
comments: true
categories: 
- ios
- objective-c
---

If you're not new to Objective-C, then I'm pretty sure you know about delegate. Normally we use it when the `outerViewController` that want to trigger some actions when user touch a button on `innerViewController`. 

A typically example will be `ListViewController` & `DetailViewController`.

## In delegate way:

```obj-c ListViewController.m
DetailViewController *controller = [[DetailViewController alloc] init];
controller.delegate = self;
[self presentViewController:controller animated:YES completion:nil];

...
- (void)detailDelegate:(DetailViewController *)controller
{
    // your actions here
}
```

```obj-c DetailViewController.h
...
@protocol DetailViewControllerDelegate <NSObject>
- (void)detailDelegate:(DetailViewController *)controller;
@end

@interface DetailViewController : UIViewController
@property (nonatomic, weak) id<DetailViewControllerDelegate> delegate;
...
```

So when user do anything with this `DetailViewController`

```obj-c DetailViewController.m
- (void)actionTouched:(UIButton *)sender
{
    [_delegate detailDelegate:self];
}
```

let say touched a button, then invoke the delegate method, so that the
`ListViewController` get notify from `DetailViewController`.

## Alternative: Using block

```obj-c DetailViewController.h
- (void)doneSomething:(void (^)(void))actionHandler;
```

Create a method signature with an empty block handler.

```obj-c DetailViewController.m
@interface DetailViewController ()
@property (nonatomic, copy) void (^done)(void); // a property for the block
@end

...
- (void)actionTouched:(id)sender
{
    self.done();
}

- (void)doneSomething:(void (^)(void))actionHandler
{
    self.done = actionHandler;
}
```

When user touched on the button, then it invoke the `done` block, the `doneSomething:` is called by `ListViewController`

```obj-c ListViewController.m
DetailViewController *controller = [[DetailViewController alloc] init];
[controller doneSomething:^{
    // your actions will be invoked when user touch the button on DetailViewController
}];
[self presentViewController:controller animated:YES completion:nil];
```

_References:_

* _[Working with Blocks](https://developer.apple.com/library/ios/documentation/Cocoa/Conceptual/ProgrammingWithObjectiveC/WorkingWithBlocks/WorkingWithBlocks.html)_
