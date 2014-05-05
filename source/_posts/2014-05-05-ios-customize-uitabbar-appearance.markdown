---
layout: post
title: "iOS - Customize UITabBar appearance"
date: 2014-05-05 09:12:19 +0800
comments: true
categories: 
- ios
---

Final look will be

{% img http://jslim89.github.com/images/posts/2014-05-05-ios-customize-uitabbar-appearance/tabbar-appearance.png New appearance %}

- The selected tab will be in dimmed background
- The selected tab icon & text will be in white color

Before that, add a helper function to turn UIColor into UIImage

You may put this code to **AppDelegate** or anywhere else
```obj-c
+ (UIImage *)imageFromColor:(UIColor *)color forSize:(CGSize)size withCornerRadius:(CGFloat)radius
{
    CGRect rect = CGRectMake(0, 0, size.width, size.height);
    UIGraphicsBeginImageContext(rect.size);
    
    CGContextRef context = UIGraphicsGetCurrentContext();
    CGContextSetFillColorWithColor(context, [color CGColor]);
    CGContextFillRect(context, rect);
    
    UIImage *image = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    // Begin a new image that will be the new image with the rounded corners
    // (here with the size of an UIImageView)
    UIGraphicsBeginImageContext(size);
    
    // Add a clip before drawing anything, in the shape of an rounded rect
    [[UIBezierPath bezierPathWithRoundedRect:rect cornerRadius:radius] addClip];
    // Draw your image
    [image drawInRect:rect];
    
    // Get the image, here setting the UIImageView image
    image = UIGraphicsGetImageFromCurrentImageContext();
    
    // Lets forget about that we were drawing
    UIGraphicsEndImageContext();
    
    return image;
}
```

Then add this to `application:didFinishLaunchingWithOptions:`

```obj-c
UIColor *backgroundColor = [UIColor greenColor];

// set the bar background color
[[UITabBar appearance] setBackgroundImage:[AppDelegate imageFromColor:backgroundColor forSize:CGSizeMake(320, 49) withCornerRadius:0]];

// set the text color for selected state
[[UITabBarItem appearance] setTitleTextAttributes:[NSDictionary dictionaryWithObjectsAndKeys:[UIColor whiteColor], UITextAttributeTextColor, nil] forState:UIControlStateSelected];
// set the text color for unselected state
[[UITabBarItem appearance] setTitleTextAttributes:[NSDictionary dictionaryWithObjectsAndKeys:[UIColor whiteColor], UITextAttributeTextColor, nil] forState:UIControlStateNormal];

// set the selected icon color
[[UITabBar appearance] setTintColor:[UIColor whiteColor]];
[[UITabBar appearance] setSelectedImageTintColor:[UIColor whiteColor]];
// remove the shadow
[[UITabBar appearance] setShadowImage:nil];

// Set the dark color to selected tab (the dimmed background)
[[UITabBar appearance] setSelectionIndicatorImage:[AppDelegate imageFromColor:[UIColor colorWithRed:26/255.0 green:163/255.0 blue:133/255.0 alpha:1] forSize:CGSizeMake(64, 49) withCornerRadius:0]];
```

Done :)
