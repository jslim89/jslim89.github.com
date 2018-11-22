---
layout: post
title: "AWS extend disk volume on an EC2 instance"
date: 2016-06-15 15:25:36 +0800
comments: true
tags: 
- aws
---

There may have certain situation that we need to extend disk volume on an instance.

## Create a new volumn on AWS

Visit **EC2** service, and click on the **Volumes** on the left menu. Then create volume

![EC2 instance list](/images/posts/2016-06-15-aws-extend-disk-volume-on-an-ec2-instance/create-volume.png)

- **Volume Type:** for normal case, just select `General Purpose SSD`, unless you have a high-write usage, then you can choose `Provisioned IOPS SSD`
- **Size (GiB):** Just put the size you need
- **Availability Zone:** Make sure you select the zone that same as the instance you want to extend
- **Snapshot ID:** Leave it
- **Encryption:** Leave it

Then create

![EC2 instance list](/images/posts/2016-06-15-aws-extend-disk-volume-on-an-ec2-instance/attach-volume.png)

Now you can see that the **Status** is `available`. Right-click the row, and click on **Attach Volume**

![EC2 instance list](/images/posts/2016-06-15-aws-extend-disk-volume-on-an-ec2-instance/attach-volume-select-instance.png)

Select the instance you want to extend

![EC2 instance list](/images/posts/2016-06-15-aws-extend-disk-volume-on-an-ec2-instance/attach-volume-select-instance-2.png)

This one just leave it, and **Attach** it.

## Server config

Now ssh into the instance

```
ubuntu@ip-xxx-31-xx-xx:~$ df -h

Filesystem      Size  Used Avail Use% Mounted on
udev            492M   12K  492M   1% /dev
tmpfs           100M  344K   99M   1% /run
/dev/xvda1      7.8G  1.6G  5.8G  22% /
none            4.0K     0  4.0K   0% /sys/fs/cgroup
none            5.0M     0  5.0M   0% /run/lock
none            497M     0  497M   0% /run/shm
none            100M     0  100M   0% /run/user
```

The volume not mounted yet, so cannot see here

```
ubuntu@ip-xxx-31-xx-xx:~$ sudo fdisk -l

Disk /dev/xvda: 8589 MB, 8589934592 bytes
255 heads, 63 sectors/track, 1044 cylinders, total 16777216 sectors
Units = sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes
Disk identifier: 0x00000000

    Device Boot      Start         End      Blocks   Id  System
/dev/xvda1   *       16065    16771859     8377897+  83  Linux

Disk /dev/xvdf: 21.5 GB, 21474836480 bytes          <----------------- NEW DISK
255 heads, 63 sectors/track, 2610 cylinders, total 41943040 sectors
Units = sectors of 1 * 512 = 512 bytes
Sector size (logical/physical): 512 bytes / 512 bytes
I/O size (minimum/optimal): 512 bytes / 512 bytes
Disk identifier: 0x00000000

Disk /dev/xvdf doesn't contain a valid partition table
```

Now need to format it to **[ext4](https://en.wikipedia.org/wiki/Ext4)** filesystem

```
ubuntu@ip-xxx-31-xx-xx:~$ sudo mkfs -t ext4 /dev/xvdf
mke2fs 1.42.9 (4-Feb-2014)
Filesystem label=
OS type: Linux
Block size=4096 (log=2)
Fragment size=4096 (log=2)
Stride=0 blocks, Stripe width=0 blocks
1310720 inodes, 5242880 blocks
262144 blocks (5.00%) reserved for the super user
First data block=0
Maximum filesystem blocks=4294967296
160 block groups
32768 blocks per group, 32768 fragments per group
8192 inodes per group
Superblock backups stored on blocks: 
    32768, 98304, 163840, 229376, 294912, 819200, 884736, 1605632, 2654208, 
    4096000

Allocating group tables: done                            
Writing inode tables: done                            
Creating journal (32768 blocks): done
Writing superblocks and filesystem accounting information: done   
```

Last is to mount it to **/storage** _(it can be any path you like)_

Edit the file **/etc/fstab** _(you may use **nano** if you're not familiar with **vim**)_

Remember to backup before edit the file

```
ubuntu@ip-xxx-31-xx-xx:$ sudo cp /etc/fstab /etc/fstab.orig
ubuntu@ip-xxx-31-xx-xx:$ sudo vim /etc/fstab
```

Update the content

```
LABEL=cloudimg-rootfs   /         ext4   defaults,discard        0 0   <------ THIS IS THE DEFAULT LINE
/dev/xvdf               /storage  ext4   defaults                0 0
```

Now create a folder for that new disk volume

```
ubuntu@ip-xxx-31-xx-xx:$ sudo mkdir /storage
```

Then mount all

```
ubuntu@ip-xxx-31-xx-xx:$ sudo mount -a
```

Double check it

```
ubuntu@ip-xxx-31-xx-xx:$ df -h
Filesystem      Size  Used Avail Use% Mounted on
udev            492M   12K  492M   1% /dev
tmpfs           100M  344K   99M   1% /run
/dev/xvda1      7.8G  1.6G  5.8G  22% /
none            4.0K     0  4.0K   0% /sys/fs/cgroup
none            5.0M     0  5.0M   0% /run/lock
none            497M     0  497M   0% /run/shm
none            100M     0  100M   0% /run/user
/dev/xvdf        20G   44M   19G   1% /storage   <------- THIS IS NEW
```
