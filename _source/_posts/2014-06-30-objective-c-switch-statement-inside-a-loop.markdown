---
layout: post
title: "Objective-C switch statement inside a loop"
date: 2014-06-30 20:52:22 +0800
comments: true
categories: 
- ios
---

Since the `switch` statement has `break`, and usually we use `break` to stop the looping process. In my mind `break` & `continue` are like brother, usually use inside a loop.

I wonder what if `continue` inside the `switch` statement. So I try it out

```obj-c
for (int i = 0; i < 10; i++) {
    switch (i) {
        case 3:
            NSLog(@"skip later"); continue;
            break;
        case 5:
            NSLog(@"with no skip");
            break;
        case 7:
            NSLog(@"skip later"); continue;
            break;
            
        default:
            break;
    }
    NSLog(@"this is %d", i);
}
```

and I get this output

```
this is 0
this is 1
this is 2
skip later
this is 4
with no skip
this is 5
this is 6
skip later
this is 8
this is 9
```

So you can see here, the `continue` skip the loop by 1 turn; where as `break` has totally no effect to the loop, but to `switch` only
