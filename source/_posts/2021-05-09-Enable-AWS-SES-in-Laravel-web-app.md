---
title: Enable AWS SES in Laravel web app
date: 2021-05-09 18:21:10
tags:
- aws
- laravel
- php
---

I believe most of AWS users will use Amazon Simple Email Service **(SES)**
for system email sending.

Before you can use in production, by default it's in _sandbox_ mode, which
you are required to pre-configure a few email addresses to receive email.

You're required to describe your use case, what you will use for, how
you will handle bounce back email, etc. in details.

E.g. You will be rejected if not provide enough information

![Failed to apply SES production](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/request-ses-production.png)

Let's get started

----

## 1. Configure in AWS console

Make sure you verify your domain.

![SES verify domain](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/verify-domain.png)

In order to verify, you need to add a TXT record to your DNS management.

![SES domain TXT record](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/ses-domain-txt-record.png)

![Namecheap DNS management](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/dns-records.png)

After domain, then add an email address _(the from email)_

![sent from email](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/add-email-address.png)

Once added, make sure verify **DKIM** & **MAIL FROM Domain**, just add a few records to DNS management


When using SES, must have this mechanism to handle bounced email.
Let say a spammer trick your system to keep blasting email to addresses that doesn't exist,
SES may block the service.

### 1.1. Create SNS topic

![Create SNS topic](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/create-sns-topic.png)

It's something like a _event listener_, when some events happen, the listener will perform certain actions.

The _action_ here means _subscription_, let's create one

![Create SNS subscription](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/create-subscription.png)

And here, I'm using webhook with HTTPS endpoint

![Configure SNS subscription](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/specify-endpoint.png)

Make sure the endpoint is available, SNS will ping it for confirmation

![SNS subscription confirmation](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/sns-subscription-created.png)

### 1.2. Configure SES to link with SNS

Go to **Notifications**, and edit

![Configure SES to use SNS](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/configure-ses-with-sns.png)

Set the _topic_ to what you created just now

![Configure SES to use SNS topic](/images/posts/2021-05-09-Enable-AWS-SES-in-Laravel-web-app/set-topic-to-ses.png)


## 2. Email Response Handler

Remember, we've specified an endpoint to the topic subscription?

Let's create the endpoint in **routes/web.php**. This will only be used by AWS

```php
<?php
Route::post('webhook/aws/ses-notification', ['uses' => 'SesController@sesNotification']);
```

Also, we need a table to keep all bounced emails

```
php artisan make:migration create_bounced_emails_table
```

with this schema

```php
<?php
Schema::create('bounced_emails', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->timestamps();
});
```

In **SesController.php**

```php
<?php
use App\Models\BouncedEmail;
use Illuminate\Http\Request;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use App\Http\Controllers\Controller;

class SesController extends Controller
{
    const NOTIFICATION_DELIVERY  = 'Delivery';

    const NOTIFICATION_BOUNCE    = 'Bounce';

    const NOTIFICATION_COMPLAINT = 'Complaint';

    public function sesNotification(Request $request)
    {
        if ($request->method() !== 'POST') {
            return response('405 (Accept POST Only)', 405)->header('Content-Type', 'text/plain');
        }

        try {
            // only process valid message from aws
            $messageRaw = Message::fromRawPostData();
            $validator   = new MessageValidator();

            // Note : Please Uncheck for Enable raw message delivery to comply with AWS SNS Validator format
            if (!$validator->isValid($messageRaw)) {
                \Log::info('Invalid AWS Message');

                return response('405 (Invalid AWS Message)', 405)->header('Content-Type', 'text/plain');
            }
            $messageRaw = $messageRaw->toArray();

            if ($messageRaw['Type'] === 'Notification') {
                $message = json_decode($messageRaw['Message'], 1);

                $this->updateMailList($message['notificationType'], $message['mail']['destination']);
            }
        } catch (\Exception $e) {
            // Handle exception here
        }

        return response('OK')->header('Content-Type', 'text/plain');
    }

    private function updateMailList($snsType, $recipients)
    {
        if ($snsType !== self::NOTIFICATION_BOUNCE) {
            return;
        }
        foreach ($recipients as $recipient) {
            $email = $this->extractEmail($recipient);
            if (empty($email)) {
                continue;
            }
            $lookup = BouncedEmail::find($email);
            if (empty($lookup)) {
                $lookup = new BouncedEmail();
                $lookup->email = $email;
                $lookup->save();
            }
        }
    }

    /**
     * Helper method to extract email
     * e.g. John Smith <john@smith.com>
     * @param string $recipient
     * @return string
     */
    private function extractEmail($recipient)
    {
        if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return $recipient;
        }
        $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
        if (preg_match($pattern, $recipient, $matches)) {
            return $matches[0];
        }
        return null;
    }
}
```

