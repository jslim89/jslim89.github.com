---
layout: post
title: "Match &amp; Replace NSString using Regex"
date: 2014-06-11 18:42:18 +0800
comments: true
tags: 
- ios
---

In this example shows, to display current date in a label in a specific format _(e.g. 31/12/14 12:59PM)_

You can see the [date format](http://unicode.org/reports/tr35/tr35-4.html#Date_Format_Patterns)

There is no such a format for 2 characters **year**, so we need to replace through regex.

```obj-c
// 1.
NSString *dateFormat = @"dd/MM/yyyy hh:mma";
NSString *date = [NSDate date];

// 2.
NSDateFormatter *formatter = [[NSDateFormatter alloc] init];
[formatter setDateFormat:dateFormat];
NSString *dateStr = [formatter stringFromDate:date];

// 3.
NSRegularExpression *regex = [NSRegularExpression regularExpressionWithPattern:@"(\\d{4})" options:NSRegularExpressionCaseInsensitive error:nil];

// 4.
[regex enumerateMatchesInString:dateStr options:0 range:NSMakeRange(0, dateStr.length) usingBlock:^(NSTextCheckingResult *result, NSMatchingFlags flags, BOOL *stop) {
    // 5.
    NSString *year = [dateStr substringWithRange:[result rangeAtIndex:0]];
    NSString *newDateFormat = [dateStr stringByReplacingCharactersInRange:[result rangeAtIndex:0] withString:[year substringWithRange:NSMakeRange(2, 2)]];

    // 6.
    dateLabel.text = [NSString stringWithFormat:@"My special date %@", newDateFormat];
}];
```

1. Specify the date format _(e.g. the result will be 31/12/2014 12:59PM)_, and select current date to display
2. Pass the format to the formatter, and store the date as string
3. We know that there is only 1 consecutive 4 integers in the string, so we want to extract it. The braces `()`
here is to capture the matches text. Double back slash `\\` here is to escape the back slash _(as we know it is
special character)_
4. Now we match the whole date string _(so we specify the range from index 0 to the string length)_ from the regex
5. We get the matched string now _(i.e. `2014` in this case)_, then we replace the characters in the range
_(i.e. `(6, 4)` which is the index for `2014`)_ with the last 2 characters of `2014`
6. Finally we set the value to the label
