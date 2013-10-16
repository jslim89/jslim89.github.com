---
layout: post
title: "Bring up UIPickerView when clicked on UIButton"
date: 2013-10-16 13:51
comments: true
categories: 
- ios
---

We don't add UIPickerView as subview. There is a trick to achieve this.

### Create a dummy UITextField
This text field is not visible to user, is a hidden field

```obj-c ViewController.m
#import "ViewController.h"

@interface ViewController ()

@property (nonatomic, strong) UITextField *pickerViewTextField;

@end

@implementation ViewController

@synthesize pickerViewTextField = _pickerViewTextField;

- (void)viewDidLoad
{
    [super viewDidLoad];

    // set the frame to zero
    self.pickerViewTextField = [[UITextField alloc] initWithFrame:CGRectZero];
    [self.view addSubview:self.pickerViewTextField];
    
    UIPickerView *pickerView = [[UIPickerView alloc] initWithFrame:CGRectMake(0, 0, 0, 0)];
    pickerView.showsSelectionIndicator = YES;
    pickerView.dataSource = self;
    pickerView.delegate = self;
    
    // set change the inputView (default is keyboard) to UIPickerView
    self.pickerViewTextField.inputView = pickerView;
    
    // add a toolbar with Cancel & Done button
    UIToolbar *toolBar = [[UIToolbar alloc] initWithFrame:CGRectMake(0, 0, 320, 44)];
    toolBar.barStyle = UIBarStyleBlackOpaque;
    
    UIBarButtonItem *doneButton = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(doneTouched:)];
    UIBarButtonItem *cancelButton = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemCancel target:self action:@selector(cancelTouched:)];
    
    // the middle button is to make the Done button align to right
    [toolBar setItems:[NSArray arrayWithObjects:cancelButton, [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:nil action:nil], doneButton, nil]];
    self.pickerViewTextField.inputAccessoryView = toolBar;

    ...
}
```

### Trigger the picker view when click on UIButton

```obj-c ViewController.m
...
- (IBAction)someButtonTouched:(UIButton *)sender
{
    [self.pickerViewTextField becomeFirstResponder];
}
```

### Add methods for bar buttons

```obj-c ViewController.m
- (void)cancelTouched:(UIBarButtonItem *)sender
{
    // hide the picker view
    [self.pickerViewTextField resignFirstResponder];
}

- (void)doneTouched:(UIBarButtonItem *)sender
{
    // hide the picker view
    [self.pickerViewTextField resignFirstResponder];

    // perform some action
}
```

### Add dataSource & delegate for UIPickerView

```obj-c ViewController.m
#pragma mark - UIPickerViewDataSource
- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView
{
    return 1;
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component
{
    return [yourItems count];
}

#pragma mark - UIPickerViewDelegate
- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component
{
    NSString *item = [yourItems objectAtIndex:row];
    
    return item;
}

- (void)pickerView:(UIPickerView *)pickerView didSelectRow:(NSInteger)row inComponent:(NSInteger)component
{
    // perform some action
}
```

Don't forget to make your `ViewController` conform to the protocols

```obj-c ViewController.h
@interface ViewController : UIViewController <UIPickerViewDataSource, UIPickerViewDelegate>
```

You're done :)
