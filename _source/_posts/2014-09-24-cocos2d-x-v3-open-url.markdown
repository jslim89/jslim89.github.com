---
layout: post
title: "Cocos2d-x v3 open URL"
date: 2014-09-24 08:13:34 +0800
comments: true
categories: 
- cocos2d-x
---

In Cocos2d-x _(I'm using version 3)_, there is no built-in method to open an external URL. However, this can be achieved. Here I'm only showing on iOS & Android platform.

```
$ cd /path/to/game
$ ls
cocos2d/       proj.ios_mac/  proj.win32/     res/  CMakeLists.txt
proj.android/  proj.linux/    proj.wp8-xaml/  src/
```

Now, this has to change the **cocos2d**'s core files.

## iOS

Edit the **./cocos2d/cocos/platform/ios/CCApplication.h**

```cpp
...
    void openURL(const char *url); // ADD THIS LINE

protected:
...
```

Then edit **./cocos2d/cocos/platform/ios/CCApplication.mm**, add the implementation for that method `openURL`

```obj-c
...
void Application::openURL(const char *url)
{
    NSString *urlString = [NSString stringWithCString:url encoding:NSASCIIStringEncoding];
    
    NSURL *nsURL = [NSURL URLWithString:urlString];
    [[UIApplication sharedApplication] openURL:nsURL];
}

NS_CC_END
```

## Android

Edit the **./cocos2d/cocos/platform/android/CCApplication.h**

```cpp
...
    void openURL(const char *url); // ADD THIS LINE

protected:
...
```

Then edit **./cocos2d/cocos/platform/android/CCApplication.cpp**, add the implementation for that method `openURL`. This will invoke the native Java method.

```cpp
...
void Application::openURL(const char *url)
{
    JniMethodInfo minfo;
    
    if (JniHelper::getStaticMethodInfo(minfo, "org/cocos2dx/lib/Cocos2dxActivity", "openURL", "(Ljava/lang/String;)V")) {
        jstring StringArg1 = minfo.env->NewStringUTF(url);
        minfo.env->CallStaticVoidMethod(minfo.classID, minfo.methodID, StringArg1);
        minfo.env->DeleteLocalRef(StringArg1);
        minfo.env->DeleteLocalRef(minfo.classID);
    }
}

NS_CC_END
```

Lastly edit **./cocos2d/cocos/platform/android/java/src/org/cocos2dx/lib/Cocos2dxActivity.java**

```java
...
// import this 2 classes
import android.content.Intent;
import android.net.Uri;

...

    private static Activity me = null; // ADD THIS
        
    public static Context getContext() {
        // ...
    }
    ...

    @Override
    protected void onCreate(final Bundle savedInstanceState) {
        ...
        this.init();
        
        me = this; // ADD THIS AFTER this.init()

        ...
    }

    // your openURL implementation
    public static void openURL(String url) {
        Intent i = new Intent(Intent.ACTION_VIEW);
        i.setData(Uri.parse(url));
        me.startActivity(i);
    }
    ...
```

You have done.

## How to use?

You can invoke `Application::getInstance()->openURL("your url")`. e.g.

**MyScene.cpp**

```cpp
switch (Application::getInstance()->getTargetPlatform()) {
    case Application::Platform::OS_IPAD:
    case Application::Platform::OS_IPHONE:
        Application::getInstance()->openURL("http://www.apple.com");
        break;
        
    case Application::Platform::OS_ANDROID:
        Application::getInstance()->openURL("http://www.google.com");
        break;
        
    default:
        break;
}
```

Happy coding :)

_Reference:_

- _[Cocos2d-x Tutorial - Opening a URL](http://www.youtube.com/watch?v=QdrCZvOXssY)_
