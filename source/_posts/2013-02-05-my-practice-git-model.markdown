---
layout: post
title: "My practice Git Model"
date: 2013-02-05 15:57
comments: true
tags: 
- git
---

Thanks to [this](http://nvie.com/posts/a-successful-git-branching-model/) post. It get me more understand in Git.

Let's have a try.

### 1. Create a new repo on [Github](https://github.com/)

![Create Repo](http://jslim89.github.com/images/posts/2013-02-05-my-practice-git-model/create-repo.png)

Your first commit to **master** branch

```
$ git clone <your-project>
$ touch README.md
$ git add README.md
$ git commit -a -m "- initial commit"
$ git push origin master
```

### 2. Create a `develop` branch

```
# Branch out `develop` from `master`
$ git checkout -b develop master

# make some changes
$ echo "This is branched out from master" >> README.md
$ git commit -a -m "- initialize develop branch"

# Push to remote server and create a new branch
$ git push origin develop
```

Now you have 2 branches in your Github

![Show 2 branches](http://jslim89.github.com/images/posts/2013-02-05-my-practice-git-model/2-branches-in-github.png)

### 3. Now add a new module

```
# Branch out `module1` from `develop`
$ git checkout -b module1 develop

# Make some changes
$ mkdir new_module
$ cd new_module
$ touch file1 file2 file3
$ cd ..

# Add & Commit
$ git add new_module
$ git commit -a -m "- added new module"

# Show all branches you have
$ git branch

# Switch to `develop`
$ git checkout develop

# Merge with branch `module1`
$ git merge --no-ff module1

# Delete `module1` branch
$ git branch -d module1

# push to remote server
$ git push origin develop
```

### 4. Everything tested fine, merge to `master` branch

```
# Switch to `master`
$ git checkout master

# Merge with branch `develop`
$ git merge --no-ff develop

# Push to remote server
$ git push origin master
```

If you work on only a single branch, consider a situation here:

Now you have assigned a new module, you work on the new module. Suddenly, the life copy got some bugs that need you to be fix urgently, but now you're working on the new module which is not yet complete.

Imagine the situation above happened, it may take you a lot of time to handle this. Thus use the branching model to avoid this situation happen.

_Reference: [A successful Git branching model](http://nvie.com/posts/a-successful-git-branching-model/)_
