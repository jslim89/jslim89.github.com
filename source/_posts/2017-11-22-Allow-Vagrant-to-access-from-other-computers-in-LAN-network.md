---
title: Allow Vagrant to access from other computers in LAN network
date: 2017-11-22 11:29:55
tags:
- vagrant
- homestead
- devops
---

I'm using [Laravel Homestead](https://laravel.com/docs/5.5/homestead). By default, the config is set to _private network_ with IP `192.168.10.10`.

Now, let's allow it to access from other clients.

### In the PC with Homestead installed _(e.g. 192.168.1.115)_

Edit the file **/etc/hosts**

```
192.168.10.10  my.dev.domain
127.0.0.1      my.dev.domain
```

### In the PC that want to access the Homestead web app

Edit the file **/etc/hosts**

```
192.168.1.115  my.dev.domain
```

Done. Now you can open up the browser, and key in the URL `http://my.dev.domain:8000`. _(The port number by default is 8000)_

References:

- [Accessing Homestead/Vagrant site from different device on same network](https://laracasts.com/discuss/channels/general-discussion/accessing-homesteadvagrant-site-from-different-device-on-same-network)
