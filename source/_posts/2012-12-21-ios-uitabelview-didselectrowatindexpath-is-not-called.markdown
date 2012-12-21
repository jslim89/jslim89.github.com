---
layout: post
title: "iOS: UITabelView - didSelectRowAtIndexPath is not called"
date: 2012-12-21 15:03
comments: true
categories: 
- ios
---

I had come across a problem which didSelectRowAtIndexPath doesn't execute when I tap the cell.
```obj-c
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    NSLog(@"Successfully tapped");
    UITableViewCell *cell = [tableView cellForRowAtIndexPath:indexPath];
    
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
}
```
No matter how I tap, it doesn't show the **log**.

{% img http://jslim89.github.com/images/posts/2012-12-21-ios-uitabelview-didselectrowatindexpath-is-not-called/attributes_inspector.png Attributes Inspector %}

As shown in the attribute inspector, the **Selection** is not `none`, **User Interaction Enabled** is also `checked`.

Finally, I found the problem...
```obj-c
- (NSIndexPath *)tableView:(UITableView *)tableView willSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    return nil;
}
```
I forgot to remove `willSelectRowAtIndexPath` in **ViewController**.
