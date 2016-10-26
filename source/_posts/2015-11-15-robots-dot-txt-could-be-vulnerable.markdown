---
layout: post
title: "robots.txt could be vulnerable"
date: 2015-11-15 11:26:20 +0800
comments: true
categories: 
- security
---

[Robots.txt](http://www.robotstxt.org/) designed to prevent search engine to crawl your page or content, some of the content probably you don't want others to search about it.

Example, below shows that this site prevent all user agent _(mostly search engine)_ to crawl the content of the entire site.

```
User-agent: *
Disallow: /
```

However, this could be a loophole for giving a chance to hacker to hack into your site, because you have exposed the paths.

Take a look on [Facebook's robots.txt](https://www.facebook.com/robots.txt), it is

```
# Notice: Crawling Facebook is prohibited unless you have express written
# permission. See: http://www.facebook.com/apps/site_scraping_tos_terms.php

User-agent: baiduspider
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: Bingbot
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: Googlebot
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: ia_archiver
Disallow: /
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: msnbot
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: Naverbot
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: seznambot
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: Slurp
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: teoma
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: Yandex
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: Yeti
Disallow: /ajax/
Disallow: /album.php
Disallow: /checkpoint/
Disallow: /contact_importer/
Disallow: /feeds/
Disallow: /file_download.php
Disallow: /hashtag/
Disallow: /l.php
Disallow: /p.php
Disallow: /photo.php
Disallow: /photos.php
Disallow: /sharer/
Disallow: /topic/

User-agent: ia_archiver
Allow: /about/privacy
Allow: /full_data_use_policy
Allow: /legal/terms
Allow: /policy.php

User-agent: *
Disallow: /
```

So the hacker may try to access in this way, e.g. _www.facebook.com/topic/_ _(I have tried it, it shows page not available)_.

### How to prevent this?

You can choose a modern web framework to develop your web application, example like [Laravel](http://laravel.com/), the [path](http://laravel.com/docs/5.1/routing) is you can specify it by your own, e.g.

```php
<?php
Route::get('foo/bar', function () {
    return 'Hello World';
});
```

When hacker try to look for _www.yoursite.com/foo_, he/she won't get anything here. So becareful when you design your web application.
