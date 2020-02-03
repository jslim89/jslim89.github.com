---
title: Install Arch Linux
date: 2020-01-22 22:49:03
tags:
- linux
- setup-configuration
- arch
---

First of all, I must say that I was referring [OSTechNix](https://www.ostechnix.com/) for the installation guide.

But when I tried to install, I faced some minor issue, that's why I write this post.
Also, I not follow everything from the guide _(but most of the things)_

## Partition the disk

Just follow [OSTechNix guide - Step 3: Partition Hard drive](https://www.ostechnix.com/install-arch-linux-latest-version/) to create partitions.

If you don't want to use double of your RAM size to create the swap, you can [refer to this](https://help.ubuntu.com/community/SwapFaq).

## Install base system

Mount the disk

```
$ mount /dev/sda1 /mnt
$ mkdir /mnt/home
$ mount /dev/sda5 /mnt/home
```

Install

```
# this step refer https://wiki.archlinux.org/index.php/Installation_guide
$ pacstrap /mnt base linux linux-firmware
$ genfstab -U /mnt >> /mnt/etc/fstab
```

Initial setup to the installed system

```
$ arch-chroot /mnt
$ pacman -S vim dhcpcd
$ vim /etc/locale.gen
$ locale-gen
$ vim /etc/locale.conf

# set your own timezone
$ ln -s /usr/share/zoneinfo/Asia/Kuala_Lumpur /etc/localtime
$ hwclock --systohc --utc
```

Set **root** password

```
$ passwd
```

### Set the hostname

```
$ vim /etc/hostname
```

Here I put my name as hostname

```
js
```

### Set the hosts file

```
$ vim /etc/hosts
```

with content

```
127.0.0.1   localhost
::1         localhost
127.0.1.1   js.local js
```

Then setup network

```
$ systemctl enable dhcpcd
```

### Install GRUB bootloader

```
$ pacman -S grub os-prober
$ grub-install /dev/sda
$ grub-mkconfig -o /boot/grub/grub.cfg
```

## Reboot

Now let's exit from the system

```
$ exit
```

Unmount the disks

```
$ umount /mnt/home
$ umount /mnt
$ reboot
```

## Post installation

After reboot, should be able to enter the system. Login as **root**

Let's update the package list

```
$ pacman -Syu
```

### Add normal user account

I create an account, `js`

```
$ useradd -m -g users -G wheel,storageower -s /bin/bash js
```

Set the password

```
$ passwd js
```

Install `sudo` package

```
$ pacman -S sudo
```

Now, before we can add **js** to `sudo`, must create a symlink for `vim` _(`vi` is not pre-installed in Arch)_

```
$ ln -s /usr/bin/vim /usr/bin/vi
```

Then edit the **sudoers** file _(/etc/sudoers)_

```
$ visudo
```

Just uncomment this line

```
%wheel ALL=(ALL) ALL
```

Now, let's try login with **js**. Exit the **root** first

```
$ exit

Login: js
Password: 
```

## Install GNOME desktop environment

Now logged in as **js**, we need root privilege to install package

```
$ sudo pacman -S xorg xorg-server
$ sudo pacman -S gnome
$ sudo systemctl start gdm.service
$ sudo systemctl enable gdm.service
```

Reboot it.

```
$ sudo reboot
```

DONE

## References:

- [How To Install Arch Linux Latest Version](https://www.ostechnix.com/install-arch-linux-latest-version/)
- [Arch Linux 2016 post installation](https://www.ostechnix.com/arch-linux-2016-post-installation/)
- [How To Install GNOME Desktop Environment In Arch Linux](https://www.ostechnix.com/how-to-install-gnome-desktop-environment-in-arch-linux/)
- [Arch Installation guide](https://wiki.archlinux.org/index.php/Installation_guide)
