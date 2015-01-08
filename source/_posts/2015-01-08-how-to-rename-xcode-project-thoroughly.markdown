---
layout: post
title: "How to rename Xcode project thoroughly"
date: 2015-01-08 09:21:39 +0800
comments: true
categories: 
- xcode
---

I'm doing with lot of projects, some projects are similar, thus I can just clone from the
existing project and rename it. But how to rename it _completely_?

## 1. Change the project name in Xcode

![Original name](http://jslim89.github.com/images/posts/2015-01-08-how-to-rename-xcode-project-thoroughly/original.png)

Click on the project, at the right pane, update the name to the name that you want,
then hit **`<Enter>`**

![Rename confirmation](http://jslim89.github.com/images/posts/2015-01-08-how-to-rename-xcode-project-thoroughly/confirm-rename.png)

Then it will prompt you which one to rename, just check all and hit **`<Enter>`**

## 2. Rename scheme

![Rename scheme](http://jslim89.github.com/images/posts/2015-01-08-how-to-rename-xcode-project-thoroughly/rename-scheme.png)

Click the scheme up there, then **Manage schemes...**

![Change scheme name](http://jslim89.github.com/images/posts/2015-01-08-how-to-rename-xcode-project-thoroughly/change-scheme-name.png)

Then change the scheme name _(click on that row, then hit **`<Enter>`**)_

## 3. Rename in Terminal

Close the Xcode first.

Then try to search for your project name

```sh
$ cd /path/to/JsFoo/
$ grep -R "JsFoo" *
JsBar.xcodeproj/project.pbxproj:        4B5454051A5E0E0200FD515D /* JsFooTests.swift in Sources */ = {isa = PBXBuildFile; fileRef = 4B5454041A5E0E0200FD515D /* JsFooTests.swift */; };
JsBar.xcodeproj/project.pbxproj:            remoteInfo = JsFoo;
JsBar.xcodeproj/project.pbxproj:        4B5454041A5E0E0200FD515D /* JsFooTests.swift */ = {isa = PBXFileReference; lastKnownFileType = sourcecode.swift; path = JsFooTests.swift; sourceTree = "<group>"; };
JsBar.xcodeproj/project.pbxproj:                4B5453EB1A5E0E0200FD515D /* JsFoo */,
JsBar.xcodeproj/project.pbxproj:                4B5454011A5E0E0200FD515D /* JsFooTests */,
JsBar.xcodeproj/project.pbxproj:        4B5453EB1A5E0E0200FD515D /* JsFoo */ = {
JsBar.xcodeproj/project.pbxproj:            path = JsFoo;
JsBar.xcodeproj/project.pbxproj:        4B5454011A5E0E0200FD515D /* JsFooTests */ = {
JsBar.xcodeproj/project.pbxproj:                4B5454041A5E0E0200FD515D /* JsFooTests.swift */,
JsBar.xcodeproj/project.pbxproj:            path = JsFooTests;
JsBar.xcodeproj/project.pbxproj:            productName = JsFoo;
JsBar.xcodeproj/project.pbxproj:            productName = JsFooTests;
JsBar.xcodeproj/project.pbxproj:                4B5454051A5E0E0200FD515D /* JsFooTests.swift in Sources */,
JsBar.xcodeproj/project.pbxproj:                INFOPLIST_FILE = JsFoo/Info.plist;
JsBar.xcodeproj/project.pbxproj:                INFOPLIST_FILE = JsFoo/Info.plist;
JsBar.xcodeproj/project.pbxproj:                INFOPLIST_FILE = JsFooTests/Info.plist;
JsBar.xcodeproj/project.pbxproj:                TEST_HOST = "$(BUILT_PRODUCTS_DIR)/JsFoo.app/JsFoo";
JsBar.xcodeproj/project.pbxproj:                INFOPLIST_FILE = JsFooTests/Info.plist;
JsBar.xcodeproj/project.pbxproj:                TEST_HOST = "$(BUILT_PRODUCTS_DIR)/JsFoo.app/JsFoo";
Binary file JsBar.xcodeproj/project.xcworkspace/xcuserdata/js.xcuserdatad/UserInterfaceState.xcuserstate matches
JsFoo/AppDelegate.swift://  JsFoo
JsFoo/ViewController.swift://  JsFoo
JsFooTests/JsFooTests.swift://  JsFooTests.swift
JsFooTests/JsFooTests.swift://  JsFooTests
JsFooTests/JsFooTests.swift:class JsFooTests: XCTestCase {
```

It seems a lot to change... No worry, there is an easy way to do it

```sh
$ grep -Rl "JsFoo" * | xargs sed -i "" "s/JsFoo/JsBar/"
sed: Binary: No such file or directory
```

`grep` to search for "JsFoo", and the `-l` option is to show the file name only,
then only pass the result to `sed` to change "JsFoo" to "JsBar"

Try to search again...

```sh
$ grep -R "JsFoo" *
Binary file JsBar.xcodeproj/project.xcworkspace/xcuserdata/js.xcuserdatad/UserInterfaceState.xcuserstate matches
JsFoo/AppDelegate.swift://  JsFoo
JsFoo/ViewController.swift://  JsFoo
JsFooTests/JsFooTests.swift://  JsFooTests.swift
JsFooTests/JsFooTests.swift://  JsFooTests
JsFooTests/JsFooTests.swift:class JsFooTests: XCTestCase {
```

Oops... Some files haven't change. The reason for this is because one of the file is actually
a binary file, up until there the instruction is break. Let's do again and exclude that binary
file

```sh
$ grep -Rl "JsFoo" JsFoo*/* | xargs sed -i "" "s/JsFoo/JsBar/"
```

Search again?

```sh
$ grep -R "JsFoo" *
Binary file JsBar.xcodeproj/project.xcworkspace/xcuserdata/js.xcuserdatad/UserInterfaceState.xcuserstate matches
```

Done! Now totally no more "JsFoo". One more thing, rename the folders

```sh
$ mv JsFoo JsBar
$ mv JsFooTests JsBarTests
```

Now you can open up your Xcode project, and build it

![Build error](http://jslim89.github.com/images/posts/2015-01-08-how-to-rename-xcode-project-thoroughly/build-error.png)

Oops... Got error?

```
<unknown>0: error: no such file or directory: '.../JsBarTests/JsBarTests.swift'
```

One more file that we forgot to rename

```sh
$ mv JsBarTests/JsFooTests.swift JsBarTests/JsBarTests.swift
```

![Successful rename](http://jslim89.github.com/images/posts/2015-01-08-how-to-rename-xcode-project-thoroughly/rename-success.png)

Done!!!
