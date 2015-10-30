---
layout: post
title: "Crawling images from a webpage"
date: 2013-04-08 15:26
comments: true
categories: 
- shell-script
- python
---

First extract the url of images  
**craw.py**

```py
import urllib2, re
req = urllib2.Request('http://yourwebsite.com/path/to/webpage')
website = urllib2.urlopen(req)

html = website.read()

# Read all png files
imgs = re.findall('"((http)s?://.*?.png)"', text)
for i in imgs:
    print i[0]
```

Then output the URLs to a text file
```
$ python craw.py > content.txt
```

Use shell script to download it  
**craw.sh**

```
#!/bin/bash
file="./content.txt"
while IFS= read -r line
do
    wget "$line"
done <"$file"
```

You're done!

_References:_

* _[UNIX: Read a File Line By Line](http://www.cyberciti.biz/faq/unix-howto-read-line-by-line-from-file/)_
* _[Extract Hyperlinks Using Python and PHP](http://kianmeng.org/blog/2013/03/11/extract-hyperlinks-using-python-and-php/)_
