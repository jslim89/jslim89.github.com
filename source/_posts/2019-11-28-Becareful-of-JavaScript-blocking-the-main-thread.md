---
title: Becareful of JavaScript blocking the main thread
date: 2019-11-28 21:16:28
tags:
- javascript
- thread
- mistake
---

I've just made a mistake on production site, the client's website.

I was working on a tracking script, which let client to include into their site.

I added a `for` loop, to update all elements _(string)_ with
[`replace`](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/replace)
method. Something like this:

```js
var processed = arr.map(function (item) {
    let charToRemove = ['a', 'b', 'c', 'etc'];
    for (var i in charToRemove) {
        item = item.replace(charToRemove[i], '');
    }
    return item.trim();
});
```

Outside of this function, is a recursive function.

Of course, I created an empty blog _(Wordpress)_ with fake content, and the script work just nice. Tested many times.

## In production

When the client embed the script into their site _(is a forum)_, after a while,
their user open a post to complain about this, making their browser & PC hang.

Then why? In my blog has no issue, but their forum got?

I found out, their forum has many tracking script, GTM, Facebook, and a lot more,
I think probably too many things to load at once, and my script is also heavy and blocking the browser main thread.

I do a test after fixing it _(by removing the `arr.map()`)_.

```js
let begin = Date.now();
recursiveFunc();
let timespan = Date.now() - begin;
console.log('total ' + timespan + 'ms');
```

Without this, it took less than 100ms, with this, took about 30 sec.

So, becareful when there are blocking loops, and try to simulate as close to production environment as possible.

Sometime shit just happen in production, but never happen in staging.
