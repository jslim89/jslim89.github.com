---
title: jQuery infinite scroll articles
date: 2019-06-24 18:33:37
tags:
- jquery
- javascript
- infinite-scroll
---

Quite often, we have seen a lot of news site _(e.g. [forbes.com](https://www.forbes.com))_,
when you scrolled until end of the article, it will shows another article.

I was working on this too, and can't find any plugin for this.

Definitely, we need a scroll event listener

```js
$(window).scroll(function () {
    if ($(document).height() - $(this).height() < $(this).scrollTop()) {
        loadAnotherArticle();
    }
});
```

The code above will load the next article when scroll to the bottom-most.

But let say we want to load the article 500px before the bottom-most.

```js
$(window).scroll(function () {
    // this is for before reach the bottom-most, it trigger to load next article
    let offsetPrefix = 500;

    if ($(document).height() - $(this).height() - offsetPrefix < $(this).scrollTop()) {
        loadAnotherArticle();
    }
});
```

But then, now every time scroll, it will trigger many requests on `loadAnotherArticle()`.
To prevent it, add a loading flag...

```js
// to prevent multiple requests on same article
var loading = false;

$(window).scroll(function () {
    // this is for before reach the bottom-most, it trigger to load next article
    let offsetPrefix = 500;

    if ($(document).height() - $(this).height() - offsetPrefix < $(this).scrollTop()) {
        if (!loading) {
            loadAnotherArticle();
        }
    }
});
```

What if want to change the URL & meta title/description when scroll to next article?

```js
// helper function to update the meta content
function updateMeta (item) {
    window.history.replaceState(item, item.title, item.url);
    document.title = item.title;
    $('meta[name="description"]').attr('content', item.description);
    $('meta[name="og:title"]').attr('content', item.og_title);
    $('meta[name="og:description"]').attr('content', item.og_description);
    $('meta[property="og:image"]').attr('content', item.image);
    $('meta[property="og:url"]').attr('content', item.url);
};
```

The tricky part comes, what URL should be used if in the middle of 1st & 2nd article?
Here what I do is, determined by the visible area, which every is higher.

Let's update the scroll event

```js
// those articles already loaded to `<body>`
var loadedArticles = [];

// ref: https://stackoverflow.com/questions/24768795/get-the-visible-height-of-a-div-with-jquery/26831113#26831113
function elementVisibleHeight ($el) {
    var elH = $el.outerHeight(),
        H   = $(window).height(),
        r   = $el[0].getBoundingClientRect(), t=r.top, b=r.bottom;
    return Math.max(0, t>0? Math.min(elH, H-t) : Math.min(b, H));
}

$(window).scroll(function () {
    ...

    // I use lodash for array processing
    // assign the visible height value to each loaded articles
    _.each(loadedArticles, function (item) {
        item.visibleHeight = elementVisibleHeight($('#article-' + item.id));
    });

    // now get the article that has maximum visible area
    var maxItem = _.maxBy(loadedArticles, function (item) {
        return item.visibleHeight;
    });

    // prevent it trigger multiple times
    if (maxItem.title != $('title').text()) {
        updateMeta(maxItem);
    }
});
```

Now the final version will be like

```js
// to prevent multiple requests on same article
var loading = false;

// those articles already loaded to `<body>`
var loadedArticles = [];

function updateMeta (item) {
    window.history.replaceState(item, item.title, item.url);
    document.title = item.title;
    $('meta[name="description"]').attr('content', item.description);
    $('meta[name="og:title"]').attr('content', item.og_title);
    $('meta[name="og:description"]').attr('content', item.og_description);
    $('meta[property="og:image"]').attr('content', item.image);
    $('meta[property="og:url"]').attr('content', item.url);
}

// ref: https://stackoverflow.com/questions/24768795/get-the-visible-height-of-a-div-with-jquery/26831113#26831113
function elementVisibleHeight ($el) {
    var elH = $el.outerHeight(),
        H   = $(window).height(),
        r   = $el[0].getBoundingClientRect(), t=r.top, b=r.bottom;
    return Math.max(0, t>0? Math.min(elH, H-t) : Math.min(b, H));
}

$(window).scroll(function () {
    // this is for before reach the bottom-most, it trigger to load next article
    let offsetPrefix = 500;

    if ($(document).height() - $(this).height() - offsetPrefix < $(this).scrollTop()) {
        if (!loading) {
            loadAnotherArticle();
        }
    }

    // I use lodash for array processing
    // assign the visible height value to each loaded articles
    _.each(loadedArticles, function (item) {
        item.visibleHeight = elementVisibleHeight($('#article-' + item.id));
    });

    // now get the article that has maximum visible area
    var maxItem = _.maxBy(loadedArticles, function (item) {
        return item.visibleHeight;
    });

    // prevent it trigger multiple times
    if (maxItem.title != $('title').text()) {
        updateMeta(maxItem);
    }
});
```

### P/S:

- `loadAnotherArticle()` implement by yourself
- The assumed article object is like

```json
{
    "id": 1,
    "title": "How to kickstart JavaScript?",
    "description": "This is the description",
    "og_title": "How to kickstart JavaScript?",
    "og_description": "This is the open graph description",
    "image": "http://jslim.net/path/to/image.jpg",
    "url": "http://jslim.net/path/to/articles/",
    "content": "<div>The html content</div>",
}
```

## References:

- [Get the visible height of a div with jQuery](https://stackoverflow.com/questions/24768795/get-the-visible-height-of-a-div-with-jquery/26831113#26831113)
