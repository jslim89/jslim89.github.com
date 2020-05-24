---
layout: page
title: Swift
permalink: /short-notes/swift/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

https://www.apple.com/my/swift/

#### Hide keyboard when tap anywhere on UITableView

```swift
let tap = UITapGestureRecognizer(target: self, action: Selector("tableViewTapped:"))
// if this doesn't set to `false`, the table view cell won't takes any effect on tap
tap.cancelsTouchesInView = false
self.tableView.addGestureRecognizer(tap)

// ...
func tableViewTapped(sender: UITapGestureRecognizer) {
    self.view.endEditing(true) // hide keyboard
}
```

##### Reference:

- [Dismiss keyboard by touching background of UITableView](http://stackoverflow.com/questions/2321038/dismiss-keyboard-by-touching-background-of-uitableview/4727589#4727589)

---

#### `UITableViewCell` dynamic height for auto-layout subviews

```swift
self.tableView.rowHeight = UITableViewAutomaticDimension
self.tableView.estimatedRowHeight = 44.0
```

##### Reference:

- [UITableView Tutorial: Dynamic Table View Cell Height](http://www.raywenderlich.com/87975/dynamic-table-view-cell-height-ios-8-swift)
