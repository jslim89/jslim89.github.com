---
layout: post
title: "iOS - UIActivityViewController custom share message to different providers"
date: 2015-01-28 13:17:43 +0800
comments: true
categories: 
- ios
- objective-c
---

Begin from iOS6, Apple simplify the share process by using `UIActivityViewController`.
The thing is that, Twitter is only allow up to `140` characters on a post,
thus we may need to customize the share text for Twitter.

One of the way is to subclass the `UIActivityItemProvider`. Here is
how you can do it:

```obj-c MyActivityItemProvider.h
#import <UIKit/UIKit.h>

@interface MyActivityItemProvider : UIActivityItemProvider

@end
```

```obj-c MyActivityItemProvider.m
#import "MyActivityItemProvider.h"

@implementation MyActivityItemProvider

// 1.
- (id)activityViewController:(UIActivityViewController *)activityViewController itemForActivityType:(NSString *)activityType
{
    // 2.
    if ([activityType isEqualToString:UIActivityTypePostToFacebook]) {
        return @"Facebook: testing 123";
    } else if ([activityType isEqualToString:UIActivityTypePostToTwitter]) {
        return @"Twitter: testing 123";
    }
    
    return @"No provider";
}

@end
```

```obj-c MyViewController.m
// 3.
MyActivityItemProvider *message = [[MyActivityItemProvider alloc] init];

NSArray *activityItems;
if (myImage != nil) {
    activityItems = @[message, myImage];
} else {
    activityItems = @[message];
}

UIActivityViewController *activityController = [[UIActivityViewController alloc] initWithActivityItems:activityItems applicationActivities:nil];
[self presentViewController:activityController animated:YES completion:nil];
```

1. Override `activityViewController:itemForActivityType:`.
2. Customize message for each provider if you want to.
3. Just treat it as a message, assign it together with image _(if you have one)_. The `UIActivityViewController` will then pick the correct message for corresponding provider.
