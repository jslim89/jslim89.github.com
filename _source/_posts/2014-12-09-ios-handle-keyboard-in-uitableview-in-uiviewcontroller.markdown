---
layout: post
title: "iOS - handle keyboard in UITableView in UIViewController"
date: 2014-12-09 22:16:37 +0800
comments: true
categories: 
- ios
- objective-c
---

Most of the time, we tend to put `textField` in a `tableView`, perhaps it is much simpler.

If your form in `UITableView` extends `UITableViewController`, then you can skip the post, `UITableViewController` will handle this issue for you.

But, in certain situations, we need to extends `UIViewController` and handle the keyboard with `UITableView` by our own.

The **ViewController.m** is subclass of `UIViewController`

```obj-c
...
@property (nonatomic) UITableView *tableView;

...

- (void)viewDidLoad
{
    _tableView = [[UITableView alloc] init];
    _tableView.translatesAutoresizingMaskIntoConstraints = NO;
    _tableView.delegate = self;
    _tableView.dataSource = self;
    _tableView.rowHeight = kTextFieldCellHeight;
    _tableView.allowsSelection = NO;
    [self.view addSubview:_tableView];

    /* add constraint for the _tableView */

    // 1.
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillShow:) name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillHide:) name:UIKeyboardWillHideNotification object:nil];
}

#pragma mark - keyboard notification
- (void)keyboardWillShow:(NSNotification *)notification
{
    NSDictionary *userInfo = [notification userInfo];
    CGSize keyboardSize = [[userInfo objectForKey:UIKeyboardFrameBeginUserInfoKey] CGRectValue].size;
    
    // 2.
    _tableView.tableFooterView = [[UIView alloc] initWithFrame:CGRectMake(0, 0, keyboardSize.width, keyboardSize.height)];
}

- (void)keyboardWillHide:(NSNotification *)notification
{
    // 3.
    _tableView.tableFooterView = nil;
}
```

1. Register for keyboard notifications
2. When the keyboard shown, add footerView to that table and make sure the height is same as keyboard.
_(when add in the footerView, the `contentSize` of the tableView is actually increase, so that
it has more room to scroll and the keyboard won't block the content below)_
3. When keyboard hide, remove the footerView, the tableView's `contentSize` will then changed back to
it's original height.

## Update: 13 Jan, 2015

```obj-c
- (void)keyboardWillShow:(NSNotification *)notification
{
    NSDictionary *userInfo = [notification userInfo];
    CGSize keyboardSize = [[userInfo objectForKey:UIKeyboardFrameBeginUserInfoKey] CGRectValue].size;
    
    UIEdgeInsets contentInsets = UIEdgeInsetsMake(0.0, 0.0, keyboardSize.height, 0.0);
    _signInTableView.contentInset = contentInsets;
    _signInTableView.scrollIndicatorInsets = contentInsets;
    
    _signUpTableView.contentInset = contentInsets;
    _signUpTableView.scrollIndicatorInsets = contentInsets;
}

- (void)keyboardWillHide:(NSNotification *)notification
{
    UIEdgeInsets contentInsets = UIEdgeInsetsZero;
    _signInTableView.contentInset = contentInsets;
    _signInTableView.scrollIndicatorInsets = contentInsets;
    
    _signUpTableView.contentInset = contentInsets;
    _signUpTableView.scrollIndicatorInsets = contentInsets;
}
```

This solution doesn't need to care about the `tableFooterView`, but change it's content inset instead.
