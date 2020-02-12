---
title: Web development testing in Android Virtual Device
date: 2020-02-11 18:10:13
tags:
- android 
- avd
- hosts
---

Some time, we will need to test web development in mobile devices.
Previously, I used to test in iOS simulator, the virtual host will just work.

E.g. http://myproject.test, by inputing this URL to simulator Safari, will just work fine.

Recently I tried to install AVD, unfortunately, the device _(simulator)_ just don't know about `myproject.test`.
In this case, need to edit the hosts file

First, find out which device you want to use

```
$ /path/to/android/sdk/platform-tools/adb devices
List of devices attached
emulator-5554	device
emulator-5556	device
```

Set the emulator to writable _(`nexus_5` here is the device name)_

![AVD create device](/images/posts/2020-02-11-Web-development-testing-in-Android-Virtual-Device/create-device.png)

Choose **x86 Images**, then choose **Android 7.1.1** _(non Google APIs)_ _(see reference below for more details)_

```
$ /path/to/android/sdk/tools/emulator -writable-system -netdelay none -netspeed full -avd nexus_5
$ /path/to/android/sdk/platform-tools/adb -s emulator-5556 root
$ /path/to/android/sdk/platform-tools/adb -s emulator-5556 remount

# push /etc/hosts
$ /path/to/android/sdk/platform-tools/adb -s emulator-5556 push /local/path/to/hosts /system/etc/hosts
```

Open browser in android device, type in the URL http://myproject.test

## References:

- [Change the Host File of an Android Emulator](https://www.thepolyglotdeveloper.com/2019/12/change-host-file-android-emulator/)
- [How to use ADB Shell when Multiple Devices are connected? Fails with "error: more than one device and emulator"](https://stackoverflow.com/questions/14654718/how-to-use-adb-shell-when-multiple-devices-are-connected-fails-with-error-mor)
