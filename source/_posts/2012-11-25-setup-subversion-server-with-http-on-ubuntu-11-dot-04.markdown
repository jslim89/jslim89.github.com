---
layout: post
title: "Setup Subversion server with HTTP on Ubuntu 11.04"
date: 2012-11-25 19:43
comments: true
tags:
- linux
- setup-configuration
- subversion
---

### Install [Apache](http://httpd.apache.org/) server and [subversion](http://subversion.apache.org/)

```
$ sudo apt-get install apache2 subversion libapache2-svn
```

### Create repository to host your projects

```
$ mkdir /path/to/your/repos
```

### Create a project

```
$ svnadmin create project_name
```

### Edit the configuration file

```
$ sudo vi /etc/apache2/mods-enabled/dav_svn.conf
```

Uncomment the following lines

```
<Location /svn>
    DAV svn
    SVNParentPath /path/to/your/repos
    AuthType Basic
    AuthName "Subversion Repository"
    AuthUserFile /etc/apache2/dav_svn.passwd
    <LimitExcept GET PROPFIND OPTIONS REPORT>
        Require valid-user
    </LimitExcept>
</Location>
```

### Create a user

```
$ sudo htpasswd -cm /etc/apache2/dav_svn.passwd your_username
```

**NOTE:  
`-c` option should only be used on the first time when you create a user as the user doesn't exists.  
`-m` option is to specify the MD5 encryption on the password.**

### Restart apache server

```
$ sudo /etc/init.d/apache2 restart
```

### Set user type/group for authorization _(Optional)_

```
$ sudo vi /etc/apache2/mods-enabled/dav_svn.conf
```

Uncomment the following lines
```
AuthzSVNAccessFile /etc/apache2/dav_svn.authz
```

Create a new file

```
$ sudo vi /etc/apache2/dav_svn.authz
```

Add few user groups and assign them to a project

```
# Create user group
[group]
group1 = user1, user2, user3
group2 = user4, user5, user6

# Assign user to a project
[proj_name:/path/to/proj]
@group1 = rw # Have read-write access
@group2 = r  # Only have read access, cannot commit the changes
user7 = rw   # A standalone user who doesn't belongs to any group
```

**NOTE: alias (@) is used to indicate it is a group, the alias must put as a prefix**

### Testing

```
$ svn co http://localhost/svn/project_name project_name
```

**Caution: For create user group and assign to specific project, it doesn't work correctly, I still discovering. If you know what I'm wrong, please email to [jslim89@gmail.com](mailto:jslim89@gmail.com)**
