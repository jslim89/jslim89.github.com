---
layout: post
title: "Integrate PayPal SDK into Laravel 4"
date: 2014-09-19 21:45:03 +0800
comments: true
categories: 
- php
---

PayPal has release an [official SDK](https://github.com/paypal/rest-api-sdk-php) to simplify our work. Here I want to show you how to integrate into [Laravel 4](http://laravel.com/).

## 1. Install PayPal SDK via composer

Edit file **composer.json**

```json composer.json
{
    ...
	"require": {
        ...
		"paypal/rest-api-sdk-php": "*"
	},
    ...
}
```

Update the dependencies

```sh
$ php composer.phar update --no-dev
```

You can now use the PayPal package in the project.

## 2. Configure PayPal

Add a config file for paypal

```php app/config/paypal.php
<?php
return array(
    // set your paypal credential
    'client_id' => 'AcT3DS8a-SmTEtSl9hNcwyscoLypndD9q5L0YcfxmaUavz3p_xwFNRE-OauO',
    'secret' => 'ENv8_RCXMfhcrzdSfAWjLWDiD_GJSD-Gbm5q2Pj92vIuobCtgLpR3SUxqAhZ',

    /**
     * SDK configuration 
     */
    'settings' => array(
        /**
         * Available option 'sandbox' or 'live'
         */
        'mode' => 'sandbox',

        /**
         * Specify the max request time in seconds
         */
        'http.ConnectionTimeOut' => 30,

        /**
         * Whether want to log to a file
         */
        'log.LogEnabled' => true,

        /**
         * Specify the file that want to write on
         */
        'log.FileName' => storage_path() . '/logs/paypal.log',

        /**
         * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
         *
         * Logging is most verbose in the 'FINE' level and decreases as you
         * proceed towards ERROR
         */
        'log.LogLevel' => 'FINE'
    ),
);
```

Setup in **IndexController.php**

```php app/controllers/IndexController.php
<?php
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

class IndexController extends BaseController
{
    ...
    private $_api_context;

    public function __construct()
    {
        ...
        // setup PayPal api context
        $paypal_conf = Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $this->_api_context->setConfig($paypal_conf['settings']);
    }
    ...
}
```

## 3. Add 2 routes for processing PayPal checkout

Add this to **routes.php**

```php app/routes.php
<?php
Route::post('payment', array(
    'as' => 'payment',
    'uses' => 'IndexController@postPayment',
));

// this is after make the payment, PayPal redirect back to your site
Route::get('payment/status', array(
    'as' => 'payment.status',
    'uses' => 'IndexController@getPaymentStatus',
));
```

Update the controller, this method is when you submit the form or checkout shopping cart, then post to this route.

```php app/controllers/IndexController.php
<?php
public function postPayment()
{
    ...

    $payer = new Payer();
    $payer->setPaymentMethod('paypal');

    $item_1 = new Item();
    $item_1->setName('Item 1') // item name
        ->setCurrency('USD')
        ->setQuantity(2)
        ->setPrice('15'); // unit price

    $item_2 = new Item();
    $item_2->setName('Item 2')
        ->setCurrency('USD')
        ->setQuantity(4)
        ->setPrice('7');

    $item_3 = new Item();
    $item_3->setName('Item 3')
        ->setCurrency('USD')
        ->setQuantity(1)
        ->setPrice('20');

    // add item to list
    $item_list = new ItemList();
    $item_list->setItems(array($item_1, $item_2, $item_3));

    $amount = new Amount();
    $amount->setCurrency('USD')
        ->setTotal(78);

    $transaction = new Transaction();
    $transaction->setAmount($amount)
        ->setItemList($item_list)
        ->setDescription('Your transaction description');

    $redirect_urls = new RedirectUrls();
    $redirect_urls->setReturnUrl(URL::route('payment.status'))
        ->setCancelUrl(URL::route('payment.status'));

    $payment = new Payment();
    $payment->setIntent('Sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirect_urls)
        ->setTransactions(array($transaction));

    try {
        $payment->create($this->_api_context);
    } catch (\PayPal\Exception\PPConnectionException $ex) {
        if (\Config::get('app.debug')) {
            echo "Exception: " . $ex->getMessage() . PHP_EOL;
            $err_data = json_decode($ex->getData(), true);
            exit;
        } else {
            die('Some error occur, sorry for inconvenient');
        }
    }

    foreach($payment->getLinks() as $link) {
        if($link->getRel() == 'approval_url') {
            $redirect_url = $link->getHref();
            break;
        }
    }

    // add payment ID to session
    Session::put('paypal_payment_id', $payment->getId());

    if(isset($redirect_url)) {
        // redirect to paypal
        return Redirect::away($redirect_url);
    }

    return Redirect::route('original.route')
        ->with('error', 'Unknown error occurred');
}
```

![PayPal summary](http://jslim89.github.com/images/posts/2014-09-19-integrate-paypal-sdk-into-laravel-4/paypal-summary.png)

Up to this point, you will see the page above.

![PayPal pay](http://jslim89.github.com/images/posts/2014-09-19-integrate-paypal-sdk-into-laravel-4/paypal-pay.png)

Then login & pay.

## 4. Add another handler to handle PayPal after payment

Before that, when the payment successfully made, it will return this 2 parameters as query string

```
token=EC-05R25178G5276364N
PayerID=LXA67A9A83UD6
```

Otherwise, ONLY `token` when the customer cancel the payment

```
token=EC-05R25178G5276364N
```

```php app/controllers/IndexController.php
public function getPaymentStatus()
{
    // Get the payment ID before session clear
    $payment_id = Session::get('paypal_payment_id');

    // clear the session payment ID
    Session::forget('paypal_payment_id');

    if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
        return Redirect::route('original.route')
            ->with('error', 'Payment failed');
    }

    $payment = Payment::get($payment_id, $this->_api_context);

    // PaymentExecution object includes information necessary 
    // to execute a PayPal account payment. 
    // The payer_id is added to the request query parameters
    // when the user is redirected from paypal back to your site
    $execution = new PaymentExecution();
    $execution->setPayerId(Input::get('PayerID'));
    
    //Execute the payment
    $result = $payment->execute($execution, $this->_api_context);

    echo '<pre>';print_r($result);echo '</pre>';exit; // DEBUG RESULT, remove it later

    if ($result->getState() == 'approved') { // payment made
        return Redirect::route('original.route')
            ->with('success', 'Payment success');
    }
    return Redirect::route('original.route')
        ->with('error', 'Payment failed');
}
```

See the **DEBUG RESULT** there, if you print it out, the output will be

```php output
PayPal\Api\Payment Object
(
    [_propMap:PayPal\Common\PPModel:private] => Array
        (
            [id] => PAY-24J48306WV121522LKQNZSPI
            [create_time] => 2014-09-19T02:47:25Z
            [update_time] => 2014-09-19T02:53:55Z
            [state] => approved
            [intent] => sale
            [payer] => PayPal\Api\Payer Object
                (
                    [_propMap:PayPal\Common\PPModel:private] => Array
                        (
                            [payment_method] => paypal
                            [payer_info] => PayPal\Api\PayerInfo Object
                                (
                                    [_propMap:PayPal\Common\PPModel:private] => Array
                                        (
                                            [email] => john.smith@example.com
                                            [first_name] => John
                                            [last_name] => Smith
                                            [payer_id] => LXA67A9A83UD6
                                            [shipping_address] => PayPal\Api\ShippingAddress Object
                                                (
                                                    [_propMap:PayPal\Common\PPModel:private] => Array
                                                        (
                                                            [line1] => 1 Main Terrace
                                                            [line2] => 
                                                            [city] => Wolverhampton
                                                            [state] => West Midlands
                                                            [postal_code] => W12 4LQ
                                                            [country_code] => GB
                                                            [recipient_name] => 
                                                        )

                                                )

                                        )

                                )

                        )

                )

            [transactions] => Array
                (
                    [0] => PayPal\Api\Transaction Object
                        (
                            [_propMap:PayPal\Common\PPModel:private] => Array
                                (
                                    [amount] => PayPal\Api\Amount Object
                                        (
                                            [_propMap:PayPal\Common\PPModel:private] => Array
                                                (
                                                    [total] => 78.00
                                                    [currency] => USD
                                                    [details] => PayPal\Api\Details Object
                                                        (
                                                            [_propMap:PayPal\Common\PPModel:private] => Array
                                                                (
                                                                    [subtotal] => 78.00
                                                                )

                                                        )

                                                )

                                        )

                                    [description] => Your item description
                                    [item_list] => PayPal\Api\ItemList Object
                                        (
                                            [_propMap:PayPal\Common\PPModel:private] => Array
                                                (
                                                    [items] => Array
                                                        (
                                                            [0] => PayPal\Api\Item Object
                                                                (
                                                                    [_propMap:PayPal\Common\PPModel:private] => Array
                                                                        (
                                                                            [name] => Item 1
                                                                            [price] => 15.00
                                                                            [currency] => USD
                                                                            [quantity] => 2
                                                                        )

                                                                )

                                                            [1] => PayPal\Api\Item Object
                                                                (
                                                                    [_propMap:PayPal\Common\PPModel:private] => Array
                                                                        (
                                                                            [name] => Item 2
                                                                            [price] => 7.00
                                                                            [currency] => USD
                                                                            [quantity] => 4
                                                                        )

                                                                )

                                                            [2] => PayPal\Api\Item Object
                                                                (
                                                                    [_propMap:PayPal\Common\PPModel:private] => Array
                                                                        (
                                                                            [name] => Item 3
                                                                            [price] => 20.00
                                                                            [currency] => USD
                                                                            [quantity] => 1
                                                                        )

                                                                )

                                                        )

                                                    [shipping_address] => PayPal\Api\ShippingAddress Object
                                                        (
                                                            [_propMap:PayPal\Common\PPModel:private] => Array
                                                                (
                                                                    [recipient_name] => 
                                                                    [line1] => 1 Main Terrace
                                                                    [line2] => 
                                                                    [city] => Wolverhampton
                                                                    [state] => West Midlands
                                                                    [postal_code] => W12 4LQ
                                                                    [country_code] => GB
                                                                )

                                                        )

                                                )

                                        )

                                    [related_resources] => Array
                                        (
                                            [0] => PayPal\Api\RelatedResources Object
                                                (
                                                    [_propMap:PayPal\Common\PPModel:private] => Array
                                                        (
                                                            [sale] => PayPal\Api\Sale Object
                                                                (
                                                                    [_propMap:PayPal\Common\PPModel:private] => Array
                                                                        (
                                                                            [id] => 48629238J1664492L
                                                                            [create_time] => 2014-09-19T02:47:25Z
                                                                            [update_time] => 2014-09-19T02:53:55Z
                                                                            [amount] => PayPal\Api\Amount Object
                                                                                (
                                                                                    [_propMap:PayPal\Common\PPModel:private] => Array
                                                                                        (
                                                                                            [total] => 78.00
                                                                                            [currency] => USD
                                                                                        )

                                                                                )

                                                                            [payment_mode] => INSTANT_TRANSFER
                                                                            [state] => completed
                                                                            [protection_eligibility] => ELIGIBLE
                                                                            [protection_eligibility_type] => ITEM_NOT_RECEIVED_ELIGIBLE,UNAUTHORIZED_PAYMENT_ELIGIBLE
                                                                            [parent_payment] => PAY-24J48306WV121522LKQNZSPI
                                                                            [links] => Array
                                                                                (
                                                                                    [0] => PayPal\Api\Links Object
                                                                                        (
                                                                                            [_propMap:PayPal\Common\PPModel:private] => Array
                                                                                                (
                                                                                                    [href] => https://api.sandbox.paypal.com/v1/payments/sale/48629238J1664492L
                                                                                                    [rel] => self
                                                                                                    [method] => GET
                                                                                                )

                                                                                        )

                                                                                    [1] => PayPal\Api\Links Object
                                                                                        (
                                                                                            [_propMap:PayPal\Common\PPModel:private] => Array
                                                                                                (
                                                                                                    [href] => https://api.sandbox.paypal.com/v1/payments/sale/48629238J1664492L/refund
                                                                                                    [rel] => refund
                                                                                                    [method] => POST
                                                                                                )

                                                                                        )

                                                                                    [2] => PayPal\Api\Links Object
                                                                                        (
                                                                                            [_propMap:PayPal\Common\PPModel:private] => Array
                                                                                                (
                                                                                                    [href] => https://api.sandbox.paypal.com/v1/payments/payment/PAY-24J48306WV121522LKQNZSPI
                                                                                                    [rel] => parent_payment
                                                                                                    [method] => GET
                                                                                                )

                                                                                        )

                                                                                )

                                                                        )

                                                                )

                                                        )

                                                )

                                        )

                                )

                        )

                )

            [links] => Array
                (
                    [0] => PayPal\Api\Links Object
                        (
                            [_propMap:PayPal\Common\PPModel:private] => Array
                                (
                                    [href] => https://api.sandbox.paypal.com/v1/payments/payment/PAY-24J48306WV121522LKQNZSPI
                                    [rel] => self
                                    [method] => GET
                                )

                        )

                )

        )

)
```

You have done.

## Update Dec 17, 2014

If you have problem with live credentials, please read through this section

### 1. Login to PayPal developer portal

![PayPal credential step 1](http://jslim89.github.com/images/posts/2014-09-19-integrate-paypal-sdk-into-laravel-4/get-credential-1.png)

Click on **Dashboard** link

### 2. Edit your app

![PayPal credential step 2](http://jslim89.github.com/images/posts/2014-09-19-integrate-paypal-sdk-into-laravel-4/get-credential-2.png)

Under **My apps**, click on the app you wanted to use in your project

### 3. Get the live credentials

![PayPal credential step 3](http://jslim89.github.com/images/posts/2014-09-19-integrate-paypal-sdk-into-laravel-4/get-credential-3.png)

Scroll down after the **Add webhook** button, click on the **Show** link

![PayPal credential step 4](http://jslim89.github.com/images/posts/2014-09-19-integrate-paypal-sdk-into-laravel-4/get-credential-4.png)

Then edit **app/config/paypal.php**, replace **client_id** & **secret** with the live credentials, and change the **mode** to `live`

#### Ack: Thanks [waiylgeek](https://disqus.com/by/waiylgeek/) for the info.
