---
layout: post
title: "Running multiple nodejs app in ONE server using Nginx"
date: 2014-03-14 07:47:19 +0800
comments: true
categories: 
- nodejs
- sails.js
- nginx
---

# Environment

This is running on Ubuntu 13.10

Here I would like to use [Sails.js framework](http://sailsjs.org/) _(version 0.9.13)_ to create node.js app.

## 1. Install node.js on Ubuntu

```sh
$ sudo apt-get install python-software-properties python g++ make
$ sudo add-apt-repository ppa:chris-lea/node.js
$ sudo apt-get update
$ sudo apt-get install nodejs
```

## 2. Create 2 apps

Install Sails.js globally
```sh
$ sudo npm -g install sails
```

Create projects
```sh
$ cd
$ mkdir public_html && cd public_html
$ sails new project1
$ sails new project2
```

Edit the home page for both projects _(to differentiate them)_

```html project1/views/home/index.ejs
<h1>This is project 1</h1>
```

```html project2/views/home/index.ejs
<h1>This is second project</h1>
```

## 3. Change the environment to production

```js project1/config/local.js
module.exports = {
    port: 8081, // change the port to 8081
    environment: 'production'
};
```

```js project2/config/local.js
module.exports = {
    port: 8082, // change the port to 8082
    environment: 'production'
};
```

In order to make the app run on background, we need [forever](https://github.com/nodejitsu/forever)

```sh
$ sudo npm install -g forever
```

Now start the app using **forever**

```sh
$ cd ~/public_html/project1
$ forever start app.js
warn:    --minUptime not set. Defaulting to: 1000ms
warn:    --spinSleepTime not set. Your script will exit if it does not stay up for at least 1000ms
info:    Forever processing file: app.js
```

Let's open up your browser, and type `localhost:8081`... Oops... it doesn't work like expected

Let's check the log

```sh
$ forever logs
info:    Logs for running Forever processes
data:        script logfile                    
data:    [0] app.js /home/username/.forever/1Vak.log
```

See the content of the file
```sh
$ less ~/.forever/1Vak.log

module.js:340
    throw err;
          ^
Error: Cannot find module 'sails'
    at Function.Module._resolveFilename (module.js:338:15)
    at Function.Module._load (module.js:280:25)
    at Module.require (module.js:364:17)
    at require (module.js:380:17)
    at Object.<anonymous> (/home/js/public_html/sails2/app.js:2:1)
    at Module._compile (module.js:456:26)
    at Object.Module._extensions..js (module.js:474:10)
    at Module.load (module.js:356:32)
    at Function.Module._load (module.js:312:12)
    at Function.Module.runMain (module.js:497:10)
error: Forever detected script exited with code: 8
```

Now, the error tell us that we need to install **Sails.js** locally

```sh
$ forever stopall # stop all process
$ cd ~/public_html/project1
$ sudo npm install sails # without -g option
```

Repeat the same thing on **project2**

Now open up the browser, it should show up the content.

## 4. Bind different domain name to different project

First, we need [Nginx](http://nginx.org/)

```sh
$ sudo apt-get install nginx
```

Configure Nginx to listen to port 80 and forword the request to different app based by the port number

```sh
$ cd /etc/nginx/sites-available/
$ sudo touch sails1.com.conf
```

Put the content to `sails1.com.conf`

```nginx sails1.com.conf
server {
  listen 80;

  server_name sails1.com;

  location / {
      proxy_pass http://localhost:8081;
      proxy_http_version 1.1;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection 'upgrade';
      proxy_set_header Host $host;
      proxy_cache_bypass $http_upgrade;
  }
}
```
This tell the server that forword the request from [sails1.com](http://sails1.com) to http://localhost:8081 _(which is the project1)_

Just do the same thing for **project2**

Enable the config. _(In order to make the config file take effect, symlink them to **sites-enabled** directory)_
```sh
$ sudo ln -s /etc/nginx/sites-available/sails1.com.conf /etc/nginx/sites-enabled/sails1.com.conf
$ sudo ln -s /etc/nginx/sites-available/sails2.com.conf /etc/nginx/sites-enabled/sails2.com.conf
```

Restart Nginx server
```sh
$ sudo service nginx restart
```

Since I'm testing on local machine, so we need to edit the **hosts** file, append the content below the hosts file
```nginx /etc/hosts
# nginx virtual host
127.0.0.1    sails1.com
127.0.0.1    sails2.com
```

## 5. Done
Basically what we do here is [Reverse proxy](http://en.wikipedia.org/wiki/Reverse_proxy), **Nginx** listen to the port 80 and passing the request to different app.

_References:_

* _[Getting sailsjs and ghost to play nice on DigitalOcean through nginx
](http://blog.gorelative.com/getting-sailsjs-and-ghost-to-play-nice-on-digitalocean-through-nginx/)_
* _[SETTING UP A VIRTUAL HOST IN NGINX](http://gerardmcgarry.com/2010/setting-up-a-virtual-host-in-nginx/)_
* _[forever Error - Cannot find moudule 'sails'](https://groups.google.com/forum/#!topic/sailsjs/0F-9ueNGLVM)_
