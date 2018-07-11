---
title: Laravel event multi listeners not working
date: 2018-07-11 23:13:08
tags:
- laravel
---

The [Laravel Event](https://laravel.com/docs/5.6/events) feature is pretty useful, just like [Trigger in DBMS](https://dev.mysql.com/doc/refman/8.0/en/triggers.html);

I've tried to register more than 1 [Listener](https://laravel.com/docs/5.6/events#defining-listeners) to an event.

E.g. in app/Providers/EventServiceProvider.php

```php
<?php
...

protected $listen = [
    AccountCreated::class => [
        SendActivationEmail::class,
        SendSMS::class,
    ],
];
```

Example above shows that once account is created, system will send the activation email automatically, followed by send SMS.

I've encounter the problem that the 2nd listener is not triggered.

### Why?

The reason behind is, there's a `return false;` in `SendActivationEmail::handler`. E.g.

```php
<?php
...
class CreateAnnouncementOfferUpdated
{
    ...

    public function handle(AccountCreated $event)
    {
        ...

        if ($event->account->is_fb_login) {
            return false;
        }

        ...
    }
}
```

The `return false;` indicate that the rest of listeners will not be executed.

So what I did was, use `return;` instead of `return false;`. i.e.

```php
if ($event->account->is_fb_login) {
    return;
}
```
