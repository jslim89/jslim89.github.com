---
layout: post
title: "How to add Google Map to your web page"
date: 2014-06-15 14:27:11 +0800
comments: true
tags: 
- javascript
- google-map
---

# Introduction
Here is to shows you how can you add google map to your web page by JavaScript _(not using iframe)_. Besides, you will also learn about searching, and marker dragging.

## 1. Obtaining an Google Map API key
Just visit [this page](https://developers.google.com/maps/documentation/javascript/tutorial#api_key), it will shows the steps on how to obtain an API key.

## 2. Add a canvas to your web page (html)

```html
<style>
#map {
    width: 100%;
    height: 400px;
}
</style>

<div id="map"></div>

<script src="https://maps.googleapis.com/maps/api/js?key={API_KEY}"></script>

<script>
// Map setup
var latlng = new google.maps.LatLng(3.12345, 101.12345);
var mapOptions = {
    zoom: 8, // set the map zoom level
    center: latlng, // set the center region
    disableDefaultUI: true,
    zoomControl: true, // whether to show the zoom control beside
    mapTypeId: google.maps.MapTypeId.ROADMAP
};
var map = new google.maps.Map(document.getElementById('map'), mapOptions);
</script>
```

Just add the code above to your page, and refresh your browser...tadaaa~ Google Map is appear on your page now.

## 2. Add a marker (or pin) to your location

```js
// ...
var map = new google.maps.Map(document.getElementById('map'), mapOptions);

// ADD THIS
var marker = new google.maps.Marker({
    map: map, // refer to the map you've just initialise
    position: latlng, // set the marker position (is based on latitude & longitude)
    draggable: true // allow user to drag the marker
});

```

Now, add the block of code after `var map = ...`. Then refresh your browser again, then you will see

![Google map with marker](http://jslim89.github.com/images/posts/2014-06-15-how-to-add-google-map-to-your-web-page/map.png)

You can even customize your marker

```js
var marker = new google.maps.Marker({
    map: map,
    position: latlng,
    draggable: true,
    icon: 'https://maps.google.com/mapfiles/kml/shapes/schools_maps.png' // ADD THIS
});
```
Just add one more line, you will see a different marker. Replace the `icon` URL to any URL that you want to show.

## 3. Add a search function
Add a search input above the map _(or anywhere your prefer)_

``` html

<!-- ADD THIS -->
<input type="text" id="address" placeholder="Address" style="width: 100%;"/>

<div id="map"></div>
...

```

Then add an event handler, so that when user hit `<ENTER>` key, it search

```js

// ...
var marker = new google.maps.Marker({
    map: map,
    position: latlng,
    draggable: true
});

// ADD LINES BELOW
// add an event handler
document.getElementById('address').onkeyup = function(e) {
    if(e.keyCode == 13) { // keyCode 13 represent <ENTER> key
        geocoding(this.value); // get the value from address text box and pass to the search function
        return false;
    }
}

var geocoder = new google.maps.Geocoder();
function geocoding(keyword) {
    geocoder.geocode({address: keyword}, function(results, status) {
        if(status == google.maps.GeocoderStatus.OK) { // if got results
            // always take the first result only
            map.setCenter(results[0].geometry.location); // set the map region to center
            marker.setPosition(results[0].geometry.location); // change the marker position
        } else {
            // if no result, just alert user
            alert('Location not found.');
        }
    });
}

```

## 4. What if you want to keep the latitude & longitude?

Add 2 more inputs after search box _(or anywhere you prefer)_. In this case use a text box, just for you to see, usually end user don't want to see this kind of data, is better to keep it hidden. _(e.g. `<input type="hidden" id="lat" value="3.1234" />`)_

```html

<input type="text" id="address" placeholder="Address" style="width: 100%;"/>

<!-- ADD THIS -->
Lat: <input type="text" id="lat" value="3.1234"/>
Lng: <input type="text" id="lng" value="101.1234"/>

```

Now, you want to set the latitude & longitude when:

- user drag the marker
- user search by address

### User drag the marker

Add an event handler to the marker.

```js

function geocoding(keyword) {
    // ...
}

// ADD THIS
google.maps.event.addListener(marker, 'dragend', function() {
    // it will run this only if user DROP the marker down (drag end)
    var position = marker.getPosition();
    // set the position value to text boxes
    document.getElementById('lat').value = position.lat();
    document.getElementById('lng').value = position.lng();
});

```

### User search by address

Just now we already add an event handler to search remember? Now just add some code to `geocoding` function

```js

function geocoding(keyword) {
    geocoder.geocode({address: keyword}, function(results, status) {
        if(status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            marker.setPosition(results[0].geometry.location);

            // ADD THIS
            document.getElementById('lat').value = results[0].geometry.location.lat();
            document.getElementById('lng').value = results[0].geometry.location.lng();
        } else {
            alert('Location not found.');
        }
    });
}

```

## 5. You are done. See the result _(for illustration purpose only)_

<style>#map img { max-width: none; }</style>

Lat: <input type="text" id="lat" value="3.1234" class="form-control"/>
Lng: <input type="text" id="lng" value="101.1234" class="form-control"/>

<div id="map" style="height: 400px;"></div>

<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>
var latlng = new google.maps.LatLng(3.12345, 101.12345);
var mapOptions = {
    zoom: 8,
    center: latlng,
    disableDefaultUI: true,
    zoomControl: true,
    mapTypeId: google.maps.MapTypeId.ROADMAP
};
var map = new google.maps.Map(document.getElementById('map'), mapOptions);
var marker = new google.maps.Marker({
    map: map,
    position: latlng,
    draggable: true,
    icon: 'http://jslim89.github.com/images/posts/2014-06-15-how-to-add-google-map-to-your-web-page/marker-js-mini.png'
});
var geocoder = new google.maps.Geocoder();

function SearchControl() {
    var text = $('<input type="text" class="input-text form-control" placeholder="Search..." style="width:200px;" />');

    $(text).keypress(function(e){
        if(e.keyCode == 13) {
            geocoding(text.val());
            return false;
        }
    }).focus(function(){
        $(this).select();
    });

    return text.get(0);
}

function geocoding(keyword) {
    geocoder.geocode({address: keyword}, function(results, status) {
        if(status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            marker.setPosition(results[0].geometry.location);
            document.getElementById('lat').value = results[0].geometry.location.lat();
            document.getElementById('lng').value = results[0].geometry.location.lng();
        } else {
            alert('Location not found.');
        }
    });
}
map.controls[google.maps.ControlPosition.TOP_RIGHT].push(new SearchControl());
google.maps.event.addListener(marker, 'dragend', function() {
    var position = marker.getPosition();
    document.getElementById('lat').value = position.lat();
    document.getElementById('lng').value = position.lng();
});
</script>

_References:_

* _[Customizing Google Maps: Custom Markers](https://developers.google.com/maps/tutorials/customizing/custom-markers)_
