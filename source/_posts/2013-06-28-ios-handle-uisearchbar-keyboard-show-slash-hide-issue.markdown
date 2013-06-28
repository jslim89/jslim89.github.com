---
layout: post
title: "iOS - Handle UISearchBar keyboard show/hide issue"
date: 2013-06-28 13:10
comments: true
categories: 
- ios
---

I have experienced that when you focus on UISearchBar, the keyboard occupied half of the screen. The question is, "Under what condition should the keyboard hide?".

One of the solution is to follow the contact book, which is add an overlay button on top of it.

```obj-c
- (void)searchBarTextDidBeginEditing:(UISearchBar *)searchBar
{
    // add the button to the main view
    UIButton *overlayButton = [[UIButton alloc] initWithFrame:self.view.frame];

    // set the background to black and have some transparency
    overlayButton.backgroundColor = [UIColor colorWithWhite:0 alpha:0.3f];

    // add an event listener to the button
    [overlayButton addTarget:self action:@selector(hideKeyboard:) forControlEvents:UIControlEventTouchUpInside];

    // add to main view
    [self.view addSubview:overlayButton];
}

- (void)hideKeyboard:(UIButton *)sender
{
    // hide the keyboard
    [self.searchBar resignFirstResponder];
    // remove the overlay button
    [sender removeFromSuperview];
}
```

So now the keyboard will hide when you touched on the dark area.

Simple :)
