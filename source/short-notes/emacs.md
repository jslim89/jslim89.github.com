---
layout: page
title: Emacs
permalink: /short-notes/emacs/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

https://www.gnu.org/software/emacs/

#### Install package

Edit the file in **~/.emacs.d/init.el**, add the following line

```el
(require-package 'evil)
(require-package 'key-chord)
```

Then run

```sh
$ emacs -nw --debug-init
```

##### Reference:

- [Melpa](http://melpa.org/#/getting-started)
