---
layout: post
title: "UIAlertView delegate in class method"
date: 2014-02-23 12:29:58 +0800
comments: true
tags: 
- ios
---

To handle the UIAlertView _(or any others class)_  delegate in [class method](https://developer.apple.com/library/ios/documentation/general/conceptual/DevPedia-CocoaCore/ClassMethod.html) just have to change the minus `-` to plus `+`

Example

```obj-c
+ (void)someMethod:(NSString *)title withMessage:(NSString *)message
{
    UIAlertView *alert = [[UIAlertView alloc]initWithTitle:title
                                                   message:message
                                                  delegate:self // same here
                                         cancelButtonTitle:@"Cancel" 
                                         otherButtonTitles:@"Done", nil];
}

// change to PLUS
+ (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    NSLog(@"Button %d selected", buttonIndex);
}
```

There is **no need** to conform to UIAlertViewDelegate protocol, without the statement below will also work.

e.g.

```obj-c
@interface ViewController : UIViewController <UIAlertViewDelegate>
```

Reference: [Can I use a class method as a delegate callback](http://stackoverflow.com/questions/8883521/can-i-use-a-class-method-as-a-delegate-callback/8884262#8884262)
