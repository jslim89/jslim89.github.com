---
layout: post
title: "iOS - Change QR code color in UIImage"
date: 2014-05-28 08:52:21 +0800
comments: true
categories: 
- ios
---

**Original QR code**

{% img http://jslim89.github.com/images/posts/2014-05-28-ios-change-qr-code-color-in-uiimage/ori-qrcode.png Original QR code %}

**Red QR code**

{% img http://jslim89.github.com/images/posts/2014-05-28-ios-change-qr-code-color-in-uiimage/red-qrcode.png Red QR code %}

In order to change the QR code color, 2 steps need to be done

### Step 1
Change the white color to transparent

```obj-c
- (UIImage *)replaceColor:(UIColor*)color inImage:(UIImage*)image withTolerance:(float)tolerance;
{
    CGImageRef imageRef = [image CGImage];
    
    NSUInteger width = CGImageGetWidth(imageRef);
    NSUInteger height = CGImageGetHeight(imageRef);
    CGColorSpaceRef colorSpace = CGColorSpaceCreateDeviceRGB();
    
    NSUInteger bytesPerPixel = 4;
    NSUInteger bytesPerRow = bytesPerPixel * width;
    NSUInteger bitsPerComponent = 8;
    NSUInteger bitmapByteCount = bytesPerRow * height;
    
    unsigned char *rawData = (unsigned char*) calloc(bitmapByteCount, sizeof(unsigned char));
    
    CGContextRef context = CGBitmapContextCreate(rawData, width, height,
                                                 bitsPerComponent, bytesPerRow, colorSpace,
                                                 kCGImageAlphaPremultipliedLast | kCGBitmapByteOrder32Big);
    CGColorSpaceRelease(colorSpace);
    
    CGContextDrawImage(context, CGRectMake(0, 0, width, height), imageRef);
    
    CGColorRef cgColor = [color CGColor];
    const CGFloat *components = CGColorGetComponents(cgColor);
    float r = components[0];
    float g = components[1];
    float b = components[2];
    //float a = components[3]; // not needed
    
    r = r * 255.0;
    g = g * 255.0;
    b = b * 255.0;
    
    const float redRange[2] = {
        MAX(r - (tolerance / 2.0), 0.0),
        MIN(r + (tolerance / 2.0), 255.0)
    };
    
    const float greenRange[2] = {
        MAX(g - (tolerance / 2.0), 0.0),
        MIN(g + (tolerance / 2.0), 255.0)
    };
    
    const float blueRange[2] = {
        MAX(b - (tolerance / 2.0), 0.0),
        MIN(b + (tolerance / 2.0), 255.0)
    };
    
    int byteIndex = 0;
    
    while (byteIndex < bitmapByteCount) {
        unsigned char red   = rawData[byteIndex];
        unsigned char green = rawData[byteIndex + 1];
        unsigned char blue  = rawData[byteIndex + 2];
        
        if (((red >= redRange[0]) && (red <= redRange[1])) &&
            ((green >= greenRange[0]) && (green <= greenRange[1])) &&
            ((blue >= blueRange[0]) && (blue <= blueRange[1]))) {
            // make the pixel transparent
            //
            rawData[byteIndex] = 0;
            rawData[byteIndex + 1] = 0;
            rawData[byteIndex + 2] = 0;
            rawData[byteIndex + 3] = 0;
        }
        
        byteIndex += 4;
    }
    
    UIImage *result = [UIImage imageWithCGImage:CGBitmapContextCreateImage(context)];
    
    CGContextRelease(context);
    free(rawData);
    
    return result;
}
```

The method above is to change the color given to transparent

### Step 2
Fill a new color to the image. You can use [MGImageUtilities](https://github.com/mattgemmell/MGImageUtilities) library

```obj-c
UIImage *qrcode = [UIImage imageNamed:@"qrcode"];
// replace the white color to transparent. NOTE: [UIColor whiteColor] won't works here
qrcode = [self replaceColor:[UIColor colorWithRed:1.0f green:1.0f blue:1.0f alpha:1.0f] inImage:image withTolerance:255];
// now fill the image with red color
qrcode = [qrcode imageTintedWithColor:[UIColor redColor]];
```

_References:_

- _[How to make one color transparent on a UIImage?](http://stackoverflow.com/questions/633722/how-to-make-one-color-transparent-on-a-uiimage/10544776#10544776)
