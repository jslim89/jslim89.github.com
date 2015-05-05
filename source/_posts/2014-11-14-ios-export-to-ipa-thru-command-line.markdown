---
layout: post
title: "iOS - export to ipa thru command line"
date: 2014-11-14 12:01:08 +0800
comments: true
categories: 
- ios
---

Recently I have come across an issue which Xcode 6 keep crashing when exporting ipa or validate the app.
I got no choice but have to export the ipa on command line.

### 1. Clearn the project

```
$ xcodebuild clean -project /path/to/project.xcodeproj -configuration Release -alltargets
```

### 2. Create archive

![Xcode - Scheme](http://jslim89.github.com/images/posts/2014-11-14-ios-export-to-ipa-thru-command-line/scheme.png)

```
$ xcodebuild archive -project /path/to/project.xcodeproj -scheme "Scheme name" -archivePath /path/to/output
```

See the image above, the scheme name you can refer to Xcode project.

E.g. **Warranty Reminder**

### 3. Export the archive to ipa

![Xcode - Build Settings](http://jslim89.github.com/images/posts/2014-11-14-ios-export-to-ipa-thru-command-line/xcode.png)

```
$ xcodebuild -exportArchive -archivePath /path/to/output.xcarchive -exportPath /path/to/output -exportFormat ipa -exportProvisioningProfile "Provisioning Profile Name"
```

Regarding the provisioning profile, follow exactly the same as what you see in Xcode.

E.g. **iOSTeam Provisioning Profile: com.jslim89.Warranty-Reminder**

Note that the output file name without the **ipa** extension.

You're done.

I have make this into a shell script, you can [download here](http://jslim89.github.com/attachments/posts/2014-11-14-ios-export-to-ipa-thru-command-line/export.sh).

Usage: Just edit the file, instruction is inside. Then run in your terminal

```
$ chmod ugo+x export.sh
$ ./export.sh
```

_References:_

- _[Using xcodebuild To Export a .ipa From an Archive](http://www.thecave.com/2014/09/16/using-xcodebuild-to-export-a-ipa-from-an-archive/)_
