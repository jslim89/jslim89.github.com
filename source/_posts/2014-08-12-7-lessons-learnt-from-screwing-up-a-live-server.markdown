---
layout: post
title: "7 lessons learnt from screwing up a live server"
date: 2014-08-12 22:21:48 +0800
comments: true
categories: 
- server
---

I've just did a **shit** today. I screw up the client's server in such a way that I remove the apache server, and never get a way to install it back.

It makes me feel uncomfortable, sweating and stimulate my brain cell until I lost for some time...

However, I've learnt some lessons here. Please read all, especially if you're newbie developer or fresh graduate.

## 1. Never bring habits from development to production

Imagine that during development, your web server suddenly doesn't work, and you googles the solution for hours. What you'll do now?
For most of us the answer would be _"remove the web server, then reinstall"_, right? And most of the time we think we're genius,
because it was so simple, so fast, so easily solved. But... I did this today on the client's server...

I've tried to setup a project for hours, but don't know why it doesn't works. I removed it. YEAH~~~ removed successfully without any
error, but later on when I try to install, it failed, and again, I have no idea why...

Please do remember this, don't bring whatever habits that you usually did in local machine to production.

## 2. Never trust solutions on stackexchange when comes to production

_"Try this! It works like a charm"_

Seen this before? If you visit stackoverflow often, you sure have seen it thousands of times.

```
$ yum erase ...
```

I run this command by referring to one of the stackexchange's question. Of course, before remove I've think before,
but since remove a package we can install it back later easily by `yum install`, so I run the command...

The consequence is, `yum install` doesn't work... That moment I was _"SHIT!!! What's wrong with that? What I've done..."_.
I immediately awake _(before that I was a little bit sleepy)_.

If you have found any solution online, please try it on local machine before apply it on live server.

## 3. Be hyper-extra careful when dealing with delete/remove something from server

Previously, I alwasy remind myself that database on live server must be extra careful, especially executing query.

But now, I only realise that when come's to deletion _(or something cannot be undone)_ must be very very careful.
Yeah, removing a package/program might not affect the process much, and it can be install back easily.

Again, for my case it doesn't install back easily _(in fact, until now still not able to install it back)_, a lot of
funny/unexpected situation may happen. Eventhough it can install back, but do you know the original configuration?
Can you configure it back exactly same as the original?

Think about it.

## 4. Always double confirm

Confirm what? I attempt to start the **httpd** server, but it always failed, no matter how I configure.
Then I run the following command

```
$ service httpd status
Stopped
```

It shows that the server is not running. Since it does not running and yet it cannot be started. So I remove it.
I never check carefully whether there are other services using it. I should double check with the following
command

```
$ netstat -tulpn | less
```

If I check it first, I may not remove the server.

So, please check find other way(s) _(if there are)_ to double confirm the thing is not running, eventhough
one side shows that it is not using/running, but is always better to double confirm.

## 5. Never make assumption

From the message above shows that **httpd** is not running, that means it is not in use.

The above statement is my assumption. It was pretty logic, doesn't it? Just like _"If the door is locked, the light is
off, that means there nobody in the house or everybody in sleep."_, but can it be someone still watching TV with no
light?

Human light to make assumption base on their pass experience, me too. When dealing with other people server, don't
make assumption, because you're not familiar with their configuration. What you configure all the time is not what
they configure.

## 6. Take 10 - 15 minutes break when feeling frustrated

Before I get the **root** access, I already spent for quite a long time on researching how to install **httpd** without
root access.

When the time that I get the **root** access, I immediately **ssh** to server and thought that I can get
it done in 15 minutes. I was rushing _(perhaps is because of overdue)_, you know, when we're rushing, our mind
cannot focus/think, once I found the _solution_ _(so call)_ then I straight away uninstall the package.

So when you're really frustrated, don't panic, take a break. I'm sure the consequence will be far better than
you're panic.

## 7. Always discuss with superior when doing something critical on server

_"I want to be independent"_

Some time, I feel like lack of confidence to get things done. Independent could be a good training for me.

Ya, independent is good. But some time, get an opinion from other perspective may not be bad, especially
get opinion from those who're more experience than you. 

If I discuss with my superior today, this _shit_ won't happen and this post won't be existed.

Thus, I advice you that don't feel _shy_ to ask your superior.

## Conclusion

Things that already happened, could not be changed. Time wasn't reverse. Remember, _mistake is a learning
process_. Today I made this mistake, hopefully can help you to prevent making the same mistake as mine.

Please remember all the points above.
