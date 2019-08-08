---
title: The efficient way to debug JS/CSS in production site
date: 2019-08-08 19:52:22
tags:
- js
- css
- debug
- web-dev
---

Recently I discovered [this package](https://github.com/luciopaiva/witchcraft), which allows us to run JavaScript & css in any domain.

Before I know about this, I debug JavaScript through [Chrome console](https://developers.google.com/web/tools/chrome-devtools/console/).
But it's quite troublesome, when I want to run a whole chunk of JavaScript code.

With this package, I can just write the code in a JS file according the domain, e.g. **google.com.js**,
so long the URL matched `google.com`, it will execute the code from **google.com.js**.

_(You can find the google sample code from the installation guide)_

## Another use case

Another use case for myself, is I can use to scrap website content, and test my script here.

I use [Puppeteer](https://github.com/GoogleChrome/puppeteer) to scrap others site content, and use JavaScript to access their DOM.
_(refer to my [simple module](https://github.com/jslim89/site-scraper))_

Everytime I want to test and see the output, I have to keep running the command over & over again. e.g.

```js
...
var body = await page.evaluate(() => {
    // JS code goes here
});
```

Running the script take longer time, as need to instantiate the browser, then load the page, evaluate the script, close browser.

With **Witchcraft**, I can now write the `// JS code goes here` to mydomain.com.js, and view the output in console.

After the output correct, then I just copy the JS code over to the scraper.

I've tried this on one of the eCommerce website

http://shopee.com.my/search?keyword=gopro

And I able to get output

![Shopee search content](/images/posts/2019-08-08-The-efficient-way-to-debug-JS-CSS-in-production-site/shopee-content.png)

The JavaScript code is pretty long

```js
setTimeout(function() {
  var productCards = document.querySelectorAll('.shopee-search-item-result__items > .shopee-search-item-result__item'), i;

  var data = [];
  for (i = 0; i < productCards.length; ++i) {
    var productCard = productCards[i];
    var anchor = productCard.querySelector('a');

    // ...

    data.push({
      'id': productId,
      'name': productName,
      'link': productLink,
      'price': price,
    });
  }

  console.log(data);
}, 3000);
```

You won't want to write these in Chrome console right? 😏

## References

- [Install Witchcraft](https://luciopaiva.com/witchcraft/how-to-install.html)
