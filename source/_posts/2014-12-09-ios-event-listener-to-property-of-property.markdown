---
layout: post
title: "iOS - event listener to property of property"
date: 2014-12-09 22:00:38 +0800
comments: true
tags: 
- ios
- objective-c
---

What does it mean? Let's look at the view hierarchy

```
JSView
 |---- titleLabel
 |---- ...
```

In this case, I have a viewController that contain a property **UILabel** `titleLabel`. I want to perform some action upon the `text` changed.

i.e.

```obj-c
jsView.titleLabel.text = @"new text";
```

Now, add an event listener _(in iOS called observer)_ to it's text changed

```obj-c
[self addObserver:self forKeyPath:@"_titleLabel.text" options:NSKeyValueObservingOptionNew | NSKeyValueObservingOptionOld context:nil];
```

you can add the line above to `init` method

```obj-c
- (void)dealloc
{
    [self removeObserver:self forKeyPath:@"_titleLabel.text"];
}
```

Don't forget to remove it after the main view deallocated, otherwise the app will crash.

```obj-c
#pragma mark - observer
-(void)observeValueForKeyPath:(NSString *)keyPath ofObject:(id)object change:(NSDictionary *)change context:(void *)context
{
    if ([keyPath isEqualToString:@"_titleLabel.text"])
    {
        NSLog(@"the title text has been changed");
    }
}
```

Once the text changed, it will output the console

### Update Jan 6, 2015

The more proper way would be

```obj-c
[_titleLabel addObserver:self forKeyPath:@"text" options:NSKeyValueObservingOptionNew | NSKeyValueObservingOptionOld context:nil];
```

```obj-c
- (void)dealloc
{
    [_titleLabel removeObserver:self forKeyPath:@"text"];
}
```

```obj-c
#pragma mark - observer
-(void)observeValueForKeyPath:(NSString *)keyPath ofObject:(id)object change:(NSDictionary *)change context:(void *)context
{
    if (object == _titleLabel) {
        if ([keyPath isEqualToString:@"text"]) {
            NSLog(@"the title text has been changed");
        }
    }
}
```

_References:_

- _[Key-Value Observing](http://nshipster.com/key-value-observing/)_
- _[Simple KVO example](http://stackoverflow.com/questions/24969523/simple-kvo-example)_
