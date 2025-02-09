---
layout: page
title: MacOS
permalink: /short-notes/mac/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

#### Enable write support for NTFS on OS X Mavericks

Retrieve the UUID of the drive you want to write on

```sh
$ diskutil info /Volumes/YourDriveName | grep UUID
   Volume UUID:              22F1DF1C-AFDE-B507-A15B-FC9296BB2E2B
```

Edit the file **/etc/fstab**

```sh
$ sudo vi /etc/fstab
```

Add the following content

```
UUID=22F1DF1C-AFDE-B507-A15B-FC9296BB2E2B none ntfs rw,auto,nobrowse
```

Unmount the drive, and then remount. It should now writable.

**Important NOTE**
```
It is also important that the HD has been safely removed, since NTFS contains a flag to notice if the disk was safely removed or not, not allowing to mount it in write mode with the native OSX driver (something similar happens under Linux). In case it happens you just need to plug it into a windows PC and safely remove the HD (so it cleans that flag).
```

[Script download](/attachments/short-notes/mac/ntfs.sh)

By [neburim](https://discussions.apple.com/people/neburim)

##### Reference:

- [NTFS Write support on Mavericks](https://discussions.apple.com/message/23816923#23816923)

---

#### Enable write support for NTFS on macOS Sierra

Find your disk name

```
$ ls -l /Volumes/
drwxr-xr-x 1 jslim staff 4096 Nov  7 12:33 'New Volume'/
lrwxr-xr-x 1 root  wheel    1 Nov  7 10:05 'Macintosh HD' -> /
```

So in this case my external hard disk name is **New Volume**

Edit/Create the file **/etc/fstab**

```
$ sudo vim /etc/fstab
```

Add the following content

```
LABEL=New\040Volume none ntfs rw,auto,nobrowse
```

`\040` is represent a _<space>_ character.

Now unmount it, and plug it back. It will appear in **/Volume** directory

##### Reference:

- [How to manually active NTFS writing in macOS Sierra](https://www.osxio.com/manually-active-ntfs-writing-macos-sierra/)

---

#### Missing Air Drop in Mountain Lion

Open up terminal

```sh
$ defaults write com.apple.NetworkBrowser BrowseAllInterfaces 1
# restart finder
$ killall Finder
```

##### Reference:

- [Air Drop missing](https://discussions.apple.com/thread/4300050#19735820)

---

#### Delete provisioning profile on XCode 5

Open up terminal

```sh
$ cd ~/Library/MobileDevice/Provisioning\ Profiles
$ ls -la
total 120K
-rw-r--r-- 1 user group  13K Oct 22 13:37 13C42BED-0555-4684-A905-5547B2293D46.mobileprovision
-rw-r--r-- 1 user group 7.7K Oct 19 12:53 3CA5CC4A-E995-41D6-BA54-66A3AFB4061F.mobileprovision
-rw-r--r-- 1 user group 7.4K Sep 11 10:57 F4BCC928-32B7-42F8-85EB-EA47ADE86C14.mobileprovision
```

See which one you want to delete (when you added the profile)

```sh
$ rm 13C42BED-0555-4684-A905-5547B2293D46.mobileprovision
```

##### Reference:

- [Delete provisioning profile from Xcode 5](http://stackoverflow.com/questions/18923095/delete-provisioning-profile-from-xcode-5/18923552#18923552)

---

#### Install `command line tools` in Xcode 5 & Mavericks

```sh
$ xcode-select --install
```

##### Reference:

- [Xcode 5.0 Error installing command line tools](http://stackoverflow.com/questions/19066647/xcode-5-0-error-installing-command-line-tools/19067279#19067279)

---

#### Configure virtual host in XAMPP

```apache
<VirtualHost web-local.mysite.com:80> // use <ServerName>:80 instead of *:80
    ServerAdmin webmaster@dummy-host.example.com
    ServerName web-local.mysite.com
    ServerAlias web-local.mysite.com
    DocumentRoot "/Users/username/public_html/mysite"
    <Directory "/Users/username/public_html/mysite/">
        DirectoryIndex index.php
        Options All
        AllowOverride All
        Order allow,deny
        allow from all
        Require all granted // this is required
    </Directory>
    ErrorLog "logs/web-local.mysite.com-error_log"
    CustomLog "logs/web-local.mysite.com-access_log" common
</VirtualHost>
```

##### Reference:

- [Adding VirtualHost fails: Access Forbidden Error 403 (XAMPP) (Windows 7)](http://stackoverflow.com/questions/9110179/adding-virtualhost-fails-access-forbidden-error-403-xampp-windows-7/9117898#9117898)

---

#### `grep` with color

Add the following content to `~/.profile`

```sh
export GREP_OPTIONS='--color=always'
export GREP_COLOR='1;37;41' # you can set the color here
```

##### Reference:

- [How can I grep with color in Mac OS X's terminal?](https://superuser.com/questions/416835/how-can-i-grep-with-color-in-mac-os-xs-terminal/417152#417152)

---

#### Terminal history not keeping

This is because of permission issue

```sh
$ ls -l ~/.bash_history 
-rw------- 1 root group 164 Apr 15 11:00 /Users/username/.bash_history
```

Simply change the owner

```sh
$ sudo chown username ~/.bash_history
$ ls -l ~/.bash_history 
-rw------- 1 username group 164 Apr 15 11:00 /Users/username/.bash_history
```

##### Reference:

- [How do I get Terminal to remember previous commands after closing window in SL 10.6.8?](http://apple.stackexchange.com/questions/22385/how-do-i-get-terminal-to-remember-previous-commands-after-closing-window-in-sl-1/74405#74405)

---

#### replace pattern with sed

I have already mention this in [Linux section](https://github.com/jslim89/js-learning-journey/tree/master/linux#replace-pattern-with-sed), but there is a small difference here. The command below will produce error

```sh
$ sed -i "s/reload('#\(\w\+\)#&#\(\w\+\)#')/href='#\1#\&#\2#';/" /path/to/file
```

Output
```
sed: 1: "AbcFile.c": invalid command code A
```

Here you have to add `-e` option for OS X, e.g.

```sh
$ sed -ie "s/reload('#\(\w\+\)#&#\(\w\+\)#')/href='#\1#\&#\2#';/" /path/to/file
```

**NOTE: You can either use `-ie` or `-i -e`**

##### Reference:

- [invalid command code ., despite escaping periods, using sed](http://stackoverflow.com/questions/19456518/invalid-command-code-despite-escaping-periods-using-sed/19457213#19457213)

---

#### Batch resize image

```sh
$ sips -Z 1024 *.png
```

`1024` is refer to **width**, the height will adjusted according to ratio

##### Reference:

- [Batch resize images in Preview](http://hints.macworld.com/article.php?story=200911231158240)

---

#### Compress and split files

```sh
$ tar -zcvf file.tar.gz file/*
$ split -b 3500m file.tar.gz file.tar.gz.
$ ls -l
-rw-r--r-- 1 js staff 6.5G Aug 10 21:20 file.tar.gz
-rw-r--r-- 1 js staff 3.5G Aug 10 21:25 file.tar.gz.aa
-rw-r--r-- 1 js staff 3.1G Aug 10 21:29 file.tar.gz.ab
```

##### Reference:

- [Create multi volume archive on a Mac](https://superuser.com/questions/173782/create-multi-volume-archive-on-a-mac/173790#173790)

---

#### Search and replace multiple files

```sh
$ grep -R -l "special keyword" * | xargs sed -i "" 's/special keyword/new keyword/'
```

* `grep -R -l "special keyword" *` - retrive all files that contains _"special keyword"_
* `sed -i "" 's/special keyword/new keyword/'` - replace _"special keyword"_ with _"new keyword"_

##### Reference:

- [sed with -i switch not working in OSX Leopard](http://hintsforums.macworld.com/showthread.php?t=95246)

---

#### Mission control not working

Restart it

```sh
$ osascript -e 'quit application "Dock"'
```

##### Reference:

- [10.7: Restart Mission Control](http://hints.macworld.com/article.php?story=20110802073945173)

---

#### Convert heic to jpg

```
$ brew install imagemagick
$ magick mogrify -monitor -format jpg *.HEIC
```

##### Reference:

- [How to convert a HEIF/HEIC image to JPEG in El Capitan?](https://apple.stackexchange.com/questions/297134/how-to-convert-a-heif-heic-image-to-jpeg-in-el-capitan/347507#347507)

---

#### Create Catalina iso file

```
$ hdiutil create -o /tmp/Catalina -size 8500m -volname Catalina -layout SPUD -fs HFS+J
$ hdiutil attach /tmp/Catalina.dmg -noverify -mountpoint /Volumes/Catalina
$ diskutil eraseDisk JHFS+ Catalina disk3
$ sudo /Applications/Install\ macOS\ Catalina.app/Contents/Resources/createinstallmedia --volume /Volumes/Catalina –-nointeraction
$ hdiutil detach /volumes/Install\ macOS\ Catalina
$ hdiutil convert /tmp/Catalina.dmg -format UDTO -o ~/Desktop/Catalina.cdr
$ mv ~/Desktop/Catalina.cdr ~/Desktop/Catalina.iso
```

Download [create-catalina-iso.sh](/attachments/short-notes/mac/create-catalina-iso.sh)

##### Reference:

- [agentsim/highsierra bootable.sh](https://gist.github.com/agentsim/00cc38c693e7d0e1b36a2080870d955b#gistcomment-3113518)

---

#### PhpStorm (ideavim) / VScode / Cursor cursor not moving when holding on `j` `k` `h` `l`

1. Find the app's unique id
   ```sh
   osascript -e 'id of app "Cursor"'
   ```
2. Then
   ```
   defaults write com.todesktop.230313mzl4w4u92 ApplePressAndHoldEnabled -bool false
   defaults write com.jetbrains.PhpStorm ApplePressAndHoldEnabled -bool false
   defaults write com.microsoft.VSCode ApplePressAndHoldEnabled -bool false
   ```
3. Restart the application

##### Reference:

- [IdeaVim j and k behavior on OS X](https://intellij-support.jetbrains.com/hc/en-us/community/posts/206750615-IdeaVim-j-and-k-behavior-on-OS-X)
- [Setting up Vim press and hold on cursor](https://github.com/getcursor/cursor/issues/801#issuecomment-1712798006)

