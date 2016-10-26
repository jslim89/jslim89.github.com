---
layout: post
title: "iOS - Countdown to milliseconds"
date: 2014-05-27 17:15:44 +0800
comments: true
categories: 
- ios
---

#import "CountdownViewController.h"

```obj-c
// default countdown time in seconds
static int defaultBufferSeconds = 10;

@interface CountdownViewController ()

@property (nonatomic, strong) UILabel *timerLabel;

@end

@implementation CountdownViewController {
    // to keep a reference for current counting
    int countdownMilliseconds;
}

@synthesize timerLabel = _timerLabel;

- (void)viewDidLoad
{
    [super viewDidLoad];

    // display countdown on this label
    self.timerLabel = [[UILabel alloc] initWithFrame:CGRectMake(40, 40, 240, 40)];
    self.timerLabel.textAlignment = NSTextAlignmentCenter;
    [self.countdownView addSubview:self.timerLabel];

    // start countdown
    [self startTimer];
}

- (void)startTimer
{
    countdownMilliseconds = defaultBufferSeconds * 1000; // reset the current counting value
    // NSTimeInterval is in "second", now schedule it to every 10 milliseconds
    [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(updateCountdown:) userInfo:nil repeats:YES];
}


- (void)updateCountdown:(NSTimer *)timer
{
    int seconds, milliseconds;
    
    // don't forget to update the current counting value
    countdownMilliseconds -= (timer.timeInterval * 1000);
    
    seconds = countdownMilliseconds / 1000;

    // since we only want to show 2 digits in milliseconds, so have to divide by 10
    milliseconds = countdownMilliseconds % 1000;
    milliseconds /= 10;
    
    self.timerLabel.text = [NSString stringWithFormat:@"Countdown: %02d seconds %02d milliseconds", seconds, milliseconds];

    // once current counting value reach 0, then stop the timer
    if (countdownMilliseconds <= 0) {
        [timer invalidate];
        timer = nil;
    }
}

@end
```

_References:_

- _[NSTimer Decrease the time by seconds/milliseconds](http://stackoverflow.com/questions/10257330/nstimer-decrease-the-time-by-seconds-milliseconds/10257616#10257616)_
