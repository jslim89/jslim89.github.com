---
layout: page
title: Docker
permalink: /short-notes/docker/
date: 2020-07-20 16:13:51
comments: false
sharing: true
footer: true
---

https://www.docker.com/

#### Clear docker log

```
$ sudo sh -c "truncate -s 0 $(docker inspect --format='{{.LogPath}}' <container_name_or_id>)"
```

##### Reference:

- [How to clear the logs properly for a Docker container?](https://stackoverflow.com/questions/42510002/how-to-clear-the-logs-properly-for-a-docker-container/42510314#42510314)
