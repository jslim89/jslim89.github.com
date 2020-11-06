---
title: Thinkpad - Set battery charging threshold in Linux
date: 2020-11-06 18:00:14
tags:
- linux
- thinkpad
---

With kernel 4.17 above, we can set the threshold directly

```
$ echo 85 > /sys/class/power_supply/BAT0/charge_start_threshold
$ echo 95 > /sys/class/power_supply/BAT0/charge_stop_threshold
```

When the battery down to 85%, the charging will start
When it's over 95%, will stop

## References:

[Archlinux Wiki - tp_smapi](https://wiki.archlinux.org/index.php/tp_smapi)
