---
layout: post
title: "ZendFramework 2 - Create a custom view helper"
date: 2013-04-19 19:02
comments: true
categories: 
- php
- zend
- programming
---

For many situations, we need some helper functions to be used in view.

i.e. When you want to debug, probably you will write

**./module/Application/view/application/index/foo.phtml**
```php
<div class="whatever">
    <?php
        /* debug here */
        echo '<pre>';
        print_r($this->form->get('foo'));
        echo '</pre>';
    ?>
</div>
```

Using `<pre>` to wrap it will be more readable, unfortunately you don't want to every time write such a long line. Now, create a helper to be able to use in all views.

Create a new file **./module/Application/src/Application/View/Helper/God.php** _(Let's call it `God` as this helper can do anything that you want provided you defined it as a function inside)_
```php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class God extends AbstractHelper
{
    public function __invoke()
    {
        return $this;
    }

    public function debug($obj, $die = false)
    {
        echo '<pre>';
        print_r($obj);
        echo '</pre>';
        if ($die) die();
    }
}
```

Now you have to edit your **./module/Application/Module.php** in order for it to load to view
```php
...
class Module
{
    ...
    public function getViewHelperConfig()
    {
        return array(
            'godHelper' => function($helper_plugin_manager) {
                $helper = new Application\View\Helper\God();
                return $helper;
            },
        );
    }
    ...
}
```
`godHelper` is an alias to be used in your view.

Now you're done, you can use in any view now. i.e.

**./module/Application/view/application/index/foo.phtml**
```php
<div class="whatever">
    <?php $this->godHelper()->debug($this->form->get('foo'), 1); ?>
</div>
```

You can now add as many functions as you want to the `God` class.

_Reference: [How to write custom view helper in Zend Framework 2 ?](http://zf2dev.com/2013/03/19/how-to-write-custom-view-helper-in-zend-framework-2/)_
