---
layout: post
title: "Use custom font in iOS application"
date: 2013-10-08 11:04
comments: true
tags: 
- ios
---

There are few steps to add custom font in your Xcode project.

## Step 1: Add `UIAppFonts` to your Info.plist

```xml
<key>UIAppFonts</key>
<array>
    <string>CUSTOMFONT-BLACK.TTF</string>
    <string>CUSTOMFONT-BLACKIT.TTF</string>
    <string>CUSTOMFONT-BOLD.TTF</string>
    <string>CUSTOMFONT-BOLDIT.TTF</string>
    <string>CUSTOMFONT-EXTRALIGHT.TTF</string>
    <string>CUSTOMFONT-EXTRALIGHTIT.TTF</string>
    <string>CUSTOMFONT-IT.TTF</string>
    <string>CUSTOMFONT-LIGHT.TTF</string>
    <string>CUSTOMFONT-LIGHTIT.TTF</string>
    <string>CUSTOMFONT-REGULAR.TTF</string>
    <string>CUSTOMFONT-SEMIBOLD.TTF</string>
    <string>CUSTOMFONT-SEMIBOLDIT.TTF</string>
</array>
```

You must add this before you can use the font

## Step 2: Drag your font into `Resources` folder

* Make sure you check **Copy items into destination group's folder (if needed)**
* Select **Create groups for any added folders**
* Check **Add to targets** for your project

## Step 3: Verify is your font now in Bundle resources

Go to **Project** -> **Targets** and select your project. In **Build Phases** tab, look for **Copy Bundle Resources** and make sure the font is there.

![Resource Bundle](/images/posts/2013-10-08-use-custom-font-in-ios-application/font-in-resource-bundle.png)

## Step 4: Print out all available font

```obj-c
for (NSString *familyName in [UIFont familyNames]) {
    for (NSString *fontName in [UIFont fontNamesForFamilyName:familyName]) {
        NSLog(@"Font: %@", fontName);
    }
}
```

And make sure your font is there

## Step 5: Start using your font

```obj-c
myLabel.font = [UIFont fontWithName:@"CustomFont-Black" size:50];
```

**NOTE: If it doesn't work, repeat `Step 2` again. Some time it may not work**

_References:_

* _[Adding custom fonts to iOS app finding their real names](http://stackoverflow.com/questions/15984937/adding-custom-fonts-to-ios-app-finding-their-real-names/15985120#15985120)_
* _[Use custom fonts in iPhone App [duplicate]](http://stackoverflow.com/questions/13029660/use-custom-fonts-in-iphone-app/13029818#13029818)_

