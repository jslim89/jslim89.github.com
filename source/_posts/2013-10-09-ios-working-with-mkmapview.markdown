---
layout: post
title: "iOS - Working with MKMapView"
date: 2013-10-09 11:45
comments: true
categories: 
- ios
---

Here I want to discuss about using **map** in iOS application

### First, import `MapKit`

```obj-c ViewController.h
#import <MapKit/MapKit.h>
```

### Then make sure your `ViewController` conform to `MKMapViewDelegate`

```obj-c ViewController.h
@interface ViewController : UIViewController <MKMapViewDelegate>

@property (nonatomic, strong) MKMapView *mapView;
// ...
@end
```

### In your `ViewController.m`, create and place the map in `ViewController`

```obj-c ViewController.m
// ...
- (void)viewDidLoad
{
    [super viewDidLoad];

    self.mapView = [[MKMapView alloc] initWithFrame:CGRectMake(0, 0, 320, 200)];

    // don't miss this
    self.mapView.delegate = self;
    // if you want it to show user's current location
    self.mapView.showsUserLocation = YES;
    [self.view addSubview:self.mapView];
}
```

### Remember to create a custom object the conform to `MKAnnotation`, lets call it `MyPlace`

```obj-c MyPlace.h
@interface MyPlace : NSObject <MKAnnotation>

@property (nonatomic, strong) NSString *placeName;
@property (nonatomic, assign) double latitude;
@property (nonatomic, assign) double longitude;

+ (id)initWithJSON:(id)json;


#pragma mark - MKAnnotation
// this will plot the marker to a correct place on map
- (CLLocationCoordinate2D)coordinate
{
    return CLLocationCoordinate2DMake(self.latitude, self.longitude);
}

// this will be shown as marker title
- (NSString *)title
{
    return self.placeName;
}

// this will be shown as marker subtitle
- (NSString *)subtitle
{
    return [NSString stringWithFormat:@"Lat: %.9f, Lng: %.9f", self.latitude, self.longitude];
}
@end
```

```obj-c MyPlace.m
#import "MyPlace.h"

@implementation MyPlace

@synthesize placeName = _placeName;
@synthesize latitude = _latitude;
@synthesize longitude = _longitude;

- (id)initWithJSON:(id)json
{
    self.placeName = [json valueForKey:@"name"];
    self.latitude = [[json valueForKey:@"lat"] doubleValue];
    self.longitude = [[json valueForKey:@"lng"] doubleValue];
}

@end
```

### Add markers, center the region

```obj-c ViewController.m
- (void)loadLocation
{
    MyPlace *place1 = [[MyPlace alloc] initWithJSON:[NSDictionary dictionaryWithObjectsAndKeys:
                            @"The place 1", @"name",
                            @"3.12345", @"lat",
                            @"101.43219", @"lng",
                            nil]];
    MyPlace *place2 = [[MyPlace alloc] initWithJSON:[NSDictionary dictionaryWithObjectsAndKeys:
                            @"The place 2", @"name",
                            @"4.98721", @"lat",
                            @"101.82665", @"lng",
                            nil]];
    MyPlace *place3 = [[MyPlace alloc] initWithJSON:[NSDictionary dictionaryWithObjectsAndKeys:
                            @"The place 1", @"name",
                            @"5.88621", @"lat",
                            @"100.99811", @"lng",
                            nil]];

    NSArray *places = @[place1, place2, place3];

    // centered the region
    MKCoordinateRegion region = [self regionForAnnotations:places];
    [self.mapView setRegion:region animated:YES];

    // NOTE: if doesn't call this method, no marker will be shown
    [self.mapView addAnnotations:places];
}

#pragma mark - MKMapViewDelegate
// this method will only be called if addAnnotation method is call
- (MKAnnotationView *)mapView:(MKMapView *)mapView viewForAnnotation:(id<MKAnnotation>)annotation
{
    // check wether is refer to the class you created just now
    if ([annotation isKindOfClass:[MyPlace class]]) {
        static NSString *identifier = @"MyLocation";
        // just like UITableViewCell, also using dequeue reusable
        MKPinAnnotationView *annotationView = (MKPinAnnotationView *)[self.mapView dequeueReusableAnnotationViewWithIdentifier:identifier];

        UIButton *rightButton;
        if (annotationView == nil) {
            annotationView = [[MKPinAnnotationView alloc] initWithAnnotation:annotation reuseIdentifier:identifier];
            // make configuration
            annotationView.enabled = YES;
            annotationView.canShowCallout = YES;
            annotationView.animatesDrop = NO;
            annotationView.pinColor = MKPinAnnotationColorGreen;
            // provide custom image as marker if you want to
            annotationView.image = [UIImage imageNamed:@"IconMarker"];
            
            // optional: you can add a button
            rightButton = [UIButton buttonWithType:UIButtonTypeDetailDisclosure];
            [rightButton addTarget:self action:@selector(placeTouched:) forControlEvents:UIControlEventTouchUpInside];
            annotationView.rightCalloutAccessoryView = rightButton;
        } else {
            annotationView.annotation = annotation;
        }
        // add a tag with a specific offset
        // remember to put this outside, the annotation will be reuse everytime,
        // if this is only set on annotation creation, you may have chances to get the wrong info
        rightButton.tag = 4000 + ((MyPlace *)annotation).theId;
        
        return annotationView;
    }
    return nil;
}

- (MKCoordinateRegion)regionForAnnotations:(NSArray *)annotations
{
    MKCoordinateRegion region;
    
    if ([annotations count] == 0) {
        region = MKCoordinateRegionMakeWithDistance(self.mapView.userLocation.coordinate, 1000, 1000);
        
    } else if ([annotations count] == 1) {
        id <MKAnnotation> annotation = [annotations lastObject];
        region = MKCoordinateRegionMakeWithDistance(annotation.coordinate, 1000, 1000);
        
    } else {
        CLLocationCoordinate2D topLeftCoord;
        topLeftCoord.latitude = -90;
        topLeftCoord.longitude = 180;
        
        CLLocationCoordinate2D bottomRightCoord;
        bottomRightCoord.latitude = 90;
        bottomRightCoord.longitude = -180;
        
        for (id <MKAnnotation> annotation in annotations)
        {
            topLeftCoord.latitude = fmax(topLeftCoord.latitude, annotation.coordinate.latitude);
            topLeftCoord.longitude = fmin(topLeftCoord.longitude, annotation.coordinate.longitude);
            bottomRightCoord.latitude = fmin(bottomRightCoord.latitude, annotation.coordinate.latitude);
            bottomRightCoord.longitude = fmax(bottomRightCoord.longitude, annotation.coordinate.longitude);
        }
        
        const double extraSpace = 1.1;
        region.center.latitude = topLeftCoord.latitude - (topLeftCoord.latitude - bottomRightCoord.latitude) / 2.0;
        region.center.longitude = topLeftCoord.longitude - (topLeftCoord.longitude - bottomRightCoord.longitude) / 2.0;
        region.span.latitudeDelta = fabs(topLeftCoord.latitude - bottomRightCoord.latitude) * extraSpace;
        region.span.longitudeDelta = fabs(topLeftCoord.longitude - bottomRightCoord.longitude) * extraSpace;
    }
    
    return [self.mapView regionThatFits:region];
}
...
```

So just call the `loadLocation` then the markers will shown.

_References:_

* _[In which case that mapView:viewForAnnotation: will be called?](http://stackoverflow.com/questions/9442830/in-which-case-that-mapviewviewforannotation-will-be-called/9442902#9442902)_
