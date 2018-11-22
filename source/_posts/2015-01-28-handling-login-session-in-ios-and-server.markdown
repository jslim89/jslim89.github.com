---
layout: post
title: "Handling login session in iOS &amp; server"
date: 2015-01-28 09:54:55 +0800
comments: true
tags: 
- ios
- php
---

When comes to server & client side integration, there are many
ways to handle the login session. Some people might keep the
login user ID or token in `NSUserDefaults`, some are to keep
the login session cookie. There is no right or wrong.

I'm here to share about session cookie way.

### In server side (e.g. PHP)

Let say once user successfully login, the server will then keep
the login user into session.

```php
<?php
session_start();
if(<correct credential>) {
    $_SESSION['curr_user'] = ...;
}
```

### In Objective-C (client-side)

What about in client side?

**AppDelegate.m**

```obj-c
// 1.
static NSString *kServerSessionCookie = @"PHPSESSID";
static NSString *kLocalCookieName = @"MyProjectCookie";
static NSString *kLocalUserData = @"MyProjectLocalUser";
static NSString *kBaseUrl = @"http://api.example.com";

// ...

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{
    // ...

    // 2.
    [self updateSession];
    
    if ([self isLoggedIn]) {
        // action if currently logged in
    } else {
        // action if currently not logged in
    }
    
    // ...
}

// 3.
- (void)saveLoginSession
{
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    
    NSArray *allCookies = [[NSHTTPCookieStorage sharedHTTPCookieStorage] cookiesForURL:[NSURL URLWithString:kBaseUrl]];
    for (NSHTTPCookie *cookie in allCookies) {
        if ([cookie.name isEqualToString:kServerSessionCookie]) {
            NSMutableDictionary* cookieDictionary = [NSMutableDictionary dictionaryWithDictionary:[defaults dictionaryForKey:kLocalCookieName]];
            [cookieDictionary setValue:cookie.properties forKey:kBaseUrl];
            [defaults setObject:cookieDictionary forKey:kLocalCookieName];
            [defaults synchronize];
            
            break;
        }
    }
}

// 4.
- (void)removeLoginSession
{
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    [defaults removeObjectForKey:kLocalCookieName];
    [defaults synchronize];
}

// 5.
- (void)updateSession
{
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSDictionary *cookieDict = [defaults dictionaryForKey:kLocalCookieName];
    NSDictionary *cookieProperties = [cookieDict valueForKey:kBaseUrl];
    if (cookieProperties != nil) {
        NSHTTPCookie *cookie = [NSHTTPCookie cookieWithProperties:cookieProperties];
        NSArray *cookies = [NSArray arrayWithObject:cookie];
        [[NSHTTPCookieStorage sharedHTTPCookieStorage] setCookies:cookies forURL:[NSURL URLWithString:kBaseUrl] mainDocumentURL:nil];
    }
}

- (BOOL)isLoggedIn
{
    return [[NSUserDefaults standardUserDefaults] objectForKey:kLocalCookieName] != nil;
}
```

1. Keep those _key_ as constants. By default, PHP session cookie name is `PHPSESSID`, if you're using other framework, the key might be changed.
2. Once user logon, the session cookie will be kept at client-side, without the cookie, the server will not know that the user is currently logged in. Thus sync it when first enter the app.
3. Upon successful login, keep the session data to `NSUserDefaults`, we can then check whether the session is exists before call any server API.
4. Once logged out, remember to remove the session.
5. Sync the session cookie kept in `NSUserDefaults` into cookie storage.
