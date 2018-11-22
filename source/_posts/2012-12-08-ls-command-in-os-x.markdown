---
layout: post
title: "ls command in OS X"
date: 2012-12-08 10:06
comments: true
tags: 
- os-x
- mac
- command-line
---

`ls` by default in OS X has no `--group-directories-first`

Open up your terminal

```
$ brew install coreutils
```

Now you can use `gls` instead of just `ls`

```
$ gls --color --group-directories-first -p
```

You can append an alias in your `~/.bash_profile`

```
alias ls='gls --color --group-directories-first -p'
```

_Reference: [ls --group-directories-first](https://github.com/skwp/dotfiles/issues/196)_
