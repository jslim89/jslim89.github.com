---
layout: post
title: "Show html description in UIWebView in a better way"
date: 2015-03-28 12:28:50 +0800
comments: true
categories: 
- swift
- ios
---

![Desire result](http://jslim89.github.com/images/posts/2015-03-28-show-html-description-in-uiwebview-in-a-better-way/end-result.gif)

Let's take a look on the output above, the **Event Description** content.
Yes, it can achieved by using `UILabel`, but somehow HTML would be easy
to construct the format. Thus I'm showing a demo on how to use `UIWebView`
to achieve this.

**This is using auto-layout**

## Add constraint

_(I assumed that you know how to add constraint through storyboard)_

![Add constraint to UIWebView](http://jslim89.github.com/images/posts/2015-03-28-show-html-description-in-uiwebview-in-a-better-way/add-constraint.png)

Now add 4 or 5 constraints to `UIWebView`, make sure the _Height_ constraint is added, you can put any value you like.

## Set `UIWebViewDelegate` to self

```swift ViewController.swift
class ViewController: UIViewController, UIWebViewDelegate {
    // ...
}
```

Go back the storyboard, hold the right-click and drag it over to the controller

![drag to delegate](http://jslim89.github.com/images/posts/2015-03-28-show-html-description-in-uiwebview-in-a-better-way/delegate.png)

## Disable scroll, format subview

Here to disable the scroll, hide the scroll indicator, disable bounces,
clear the `UIWebView` background

```swift ViewController.swift
self.descriptionWebView.scrollView.scrollEnabled = false
self.descriptionWebView.scrollView.bounces = false
self.descriptionWebView.scrollView.showsHorizontalScrollIndicator = false
self.descriptionWebView.scrollView.showsVerticalScrollIndicator = false
for subview in self.descriptionWebView.subviews {
    if subview.isKindOfClass(UIScrollView) {
        for shadowView in subview.subviews {
            if shadowView.isKindOfClass(UIImageView) {
                (shadowView as UIView).hidden = true
            }
        }
    }
}
```

## Add content & update height

Add a wrapper to the content _(use `main-wrapper` here)_

```swift ViewController.swift
self.descriptionWebView.loadHTMLString("<div id='main-wrapper'>\(html)</div>", baseURL: nil)
```

Implement the `UIWebViewDelegate`

```swift ViewController.swift
func webViewDidFinishLoad(webView: UIWebView) {
    if let output = webView.stringByEvaluatingJavaScriptFromString("document.getElementById(\"main-wrapper\").offsetHeight;") {
        
        if let height = output.toDouble() {
            println("webView content height \(height)")
            println("webview constraint before \(webView.constraints())")

            let heightConstraint = webView.constraints()[0] as NSLayoutConstraint
            // here is to update the height of UIWebView
            heightConstraint.constant = CGFloat(height)
            
            println("webview constraint after \(webView.constraints())")
        }
    }
}
```

The output would be

```
webView content height 187.0
webview constraint before [<NSLayoutConstraint:0x786d49f0 V:[UIWebView:0x786d51e0(30)]>]
webview constraint after [<NSLayoutConstraint:0x786d49f0 V:[UIWebView:0x786d51e0(187)]>]
```

The `UIWebView` constraints here has only 1, thus I assumed that the first
constraint is the _Height_ constraint, by changing the value of the first
constraint, the height of `UIWebView` will be changed.

## Open link by using safari (optional)

Now, if you want user to open safari when they click on the link, implement
another method

```swift
func webView(webView: UIWebView, shouldStartLoadWithRequest request: NSURLRequest, navigationType: UIWebViewNavigationType) -> Bool {
    if navigationType == .LinkClicked {
        UIApplication.sharedApplication().openURL(request.URL)
        return false
    }
    return true
}
```

## Completed

Make sure the `UIWebView` is inside `UIScrollView`. As by using auto-layout,
the `UIScrollView` content height will be expand based on its subviews.
