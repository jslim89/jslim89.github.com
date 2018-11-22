---
layout: post
title: "Enable ssh login without password"
date: 2016-06-29 16:52:25 +0800
comments: true
tags: 
- linux
- ssh
---

### In client PC

Generate ssh key if you don't have one

```
$ ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
```

Then copy _(to clipboard)_ the public key _(with the extension `.pub`)_, e.g.

```
$ pbcopy < ~/.ssh/id_rsa.pub
```

### In server

Add the public key to **~/.ssh/authorized_keys**

```
$ vim ~/.ssh/authorized_keys
```

Then paste the public key to next line of this file, save it

### Test it

```
$ ssh -i ~/.ssh/id_rsa user@xxx.xxx.xxx.xxx
```

You should be able to login without prompt you to input password

References:

- [Generating a new SSH key and adding it to the ssh-agent](https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/)
