---
title: 'Manipulate tmux with shell script'
date: 2024-01-12 17:02:53
tags:
- tmux
- shell
---

I used to open a few windows in terminal with tmux session,
and I did the same thing everyday when I start to to work.

Recently, I find a way to start up the tmux with standard working windows.

```sh
#!/bin/bash

# start new detached tmux session, open up vim-dadbod-ui
tmux new-session -d -s my-session-name 'vim +DBUI';


# open a new window
tmux new-window;


# split the 2nd window
tmux split-window -v; # split - 1 top & 1 bottom
tmux select-pane -U; # move the cursor to upper window
tmux send 'htop' ENTER; # run htop


# open another new window (3rd window)
tmux new-window;
tmux send 'run some command' ENTER;


# attach the session
tmux a;
```

