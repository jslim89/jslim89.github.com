---
layout: page
title: Apache
permalink: /short-notes/css/
date: 2020-05-22 21:13:51
comments: false
sharing: true
footer: true
---

#### Exclude certain object to the css styling

**Original**

```html
...
<style>
  td span { border: 1px solid; display: block; }
</style>
...
<table>
  <tr><td><span class="foo">This want to be excluded from Internal Style</span></td></tr>
  <tr><td><span>This want to be follow the Internal Style</span></td></tr>
</table>
...
```

Let say I want to exclude the span with the class `foo`, just change to

```css
td span:not(.foo) { border: 1px solid; display: block; }
```

##### Reference:

- [How to exclude a CSS formatting?](http://stackoverflow.com/questions/6585924/how-to-exclude-a-css-formatting#answers)

---

#### Add a comma to `li` except for the last 1

```html
<ul>
    <li>alpha</li>
    <li>beta</li>
    <li>lambda</li>
    <li>kapa</li>
    <li>gamma</li>
</ul>
```
The desire output is `alpha, beta, lambda, kapa, gamma`. Add a comma to every `li` expect for the last item

```css
ul {
    line-height: 0;
}
li:not(:last-child):after {
    content: ', ';
}
```

##### Reference:

- [CSS :not(:last-child):after selector](http://stackoverflow.com/questions/5449872/css-notlast-childafter-selector)

---

#### CSS3 odd & even

Take a table rows as example

```css
table tr:nth-child(even) {
    background-color: #dfdfdf;
}
table tr:nth-child(odd) {
    background-color: #b38a2f;
}
```

##### Reference:

- [EVEN AND ODD RULES](http://www.w3.org/Style/Examples/007/evenodd.en.html)

---

#### Position a `div` on top of `img`

```html
<div id="container">
    <img src="http://example.com/img.jpg">
    <div id="inner">This is my div</div>div>
</img>div>
```

Set the container position to `relative`, and the inner div to `absolute`.

```css
#container {
    position: relative;    
}

#inner {
   position: absolute;
   top: 10px; // position y
   left: 10px; // position x
    
   padding: 5px;
   background-color: white;
   border: 2px solid red;
}
```

##### Reference:

- [position a div on top of an image](http://stackoverflow.com/questions/4218204/position-a-div-on-top-of-an-image/4218306#4218306)

---

#### Background image 100%

```css
#wrapper {
    background: url('../img/bg.jpg') no-repeat center center fixed; 
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}
```

##### Reference:

- [Perfect Full Page Background Image](http://css-tricks.com/perfect-full-page-background-image/)

---

#### Add border to text

Add white border with 2px width

```css
text-shadow: 2px 0 0 #fff, -2px 0 0 #fff, 0 2px 0 #fff, 0 -2px 0 #fff, 1px 1px #fff, -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff;
```

##### Reference:

- [Text border using css (border around text)](http://stackoverflow.com/questions/13426875/text-border-using-css-border-around-text/13427256#13427256)

---

#### Set 100% height by CSS only

Set the height of a `div` relative to `body`

```css
#slideshow {
    height: 100vh;
}
```

##### Reference:

- [How to Make Div Element 100% Height of Browser Window Using CSS Only](http://stanislav.it/how-to-make-div-element-100-height-of-browser-window-using-css-only/)

---

#### Google map zoom control not showing properly

```css
.gmnoprint img {
    max-width: none; 
}
```

##### Reference:

- [google map zoom controls not displaying correctly](https://stackoverflow.com/questions/9904379/google-map-zoom-controls-not-displaying-correctly/18723355#18723355)

---

#### Long text word wrap issue

Add this zero-width space character to anywhere of the string

```
&#8203;
```

Another better solution would be implemented in CSS

```css
 -ms-word-break: break-all;
     word-break: break-all;

     /* Non standard for webkit */
     word-break: break-word;

-webkit-hyphens: auto;
   -moz-hyphens: auto;
    -ms-hyphens: auto;
        hyphens: auto;
```

##### Reference: 

- [wordwrap a very long string](http://stackoverflow.com/questions/856307/wordwrap-a-very-long-string/856322#856322)
- [Word wrapping/hyphenation using CSS.](http://kenneth.io/blog/2012/03/04/word-wrapping-hypernation-using-css/)

---

#### Hide scrollbar

```css
.container {
    scrollbar-width: none; // for firefox
}
.container::-webkit-scrollbar { // for chrome/safari
    width: 0;
    height: 0;
}
```

---

#### Enforce image height according to width

This can be done by setting the aspect ratio

```html
<div class="container">
    <a href="#">
        <img src="/path/to/img1.jpg">
    </a>
    <a href="#">
        <img src="/path/to/img2.jpg">
    </a>
    <a href="#">
        <img src="/path/to/img3.jpg">
    </a>
</div>
```

```css
.container {
  display: flex;

  a {
    flex-grow: 1;
    flex-basis: 0;
    --aspect-ratio: 1; // <---- SET THIS

    img {
      width: 100%;
      height: 100%; // <----- set the height to 100%
      object-fit: cover;
    }
  }
}
```

##### Reference:

- [Aspect Ratio Boxes](https://css-tricks.com/aspect-ratio-boxes/)

---

### Bootstrap

#### Set the width for navbar to collapse

In this example shows the navbar will collapse when the width is **1200px**

```css
@media (max-width: 1200px) {
    .navbar-header {
        float: none;
    }
    .navbar-left,.navbar-right {
        float: none !important;
    }
    .navbar-toggle {
        display: block;
    }
    .navbar-collapse {
        border-top: 1px solid transparent;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
    }
    .navbar-fixed-top {
        top: 0;
        border-width: 0 0 1px;
    }
    .navbar-collapse.collapse {
        display: none!important;
    }
    .navbar-nav {
        float: none!important;
        margin-top: 7.5px;
    }
    .navbar-nav>li {
        float: none;
    }
    .navbar-nav>li>a {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .collapse.in{
        display:block !important;
    }
}
```

**NOTE: For Bootstrap 3.1**

##### Reference:

- [Twitter Bootstrap 3 navbar-collapse - set width to collapse](http://stackoverflow.com/questions/19703550/twitter-bootstrap-3-navbar-collapse-set-width-to-collapse/19705550#19705550)

---

### jQuery UI

#### Make autocomplete result scrollable

```css
/* highlight results */
.ui-autocomplete span.hl_results {
    background-color: #ffff66;
}
 
/* loading - the AJAX indicator */
.ui-autocomplete-loading {
    background: white url('../img/ui-anim_basic_16x16.gif') right center no-repeat;
}
 
/* scroll results */
.ui-autocomplete {
    max-height: 250px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
    /* add padding for vertical scrollbar */
    padding-right: 5px;
}
 
.ui-autocomplete li {
    font-size: 16px;
}
 
/* IE 6 doesn't support max-height
* we use height instead, but this forces the menu to always be this tall
*/
* html .ui-autocomplete {
    height: 250px;
}
```

##### Reference:

- [How to Create a jquery-ui Autocomplete Step by Step](http://www.pontikis.net/blog/jquery-ui-autocomplete-step-by-step)
