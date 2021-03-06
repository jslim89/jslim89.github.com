---
layout: page
title: Regular Expression
permalink: /short-notes/regex/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

#### Match decimal value

```
/\d+(\.\d+)?/
```

- `\d+` - match 1 or more digits
- `(\.\d+)` - make a group of matches, match a `.` _(dot)_ followed by 1 or more digits
- `?` - optional to match for the preceding token

##### Reference:

- [Decimal number regular expression, where digit after decimal is optional](http://stackoverflow.com/questions/12117024/decimal-number-regular-expression-where-digit-after-decimal-is-optional/12117062#12117062)

---

#### Match OR exact value

Let's say want to match _monthly_ or _daily_

```
/\bmonthly\b|\bdaily\b/
```

Wrap the word with `\b`, means the word has no other character in front or behind.

What if **without `\b`**? then it will matches

- monthly
- daily
- foomonthly
- monthlybar
- foodaily
- dailybar

##### Reference:

- [Alternation with The Vertical Bar or Pipe Symbol](http://www.regular-expressions.info/alternation.html)
