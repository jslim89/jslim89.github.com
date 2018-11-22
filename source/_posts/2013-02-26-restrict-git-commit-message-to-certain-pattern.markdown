---
layout: post
title: "Restrict Git commit message to certain pattern"
date: 2013-02-26 19:21
comments: true
tags: 
- python
- git
---

For certain reason, some time you want your commit message to follow a standard pattern.

i.e. PJK-001: my message

Want to have a project code and assignment code there.

Now, open up your project git directory.

i.e. **/path/to/your/project/.git/hooks/**

and edit **commit-msg** file _(if doesn't exist, create it)_

**(NOTE: This example I'll write in Python)**

```py
#!/usr/bin/python

import sys
import re

message_file = sys.argv[1]
message = open(message_file, 'r').read()

# Match with a specified pattern
match = re.match(r'PJK-(\d+):\ ', message)

if match is None:
    print 'Your commit has been rejected!'
    print 'Commit message must begin with \'PJK-(\d+): \''
    print 'i.e. PJK-12: this is a commit message'
    sys.exit(1)
```

Simple right? But don't remember to change it to executable and no `.py` extension

```
$ chmod 755 /path/to/your/project/.git/hooks/commit-msg
```

References:

- _[Customizing Git - An Example Git-Enforced Policy](http://git-scm.com/book/en/Customizing-Git-An-Example-Git-Enforced-Policy#Client-Side-Hooks)_
- _[Alternative to the 'match = re.match(); if match: …' idiom?](http://stackoverflow.com/questions/1152385/alternative-to-the-match-re-match-if-match-idiom)_
