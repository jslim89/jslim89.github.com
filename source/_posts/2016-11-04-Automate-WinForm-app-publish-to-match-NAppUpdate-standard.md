---
title: Automate WinForm app publish to match NAppUpdate standard
date: 2016-11-04 12:00:45
tags:
- c#
- winform
- nappupdate
---

If you're using [NAppUpdate](https://github.com/synhershko/NAppUpdate) framework to publish your WinForm app, this post is for you.

Assume that you already setup NAppUpdate successfully. Now we want to simplify it to just 1 click publish, and the others clients can just update it

## Server side

In your server, you need a path to store the xml file. For my case, I will create a subdomain for it. E.g.

[pub.yoursite.com](pub.yoursite.com)

```
cd /var/www/pub.yoursite.com
```

Assume your site hosted in this path

```
.
├── releases
├── index.php
└── release.xml
```

The **index.php** you can just simply put some dummy text into it, because the most important file is **release.xml**

The content of **release.xml** is auto generated. I've created the [php file](/attachments/posts/2016-11-04-Automate-WinForm-app-publish-to-match-NAppUpdate-standard/publish.php), download it to your server, and place it anywhere, _(e.g. /path/to/publish.php)_

Then create a cronjob for it

```
* * * * * /usr/bin/php /path/to/publish.php > /dev/null 2>&1
```

This cronjob will run every minute, so once you have new file published to server, it will auto generate the **release.xml**

## WinForm _(Visual Studio)_

First you need to build the project

![Build the project](/images/posts/2016-11-04-Automate-WinForm-app-publish-to-match-NAppUpdate-standard/build.png)

Then go to property there

![Project property](/images/posts/2016-11-04-Automate-WinForm-app-publish-to-match-NAppUpdate-standard/property.png)

And the go to **Publish** tab

![Publish settings](/images/posts/2016-11-04-Automate-WinForm-app-publish-to-match-NAppUpdate-standard/publish.png)

Set your FTP details, then publish it


## Server side

Edit the file **/path/to/publish.php**, change the constants to match your own

- **PUBLISH_INPUT_ROOT**: `/home/ftpuser/path/to/releases/Application\ Files`
- **PUBLISH_OUTPUT_ROOT**: `/var/www/pub.yoursite.com/releases`
- **PUBLISH_XML_FEED**: `/var/www/pub.yoursite.com/release.xml`
- **WINFORM_PREFIX**: `YourProject_`
- **BASE_URL**: `http://pub.yoursite.com`


Now once your app published, the cronjob will execute this php script, and the script will generate **release.xml**,
and copy the necessary files to **releases** folder.

```
.
├── releases
│   ├── v1.0.0.1
│   └── v1.0.0.2
├── index.php
└── release.xml
```

Inside the **releases** folder will then have all version of what you have published.
