---
layout: post
title: "Invoke a method call from AppDelegate via Notification"
date: 2013-09-16 12:12
comments: true
categories: 
- ios
---

Think of a scenario here:

You have a class **LoginViewController**, while Facebook login api call is keep on **AppDelegate.m**, and you want to perform some action after success login via Facebook.

In **AppDelegate.m**

```obj-c
- (void)sessionStateChanged:(FBSession *)session state:(FBSessionState)state error:(NSError *)error
{
    switch (state) {
        case FBSessionStateOpen: {
            // call the method and pass the accessToken to it
            [[NSNotificationCenter defaultCenter] postNotificationName:@"PerformFacebookLogin" object:nil userInfo:[NSDictionary dictionaryWithObjectsAndKeys:session.accessTokenData.accessToken, @"fb_token", nil]];
            
            break;
        }
        case FBSessionStateClosed:
        case FBSessionStateClosedLoginFailed:
            // Login failed or want to end the session
            [FBSession.activeSession closeAndClearTokenInformation];
            
            break;
        default:
            break;
    }
    
    if (error) {
        UIAlertView *alertView = [[UIAlertView alloc]
                                  initWithTitle:@"Facebook login failed"
                                  message:error.description
                                  delegate:nil
                                  cancelButtonTitle:@"OK"
                                  otherButtonTitles:nil];
        [alertView show];
    }
}

- (void)openSession
{
    NSArray *permissions = [NSArray arrayWithObjects:
                            @"email",
                            @"user_birthday",
                            nil];
    [FBSession openActiveSessionWithReadPermissions:permissions
                                       allowLoginUI:YES
                                  completionHandler:
     ^(FBSession *session,
       FBSessionState state, NSError *error) {
         [self sessionStateChanged:session state:state error:error];
     }];
}

- (BOOL)application:(UIApplication *)application openURL:(NSURL *)url sourceApplication:(NSString *)sourceApplication annotation:(id)annotation
{
    return [FBSession.activeSession handleOpenURL:url];
}
```

In **LoginViewController.m**

```obj-c
- (void)viewDidLoad
{
    ...

    // listen to notification, then call facebookLogin method
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(facebookLogin:) name:@"PerformFacebookLogin" object:nil];
}
...
- (void)facebookLogin:(NSNotification *)notification
{
    NSDictionary *userInfo = [notification userInfo];
    NSString *token = [userInfo valueForKey:@"fb_token"];

    // do what you want after success login via Facebook
}
```

References:

- _[Access current view from AppDelegate](http://stackoverflow.com/questions/18707531/access-current-view-from-appdelegate/18707599#18707599)_
- _[How to pass object with NSNotificationCenter](http://stackoverflow.com/questions/7896646/how-to-pass-object-with-nsnotificationcenter/7896761#7896761)_
