---
title: Laravel Unit Test Mail Send
date: 2021-02-02 09:54:33
tags:
- laravel
- unit-test
- phpunit
---

## Case 1: Mail send without Mailable class

Sometime we don't use mailable class, instead we want to send directly with `send` method.

```php
<?php
function sendMailOrderShipped($order)
{
    $subject = 'Order ' . $order->id . ' is shipped';
    $recipient = $order->user->email;
    \Illuminate\Support\Facades\Mail::send('emails.order-shipped', compact('order'), function ($message) use ($subject, $recipient) {
        $message
            ->to($recipient)
            ->subject($subject)
        ;
    });
}
```

Unit test case


```php
<?php
/** @test */
public function user_should_receive_order_shipped_email()
{
    Mail::fake();

    $user = User::factory()->create();
    $deal = Order::factory()
        ->for($user)
        ->create();


    $recipient = $user->email;
    $subject = 'Order ' . $order->id . ' is shipped';

    Mail::shouldReceive('send')
        ->with(
            'emails.order-shipped',
            Mockery::type('array'),
            Mockery::on(function (\Closure $closure) use ($subject, $recipient) {
                $mock = Mockery::mock(\Illuminate\Mail\Message::class);
                $mock->shouldReceive('to')->once()->with($recipient)->andReturn($mock);
                $mock->shouldReceive('subject')->once()->with($subject);

                $closure($mock);

                return true;
            }),
        )
        ->times(1)
    ;

    sendMailOrderShipped($order);
}
```
