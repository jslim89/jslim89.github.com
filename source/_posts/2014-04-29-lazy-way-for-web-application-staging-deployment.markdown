---
layout: post
title: "Lazy way for web application staging deployment"
date: 2014-04-29 21:19:55 +0800
comments: true
categories: 
- php
- deployment
---

When comes to deployment, usually what we do is copy modified files over the server. It may takes some time on doing so.

I've discover an easy way to do deployment.

Example: I have a PHP web app hosted on [Bitbucket](https://bitbucket.org), let say [https://jslim89@bitbucket.org/myorganization/my-private-app.git](https://jslim89@bitbucket.org/myorganization/my-private-app) and this project has 3 developers involved. Now I want to deploy on staging server for client to test.

### SSH to the server

```
$ ssh root@123.123.123.123
```

### Assumed the project want to store in **/var/www/mywebapp.com**

```
$ mkdir /var/www/mywebapp.com
$ cd /var/www/mywebapp.com
$ git init
$ git remote add jslim89 https://jslim89@bitbucket.org/myorganization/my-private-app.git
```

The Git URL please **don't** use **SSH**, use **HTTPS** instead. The reason here is we don't want to put our private key on server. Besides, there are 3 developers share the same copy.

The last command here you can see I put my username, you can put whatever you want as long as you can remember. Usually what we see is `git remote add origin ....`, but in this case different developers may have different URL, e.g. [https://dev1@bitbucket.org/myorganization/my-private-app.git](https://dev1@bitbucket.org/myorganization/my-private-app.git). Thus use **username** rather than **origin**

### Update the project

```
$ git pull jslim89 master
# Then type your bitbucket account password, because we are not using private key
```

**jslim89** here refer to the URL [https://jslim89@bitbucket.org/myorganization/my-private-app.git](https://jslim89@bitbucket.org/myorganization/my-private-app.git), **master** refer to remote master branch


### For other developers

They also do the same step with you

```
$ git remote add dev1 https://dev1@bitbucket.org/myorganization/my-private-app.git
$ git pull dev1 master
# type in their own password
```

### Done :)
