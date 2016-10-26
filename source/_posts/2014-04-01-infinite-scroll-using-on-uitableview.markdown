---
layout: post
title: "Infinite scroll using on UITableView"
date: 2014-04-01 21:38:01 +0800
comments: true
categories: 
- ios
---

If we have 100 records, perhaps we can GET them in one short. But what if, we have 1000 or even 10k records?

Thus, here I shows an example on implement lazy loading on iOS app, as well as pull to refresh.

## Dependencies

- [SVPullToRefresh](https://github.com/samvermette/SVPullToRefresh) - _(see the [installation](https://github.com/samvermette/SVPullToRefresh#manually))_
- [AFNetworking](https://github.com/AFNetworking/AFNetworking) - to make HTTP request

## Create a `UIViewController`

**MyListViewController.h**

```obj-c
@interface MyListViewController : UIViewController <UITableViewDataSource, UITableViewDelegate>

@end
```

**MyListViewController.m**

```obj-c
#import <QuartzCore/QuartzCore.h>
#import "MyListViewController.h"
#import "AFNetworking.h"
#import "UIScrollView+SVPullToRefresh.h"
#import "UIScrollView+SVInfiniteScrolling.h"

static int initialPage = 1; // paging start from 1, depends on your api

@interface MyListViewController ()

@property (nonatomic, strong) UITableView *tableView;

// to keep track of what is the next page to load
@property (nonatomic, assign) int currentPage;
// to keep the objects GET from server
@property (nonatomic, strong) NSMutableArray *myList;

@end

@implementation MyListViewController

@synthesize tableView = _tableView;

@synthesize currentPage = _currentPage;
@synthesize myList = _myList;

- (void)viewDidLoad
{
    [super viewDidLoad];

    // initialize
    _myList = [NSMutableArray array];
    _currentPage = initialPage;

    // init table list
    self.tableView = [[UITableView alloc] initWithFrame:self.view.bounds];
    self.tableView.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin| UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleHeight;
    self.tableView.delegate = self;
    self.tableView.dataSource = self;
    [self.view addSubview:self.tableView];
    
    __weak typeof(self) weakSelf = self;
    // refresh new data when pull the table list
    [self.tableView addPullToRefreshWithActionHandler:^{
        weakSelf.currentPage = initialPage; // reset the page
        [weakSelf.myList removeAllObjects]; // remove all data
        [weakSelf.tableView reloadData]; // before load new content, clear the existing table list
        [weakSelf loadFromServer]; // load new data
        [weakSelf.tableView.pullToRefreshView stopAnimating]; // clear the animation
        
        // once refresh, allow the infinite scroll again
        weakSelf.tableView.showsInfiniteScrolling = YES;
    }];

    // load more content when scroll to the bottom most
    [self.tableView addInfiniteScrollingWithActionHandler:^{
        [weakSelf loadFromServer];
    }];
}

- (void)loadFromServer
{
    AFHTTPRequestOperationManager *manager = [AFHTTPRequestOperationManager manager];
    [manager GET:[NSString stringWithFormat:@"http://api.example.com/list/%d", _currentPage] parameters:nil success:^(AFHTTPRequestOperation *operation, id responseObject) {

        // if no more result
        if ([[responseObject objectForKey:@"items"] count] == 0) {
            self.tableView.showsInfiniteScrolling = NO; // stop the infinite scroll
            return;
        }
            
        _currentPage++; // increase the page number
        int currentRow = [_myList count]; // keep the the index of last row before add new items into the list

        // store the items into the existing list
        for (id obj in [responseObject valueForKey:@"items"]) {
            [_myList addObject:obj];
        }
        [self reloadTableView:currentRow];

        // clear the pull to refresh & infinite scroll, this 2 lines very important
        [self.tableView.pullToRefreshView stopAnimating];
        [self.tableView.infiniteScrollingView stopAnimating];

    } failure:^(AFHTTPRequestOperation *operation, NSError *error) {
        self.tableView.showsInfiniteScrolling = NO;
        NSLog(@"error %@", error);
    }];
}

- (void)reloadTableView:(int)startingRow;
{
    // the last row after added new items
    int endingRow = [_myList count];
    
    NSMutableArray *indexPaths = [NSMutableArray array];
    for (; startingRow < endingRow; startingRow++) {
        [indexPaths addObject:[NSIndexPath indexPathForRow:startingRow inSection:0]];
    }
    
    [self.tableView insertRowsAtIndexPaths:indexPaths withRowAnimation:UITableViewRowAnimationFade];
}


#pragma mark - UITableViewDelegate
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    id item = [_myList objectAtIndex:indexPath.row];
    NSLog(@"Selected item %@", item);
}

#pragma mark - UITableViewDataSource
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [_myList count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *cellIdentifier = @"MyListCell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier];
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:cellIdentifier];
    }
    
    // minus 1 because the first row is the search bar
    id item = [_myList objectAtIndex:indexPath.row];

    cell.textLabel.text = [item valueForKey:@"name"];
    
    return cell;
}

@end
```

### EDIT:

Remember to set the `autoresizingMask`, e.g.

```obj-c
self.tableView.autoresizingMask = UIViewAutoresizingFlexibleBottomMargin| UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleHeight;
```

Otherwise the contentSize of `scrollView` will have problem when calling `showsInfiniteScrolling = NO;`

See the images below:

**Not working example**

![content size problem](http://jslim89.github.com/images/posts/2014-04-01-infinite-scroll-using-on-uitableview/contentsize-notwork.gif)

**Working example**

![content size solved](http://jslim89.github.com/images/posts/2014-04-01-infinite-scroll-using-on-uitableview/contentsize-work.gif)

Done :)