Here's the sample output of `$messageRaw`

```
Array
(
    [Type] => Notification
    [MessageId] => 11111111-2222-3333-4444-555555555555
    [TopicArn] => arn:aws:sns:ap-southeast-1:999999999999:ses-notification
    [Message] => {"notificationType":"Bounce","bounce":{"feedbackId":"aaaaaaaaaaaaaaaa-bbbbbbbb-cccc-dddd-eeee-ffffffffffff-000000","bounceType":"Permanent","bounceSubType":"OnAccountSuppressionList","bouncedRecipients":[{"emailAddress":"John <test123123123123123@gmail.com>","action":"failed","status":"5.1.1","diagnosticCode":"Amazon SES did not send the message to this address because it is on the suppression list for your account. For more information about removing addresses from the suppression list, see the Amazon SES Developer Guide at https://docs.aws.amazon.com/ses/latest/DeveloperGuide/sending-email-suppression-list.html"}],"timestamp":"2021-05-08T14:35:45.000Z","reportingMTA":"dns; amazonses.com"},"mail":{"timestamp":"2021-05-08T14:35:45.469Z","source":"noreply@yoursite.com","sourceArn":"arn:aws:ses:ap-southeast-1:999999999999:identity/noreply@yoursite.com","sourceIp":"202.202.202.202","sendingAccountId":"999999999999","messageId":"1111111111111111-22222222-3333-4444-5555-666666666666-000000","destination":["John <test123123123123123@gmail.com>"],"headersTruncated":false,"headers":[{"name":"Message-ID","value":"<99999999999999999999999999999999@swift.generated>"},{"name":"Date","value":"Sat, 08 May 2021 22:35:44 +0800"},{"name":"Subject","value":"YourSite: Test send mail"},{"name":"From","value":"YourSite <noreply@yoursite.com>"},{"name":"To","value":"John <test123123123123123@gmail.com>"},{"name":"MIME-Version","value":"1.0"},{"name":"Content-Type","value":"multipart/mixed; boundary=\"_=_swift_5555555555_77777777777777777777777777777777_=_\""}],"commonHeaders":{"from":["YourSite <noreply@yoursite.com>"],"date":"Sat, 08 May 2021 22:35:44 +0800","to":["John <test123123123123123@gmail.com>"],"messageId":"<99999999999999999999999999999999@swift.generated>","subject":"YourSite: Test send mail"}}}
    [Timestamp] => 2021-05-08T14:35:45.908Z
    [SignatureVersion] => 1
    [Signature] => UUUUUUUUUUUUUUUUUUUUUUUUUUUUU+kkkkkkkkkkk/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCC/zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz/eeeee/qqqqqqqq+BBB+nnnnnnnnnnnnnnnnnnnnnnnnnn/e/yyyyyyyyyyy+dddd+XXXXXXXXXXXXXXXXXXXXXXXXX/ww/rr+HHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH+g==
    [SigningCertURL] => https://sns.ap-southeast-1.amazonaws.com/SimpleNotificationService-99999999999999999999999999999999.pem
    [UnsubscribeURL] => https://sns.ap-southeast-1.amazonaws.com/?Action=Unsubscribe&SubscriptionArn=arn:aws:sns:ap-southeast-1:999999999999:ses-notification:11111111-2222-3333-4444-555555555555
)
```

### 3. Prevent to send to invalid email

In [Laravel Notification](https://laravel.com/docs/8.x/notifications) feature, there's a `via` method

```php
<?php
public function via($notifiable)
{
    $channels = [];
    $bounced = BouncedEmail::find($notifiable->routeNotificationForMail());
    if (empty($bounced)) { // only send if it's not in the bounced list
        $channels[] = 'mail';
    }

    return $channels;
}
```
