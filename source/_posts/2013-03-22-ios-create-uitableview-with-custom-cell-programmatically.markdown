---
layout: post
title: "iOS - Create UITableView with custom cell programmatically"
date: 2013-03-22 18:59
comments: true
categories: 
- ios
- programming
---

Personally, I don't like to use Xcode's interface builder. I felt that everything programmatically will be easier to debug.

This post is share about create table view and custom table cell.

Create custom cell

**JSCustomCell.h**
```obj-c
#import <UIKit/UIKit.h>

// extends UITableViewCell
@interface JSCustomCell : UITableViewCell

// now only showing one label, you can add more yourself
@property (nonatomic, strong) UILabel *descriptionLabel;

@end
```

**JSCustomCell.m**
```obj-c
#import "JSCustomCell.h"

@implementation JSCustomCell

@synthesize descriptionLabel = _descriptionLabel;

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // configure control(s)
        self.descriptionLabel = [[UILabel alloc] initWithFrame:CGRectMake(5, 10, 300, 30)];
        self.descriptionLabel.textColor = [UIColor blackColor];
        self.descriptionLabel.font = [UIFont fontWithName:@"Arial" size:12.0f];
        
        [self addSubview:self.descriptionLabel];
    }
    return self;
}

@end
```

**JSViewController.h**
```obj-c
#import <UIKit/UIKit.h>

// Tell the compiler to conform to these protocols
@interface JSViewController : UIViewController <UITableViewDelegate, UITableViewDataSource>

@end
```

**JSViewController.m**
```obj-c
#import "JSViewController.h"
#import "JSCustomCell.h"

@interface JSViewController ()

@end

@implementation JSViewController {
    UITableView *tableView;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // init table view
    tableView = [[UITableView alloc] initWithFrame:self.view.bounds style:UITableViewStylePlain];

    // must set delegate & dataSource, otherwise the the table will be empty and not responsive
    tableView.delegate = self;
    tableView.dataSource = self;

    tableView.backgroundColor = [UIColor cyanColor];

    // add to canvas
    [self.view addSubview:tableView];
}

#pragma mark - UITableViewDataSource
// number of section(s), now I assume there is only 1 section
- (NSInteger)numberOfSectionsInTableView:(UITableView *)theTableView
{
    return 1;
}

// number of row in the section, I assume there is only 1 row
- (NSInteger)tableView:(UITableView *)theTableView numberOfRowsInSection:(NSInteger)section
{
    return 1;
}

// the cell will be returned to the tableView
- (UITableViewCell *)tableView:(UITableView *)theTableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *cellIdentifier = @"HistoryCell";
    
    // Similar to UITableViewCell, but 
    JSCustomCell *cell = (JSCustomCell *)[theTableView dequeueReusableCellWithIdentifier:cellIdentifier];
    if (cell == nil) {
        cell = [[JSCustomCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:cellIdentifier];
    }
    // Just want to test, so I hardcode the data
    cell.descriptionLabel.text = @"Testing";
    
    return cell;
}

#pragma mark - UITableViewDelegate
// when user tap the row, what action you want to perform
- (void)tableView:(UITableView *)theTableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    NSLog(@"selected %d row", indexPath.row);
}

@end
```

Simple? Basically is all the same, the only different is you have to configure those controls programmatically.

Have fun :)
