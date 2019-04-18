---
title: window.onbeforeunload trigger ajax request
date: 2019-04-17 18:28:42
tags:
- javascript
---

Initially I was using jQuery.ajax call to send a request to server before user
leave the page.

```js
window.onbeforeunload = function(evt) {
    const endpoint = '/lock/end-timestamp.php';
    $.ajax({
        url: endpoint,
        dataType: 'json',
        type: 'post',
        async: false, // synchronous request
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            param1: 'data1',
            param2: 'data2',
        },
    });
};
```

And I set the **async** to `false`, it doesn't work.

After googled, I found a better solution `navigator.sendBeacon`, and it works well.

```js
window.onbeforeunload = function(evt) {
    const endpoint = '/lock/end-timestamp.php';
    var formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('param1', 'data1');
    formData.append('param2', 'data2');
    navigator.sendBeacon(endpoint, formData);
};
```

References:

- [window.onbeforeunload ajax request in Chrome](https://stackoverflow.com/questions/4945932/window-onbeforeunload-ajax-request-in-chrome)
- [Navigator.sendBeacon()](https://developer.mozilla.org/en-US/docs/Web/API/Navigator/sendBeacon)
- [Setting HTTP Headers in a Beacon Request](http://usefulangle.com/post/63/javascript-navigator-sendbeacon-set-form-http-header)
