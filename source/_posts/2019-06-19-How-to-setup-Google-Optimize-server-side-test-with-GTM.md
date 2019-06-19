---
title: How to setup Google Optimize server side test with GTM
date: 2019-06-19 20:22:53
tags:
- google-optimize
- google-analytics
- ab-test
---

Recently I come across A/B testing, to see which algorithm is most effective to trigger user clicks.

Here I use [Google Optimize](https://optimize.google.com/), setup via
[Google Tag Manager](https://tagmanager.google.com/) to do the job.

### 1. Setup Google Optimize

I assume you have Google Analytics ready. Let's create an experiment, and select the type **A/B test**.

![Google Optimize setup](/images/posts/2019-06-19-How-to-setup-Google-Optimize-server-side-test-with-GTM/google-optimize.png)

Create a variant, put in whatever name you like. Then set **Page Targeting** to
**URL** _equals_ `SERVER_SIDE`. Link up with GA & set the objective

### 2. Setup in GTM

#### 1. Create Data Layer variables

![GTM data layer variables](/images/posts/2019-06-19-How-to-setup-Google-Optimize-server-side-test-with-GTM/gtm-data-layer.png)

Create 2 variables: experiment ID & variant

![GTM data layer variables - experiment ID](/images/posts/2019-06-19-How-to-setup-Google-Optimize-server-side-test-with-GTM/gtm-data-layer-expid.png)

![GTM data layer variables - variant](/images/posts/2019-06-19-How-to-setup-Google-Optimize-server-side-test-with-GTM/gtm-data-layer-expvar.png)

#### 2. Create new tag for Google Analytics (if you don't have)

![GTM tag - google analytics](/images/posts/2019-06-19-How-to-setup-Google-Optimize-server-side-test-with-GTM/gtm-ga-fields-to-set.png)

- Check the option **Enable overriding settings in this tag**
- Add 2 variables to **Fields to Set**
- Set the trigger to All Page

![GTM tag - google analytics](/images/posts/2019-06-19-How-to-setup-Google-Optimize-server-side-test-with-GTM/gtm-pageview-tag.png)

The fields to set is allow you to define which experiment & which variant to run.

### 3. Coding

In server side _(I use PHP here)_, randomly choose a variant, and select the algorithm to use

```php
<?php
$exp_var = rand(0, 1);
$exp_id = 'xxxxxxxxxx';
if ($exp_var == 1) {
    // if variant 1, which algorithm to use
} else {
    // by default, which algorithm to use
}
```

In front end, trigger the experiment manually, by setting the `dataLayer` variable

```html
<script>
<!-- Google Tag Manager -->
var dataLayer = [];
function gtag() {dataLayer.push(arguments)}

dataLayer.push({
    'gaExpId': '<?php echo $expid; ?>',
    'gaExpVar': '<?php echo $exp_var; ?>',
});

(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-1234567');
</script>
```

Note that the `dataLayer.push` must before the GTM script.

If you have any questions, [please ask here](https://support.google.com/optimize/community).

## References:

- [Optimize - Server-side Experiments](https://developers.google.com/optimize/devguides/experiments)
- [Optimize JavaScript API](https://support.google.com/optimize/answer/9059383?hl=en)
- [Google Tag Manager & Optimize Server-Side experiment sending variation](https://stackoverflow.com/questions/48386350/google-tag-manager-optimize-server-side-experiment-sending-variation/52157837#52157837)
