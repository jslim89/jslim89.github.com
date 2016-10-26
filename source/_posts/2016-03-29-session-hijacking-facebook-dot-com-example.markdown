---
layout: post
title: "Session hijacking: facebook.com example"
date: 2016-03-29 10:55:13 +0800
comments: true
categories: 
- hacking
- session-hijacking
---

First of all, [session hijacking](https://en.wikipedia.org/wiki/Session_hijacking) is a technique that steal the cookies from authenticated user, and _lie_ to the server that you're the authenticated user.

I'm going to show an example on how to get into the facebook account.

Assumption: I already have the authenticated cookies on my hand.

## 1. Open facebook.com with Firefox

I still think that Firefox is the best browser ever for developer.

![Firefox - facebook login page](/images/posts/2016-03-29-session-hijacking-facebook-dot-com-example/fb-before-login.png)

You can see the Facebook login page now. Now I need to import the cookies into here.

But before that, please download [Advanced Cookie Manager](https://addons.mozilla.org/en-US/firefox/addon/cookie-manager/) plugin fo Firefox.

## 2. Import the cookies to facebook.com

![Firefox plugin: Advanced cookie manager](/images/posts/2016-03-29-session-hijacking-facebook-dot-com-example/cookie-manager-begin-edit.png)

Go to **Manage Cookies** menu, select "facebook.com" in the **Domain** there. Now you can see a few cookies here.

See the box I highlighted? Facebook use _https_, so _httpOnly_ choose `false`, _isSecure_ choose `true` and _isSession_ also `true`. These are session cookies.

![Advanced cookie manager with all session cookies](/images/posts/2016-03-29-session-hijacking-facebook-dot-com-example/added-all-cookie.png)

Now will be like this.

## 3. Successful login

Refresh the page, and now...

![Successful login](/images/posts/2016-03-29-session-hijacking-facebook-dot-com-example/login-successful.png)

DONE!

## Summary

The concept is like you want to go to foreign country, but you don't have a passport. Now you _steal_/_get_ the passport from someone _(ignore the passport photo, just an example here)_. Now you tell the custom that you're actually the _someone_, and you'll get pass.
