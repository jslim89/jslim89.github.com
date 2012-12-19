---
layout: post
title: "Crawling sequantial images with shell script"
date: 2012-12-19 22:43
comments: true
categories: 
- linux
- shell-script
---

Let say you want to download from **http://www.domain.com/images/**, and you know that the images name is from **001** to **100**

Lets write a shell script
```sh
#!/bin/bash
max=100
for i in `seq 1 $max`
do
    url=`printf "http://www.domain.com/images/%03d.jpg" $i`
    wget $url
done
```

1. First declare a maximum number for the images' name
2. Write a `for` loop to iterate through from 1 _(as the images' name is start from 1)_to the maximum
3. The command inside _backquote_ (\`) basically is refer to sub-command which will pass the output to the `url`. In this case is using `printf` to format the image name with trailing **00** in front if it then store in `url` variable
4. Finally use `wget` to download the images
