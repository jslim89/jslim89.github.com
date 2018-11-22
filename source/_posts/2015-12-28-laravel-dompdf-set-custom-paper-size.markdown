---
layout: post
title: "Laravel-dompdf set custom paper size"
date: 2015-12-28 16:56:10 +0800
comments: true
tags: 
- php
- laravel
---

Often, many web application required functions like _export to pdf_, but not all the time is [A4 paper size](http://www.papersizes.org/a-paper-sizes.htm).

I'm using [laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) for the project. I will not talk about the setup. Let's see the code directly.

**PdfController.php**

```php
<?php
$file = 'download-file-name.pdf';

$pdf = \PDF::loadView('download.pdf', ['data' => $some_data])
    ->setPaper('a4', 'landscape');
return $pdf->download($file);
```

The code above, will generate & download the pdf file in A4 paper size. But what if different paper size is needed?

You can set the paper size to any size you want

```php
$pdf->setPaper([0, 0, 685.98, 396.85], 'landscape');
```

_(Bare in mind that the value is in point unit)_

You can measure the paper with your ruler.

![Measure paper size](/images/posts/2015-12-28-laravel-dompdf-set-custom-paper-size/measure-paper.jpg)

But what you measure is in centimeter _(cm)_ or millimeter _(mm)_, so you can ask google to convert the unit for you

![Google converter](/images/posts/2015-12-28-laravel-dompdf-set-custom-paper-size/google-converter.png)

Done.

References:

- [dompdf - usage](https://code.google.com/p/dompdf/wiki/Usage)
