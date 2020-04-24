---
title: Activate Windows 10 in VirtualBox
date: 2020-04-24 21:13:51
tags:
- virtualbox
- windows
---

I'm using Windows 10 in VirtualBox for testing purpose, where my main OS is formatted to Ubuntu.

I noticed I can activate the Windows 10 with the key sealed in hardware,
get the key with the follwing command

```
$ sudo tail -c +56 /sys/firmware/acpi/tables/MSDM
ABCDE-12345-FGHIJ-67890-KLMNO
```

![Windows 10 activated in VirtualBox](/images/posts/2020-04-24-Activate-Windows-10-in-VirtualBox/windows-10-activated.png)

## References:

- [[HowTo] Legally use Windows 10 in VirtualBox](https://forum.manjaro.org/t/howto-legally-use-windows-10-in-virtualbox/71996)
