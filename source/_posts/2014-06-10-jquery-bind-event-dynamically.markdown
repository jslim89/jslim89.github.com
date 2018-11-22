---
layout: post
title: "jQuery bind event dynamically"
date: 2014-06-10 08:55:23 +0800
comments: true
tags: 
- jquery
---

Bind event **dynamically** means to bind the event to new DOM element added from jQuery/JavaScript.

e.g.

```html
<div id="btn-wrapper">
    <button type="button" id="btn-1">Button 1</button>
    <button type="button" id="btn-2">Button 2</button>
</div>
```

```js
$(document).ready(function() {
    // only take effect on Button 1 & 2
    $('button').click(function() {
        alert($(this).text() + ' clicked');
    });

    // add 2 more buttons
    $('#btn-wrapper').append($('<button>').attr({ 'type': 'button', 'id': 'btn-3' }))
        .append($('<button>').attr({ 'type': 'button', 'id': 'btn-4' }));
});
```

For jQuery common event like `click`, we can use [.on()](http://api.jquery.com/on/) to bind event dynamically.

e.g.

```js
$(document).ready(function() {
    // only take effect on all buttons inside #btn-wrapper
    $('#btn-wrapper').on('click', 'button', function() {
        alert($(this).text() + ' clicked');
    });
    ...
});
```

### Problem

For certain cases, example like [jQuery UI sortable](http://jqueryui.com/sortable/), we cannot use `on`.

After try & error, I get a solution which is external function.

```js
$(document).ready(function() {
    function reloadSortable() {
        $('.list-container').sortable({
            connectWith: 'ul',
            receive: function(e, ui) {
                alert('item received');
            },
            remove: function(e, ui) {
                alert('item removed');
            }
        });
    }

    // add 2 more buttons
    $('#container-wrapper').append($('<ul>').attr({ 'class': 'list-container' }));
    reloadSortable(); // call this after add new element(s)
});
```
