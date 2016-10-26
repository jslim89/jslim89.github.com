---
layout: post
title: "Distribute iOS app via website"
date: 2014-03-31 21:50:44 +0800
comments: true
categories: 
- ios
---

The first time I seen this is [emu4ios.net](http://emu4ios.net/). Then I found this is very useful and convenient for my beta tester.

I shows an exmple to host the **ipa** & **plist** files on [Dropbox](https://db.tt/mBV8M1u). The reason for not host them on my personal website is due to SSL issue. [See here](https://discussions.apple.com/message/25140827#25140827)

Without **https**

![Invalid certificate](http://jslim89.github.com/images/posts/2014-03-31-distribute-ios-app-via-website/invalid-cert.png)

## 1st: Generate ipa file

Open your XCode, then archive the project

![Archive](http://jslim89.github.com/images/posts/2014-03-31-distribute-ios-app-via-website/archive.png)

Then select distribute

![Distribute](http://jslim89.github.com/images/posts/2014-03-31-distribute-ios-app-via-website/distribute.png)

Select **Save for Enterprise or Ad Hoc Deployment** distribution method

![Distribution method](http://jslim89.github.com/images/posts/2014-03-31-distribute-ios-app-via-website/distribution-method.png)

Then choose the provisioning profile that you have selected before Archive

![Select provisioning profile](http://jslim89.github.com/images/posts/2014-03-31-distribute-ios-app-via-website/provisioning-profile-selection.png)

Finally save the **ipa** file

![Save ipa to anywhere](http://jslim89.github.com/images/posts/2014-03-31-distribute-ios-app-via-website/save-ipa.png)

## 2nd: Create a .plist

**MyApp.plist**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>items</key>
    <array>
        <dict>
            <key>assets</key>
            <array>
                <dict>
                    <key>kind</key>
                    <string>software-package</string>
                    <key>url</key>
                <string>https://dl.dropboxusercontent.com/u/12345678/MyApp/MyApp.ipa</string>
                </dict>
            </array>
            <key>metadata</key>
            <dict>
                <key>bundle-identifier</key>
                <string>com.example.MyApp</string>
                <key>bundle-version</key>
                <string>1.0.0</string>
                <key>kind</key>
                <string>software</string>
                <key>title</key>
                <string>My App</string>
            </dict>
        </dict>
    </array>
</dict>
</plist>
```

## 3rd: Move both `ipa` & `plist` files to your Dropbox `Public` folder

e.g.

```
Dropbox
  |---- MyApp
          |------ MyApp.ipa
          |------ MyApp.plist
```

## 4th: Get the public link from Dropbox

![Copy Dropbox public link](http://jslim89.github.com/images/posts/2014-03-31-distribute-ios-app-via-website/dropbox-public-link.png)

Then send the link over to your beta tester, then they will be able to install via Safari.
