---
title: JavaScript class define a callback delegate method
date: 2017-03-02 15:12:55
tags:
- javascript
---

I recently working on a JavaScript project, which required to write _class_.

Usually I just use 3rd party plugin for most of my project, e.g. `jQuery.ajax`

```js
$.ajax({
    url: ...,
    success: function (data, textStatus, jqXHR) {
    }
});
```

something like that, but when I try to create a _class_ which has a method like `success` in `jQuery.ajax`, to let the user who use my library to be able to handle the callback. So here is what I found:

**FooBar.js**

Your defination goes here

```js
(function() {
    this.FooBar = function() {
        initializeEvents.call(this);
    }

    FooBar.prototype.close = function() {
        // this will invoke the method that defined in the implementation script
        this.options.close.call(this);
    }

    function initializeEvents() {
        if (this.closeButton) {
            this.closeButton.addEventListener('click', this.close.bind(this));
        }
    }
});
```

**script.js**

Your implementation goes here

```js
var foobar = new FooBar({
    close: function() {
        alert('you clicked on close button');
    }
});
```

If let say you want to pass extra params

```js
this.options.close.call(this, 'extra message');

---

close: function(msg) {
    alert('you clicked on close button with ' + msg);
}
```

References:

- [Building Your Own JavaScript Modal Plugin](https://scotch.io/tutorials/building-your-own-javascript-modal-plugin)
