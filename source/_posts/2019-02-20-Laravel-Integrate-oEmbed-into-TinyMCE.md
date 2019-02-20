---
title: Laravel - Integrate oEmbed into TinyMCE
date: 2019-02-20 23:21:26
tags:
- laravel
- tinymce
- oembed
---

[oEmbed](https://oembed.com/) is a kind of standard to retrieve 3rd party site info.

Example, in rich editor like [TinyMCE](https://www.tiny.cloud/), if want to embed
a YouTube video, what we usually do is

1. Get the YouTube video embed code
2. Click on the TinyMCE view source button
3. Paste the embed code into the content

I did that previously, and it works. But when I do the same thing to Instagram
post, it doesn't work so well, especially embed more than 1 post.

If you ever use Wordpress before, you will notice when you paste a YouTube link
directly to editor, and it will load the video, without the need of embed code.

Let's add the oEmbed feature into your Laravel app

### 1. install a composer package

Because I couldn't found any good JavaScript oEmbed plugin, then I decided to use
[this PHP plugin](https://github.com/oscarotero/Embed).

```
$ composer require embed/embed
```

### 2. Create an API endpoint

Add a line to **routes/web.php**

```php
Route::get('oembed', ['as' => 'oembed', 'uses' => 'YourController@oEmbed']);
```

In **YourController.php**

```php
<?php
public function oEmbed(Request $request)
{
    $rules = [
        'url' => 'required|url',
    ];
    $validator = \Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json([
            'error' => $validator->messages()->first()
        ], 400);
    }

    try {
        $info = \Embed\Embed::create($request->input('url'));

        return response()->json([
            'title' => $info->title, //The page title
            'description' => $info->description, //The page description
            'url' => $info->url, //The canonical url
            'type' => $info->type, //The page type (link, video, image, rich)
            'tags' => $info->tags, //The page keywords (tags)

            'images' => $info->images, //List of all images found in the page
            'image' => $info->image, //The image choosen as main image
            'image_width' => $info->imageWidth, //The width of the main image
            'image_height' => $info->imageHeight, //The height of the main image

            'code' => $info->code, //The code to embed the image, video, etc
            'width' => $info->width, //The width of the embed code
            'height' => $info->height, //The height of the embed code
            'aspect_ratio' => $info->aspectRatio, //The aspect ratio (width/height)

            'author_name' => $info->authorName, //The resource author
            'author_url' => $info->authorUrl, //The author url

            'provider_name' => $info->providerName, //The provider name of the page (Youtube, Twitter, Instagram, etc)
            'provider_url' => $info->providerUrl, //The provider url
            'provider_icons' => $info->providerIcons, //All provider icons found in the page
            'provider_icon' => $info->providerIcon, //The icon choosen as main icon

            'published_date' => $info->publishedDate, //The published date of the resource
            'license' => $info->license, //The license url of the resource
            'linked_data' => $info->linkedData, //The linked-data info (http://json-ld.org/)
            'feeds' => $info->feeds, //The RSS/Atom feeds
        ];
    } catch (\Embed\Exceptions\InvalidUrlException $e) {
        return response()->json([
            'error' => sprintf('%s: %s', get_class($e), $e->getMessage())
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'error' => sprintf('%s: %s', get_class($e), $e->getMessage())
        ], 500);
    }
}
```

Now, you can test your endpoint

GET https://yoursite.com/oembed?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DG5seUqK1EeE

The most important data is the **code**.

### 3. Show the 3rd party content into TinyMCE

```js
function isValidURL(str) {
    var a  = document.createElement('a');
    a.href = str;
    return (a.host && a.host != window.location.host);
}

tinymce.init({
    // remember to add a 'paste' plugin
    plugins: '... ... ... paste',

    // make the embed content non-editable
    noneditable_noneditable_class: 'embed-content',

    // here we interrupt the paste process
    paste_preprocess: function(plugin, args) {
        let input = args.content.trim();

        // skip if not a valid URL
        if (!isValidURL(input)) return;

        $.ajax({
            url: '/oembed',
            dataType: 'json',
            type: 'get',
            async: false, // set the async to false, because we want to replace the original content with the embed code
            data: {
                url: input
            },
            success: function (data, textStatus, jqXHR) {
                if (!data.code) return;

                // wrap the embed code with a div, to prevent it to be editable
                args.content = '<div class="embed-content">' + data.code + '</div>';
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            complete: function (jqXHR, textStatus) {
            }
        });
    },
});
```

Now, let's try to paste a YouTube link to TinyMCE editor. It should works 😉

## References:

- [Check if a JavaScript string is a URL](https://stackoverflow.com/questions/5717093/check-if-a-javascript-string-is-a-url/34695026#34695026)
- [TinyMCE - Noneditable plugin](https://www.tiny.cloud/docs/plugins/noneditable/)
- [TinyMCE - Paste plugin](https://www.tiny.cloud/docs/plugins/paste/)
