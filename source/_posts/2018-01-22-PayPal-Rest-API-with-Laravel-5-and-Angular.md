---
title: PayPal Rest API with Laravel 5 and Angular
date: 2018-01-22 14:35:45
tags:
- php
- paypal
- angular
- laravel
---

Previously I wrote a post regarding to [Integrate PayPal SDK into Laravel 4](/blog/2014/09/19/integrate-paypal-sdk-into-laravel-4/). And I believe now Laravel 5 is quite different from Laravel 4. Also, PayPal API also updated now.

This post involved Front End (Angular 5) & Back End (Laravel 5).

And I assumed you know how to create PayPal account _([you may refer here if needed](/blog/2014/09/19/integrate-paypal-sdk-into-laravel-4/))_.

## In Laravel 5

_(Assumed that you're terminal always in the project root)_

### 1. Install the PayPal SDK

```sh
$ composer install paypal/rest-api-sdk-php --save
```

### 2. Add PayPal config

**config/paypal.php**

```php
<?php
return [
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'secret' => env('PAYPAL_CLIENT_SECRET'),

    /**
     * SDK configuration 
     */
    'settings' => [

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
    ],
    'webhooks' => [
        'payment_sale_completed' => env('PAYPAL_PAYMENT_SALE_COMPLETED_WEBHOOK_ID'),
    ],
];
```

Add the following content to **.env**

```
PAYPAL_CLIENT_ID=AcT3DS8a-SmTEtSl9hNcwyscoLypndD9q5L0YcfxmaUavz3p_xwFNRE-OauO
PAYPAL_CLIENT_SECRET=ENv8_RCXMfhcrzdSfAWjLWDiD_GJSD-Gbm5q2Pj92vIuobCtgLpR3SUxqAhZ
PAYPAL_PAYMENT_SALE_COMPLETED_WEBHOOK_ID=CTU22487IE5K8012E
```

### 3. PayPal callback handler _(Webhooks)_

Now we practice [Webhooks](https://developer.paypal.com/docs/integration/direct/webhooks/) here.

Let's login to [PayPal developer console](https://developer.paypal.com). _(I assumed you already created an app)_

![PayPal app](/images/posts/2018-01-22-PayPal-Rest-API-with-Laravel-5-and-Angular/paypal-app.png)

1. Click on **My Apps & Credentials**
2. Look for **REST API apps**
3. Click on the app you want to deal with

![PayPal add sandbox webhooks](/images/posts/2018-01-22-PayPal-Rest-API-with-Laravel-5-and-Angular/add-webhooks.png)

Then look for **SANDBOX WEBHOOKS** section. You can add any call back here with the URL route you want to handle.

In my side, I selected **Payment Sale Completed**. Which mean every time the payment sucessfully made, PayPal will trigger the URL there in POST request.

### 4. Now let's create a handler in your controller

Edit the **routes/api.php**

```php
<?php
Route::group(['prefix' => 'webhooks'], function () {
    Route::post('paypal/payment-sale-completed', ['uses' => 'PayPalController@webhooksPaymentSaleCompleted']);
});
```

Then create a controller **app/Http/Controllers/PayPalController.php**

```php
<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\File;
use App\Http\Controllers\Controller;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;
use PayPal\Api\VerifyWebhookSignature;
use PayPal\Api\WebhookEvent;

use App\Models\User;

class PayPalController extends Controller
{
    private $_api_context;

    public function __construct()
    {
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(config('paypal.client_id'), config('paypal.secret'))
        );
        $this->_api_context->setConfig(config('paypal'));
    }

    /**
     * Webhook (Payment sale completed)
     * 
     * @param Request $request 
     * @return void
     */
    public function webhooksPaymentSaleCompleted(Request $request)
    {
        /** @var string $request_body */
        $request_body = file_get_contents('php://input');

        /** @var array $headers */
        $headers = $request->headers->all();
        $headers = array_change_key_case($headers, CASE_UPPER);

        $signature_verification = new VerifyWebhookSignature();
        $signature_verification->setAuthAlgo($headers['PAYPAL-AUTH-ALGO'][0]);
        $signature_verification->setTransmissionId($headers['PAYPAL-TRANSMISSION-ID'][0]);
        $signature_verification->setCertUrl($headers['PAYPAL-CERT-URL'][0]);
        // get the webhook ID in config file
        $signature_verification->setWebhookId(config('paypal.webhooks.payment_sale_completed')); // Note that the Webhook ID must be a currently valid Webhook that you created with your client ID/secret.
        $signature_verification->setTransmissionSig($headers['PAYPAL-TRANSMISSION-SIG'][0]);
        $signature_verification->setTransmissionTime($headers['PAYPAL-TRANSMISSION-TIME'][0]);

        $signature_verification->setRequestBody($request_body);
        $req = clone $signature_verification;

        // for error message, I log it into a file for debug purpose
        $exception_log_file = storage_path('logs/paypal-exception.log');

        try {
            /** @var \PayPal\Api\VerifyWebhookSignatureResponse $output */
            $output = $signature_verification->post($this->_api_context);
        } catch (\Exception $ex) {
            file_put_contents($exception_log_file, $ex->getMessage());
            exit(1);
        }
        $status = $output->getVerificationStatus(); // 'SUCCESS' or 'FAILURE'
        // if the status is not success, then end here
        if (strtoupper($status) !== 'SUCCESS') exit(1);

        $json = json_decode($request_body, 1);

        // Because PayPal don't let us to add in custom data in JSON form, so I add it to a field 'custom' as encoded string. Now decode to get the data back
        $custom_data = json_decode($json['resource']['custom'], 1);
        $user = User::find($custom_data['user_id']); // to get the User

        // save the payment info

        // generate invoice

        // email to user

        echo $status; // at the end must echo the status
        exit(1);
    }
}
```

![PayPal webhooks list](/images/posts/2018-01-22-PayPal-Rest-API-with-Laravel-5-and-Angular/paypal-webhook-list.png)

You can see there are green tick and yellow exclamation mark. The `echo $status;` is to tell the PayPal server that this webhook has been processed. Otherwise it will resend the POST webhook request.

## In Angular

Here is pretty simple, just need to add in the JavaScript code to the component.

### 1. Add PayPal to environment settings

```ts
...
services: {
  paypal: {
    clientId: 'AcT3DS8a-SmTEtSl9hNcwyscoLypndD9q5L0YcfxmaUavz3p_xwFNRE-OauO',
  },
}
...
```

### 2. Add the PayPal express checkout button to a component

**pricing.component.html** for example

```html
<div id="paypal-button-container"></div>
```

Update the **pricing.component.ts**

```ts
declare var $:any; // want to use jQuery here
declare var paypal:any;

@Component({
  selector: 'app-pricing',
  templateUrl: './pricing.component.html',
  styleUrls: ['./pricing.component.scss']
})
...

export class PricingComponent implements OnInit, AfterViewChecked {

  private didRenderPaypal: boolean = false;

  ...

  ngAfterViewChecked() {
    this.configurePaypal();
  }
  
  configurePaypal() {
    if (!this.didRenderPaypal) {

      var userId = 2;

      this.loadPaypalScript().then(() => {
        paypal.Button.render({
            env: 'sandbox', // sandbox | production
            // Create a PayPal app: https://developer.paypal.com/developer/applications/create
            client: {
              sandbox:    environment.services.paypal.clientId,
              production: environment.services.paypal.clientId
            },
            // Show the buyer a 'Pay Now' button in the checkout flow
            commit: true,

            // payment() is called when the button is clicked
            payment: function(data, actions) {

              // Make a call to the REST api to create the payment
              return actions.payment.create({
                payment: {
                  transactions: [
                    {
                      amount: {
                        total: $('#total').val(),
                        currency: 'MYR',
                        details: {
                          subtotal: $('#subtotal').val(),
                          tax: $('#tax').val(),
                        }
                      },
                      custom: JSON.stringify({ // YOU CAN ADD CUSTOM DATA HERE
                        user_id: userId,
                        qty: $('#qty').val()
                      })
                    }
                  ]
                }
              });
            },

            // onAuthorize() is called when the buyer approves the payment
            onAuthorize: function(data, actions) {
              // Make a call to the REST api to execute the payment
              return actions.payment.execute().then(function() {
                console.log(data);
                window.alert('Payment Complete!');
              });
            }

        }, '#paypal-button-container');
      });
    }
  }

  private loadPaypalScript(): Promise<any> {
      this.didRenderPaypal = true;
      return new Promise((resolve, reject) => {
          const scriptElement = document.createElement('script');
          scriptElement.src = 'https://www.paypalobjects.com/api/checkout.js';
          scriptElement.onload = resolve;
          document.body.appendChild(scriptElement);
      });
  }
}
```

The PayPal JavaScript file must be injected during run time.

You can see I use a lot of jQuery _(e.g. `$('total').val()`)_, is because the code block inside is cannot be set a typescript variable directly, the total amount may change if user change products. Thus, jQuery can ensure it get the correct value from the form.

Now you run your Angular app, you should see a PayPal button there.

![PayPal payment popup](/images/posts/2018-01-22-PayPal-Rest-API-with-Laravel-5-and-Angular/paypal-payment-popup.png)

Once you make the payment, the webhook will be triggered.

## Update: 2018-01-25

Let say you don't want the payment processing part by webhooks, you can do it in the JavaScript success block.

### Angular part

Edit **pricing.component.ts**

```ts
...
onAuthorize: function(data, actions) {
  // Make a call to the REST api to execute the payment
  return actions.payment.execute().then(function() {
    that.http
      .post(
        'https://www.yoursite.com/api/paypal/checkout',
        data
      )
      .toPromise()
      .then(res => {
        // success submit
        console.log(res.json());
      })
      .catch(res => {
        // POST error
        console.log(res);
      });
  });
}
...
```

### Laravel part

Add a new route

Edit the **routes/api.php**

```php
<?php
Route::post('paypal/checkout', ['uses' => 'PayPalController@checkout']);
```

Then edit **app/Http/Controllers/PayPalController.php**

```php
<?php
...
use PayPal\Exception\PayPalConnectionException;
...

public function checkout(Request $request)
{
    ...
    // validate input

    // get payment detail and verify
    try {
        $payment = Payment::get($request->get('paymentID'), $this->_api_context);
    } catch (PayPalConnectionException $e) {
        $error_json = json_decode($e->getData(), 1);
        print_r($error_json);
        exit(1);
    }

    // generate and email pdf
    ...
}
```

References:

- [angular 4 and paypal express checkout](https://github.com/paypal/paypal-checkout/issues/368#issuecomment-326829806)
