---
layout: post
title: "Git - push a specific commit to a new repo"
date: 2014-07-02 17:10:21 +0800
comments: true
categories: 
- git
---

Developer A was previously push everything to **my-repo**, eventually developer B created a new repo and clone all commits from old repo.

Developer B doesn't inform A, thus A continue to work on old repo.

Now, B request A to push the latest commit to the new repo

First of all, add the new repo URL to the project

```
$ cd /path/to/project
$ git remote add neworigin git@bitbucket.org:myname/my-new-repo.git
```

Then now A can push a specific commit to the new repo

```
$ git push neworigin 7300a6130d9447e18a931e898b64eefedea19544:master
Counting objects: 25, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (13/13), done.
Writing objects: 100% (13/13), 1.12 KiB | 0 bytes/s, done.
Total 13 (delta 7), reused 0 (delta 0)
To git@bitbucket.org:janeyee/lpt-malaysia.git
   2a94403..7300a61  7300a6130d9447e18a931e898b64eefedea19544 -> master
```

- `neworigin` refer to the new URL added just now
- `7300a6130d9447e18a931e898b64eefedea19544` is the commit hash
- `master` is branch in remote _(Bitbucket)_

_References:_

- _[git - pushing specific commit](http://stackoverflow.com/questions/3230074/git-pushing-specific-commit/3230241#3230241)_
