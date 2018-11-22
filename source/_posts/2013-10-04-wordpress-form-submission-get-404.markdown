---
layout: post
title: "Wordpress form submission get 404"
date: 2013-10-04 15:59
comments: true
tags: 
- php
- wordpress
---

Have you ever face a problem, when there is **GET** request, it shows the form properly. However when there is **POST** request, there are 2 situations:

1. The form has errors, then show error messages properly.
2. There is no error, it throws **404** _(page not found)_.

I've struggling for few hours, and this is weird.

Example form **(not working)**

```html
<form method="POST">
    <input type="text" name="name" />
    <input type="text" name="email" />
</form>
```

Finally I found out the reason behind, because of the `name="name"`. The `name` is reserve word in WordPress.

So just change it to others

```html
<form method="POST">
    <input type="text" name="user_name" />
    <input type="text" name="email" />
</form>
```

Then it works well.

**NOTE: Beside `name`, actually there are others reserve words like `day`, `month` and `year`**

Hope this helps :)

Reference: [404 pops after custom form submission by POST](http://wordpress.org/support/topic/404-pops-after-custom-form-submission-by-post#post-1291358)
