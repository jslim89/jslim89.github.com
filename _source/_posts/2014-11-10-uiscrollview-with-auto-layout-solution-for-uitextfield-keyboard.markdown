---
layout: post
title: "UIScrollView with auto-layout solution for UITextField (keyboard)"
date: 2014-11-10 21:26:32 +0800
comments: true
categories: 
- ios
- objective-c
---

I believe there are many people are facing this problem. Last time my approach was to change the the `scrollView`'s `contentSize` upon keyboard appear, and restore to normal when keyboard dismiss.

Now the problem in auto layout of `scrollView` was the `contentSize` is based on it's subviews. I have figured out a way to do that

In **ViewController.m**

```obj-c
@interface ViewController ()

@property (nonatomic) UIScrollView *scrollView;
@property (nonatomic) UITextField *textField;

@end

@implementation TSPromotionVoucherFormViewController {
    // 1.
    UIView *_dummyView;
    NSLayoutConstraint *_dummyViewHeightConstraint;
}

- (void)viewDidLoad {
    [super viewDidLoad];
    
    // 2.
    _scrollView = [[UIScrollView alloc] init];
    _scrollView.translatesAutoresizingMaskIntoConstraints = NO;
    [self.view addSubview:_scrollView];
    
    _dummyView = [[UIView alloc] init];
    _dummyView.translatesAutoresizingMaskIntoConstraints = NO;
    _dummyView.backgroundColor = [UIColor clearColor];
    [_scrollView addSubview:_dummyView];
    
    _textField = [[UITextField alloc] init];
    _textField.translatesAutoresizingMaskIntoConstraints = NO;
    _textField.placeholder = @"Dummy text";
    [_scrollView addSubview:_textField];
    
    // 3.
    NSDictionary *viewsDictionary = NSDictionaryOfVariableBindings(_scrollView
                                                                   , _dummyView
                                                                   , _textField);
    
    NSDictionary *matrics = @{@"inputMargin": @(20), @"textHeight": @(44)};
    
    NSArray *constraints = [NSLayoutConstraint
                            constraintsWithVisualFormat:@"V:|[_scrollView]|"
                            options:0
                            metrics:nil
                            views:viewsDictionary];
    [self.view addConstraints:constraints];

    constraints = [NSLayoutConstraint
                   constraintsWithVisualFormat:@"|[_scrollView]|"
                   options:0
                   metrics:nil
                   views:viewsDictionary];
    [self.view addConstraints:constraints];

    constraints = [NSLayoutConstraint
                   constraintsWithVisualFormat:@"V:|-40-[_textField(textHeight)]-5-[_dummyView]-5-|"
                   options:0
                   metrics:matrics // remember to pass in the matrics if you want to use the "variables"
                   views:viewsDictionary];
    [_scrollView addConstraints:constraints];
    constraints = [NSLayoutConstraint
                   constraintsWithVisualFormat:@"|-inputMargin-[textField]-inputMargin-|"
                   options:0
                   metrics:matrics
                   views:viewsDictionary];
    [_scrollView addConstraints:constraints];
    constraints = [NSLayoutConstraint
                   constraintsWithVisualFormat:@"|-inputMargin-[_dummyView]-inputMargin-|"
                   options:0
                   metrics:matrics
                   views:viewsDictionary];
    [_scrollView addConstraints:constraints];
    
    // 4.
    _dummyViewHeightConstraint = [NSLayoutConstraint constraintWithItem:_dummyView
                                                              attribute:NSLayoutAttributeHeight
                                                              relatedBy:NSLayoutRelationEqual
                                                                 toItem:nil
                                                              attribute:NSLayoutAttributeNotAnAttribute
                                                             multiplier:1.0f
                                                               constant:0];
    [_dummyView addConstraint:_dummyViewHeightConstraint];
    
    // 5.
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillShow:) name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillHide:) name:UIKeyboardWillHideNotification object:nil];
}

#pragma mark - keyboard notification
- (void)keyboardWillShow:(NSNotification *)notification
{
    // 6.
    NSDictionary *userInfo = [notification userInfo];
    CGSize keyboardSize = [[userInfo objectForKey:UIKeyboardFrameBeginUserInfoKey] CGRectValue].size;
    
    [_dummyView removeConstraint:_dummyViewHeightConstraint];
    
    _dummyViewHeightConstraint = [NSLayoutConstraint constraintWithItem:_dummyView
                                              attribute:NSLayoutAttributeHeight
                                              relatedBy:NSLayoutRelationEqual
                                                 toItem:nil
                                              attribute:NSLayoutAttributeNotAnAttribute
                                             multiplier:1.0f
                                               constant:keyboardSize.height];
    [_dummyView addConstraint:_dummyViewHeightConstraint];
}

- (void)keyboardWillHide:(NSNotification *)notification
{
    // 7.
    [_dummyView removeConstraint:_dummyViewHeightConstraint];
    
    _dummyViewHeightConstraint = [NSLayoutConstraint constraintWithItem:_dummyView
                                              attribute:NSLayoutAttributeHeight
                                              relatedBy:NSLayoutRelationEqual
                                                 toItem:nil
                                              attribute:NSLayoutAttributeNotAnAttribute
                                             multiplier:1.0f
                                               constant:0];
    [_dummyView addConstraint:_dummyViewHeightConstraint];
}
```

In the case above, you have a `UIScrollView` in your ViewController, and a `UITextField` inside the `UIScrollView`.

I remention the problem here, when the `textField` on focus, the keyboard will brought up, and the `scrollView`'s contentSize still the same,
the keyboard may blocked the `textField` and become invisible, yet it cannot scroll up.

Let's begin to solve this

1. Declare 2 ivars, a dummy view & it's height constraint.
2. Initialize all views, and make sure `translatesAutoresizingMaskIntoConstraints` is set to `NO` for all views that wanted to use auto layout.
3. Add constraints to all these view. The `matrics` is actually variables that wanted to use inside the constraints' visual format.
4. Initialize the dummy view's height constraint separately to that ivar. Since it is dummy, so set the height _(refer the `constant` argument)_ to 0.
5. Remember to add keyboard's notification _(listen to keyboard's event)_
6. When the keyboard is shown, remove the dummy view's constraint first, then set the dummy view's height to keyboard's height, then only add the height constraint back to the dummy view.
7. When keyboard is hide, do the same thing with **6**, but the `constant` now is **0**.

Run it, and it will works as expected. Enjoy :)
