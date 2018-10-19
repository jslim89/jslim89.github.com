---
title: TinyMCE insert js/css into iFrame head element
date: 2018-10-17 22:47:56
tags:
- js
- tinymce
---

TinyMCE is an iFrame element with full HTML content.

Sometime we would like to add in Bootstrap & jQuery or any plugins into it.
Let's see how we achieve it.

```js
tinymce.init({
    selector: '.tinymce',
});
```

Above is the basic setup. To add in Bootstrap stylesheet, just add in `content_css` option

```js
content_css: [
    'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css',
    '/css/custom-mce.css', // if you want to add in custom styles
],
```

What about JavaScript? We can make use of `init_instance_callback` option

```js
init_instance_callback: function(editor) {
    // get the head element
    let head = editor.dom.select('head')[0];

    // just add in whatever plugins needed
    addScript('https://code.jquery.com/jquery-3.3.1.min.js');

    // this is needed, without the delay, it will show jQuery is not defined
    setTimeout(function () {
        addScript('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js');
        addScript('/path/to/your/script.js');
    }, 500);

    // helper function
    function addScript (scriptUrl) {
        editor.dom.add(
            head,
            'script',
            {
                src: scriptUrl,
                type: 'text/javascript'
            }
        );
    }
}
```

References:

- [TinyMCE - Content Appearance](https://www.tiny.cloud/docs/configure/content-appearance/#content_css)
- [Access head tag and add html](http://archive.tinymce.com/forum/viewtopic.php?pid=115216#p115216)
