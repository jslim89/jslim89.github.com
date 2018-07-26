---
title: Python trick - copy file among computers in LAN network
date: 2018-07-27 00:07:34
tags:
- python
---

The conventional way of file transfer between PCs, is via thumb drive.

Some time the file size are too large and not able to copy to FAT thumb drive.

Now, let's play a little trick...

Open up a terminal, and `cd` to the directory that the files you want to copy over.

```sh
$ cd /path/to/file-dir
$ python -m SimpleHTTPServer
Serving HTTP on 0.0.0.0 port 8000 ...
```

The 2nd line actually serve as a HTTP server in _current directory_.

Now open up another terminal, and type

```sh
$ ifconfig
...
en1: flags=8888<UP,BROADCAST,SMART,RUNNING,SIMPLEX,MULTICAST> mtu 1500
	ether 33:33:33:44:44:44
	inet6 fe80::66:5555:cccc:d666%en1 prefixlen 64 secured scopeid 0x6
	inet 192.168.0.122 netmask 0xffffff00 broadcast 192.168.0.255
	nd6 options=201<PERFORMNUD,DAD>
	media: autoselect
	status: active
...
```

Now you found out your IP is `192.168.0.122`.

### Another PC where you need the files

Open up browser and type the URL [192.168.0.122:8000](http://192.168.0.122:8000), you will see

![List of current directory](/images/posts/2018-07-27-Python-trick-copy-file-among-computers-in-LAN-network/directory-file-list.png)

Just click on the files you need, and download it :)
