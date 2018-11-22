---
layout: post
title: "Use UIWebView with UIScrollView"
date: 2014-07-18 21:27:08 +0800
comments: true
tags: 
- ios
---

When we want to display a bunch of description, `UIWebView` is the choice.

Usually we do it in this way

**ViewController.m**

```obj-c
_webView = [[UIWebView alloc] initWithFrame:CGRectMake(0, 0, 320, 300)];
[self.view addSubview:_webView];
```

What it will look like

![Normal webview](http://jslim89.github.com/images/posts/2014-07-18-use-uiwebview-with-uiscrollview/webview-1.png)

**NOTE: the "A button" is just for you to see the difference of different changes of `webview`**

But what if we want it to transparent? Let's try to set the background to `clearColor`

**ViewController.m**

```obj-c
_webView.backgroundColor = [UIColor clearColor];
```

![webview with background transparent](http://jslim89.github.com/images/posts/2014-07-18-use-uiwebview-with-uiscrollview/webview-2.png)

Ooopsss.... Only its background is transparent. Want to make the whole view transparent? Let's do in this way

**ViewController.m**

```obj-c
_webView.opaque = NO;
_webView.backgroundColor = [UIColor clearColor];
for (UIView* subView in [_webView subviews])
{
    if ([subView isKindOfClass:[UIScrollView class]]) {
        for (UIView* shadowView in [subView subviews])
        {
            if ([shadowView isKindOfClass:[UIImageView class]]) {
                [shadowView setHidden:YES];
            }
        }
    }
}
```

and see the result

![transparent webview](http://jslim89.github.com/images/posts/2014-07-18-use-uiwebview-with-uiscrollview/webview-3.png)

Hmm... it looks better. Now let's make it smooth, make the whole thing scroll together _(html content and the "A button")_

Now, the problem is we need to know how much space _(the height of the webView)_ needed for the html content. One of the way is we can get it thru JavaScript

First, add `delegate` to the `webView`

**ViewController.h**

```obj-c
@interface ViewController : UIViewController <UIWebViewDelegate>
```

**ViewController.m**

```obj-c
_webView.delegate = self;
```

Then implement `webViewDidFinishLoad:`

```obj-c
#pragma mark - UIWebViewDelegate
- (void)webViewDidFinishLoad:(UIWebView *)webView
{
    // get the html content height
    NSString *output = [webView stringByEvaluatingJavaScriptFromString:@"document.getElementById(\"main-wrapper\").offsetHeight;"];

    // adjust the height of webView
    CGRect frame = webView.frame;
    frame.size.height = [output intValue];
    webView.frame = frame;
}
```

Before that, add a `<div>` _(with a unique ID, in this case I use "main-wrapper")_ to wrap the html content, e.g.

```obj-c
[_webView loadHTMLString:[NSString stringWithFormat:@"<div id=\"main-wrapper\">%@</div>div>", html] baseURL:nil];
```

let's run it and see

![full height webview](http://jslim89.github.com/images/posts/2014-07-18-use-uiwebview-with-uiscrollview/webview-4.png)

Now it looks better, but we cannot see the content if too long, and also overlap with the button. To solve this, let's add a `UIScrollView` in between the `webView` and `self.view`

**ViewController.m**

```obj-c
_scrollView = [[UIScrollView alloc] initWithFrame:self.view.bounds];
_scrollView.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin | UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleTopMargin;
[self.view addSubview:_scrollView];

...

[_scrollView addSubview:_webView];

...

[_scrollView addSubview:_aButton]; // the button below
```

Add these lines to bottom of `webViewDidFinishLoad:`

```obj-c
// adjust the position of the button
frame = _button.frame;
frame.origin.y = CGRectGetMaxY(webView.frame) + 15;
_button.frame = frame;

// adjust the content size of scrollView
_scrollView.contentSize = CGSizeMake(_scrollView.contentSize.width, CGRectGetMaxY(_button.frame) + 15);
```

_(**15** is margin)_

![scrollable webview](http://jslim89.github.com/images/posts/2014-07-18-use-uiwebview-with-uiscrollview/webview-5.png)

See the scroll bar on the right side?

We're done here. Now we have a smooth scrollable content view

**Ahhhhh!!!! One more thing here**

The initial height of `UIWebView` must not be `0`, e.g.

**ViewController.m**

```obj-c
_webView = [[UIWebView alloc] initWithFrame:CGRectMake(0, 0, 320, 0)]; // <--- if height is 0
```

The result will be

![wrong height webview](http://jslim89.github.com/images/posts/2014-07-18-use-uiwebview-with-uiscrollview/webview-6.png)

This is due to `document.getElementById('main-wrapper').offsetHeight;` return a wrong value, and I don't know why _(if you know please comment)_, this took me 1 hours++ to figure out, at lease you must put `1` for the height.
