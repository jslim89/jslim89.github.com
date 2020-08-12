---
layout: post
title: "Delete Linux partition from dual-boot (Windows 7 &amp; Linux)"
date: 2012-11-30 20:35
comments: true
tags: 
- linux
- setup-configuration
- windows
---

First time I did this by just delete the partition via [Disk Management](http://www.softwareok.com/?seite=faq-Win-7&faq=15) on Windows 7. What is the consequence?

It turn up totally cannot boot into any OSes. That is be cause the GRUB bootloader is removed together.  
This take me half day to figure it out, and struggling me.

Here is the step on how to safely remove the Linux.

### Using Windows 7 boot disc
1. Select a language, a time, a currency, a keyboard or an input method, and then click Next.
2. Choose **Repair your computer**.
3. Choose the option that you want to repair.
4. You'll see some thing like this, then select **Command Prompt**.

![Windows 7 Repair](/images/posts/2012-11-30-delete-linux-partition-from-dual-boot-windows-7-and-linux/windows_7_repair.jpg)

### Type some command in cmd
```
bootrec.exe /fixmbr
bootrec.exe /fixboot
```
Then reboot your machine. It should auto-boot into Windows 7 without letting you to select OSes.

### Delete and extend partition
Now click on start menu and search for **Computer Management**

![Computer Management](/images/posts/2012-11-30-delete-linux-partition-from-dual-boot-windows-7-and-linux/computer_management.png)

Click on **Disk Management**

![Disk Management](/images/posts/2012-11-30-delete-linux-partition-from-dual-boot-windows-7-and-linux/disk_management.png)

Right-click the partition that you want to delete _(i.e. Linux partition in this case)_. Then select **Delete Volume...**

![Delete Volume](/images/posts/2012-11-30-delete-linux-partition-from-dual-boot-windows-7-and-linux/delete_volume.png)

You'll get a **Free Space**. Now right-click the **Free Space** and **Delete Partition...**

![Delete Partition](/images/posts/2012-11-30-delete-linux-partition-from-dual-boot-windows-7-and-linux/delete_partition.png)

Now you get an **Unallocated** space. Right-click the drive before the **Unallocated** space _(i.e. D-drive in this case)_. Select **Extend Volume...**

![Extend Volume](/images/posts/2012-11-30-delete-linux-partition-from-dual-boot-windows-7-and-linux/extend_volume.png)

Finally, your drive has more space ^^

![Extended Successful](/images/posts/2012-11-30-delete-linux-partition-from-dual-boot-windows-7-and-linux/completed.png)
