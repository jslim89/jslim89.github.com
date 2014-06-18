---
layout: post
title: "Dealing iOS 7 UIStatusBar"
date: 2014-06-16 21:19:18 +0800
comments: true
categories: 
- ios
---

I have a hard time on dealing with iOS 7 status bar.

In this example, I will set the status bar for each `UIViewController`

So now go to Info.plist file, and set `View controller-based status bar appearance` to `YES`

{% img http://jslim89.github.com/images/posts/2014-06-16-dealing-ios-7-uistatusbar/plist.png Info.plist %}

I just want to share few cases of dealing with status bar.

## 1. With UINavigationController with WHITE text color
Now create the very first (root) view controller with the following content

In `JSAppDelegate.m` _(I'm using my own prefix `JS`)_, add the lines below

```obj-c JSAppDelegate.m
JSMainViewController *mainViewController = [[JSMainViewController alloc] init];
UINavigationController *navigationController = [[UINavigationController alloc] initWithRootViewController:mainViewController];
self.window.rootViewController = navigationController;
```

In `JSMainViewController.m`, add this to `viewDidLoad`

```obj-c JSMainViewController.m
self.title = @"Light Content";
[self setNeedsStatusBarAppearanceUpdate];
```

and add this function

```obj-c JSMainViewController.m
- (UIStatusBarStyle) preferredStatusBarStyle
{
    return UIStatusBarStyleLightContent; // light content means white text color
}
```

Run it...Opps... why it still in black text color?

{% img http://jslim89.github.com/images/posts/2014-06-16-dealing-ios-7-uistatusbar/light-content-1.png Still in black color %}

The reason is here, since the main view controller is inside a `UINavigationController`, then the status bar will follow the `UINavigationController` style.

Now go back to `JSAppDelegate`, and change the `UINavigationBar` style.

```obj-c
...
navigationController.navigationBar.barStyle = UIBarStyleBlackTranslucent; // THIS LINE
self.window.rootViewController = navigationController;
```

Run it... It works

{% img http://jslim89.github.com/images/posts/2014-06-16-dealing-ios-7-uistatusbar/light-content-2.png Now it works %}

## 2. `UIImageView` not cover up the top position
Now create another `UIViewController`, just name it `JSTransparentBgViewController`, but hide the `UINavigationBar` in this case

So add a function, before the view actually appear, hide the navigation bar

```obj-c JSTransparentBgViewController.m
- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    [self.navigationController setNavigationBarHidden:YES];
}
```

Then in `viewDidLoad`, add this

```obj-c JSTransparentBgViewController.m
// set a light gray color so that you can see the text on status bar
self.view.backgroundColor = [UIColor colorWithRed:235/255.0 green:235/255.0 blue:235/255.0 alpha:1];

[self setNeedsStatusBarAppearanceUpdate];

// by using scroll view, you can scroll & see the result
UIScrollView *scrollView = [[UIScrollView alloc] initWithFrame:self.view.bounds];
scrollView.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin | UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleTopMargin;
scrollView.contentSize = CGSizeMake(CGRectGetWidth(scrollView.frame), 800);
[self.view addSubview:scrollView];

// this image should cover up the top portion of screen
UIImageView *coverImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, 0, CGRectGetWidth(scrollView.frame), 300)];
coverImageView.image = [UIImage imageNamed:@"Cover"];
[scrollView addSubview:coverImageView];
```

You will get a result like this

{% img http://jslim89.github.com/images/posts/2014-06-16-dealing-ios-7-uistatusbar/image-statusbar-1.png extra 20px %}

so now you see that the status bar is not covered up

To fix this, just.... hack it

```obj-c JSTransparentBgViewController.m
UIImageView *coverImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, -20, CGRectGetWidth(scrollView.frame), 300)];
```

change the position-y to `-20` _(as we already know that height of status bar is 20)_. Run it

{% img http://jslim89.github.com/images/posts/2014-06-16-dealing-ios-7-uistatusbar/image-statusbar-2.png Image cover the status bar %}

Yeaaaaahh.... DONE :)

You can [download the souce code](https://github.com/jslim89/ios7-statusbar-example/archive/master.zip), or visit my [GitHub repo](https://github.com/jslim89/ios7-statusbar-example). You're welcome to fork it.

**Any suggestions just comment below**

_References:_

* _[iOS 7 Set status bar style per view controller](http://stackoverflow.com/questions/19013975/ios-7-set-status-bar-style-per-view-controller/19014724#19014724)_
* _[UIStatusBarStyle PreferredStatusBarStyle does not work on iOS 7](http://stackoverflow.com/questions/19108513/uistatusbarstyle-preferredstatusbarstyle-does-not-work-on-ios-7/19365160#19365160)_
