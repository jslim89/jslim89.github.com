---
layout: post
title: "iOS disable support for iPad - hide from iPad AppStore"
date: 2014-11-18 18:38:41 +0800
comments: true
tags: 
- ios
---

I'm sure you know about Whatsapp. Whatsapp was not available in iPad AppStore.
To achieve this, change **Info.plist** file, right-click -> Open as -> Source Code

Then search for `UIRequiredDeviceCapabilities`, most probably you will see

**Info.plist**

```xml
<key>UIRequiredDeviceCapabilities</key>
<array>
    <string>armv7</string>
    <string>telephony</string> <!-- ADD THIS LINE -->
</array>
```

It means this app only support for those device which can **call**.

_References:_

- _[How to hide an iPhone App from the iPad's AppStore](http://stackoverflow.com/questions/26302092/how-to-hide-an-iphone-app-from-the-ipads-appstore/26302371#26302371)_
- _[Device Compatibility](https://developer.apple.com/library/ios/documentation/DeviceInformation/Reference/iOSDeviceCompatibility/DeviceCompatibilityMatrix/DeviceCompatibilityMatrix.html)_
