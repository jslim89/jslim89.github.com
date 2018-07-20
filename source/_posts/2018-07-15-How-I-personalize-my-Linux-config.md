---
title: How I personalize my Linux config
date: 2018-07-15 22:20:58
tags:
- linux
- config
---

I begin touching Linux back in 2011. I still remember, I learning vim, command line, etc.

The first thing my mentor told me to do was, clone his [dotfiles](https://www.quora.com/What-are-dotfiles).
If you first time heard about this term, I'm pretty sure that you're new to [Unix](https://en.wikipedia.org/wiki/Unix) world.

Today, I have my own config in [my GitHub repo](https://github.com/jslim89/dotfiles).

In Unix, every programs' config will be kept in [home directory](http://www.linfo.org/home_directory.html) _(~)_.
For example, for vim config will be kept in `~/.vimrc` OR `/home/username/.vimrc`.

Also, you can add [alias](http://www.linfo.org/alias.html) to shorthen your command. Just have to edit the file `~/.bashrc`, and add

```sh
alias gitdiff='git diff --color'
```

which mean, when you type in command `gitdiff`, it actually equivalent to `git diff --color`.

I add all my config to [my GitHub repo](https://github.com/jslim89/dotfiles) and managed via [homesick](https://github.com/technicalpickles/homesick).

If you're new to unix environment, you'll find it actually save you lots of time.
