---
layout: post
title: "Vim - Set indentation based on file type"
date: 2013-02-05 17:13
comments: true
tags: 
- vim
---

Let say now you want your **.js** _(JavaScript)_ file to 2 space indent.

Create a file named `javascript.vim` **(NOT `js.vim`)**

```
$ touch ~/.vim/ftplugin/javascript.vim
```

Add the content below to `~/.vim/ftplugin/javascript.vim`

```vim
" Auto expand tabs to spaces (use space rather than tab)
setlocal expandtab
setlocal shiftwidth=2
setlocal tabstop=2

" Auto indent after a {
setlocal autoindent
setlocal smartindent
```

Use `setlocal` rather than `set` because you only want it to apply to **.js** files.

_Reference: [Changing Vim indentation behavior by file type](http://stackoverflow.com/questions/158968/changing-vim-indentation-behavior-by-file-type#answers)_
