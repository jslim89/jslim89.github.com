---
title: Why Google Analytics pageviews different from filter by day and by month
date: 2020-01-06 16:00:31
tags:
- google-analytics
- pageviews
---

I wrote a script to sync Google Analytics _(GA)_ pageviews to own DB in daily basis.
E.g.

- 2019-12-01 -> `342` pageviews
- 2019-12-02 -> `621` pageviews
- 2019-12-03 -> `781` pageviews
- 2019-12-04 -> `388` pageviews
- 2019-12-05 -> `562` pageviews
- ...
- 2019-12-31 -> `597` pageviews

So that I can generate report _(in my web app, not GA)_ and filter by date range.

But then, I encounter an issue, when I filter the report range from `2019-06-01` to `2019-06-30`,
the total pageviews are different from GA report.

Then I cross check it day by day, and both _(GA & web app)_ are tally.

![Google Analytics pageviews compare - 2019-06-01 to 2019-06-21](/images/posts/2020-01-06-Why-Google-Analytics-pageviews-different-from-filter-by-day-and-by-month/2019-06-01-to-2019-06-21.png)

👆 from `2019-06-01` to `2019-06-21`, both are tally

![Google Analytics pageviews compare - 2019-06-01 to 2019-06-22](/images/posts/2020-01-06-Why-Google-Analytics-pageviews-different-from-filter-by-day-and-by-month/2019-06-01-to-2019-06-22.png)

👆 from `2019-06-01` to `2019-06-22`, the result are different.
From here, I assume the result for `2019-06-22` has problem

![Google Analytics pageviews compare - 2019-06-22](/images/posts/2020-01-06-Why-Google-Analytics-pageviews-different-from-filter-by-day-and-by-month/2019-06-22.png)

But when I cross check for `2019-06-22`, both are tally again 🤔

![Google Analytics pageviews compare - 2019-06-19 to 2019-06-22](/images/posts/2020-01-06-Why-Google-Analytics-pageviews-different-from-filter-by-day-and-by-month/2019-06-19-to-2019-06-22.png)

👆 from `2019-06-19` to `2019-06-22`, the result are tally also.

Then after googled for a while, I think is caused by the GA sampling, perhaps from range 
`2019-06-01` to `2019-06-22`, the data set are too large, thus Google just pick a sample set of data. See the references 👇

## References:

- [Google Analytics: Different stats for date range vs single month?
](https://moz.com/community/q/google-analytics-different-stats-for-date-range-vs-single-month)
- [About data sampling](https://support.google.com/analytics/answer/2637192?hl=en-GB&visit_id=637138919541287620-4017250485&rd=1)
- [Google Analytics Query Explorer](https://ga-dev-tools.appspot.com/query-explorer/)
