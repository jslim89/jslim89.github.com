---
layout: post
title: "Laravel 4 - two pagination in a single page"
date: 2014-08-23 06:27:19 +0800
comments: true
tags: 
- php
- laravel4
---

Alright, if you're existing [Laravel](http://laravel.com) user, you know that Laravel has make pagination easy. If you're new, I will show you how.

First of all, in your **app/routes.php**

```php
<?php
Route::get('items', array(
    'as' => 'items', 
    'uses' => 'ItemController@getIndex'
));
...
```

When user enter `http://<your url>.com/items`, it will matched and call the `getIndex` method in `ItemController` class

**app/controllers/ItemController.php**

```php
<?php
...
public function getIndex()
{
    // 1
    $items_per_page = Input::get('per_pg', 10);

    // 2
    $items = Item::paginate($items_per_page);

    // 3
    $this->layout->content = View::make('items.index')
        ->with('items', $items);
}
```

**app/models/Item.php**

```php
<?php
// 4
class Item extends Eloquent
{
	protected $table = 'item';
}
```

1. Get the data from query string
2. Look for the `Item` and paginate it to _n_ items per page
3. Pass in the necessary value to the view
4. Create a class named `Item` and extends `Eloquent` ORM, it will make your life easy

Do you realise there is no current page for pagination? Laravel did it for you. It will passed in the `page=3` for
example into the query string, so the `paginate` method will get the value from `page` _(in this case is **3**)_
and indicate that it is the current selected page.

**The problem now is, what if I want to have 2 pagination in a single page?**

Assumption:

- each page has 10 items

Let's take an example, there are 2 tabs of listing, both under the same page/same URL, let's called it
**impression** & **conversion**. Example, I have `999` items in **impression**, but I have only `44` items
in **conversion**, on the **impression** when I click on page 10, it will shows the 101th item to 110th item,
whereas **conversion** tab shows nothing. You know why? Because both of them taking the same argument from query
string, i.e. `page=10`, both of them also look for page 10. What we want is **impression** page 10, but **conversion**
remain page 1.

![No ajax](/images/posts/2014-08-23-laravel-4-two-pagination-in-a-single-page/no-ajax.gif)

## Solution
One of the solution that I found out is implement by using AJAX.

### 1. Add in an extra routes

**app/routes.php**

```php
<?php
// ADD THIS
Route::get('items/ajax/{type}', array(
    'as' => 'items.type', 
    'uses' => 'ItemController@getItemType'
))->where('type', 'impression|conversion');

Route::get('items', array(
    'as' => 'items', 
    'uses' => 'ItemController@getIndex'
));
...
```

The `where` condition is to only match for either `impression` or `conversion`. Note that
you must put it above the `items`. Always put the more precise route to above.

### 2. Implement the ajax method in `ItemController`

**app/controllers/ItemController.php**

```php
<?php
...
public function getItemType($type)
{
    $items_per_page = Input::get('per_pg', 10);

    if ($type == 'impression') {
        $items = Impression::paginate($items_per_page);
    } else {
        $items = Conversion::paginate($items_per_page);
    }

    // 1.
    $view = View::make('item_type')->with('items', $items);
    echo $view;
    exit;
}
```

1. I'm not return JSON on the AJAX call, but instead, I return the whole bunch of HTML.

But before that, make sure you create a **model** class for both **impression** & **conversion** just like
what you did for **Item.php** just now.

### 3. Create a new view file that keep only the particular section of code

**app/views/item_type.php**

```php
<table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Key</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $item): ?>
    <tr>
      <td><?php echo $item->id; ?></td>
      <td><?php echo $item->key; ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

// 1.
<?php echo $items->appends(Input::except('page'))->links(); ?>
```

1. The pagination link I also included into the ajax, this will make your life easier.

### 4. Then add in the Ajax call on the main view file

add the section to bottom right before `</body>`

**app/views/items.php**

```js
$(function() {
    // 1.
    function getPaginationSelectedPage(url) {
        var chunks = url.split('?');
        var baseUrl = chunks[0];
        var querystr = chunks[1].split('&');
        var pg = 1;
        for (i in querystr) {
            var qs = querystr[i].split('=');
            if (qs[0] == 'page') {
                pg = qs[1];
                break;
            }
        }
        return pg;
    }

    // 2.
    $('#impression').on('click', '.pagination a', function(e) {
        e.preventDefault();
        var pg = getPaginationSelectedPage($(this).attr('href'));

        $.ajax({
            url: '/items/ajax/impression',
            data: { page: pg },
            success: function(data) {
                $('#impression').html(data);
            }
        });
    });

    $('#conversion').on('click', '.pagination a', function(e) {
        e.preventDefault();
        var pg = getPaginationSelectedPage($(this).attr('href'));

        $.ajax({
            url: '/items/ajax/conversion',
            data: { page: pg },
            success: function(data) {
                $('#conversion').html(data);
            }
        });
    });

    // 3.
    $('#impression').load('/items/ajax/impression?page=1');
    $('#conversion').load('/items/ajax/conversion?page=1');
});
```

1. Look for the selected page number
2. Create an event listener to listen the `onclick` event on the pagination link.
3. When the page first loaded _(which has no any click yet)_, load the first page of both items.

You have done. See the result

![Ajax](/images/posts/2014-08-23-laravel-4-two-pagination-in-a-single-page/ajax.gif)

[Download the sample source code](/attachments/posts/2014-08-23-laravel-4-two-pagination-in-a-single-page/sample.zip)
