---
layout: post
title: "Integrate iPay88 to iOS mobile platform"
date: 2015-03-31 13:36:44 +0800
comments: true
categories: 
- ios
- php
- swift
- payment-gateway
---

The first payment gateway I dealed with was PayPal. At that moment,
I found it sucks, very tedious to integrate into iOS platform.
Until now, I need to work with [iPay88](http://www.ipay88.com/), which
has no native iOS SDK, and the process even more tedious than PayPal.
I share this because of the toughness &amp; not easy to find any
resources on web. Hope you enjoy.

## 1. Register a merchant account with iPay88

You will get **Merchant Code** and **Merchant Key**.

After that, submit a **request URL** to iPay88 support. Where the **request
URL** is the page that you submit to iPay88 site, you will know later.

## 2. Create a page that support submit payment info to iPay88

Create a page like below _(just an example)_.

**payment.php**

```html
<!DOCTYPE html>
<html>
    <head>
        <title>Payment...</title>
    </head>
    <body>
        <h1>Processing Payment...</h1>
        <form method="POST" action="https://www.mobile88.com/epayment/entry.asp" id="frm-submit">
            <p>If this page does not redirect to payment gateway in seconds, please click RETRY button below</p>
            <button type="Submit">RETRY</button>
            <input name="RefNo" type="hidden" value="REF000001">
            <input name="Amount" type="hidden" value="1.00">
            <input name="Signature" type="hidden" value="wsE29QS6EaqI7izfDIRPv6ku/MA=">
            <input name="ProdDesc" type="hidden" value="Buy RM1.00 credit">
            <input name="UserName" type="hidden" value="Js Lim">
            <input name="UserEmail" type="hidden" value="js@lim.com">
            <input name="UserContact" type="hidden" value="0123456789">
            <input name="MerchantCode" type="hidden" value="M00001">
            <input name="Currency" type="hidden" value="MYR">
            <input name="ResponseURL" type="hidden" value="http://mysite.com/response.php">
            <input name="BackendURL" type="hidden" value="http://mysite.com/backend.php">
        </form>
        <script type="text/javascript">
            setTimeout(function() {
                document.getElementById('frm-submit').submit();
            }, 100);
        </script>
    </body>
</html>
```

The **Signature** has a formula to generate, will be provided from iPay88

```php
<?php
function iPay88_signature($ref_no, $amount) {
    $key = 'apple';
    $code = 'M00001';
    $currency = 'MYR';
    $source = sprintf('%s%s%s%s%s', $key, $code, $ref_no, $amount, $currency);
    return base64_encode(hex2bin(sha1($source)));
}

function hex2bin($hexSource) {
    $bin = '';
    for ($i=0;$i<strlen($hexSource);$i=$i+2) {
        $bin .= chr(hexdec(substr($hexSource,$i,2)));
    }
    return $bin;
}
```

A hint here, if your amount is

```html
<input name="Amount" type="hidden" value="1.00">
```

then your signature source must be _appleM00001REF000001**100**MYR_

if amount is

```html
<input name="Amount" type="hidden" value="1">
```

then your signature source must be _appleM00001REF000001**1**MYR_

If you notice that all the inputs are hardcoded, please don't do this
to your site, you can change to variable from query string. e.g.

```php
<input name="RefNo" type="hidden" value="<?php echo $_REQUEST['ref']; ?>">
```

Then you access in browser: http://mysite.com/payment.php

So in this case, the **request URL** is http://mysite.com/payment.php

But wait a minute, during the development, the URL should be **localhost**.
Don't worry, we can use virtual host here, you can refer this in [my other
blog post](http://jslim.net/blog/2014/01/17/setup-php-environment-in-mavericks-using-xampp/)
section 3. Make sure you put the real url to **/etc/hosts**

```
127.0.0.1        mysite.com
```

So when you access http://mysite.com, you are not refering to the live site,
but the development site in your local machine, now you can test with
iPay88.

#### This cannot be done in cURL
I have tried using cURL as well, in fact it need to be a POST request in a webpage form.

## 3. Create a response handler

**response.php**

```php
<?php
if (isset($_REQUEST['ErrDesc'])) {
    redirect('failure.php');
}
if (isset($_REQUEST['Signature']) && $_REQUEST['Signature'] != $my_signature) {
    // NOTE that $my_signature you can store it in session before submit the form to iPay88
    redirect('failure.php'); // for security purpose
}

// store the info returned by iPay88, you will get this in the document provided by iPay88

// after process everything, redirect to success page
redirect('success.php');

function redirect($url) {
    header('Location: ' . $url);
    exit;
}
```

The file above will be a POST request, if you don't want to use `$_REQUEST`, you can use `$_POST`

### Bare in mind _(Updated: May 12, 2015)_

Please take note on the **backend.php**

Sometime, there are some weird issue that may caused the **response.php** not working correctly. Thus, **backend.php** is needed here _(I have faced the issue with CIMB click)_.

The **backend.php** will not work if you check for login session. e.g.

```php
<?php
if (is_loggedin()) exit; // will exit here

// code won't execute
...
```

That means you cannot deal with any session here _(backend.php)_

## 4. Create a success & failure page

**success.php**

```html
<button type="button" onclick="window.location.href='com.mysite.myapp://successClicked';">Done</button>
```

**failure.php**

```php
<button type="button" onclick="window.location.href='com.mysite.myapp://closeClicked';">Close</button>
```

See **com.mysite.myapp**? Is this weird to you? If you have use [Waze API](https://www.waze.com/about/dev)
you will see `waze://?q=<address search term>` this, `waze://` is the custom scheme that only
use in Waze app. Same to this, `com.mysite.myapp://` is only use in this app

## 5. Show the page above in your app using `UIWebView`

Create a custom web view to handle payment

**PaymentWebViewController.m**

```obj-c
#import "PaymentWebViewController.h"

@interface PaymentWebViewController ()

@property (nonatomic, weak) UIWebView *webView;
@property (nonatomic, strong) UIWebView *popupWebView;

@end

@implementation PaymentWebViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // add in a refresh button
    self.navigationItem.rightBarButtonItem = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemRefresh target:self action:@selector(refreshButtonTapped:)];
    
    UIWebView *webView = [[UIWebView alloc] initWithFrame:self.view.bounds];
    webView.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin | UIViewAutoresizingFlexibleHeight | UIViewAutoresizingFlexibleTopMargin;
    webView.delegate = self;
    [self.view addSubview:webView];
    _webView = webView;
    

    NSDictionary *params = @{
                             @"ref": @"REF000001"
                             , @"amount": @"1.00"
                             // ...
                             }
    NSURLRequest *request = [[NSURLRequest alloc] initWithURL:[NSURL URLWithString:[NSString stringWithFormat:@"http://mysite.com/payment.php?%@", [self httpBuildQuery:params]]]];
    [_webView loadRequest:request];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}


- (NSString *)httpBuildQuery:(NSDictionary *)params
{
    NSMutableArray *parts = [NSMutableArray array];
    for (id key in params) {
        id value = params[key];
        
        NSString *keyString = [NSString stringWithFormat:@"%@", key];
        NSString *valueString = [NSString stringWithFormat:@"%@", value];
        
        NSString *part = [NSString stringWithFormat: @"%@=%@", [keyString stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding], [valueString stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding]];
        [parts addObject: part];
    }
    return [parts componentsJoinedByString: @"&"];
}

- (void)refreshButtonTapped:(id)sender
{
    [_webView reload];
}

- (BOOL)webView:(UIWebView *)webView shouldStartLoadWithRequest:(NSURLRequest *)request navigationType:(UIWebViewNavigationType)navigationType
{
    // Update: May 12, 2015
    // a hack to solve the Maybank2u TAC popup window issue
    if ([request.URL.path containsString:@"m2uTACProcess.do"]) {

        // init a new webview (as a popup)
        _popupWebView = [[UIWebView alloc] initWithFrame:CGRectMake(0, 0, 280, 400)];
        [_popupWebView loadRequest:request];
        
        // show it in a popup, I tested with https://github.com/rnystrom/RNBlurModalView

        return NO;
    }

    if ([[request.URL scheme] isEqual:@"com.mysite.myapp"]) {
        NSArray *component = [request.URL.description componentsSeparatedByString:@"://"];
        if ([component[1] isEqual:@"closeClicked"]) { // com.mysite.myapp://closeClicked
            // action to perform after payment failed
        }
        if ([component[1] isEqual:@"successClicked"]) { // com.mysite.myapp://successClicked
            // action to perform after success
        }
        return NO;
    }
    return YES;
}
@end
```

See **com.mysite.myapp** again? We detect if the URL scheme is `com.mysite.myapp`, then
perform some action.

When the time your app open this web view, you will see iPay88 page. The whole integration is done.
