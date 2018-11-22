---
layout: post
title: "Bootstrap 3 dropdown menu not showing on mobile browser"
date: 2014-04-29 08:46:15 +0800
comments: true
tags: 
- css
- bootstrap3
---

I believe that most of you had face this kind of problem. [Bootstrap 3 dropdown menu](http://getbootstrap.com/components/#btn-dropdowns)
is clickable on desktop browser, but when comes to mobile, then it has no effect at all.

**index.html**

```html
<div id="main-wrapper">
    <div id="bg-holder">
        <div class="navbar" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button id="btn-navbar-toggle" type="button" class="navbar-toggle bg bg-green" data-toggle="collapse" data-target=".navbar-collapse">
                        Menu
                    </button>
                    <a class="navbar-brand" href="/">Logo</a>
                </div>
                <div class="collapse navbar-collapse bs-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a class="bg bg-green" href="/">Home</a></li>
                        <li><a class="bg bg-green" href="/page1.php">Page 1</a></li>
                        <li><a class="bg bg-green" href="/page2.php">Page 2</a></li>
                        <li><a class="bg bg-green" href="/page3.php">Page 3</a></li>
                        <li><a class="bg bg-green" href="/page4.php">Page 4</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>

        <div id="pg-1" class="container common-wrapper">
            <div class="row">
                <div class="col-xs-12 pole-content-holder">
                    <div class="pole left"></div>
                    <div class="pole right"></div>
                    <div id="pole-heading" class="center-div">
                        <h1>
                            Page 1 <img src="/assets/img/icon-pg1.png"/>
                            <!-- Not touchable on mobile -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    Item 1 <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/index.html">Item 1</a></li>
                                    <li><a href="/index.html?item=2">Item 2</a></li>
                                    <li><a href="/index.html?item=3">Item 3</a></li>
                                    <li><a href="/index.html?item=4">Item 4</a></li>
                                </ul>
                            </div>
                        </h1>
                    </div>
                    ...
```

I've googled for hours, even try to modify the **bootstrap.min.js** file, it still doesn't works.

Then I try to bind a "click" event on that button

i.e.

```js
$('.dropdown-toggle').click(function() {
    alert('dropdown clicked');
});
```

The alert works on desktop, but has no effect on mobile browser.

Then I try to move the whole chunk of html to the top

i.e.

```html
<div id="main-wrapper">
                            <!-- Move to here -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    Item 1 <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/index.html">Item 1</a></li>
                                    <li><a href="/index.html?item=2">Item 2</a></li>
                                    <li><a href="/index.html?item=3">Item 3</a></li>
                                    <li><a href="/index.html?item=4">Item 4</a></li>
                                </ul>
                            </div>
    <div id="bg-holder">
        <div class="navbar" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button id="btn-navbar-toggle" type="button" class="navbar-toggle bg bg-green" data-toggle="collapse" data-target=".navbar-collapse">
                        Menu
                    </button>
                    <a class="navbar-brand" href="/">Logo</a>
                </div>
                <div class="collapse navbar-collapse bs-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a class="bg bg-green" href="/">Home</a></li>
                        <li><a class="bg bg-green" href="/page1.php">Page 1</a></li>
                        <li><a class="bg bg-green" href="/page2.php">Page 2</a></li>
                        <li><a class="bg bg-green" href="/page3.php">Page 3</a></li>
                        <li><a class="bg bg-green" href="/page4.php">Page 4</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>

        <div id="pg-1" class="container common-wrapper">
            <div class="row">
                <div class="col-xs-12 pole-content-holder">
                    <div class="pole left"></div>
                    <div class="pole right"></div>
                    <div id="pole-heading" class="center-div">
                        <h1>
                            Page 1 <img src="/assets/img/icon-pg1.png"/>
                        </h1>
                    </div>
                    ...
```

The html above works, then I try move the dropdown menu down to the DOM element level by level.
Finally, I found out this might be the view overlapping issue. Then I realize the `.navbar` **z-index**
was set to _1000_. i.e.

```css
.navbar {
    z-index: 1000;
    ...
}
```

Now, I move the dropdown menu to the original place, then change the css

```css
.pole-content-holder > #pole-heading {
    z-index: 1001;
}
```

Just **ONE** line of code, solve my issue that makes me struggle for hours. This may not be exactly your case,
I'm just share how I debug and found the solution, hope this can be a guideline for you :)
