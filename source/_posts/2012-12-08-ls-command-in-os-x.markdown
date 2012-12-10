---
layout: post
title: "ls command in OS X"
date: 2012-12-08 10:06
comments: true
categories: 
- os-x
- mac
- command-line
---

`ls` by default in OS X has no `--group-directories-first`

Open up your terminal
```sh
$ brew install coreutils
```
Now you can use `gls` instead of just `ls`
```sh
$ gls --color --group-directories-first -p
```

You can append an alias in your `~/.bash_profile`
```sh
alias ls='gls --color --group-directories-first -p'
```

_Reference: [ls --group-directories-first](https://github.com/skwp/dotfiles/issues/196)_
