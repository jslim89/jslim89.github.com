---
layout: post
title: "iOS - crop image based on a mask"
date: 2014-12-09 09:08:22 +0800
comments: true
tags: 
- objective-c
- ios
---

I have a mask here _(the Sun Goku hair)_, and I want to put a face to this mask.

![mask](http://jslim89.github.com/images/posts/2014-12-09-ios-crop-image-based-on-a-mask/mask.png)

## Setup the base view

**ViewController.m**

```obj-c
#import "ViewController.h"

@interface ViewController () <UIActionSheetDelegate>

@property (nonatomic) UIImageView *maskView;
@property (nonatomic) UIImageView *cropImageView;
@property (nonatomic) UIButton *photoButton;

@end

@implementation ViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    
    _maskView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"sungoku"]];
    _maskView.center = CGPointMake(CGRectGetWidth(self.view.frame) / 2.0, (CGRectGetHeight(self.view.frame) / 2.0) - 30);
    [self.view addSubview:_maskView];
    
    // kCropFrame = the image frame
    // where _cropImageView is relative to self.view (not _maskView), thus have to adjust the frame
    _cropImageView = [[UIImageView alloc] initWithFrame:CGRectMake(CGRectGetMinX(kCropFrame) + CGRectGetMinX(_maskView.frame), CGRectGetMinY(kCropFrame) + CGRectGetMinY(_maskView.frame), CGRectGetWidth(kCropFrame), CGRectGetHeight(kCropFrame))];
    [self.view insertSubview:_cropImageView belowSubview:_maskView];
    
    _photoButton = [UIButton buttonWithType:UIButtonTypeCustom];
    _photoButton.frame = CGRectMake(20, CGRectGetMaxY(_maskView.frame) + 40, CGRectGetWidth(self.view.frame) - 40, 40);
    _photoButton.layer.cornerRadius = 5;
    _photoButton.backgroundColor = [UIColor colorWithRed:0.2 green:0.6 blue:0.8 alpha:1];
    _photoButton.titleLabel.textColor = [UIColor whiteColor];
    [_photoButton setTitle:@"Photo" forState:UIControlStateNormal];
    [_photoButton addTarget:self action:@selector(photoTapped:) forControlEvents:UIControlEventTouchUpInside];
    [self.view addSubview:_photoButton];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - event
- (void)photoTapped:(UIButton *)sender
{
    UIActionSheet *actionSheet = [[UIActionSheet alloc] initWithTitle:nil
                                                             delegate:self
                                                    cancelButtonTitle:@"Cancel"
                                               destructiveButtonTitle:nil
                                                    otherButtonTitles:@"Take Photo", @"Choose Existing Photo", nil];
    [actionSheet showInView:self.view];
}

#pragma mark - UIActionSheetDelegate
- (void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if (buttonIndex == actionSheet.cancelButtonIndex) return;
    
    UIImagePickerController *picker = [[UIImagePickerController alloc] init];
    picker.delegate = self;
    picker.allowsEditing = YES;
    picker.sourceType = buttonIndex == 1 ? UIImagePickerControllerSourceTypePhotoLibrary : UIImagePickerControllerSourceTypeCamera;
    [self presentViewController:picker animated:YES completion:nil];
}

@end
```

The code above create a simple view with a mask, button and a place holder for the cropped image.

Where the `kCropFrame` is a macro I define in **pch** file. This value I want to use it across multiple viewControllers.

i.e. `#define kCropFrame CGRectMake(45, 80, 95, 62)`.

The question here is "How do you know the number?"

1. Open up the image, select the area that I wanted to crop _(get the `width` & `height`)_

![select crop area](http://jslim89.github.com/images/posts/2014-12-09-ios-crop-image-based-on-a-mask/crop-area.png)

2. Drag all the way to top left _(get the position `x` & `y`)_

![get crop area position (top left)](http://jslim89.github.com/images/posts/2014-12-09-ios-crop-image-based-on-a-mask/crop-area-xy.png)

Now I got the `CGRect` value

## Crop image controller

**CropViewController.h**

```obj-c
import <UIKit/UIKit.h>

// 1.
@protocol CropViewControllerDelegate <NSObject>

- (void)cropViewControllerDidCroppedImage:(UIImage *)image;

@end

@interface CropViewController : UIViewController <UIScrollViewDelegate>

@property (nonatomic, weak) id<CropViewControllerDelegate> delegate;

// 2. 
@property (nonatomic) UIImage *faceImage;

@end
```

1. Delegate method after cropping the image
2. Accept the raw image from parent viewController

**CropViewController.m**

```obj-c
#import "CropViewController.h"

@interface CropViewController ()

@property (nonatomic) UIImageView *maskImageView;
@property (nonatomic) UIScrollView *faceScrollView;
@property (nonatomic) UIImageView *faceImageView;

@end

@implementation CropViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    self.view.backgroundColor = [UIColor whiteColor];
    self.title = @"Crop";
    self.navigationItem.leftBarButtonItem = [[UIBarButtonItem alloc] initWithTitle:@"Close" style:UIBarButtonItemStylePlain target:self action:@selector(closeTapped:)];
    self.navigationItem.rightBarButtonItem = [[UIBarButtonItem alloc] initWithTitle:@"Done" style:UIBarButtonItemStylePlain target:self action:@selector(doneTapped:)];
    
    // 1.
    _faceScrollView = [[UIScrollView alloc] initWithFrame:self.view.bounds];
    _faceScrollView.autoresizingMask = UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleBottomMargin;
    _faceScrollView.delegate = self;
    _faceScrollView.showsHorizontalScrollIndicator = NO;
    _faceScrollView.showsVerticalScrollIndicator = NO;
    [self.view addSubview:_faceScrollView];
    
    _faceImageView = [[UIImageView alloc] initWithImage:_faceImage];
    _faceScrollView.contentSize = _faceImageView.bounds.size;
    _faceScrollView.maximumZoomScale = 2;
    _faceScrollView.minimumZoomScale = _faceScrollView.frame.size.width  / _faceImageView.frame.size.width;;
    _faceScrollView.zoomScale = _faceScrollView.minimumZoomScale;
    [_faceScrollView addSubview:_faceImageView];

    // 3.a.
    UIView *overlayView = [[UIView alloc] initWithFrame:self.view.bounds];
    overlayView.autoresizingMask = UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleBottomMargin;
    overlayView.userInteractionEnabled = NO;
    [self.view addSubview:overlayView];
    
    // 2.
    _maskImageView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"sungoku"]];
    _maskImageView.center = CGPointMake(CGRectGetWidth(self.view.frame) / 2.0, (CGRectGetHeight(self.view.frame) / 2.0) - 30);
    [self.view addSubview:_maskImageView];

    // 3.b.
    UIBezierPath *overlayPath = [UIBezierPath bezierPathWithRect:overlayView.bounds];
    UIBezierPath *transparentPath = [UIBezierPath bezierPathWithOvalInRect:CGRectMake(CGRectGetMinX(_maskImageView.frame) + CGRectGetMinX(kCropFrame), CGRectGetMinY(_maskImageView.frame) + CGRectGetMinY(kCropFrame), CGRectGetWidth(kCropFrame), CGRectGetHeight(kCropFrame))];
    [overlayPath appendPath:transparentPath];
    [overlayPath setUsesEvenOddFillRule:YES];
    CAShapeLayer *fillLayer = [CAShapeLayer layer];
    fillLayer.path = overlayPath.CGPath;
    fillLayer.fillRule = kCAFillRuleEvenOdd;
    fillLayer.fillColor = [UIColor colorWithWhite:0 alpha:0.5].CGColor;
    [overlayView.layer addSublayer:fillLayer];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - helper
// 4.
- (UIImage *)image:(UIImage *)image cropInRect:(CGRect)rect
{
    CGImageRef imageRef = CGImageCreateWithImageInRect([image CGImage], rect);
    UIImage *cropped = [UIImage imageWithCGImage:imageRef];
    CGImageRelease(imageRef);
    
    return cropped;
}

#pragma mark - event
- (void)closeTapped:(id)sender
{
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (void)doneTapped:(id)sender
{
    // 5.
    CGRect placeholderInGlobalSpace = [self.view convertRect:kCropFrame fromView:_maskImageView];
    // 6.
    CGRect selectedRectInFaceImage = [self.view convertRect:placeholderInGlobalSpace toView:_faceImageView];
    
    // 7.
    UIImage *croppedImage = [self image:_faceImage cropInRect:selectedRectInFaceImage];
    
    if ([_delegate respondsToSelector:@selector(cropViewControllerDidCroppedImage:)]) {
        [_delegate cropViewControllerDidCroppedImage:croppedImage];
    }
    [self dismissViewControllerAnimated:YES completion:nil];
}

#pragma mark - UIScrollViewDelegate
// 8.
- (UIView *)viewForZoomingInScrollView:(UIScrollView *)faceScrollView
{
    return _faceImageView;
}
```

1. Create an `UIImageView` and attach it to a `UIScrollView`, this is to enable the zooming
2. Put a mask on top of the `scrollView`
3. a) Overlay is a semi-transparent area to gray out the main image
   b) Mask out the middle area _(the area that we're going to crop)_
4. Create a helper function for cropping image
5. Since both the **mask** & the **main image** are not sibling, thus have to convert their position to base on the root view
6. After got the `frame` of the mask relative to the root view, then get the frame relative to the main image view
7. Crop the image base on the rectangle we got just now
8. For zooming purpose, is a delegate method from `UIScrollView`

**ViewController.m**

```obj-c
#import "CropViewController.h"

...
// 1.
@interface ViewController () <UIActionSheetDelegate, UIImagePickerControllerDelegate, UINavigationControllerDelegate, CropViewControllerDelegate>

...

#pragma mark - CropViewControllerDelegate
// 2.
- (void)cropViewControllerDidCroppedImage:(UIImage *)image
{
    // Solution 1: crop the UIImage to oval shape
    UIBezierPath *path = [UIBezierPath bezierPathWithOvalInRect:CGRectMake(0, 0, image.size.width, image.size.height)];
    //you have to account for the x and y values of your UIBezierPath rect
    UIGraphicsBeginImageContext(image.size);
    //this gets the graphic context
    CGContextRef context = UIGraphicsGetCurrentContext();
    //you can stroke and/or fill
    CGContextSetFillColorWithColor(context, [UIColor colorWithPatternImage:image].CGColor);
    [path fill];
    //now get the image from the context
    UIImage *bezierImage = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    // Solution 2: the image remain in rectangle, mask the UIImageView
    /*
    UIBezierPath *path = [UIBezierPath bezierPathWithOvalInRect:_cropImageView.bounds];
    CAShapeLayer *maskLayer = [CAShapeLayer layer];
    maskLayer.path = path.CGPath;
    _cropImageView.layer.mask = maskLayer;
     */
    
    // save to library
    // uncomment it if you want to see the effect of the image after cropping
    // UIImageWriteToSavedPhotosAlbum(image, nil, nil, nil);
    
    _cropImageView.image = bezierImage;
}

#pragma mark - UIImagePickerControllerDelegate
// 3.
- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingMediaWithInfo:(NSDictionary *)info
{
    [self dismissViewControllerAnimated:YES completion:^{
        
        CropViewController *controller = [[CropViewController alloc] init];
        controller.delegate = self;
        controller.faceImage = info[UIImagePickerControllerEditedImage];
        
        UINavigationController *navController = [[UINavigationController alloc] initWithRootViewController:controller];
        [self presentViewController:navController animated:YES completion:nil];
        
    }];
}

- (void)imagePickerControllerDidCancel:(UIImagePickerController *)picker
{
    [self dismissViewControllerAnimated:YES completion:nil];
}
```

1. Conform to those protocols
2. The delegate method of after cropping the image. There are 2 solution here:
**Solution 1** is to crop the actual image to oval shape; where **Solution 2**
remain the image as rectangle, but mask out the `UIImageView` to display it as
oval shape. _(you uncomment the line and save it to see what is the difference)_
3. The delegate method after taking photo, make sure pass the image to the crop view controller.

Test it, you can adjust the main image.

![Adjust the image to the mask](http://jslim89.github.com/images/posts/2014-12-09-ios-crop-image-based-on-a-mask/adjust-image.png)

Then the final result will be like

![The final result](http://jslim89.github.com/images/posts/2014-12-09-ios-crop-image-based-on-a-mask/result.png)

You can download the sample project in my [GitHub repo](https://github.com/jslim89/CropImageObjc-Example).

_References:_

- _[how to draw oval shape in UIImageview](https://stackoverflow.com/questions/23779116/how-to-draw-oval-shape-in-uiimageview/23779728#23779728)_
