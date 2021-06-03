---
title: Shell script - remove lines contain some words
date: 2021-06-03 19:29:55
tags:
- shell
- bash
- linux
---

A sample file **demo.txt** contains the following content

```
1 Linux Operating System
2 Unix Operating System
3 RHEL
4 Red Hat
5 Fedora
6 Arch Linux
7 CentOS
8 Debian
9 Ubuntu
10 openSUSE
```

## How do we delete lines that contain `operating`?

#### 1. Find the line number

```
$ grep -in "operating" demo.txt
1:1 Linux Operating System
2:2 Unix Operating System
```

#### 2. Then use `awk` to get the first chunk

```
$ grep -in "operating" demo.txt | awk '{print $1}'
1:1
2:2
```

#### 3. Get only the line number

This will split the string by delimiter `:`, and get the first chunk

```
$ grep -in "operating" demo.txt | awk '{print $1}' | cut -d ':' -f 1
1
2
```

#### 4. Delete by using `sed` command

The `sed` command delete by line number like this

```
$ sed '23d;45d;102d' filename.ext
```

Before that we already can get line number, so now we have to join it into a single line

```
$ grep -in "operating" demo.txt | awk '{print $1}' | cut -d ':' -f 1 | xargs -I'{}' echo '{}d' | paste -d';' -s
1d;2d
```

- with `xargs`, it append a character "d"
- with `paste`, 2 lines joined into a single line

At the end, we can

```
$ grep -in "operating" demo.txt | awk '{print $1}' | cut -d ':' -f 1 | xargs -I'{}' echo '{}d' | paste -d';' -s | xargs -I'{}' sed '{}' demo.txt
3 RHEL
4 Red Hat
5 Fedora
6 Arch Linux
7 CentOS
8 Debian
9 Ubuntu
10 openSUSE
```

It will output to stdout, if you want to replace the file, use `-i` option

```
$ grep -in "operating" demo.txt | awk '{print $1}' | cut -d ':' -f 1 | xargs -I'{}' echo '{}d' | paste -d';' -s | xargs -I'{}' sed -i '{}' demo.txt
```

### Make it into a shell script function

```sh
delete_lines_contain_text () {
    txt=$1
    file=$2
    grep -in $txt $file | awk '{print $1}' | cut -d ':' -f 1 | xargs -I'{}' echo '{}d' | paste -d';' -s | xargs -I'{}' sed -i '{}' $file
}

# usage
delete_lines_contain_text "operating" demo.txt
```

## References:

- [How to delete lines from a file using sed command](https://www.2daygeek.com/linux-remove-delete-lines-in-file-sed-command/)
