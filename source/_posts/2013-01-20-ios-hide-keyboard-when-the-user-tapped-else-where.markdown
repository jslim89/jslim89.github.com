---
layout: post
title: "iOS: Hide keyboard when the user tapped else where"
date: 2013-01-20 18:00
comments: true
categories: 
- ios
- programming
---

In certain circumstances (i.e. The table row to edit.), we do want the keyboard to be hide in order to improve user experience

In **YourViewController.m**

```obj-c
- (void)viewDidLoad
{
    ...

    UITapGestureRecognizer *gestureRecognizer = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(hideKeyboard:)];
        
    gestureRecognizer.cancelsTouchesInView = NO;
    [self.tableView addGestureRecognizer:gestureRecognizer];
}

- (void)hideKeyboard:(UIGestureRecognizer *)gestureRecognizer
{
    CGPoint point = [gestureRecognizer locationInView:self.tableView];
    NSIndexPath *indexPath = [self.tableView indexPathForRowAtPoint:point];
    
    // Let say you are editing first section first row
    if (indexPath != nil && indexPath.section == 0 && indexPath.row == 0) {
        return;
    }
    [self.firstRowTextField resignFirstResponder];
}
```

In this case we are talking about the first section first row contain a UITextField which allows user to edit.

You want the keyboard to keep appear if the user tap back the row which they're editing, so just `return` and end the function. Otherwise, the keyboard will be hide (i.e. make the `firstRowTextField` lost focus)

`UITapGestureRecognizer` is like an event listener to listen for **Tap** event, if the **Tap** event occurred, `hideKeyboard` will be triggered and passed itself as an argument. The `hideKeyboard` will then act accordingly.
