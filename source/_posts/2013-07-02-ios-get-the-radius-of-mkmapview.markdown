---
layout: post
title: "iOS - Get the radius of MKMapView"
date: 2013-07-02 14:27
comments: true
categories: 
- ios
---

Some time we might have too much locations want to show to user, unfortunately, it may cause some performance issue if load all locations in one short.

One of the solution is lazy loading, only load the visible location in map. Thus, in order to achieve this, we need **Center Coordinate** and **"Radius"** _(actually it is not radius, but just call it radius)_

To get center coordinate
```obj-c
- (CLLocationCoordinate2D)getCenterCoordinate
{
    CLLocationCoordinate2D centerCoor = [self.mapView centerCoordinate];
    return centerCoor;
}
```

For getting radius, depends on where you want to get the 2nd point. Lets take the **Top Center**
```obj-c
- (CLLocationCoordinate2D)getTopCenterCoordinate
{
    // to get coordinate from CGPoint of your map
    CLLocationCoordinate2D topCenterCoor = [self.mapView convertPoint:CGPointMake(self.mapView.frame.size.width / 2.0f, 0) toCoordinateFromView:self.mapView];
    return topCenterCoor;
}
```

To get the radius in **meter**
```obj-c
- (CLLocationDistance)getRadius
{
    CLLocationCoordinate2D centerCoor = [self getCenterCoordinate];
    // init center location from center coordinate
    CLLocation *centerLocation = [[CLLocation alloc] initWithLatitude:centerCoor.latitude longitude:centerCoor.longitude];
    
    CLLocationCoordinate2D topCenterCoor = [self getTopCenterCoordinate];
    CLLocation *topCenterLocation = [[CLLocation alloc] initWithLatitude:topCenterCoor.latitude longitude:topCenterCoor.longitude];

    CLLocationDistance radius = [centerLocation distanceFromLocation:topCenterCoor];

    return radius;
}
```

### Alternative

Another way for getting the radius is to apply **Pythagorean theorem**, which is to get 3 points, **Top Left**, **Top Right** and **Center**.

Then we calculate the 3 distances base on this 3 points

```obj-c
- (CLLocationDistance)getRadius
{
    // get center coordinate
    CLLocationCoordinate2D centerCoor = [self.mapView centerCoordinate];
    CLLocation *centerLocation = [[CLLocation alloc] initWithLatitude:centerCoor.latitude longitude:centerCoor.longitude];
    
    // get top left coordinate
    CLLocationCoordinate2D topLeftCoor = [self.mapView convertPoint:CGPointMake(0, 0) toCoordinateFromView:self.mapView];
    CLLocation *topLeftLocation = [[CLLocation alloc] initWithLatitude:topLeftCoor.latitude longitude:topLeftCoor.longitude];
    
    // get top right coordinate
    CLLocationCoordinate2D topRightCoor = [self.mapView convertPoint:CGPointMake(self.mapView.frame.size.width, 0) toCoordinateFromView:self.mapView];
    CLLocation *topRightLocation = [[CLLocation alloc] initWithLatitude:topRightCoor.latitude longitude:topRightCoor.longitude];
    
    // the distance from center to top left
    CLLocationDistance hypotenuse = [centerLocation distanceFromLocation:topLeftLocation];

    // half of the distance from top left to top right
    CLLocationDistance x = [topLeftLocation distanceFromLocation:topRightLocation] / 2.0f;
    
    // what we want is this
    CLLocationDistance y = sqrt(pow(hypotenuse, 2.0) - pow(x, 2.0));
    NSLog(@"h² = x² + y²");
    NSLog(@"y² = h² - x²");
    NSLog(@"y = sqrt(h² - x²)");
    NSLog(@"%.9f = sqrt(%.9f² - %.9f²)", y, hypotenuse, x);

    return y;
}
```

{% img http://jslim89.github.com/images/posts/2013-07-02-ios-get-the-radius-of-mkmapview/pythagorean-theorem.png Pythagorean theorem %}

_References:_

* _[Pythagorean theorem](http://en.wikipedia.org/wiki/Pythagoras#Pythagorean_theorem)_
