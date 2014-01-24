---
layout: post
title: "Generate a random string with JavaScript"
date: 2012-12-26 19:24
comments: true
categories: 
- programming
- javascript
---

In order to generate a random string, we must first specify **length of string** and **character set**

```js
function generateRandom(lengthOfString, charset) {
    // The scope of the character
    if(charset == null) charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';

    var rand = '';

    while(lengthOfString > 0) {
        /*
        since the index of char is start from 0, so no need to +1 on the random number
        we want the range from 0 - lengthOfString
        */
        rand += charset.charAt(Math.floor(Math.random() * charset.length));
        lengthOfString--;
    }
    return rand;
}
```

_References:_

* _[W3 School - JavaScript random() Method](http://www.w3schools.com/jsref/jsref_random.asp)_
* _[W3 School - JavaScript charAt() Method](http://www.w3schools.com/jsref/jsref_charat.asp)_
