---
title: Use Google Analytics API with Laravel 5
date: 2019-04-02 20:22:48
tags:
- google-analytics
- google
- laravel
- php
---

No doubt that Google is very powerful. I've been researching for
Google Analytics _(GA)_ API for quite sometime, due to it's complex documentation
_(somehow I think is very hard to find what I need)_.

I will show the demo in [Laravel command](https://laravel.com/docs/5.8/artisan),
it's easier and comfortable for me.

## 1. Request API key

First, we must [create a service account](https://developers.google.com/analytics/devguides/reporting/core/v3/quickstart/service-php) in order to call the API.

![Google service account](/images/posts/2019-04-02-Use-Google-Analytics-API-with-Laravel-5/service-account.png)

Once you create, you will download the key file.

## 2. Grant your service account to access Google Analytics

Without this step, you won't be able to get any data. Because GA will treat
the service account as an actual account, must grant at least a READ access.

![Google Analytics add user](/images/posts/2019-04-02-Use-Google-Analytics-API-with-Laravel-5/ga-add-user-step-1.png)

Let's add the service account user to **View** level.

![Google Analytics add user](/images/posts/2019-04-02-Use-Google-Analytics-API-with-Laravel-5/ga-add-user-step-2.png)

Remember to set the permission to readonly

## 3. Make use of [Google SDK](https://github.com/google/google-api-php-client)

In Laravel project, run this

```
$ composer require google/apiclient:^2.0
```

After installed, then create a command for testing

```
$ php artisan make:command GetGAResult
```

Now let's edit the file

```php
<?php
// ...

public function handle()
{
    $start = '2019-01-01';
    $end = '2019-03-31';
    $ga_id = 'UA-123456789-3';

    // define what you need here
    $fields = [
        'metrics' => [
            'ga:pageviews',
            'ga:uniquePageviews',
        ],
        'dimensions' => [
            'ga:pagePath',
        ],
    ];
}
```

If you want to know more about what to know about what are the fields available,
can [refer here](https://ga-dev-tools.appspot.com/query-explorer/).

![Query explorer](/images/posts/2019-04-02-Use-Google-Analytics-API-with-Laravel-5/query-explorer.png)

```php
private function getGADataByDate($ga_id, $start_date, $end_date, $fields, $callback)
{
    $client = new \Google_Client();
    $client->setApplicationName('My report');
    $client->setAuthConfig('/path/to/service-account-credential.json');
    $client->setScopes([\Google_Service_Analytics::ANALYTICS_READONLY]);

    // find the view ID
    $analytics = new \Google_Service_Analytics($client);
    $accounts = $analytics->management_accounts->listManagementAccounts();
    $items = $accounts->getItems();
    $first_account = $items[0]->getId();
    $profiles = $analytics->management_profiles
        ->listManagementProfiles($first_account, '~all');
    foreach ($profiles->getItems() as $profile) {
        if ($ga_id == $profile->getWebPropertyId()) {
            $view_id = $profile->getId();
            break;
        }
    }
    if (!isset($view_id)) {
        $this->error(sprintf('GA view ID not found for %s.', $ga_id));
        return null;
    }

    // begin retrieve report
    $ga_reporting = new \Google_Service_AnalyticsReporting($client);

    $date_range = new \Google_Service_AnalyticsReporting_DateRange();
    $date_range->setStartDate($start_date);
    $date_range->setEndDate($end_date);

    $metrics = [];
    foreach ($fields['metrics'] as $metric_conf) {
        $metric = new \Google_Service_AnalyticsReporting_Metric();
        $metric->setExpression($metric_conf);
        $metric->setAlias($metric_conf);
        $metrics[] = $metric;
    }

    $dimensions = [];
    foreach ($fields['dimensions'] as $dimension_conf) {
        $dimension = new \Google_Service_AnalyticsReporting_Dimension();
        $dimension->setName($dimension_conf);
        $dimensions[] = $dimension;
    }

    // sort by pageviews
    $sort = new \Google_Service_AnalyticsReporting_OrderBy();
    $sort->setSortOrder('DESCENDING');
    $sort->setOrderType('VALUE');
    $sort->setFieldName('ga:pageviews');

    $ga_request = new \Google_Service_AnalyticsReporting_ReportRequest();
    $ga_request->setViewId((string)$view_id);
    $ga_request->setDateRanges($date_range);
    $ga_request->setPageSize(10); // pagination, total retrieve per request
    $ga_request->setMetrics($metrics);
    $ga_request->setDimensions($dimensions);
    $ga_request->setOrderBys([$sort]);

    $body = new \Google_Service_AnalyticsReporting_GetReportsRequest();
    $body->setReportRequests([$ga_request]);

    $data = $ga_reporting->reports->batchGet($body);
    $this->info(sprintf('Getting page %d', 1));
    $callback($data->reports[0]);

    $page = 1;
    $total_pages = 5; // total number of pages to retrieve
    while ($data->reports[0]->nextPageToken > 0 && $page < $total_pages) {
        $this->info(sprintf('Getting page %d', ($page + 1)));
        // There are more rows for this report. we apply the next page token to the page token of the orignal body.
        $body->reportRequests[0]->setPageToken($data->reports[0]->nextPageToken);
        $data = $ga_reporting->reports->batchGet($body);
        $callback($data->reports[0]); // due to too much data, get by chunk
        $page++;
    }
}
```

Now, let's update the `handle()`

```php
<?php
// ...

public function handle()
{
    // ...

    // later will output in table form
    $headers = ['Page', 'Pageviews', 'Unique pageviews'];
    $final_results = [];

    $this->getData($ga_id, $start, $end, $fields, function ($report) use ($fields, &$final_results) {
        if (empty($report)) return;

        $rows = $report->getData()->getRows();

        foreach ($rows as $row) {
            $dimensions = $row->getDimensions();
            $metrics = $row->getMetrics();

            $page_path = '';
            $pageviews = 0;
            $unique_pageviews = 0;

            // metrics
            $metric = $metrics[0];
            $metric_values = $metric->getValues();
            foreach ($metric_values as $j => $value) {
                if ($fields['metrics'][$j] == 'ga:pageviews') {
                    $pageviews = $value;
                    continue;
                }
                if ($fields['metrics'][$j] == 'ga:uniquePageviews') {
                    $unique_pageviews = $value;
                    continue;
                }
            }

            // dimensions
            foreach ($dimensions as $j => $dimension) {
                if ($fields['dimensions'][$j] == 'ga:pagePath') {
                    $page_path = $dimension;
                    break;
                }
            }

            $final_results[] = [
                $page_path,
                $pageviews,
                $unique_pageviews,
            ];
        }
    });

    $this->table($headers, $final_results);
}
```

Now, let's try it

```
$ php artisan get-ga-result
```

Here's the output

```
+-------------------------------------------------------------------------+-----------+------------------+
| Page                                                                    | Pageviews | Unique pageviews |
+-------------------------------------------------------------------------+-----------+------------------+
| /blog/2018/03/13/Angular-4-download-file-from-server-via-http/          | 157       | 146              |
| /blog/2016/06/06/aws-ec2-enable-remote-access-on-mysql/                 | 21        | 20               |
| /blog/2015/12/28/laravel-dompdf-set-custom-paper-size/                  | 9         | 9                |
| /blog/2012/12/28/ajax-change-dropdown-list-value-with-jquery/           | 8         | 8                |
| /blog/2014/07/14/remove-the-1px-shadow-from-uisearchbar/                | 6         | 5                |
| /blog/2018/01/30/CefSharp-listen-to-JavaScript-event/                   | 5         | 5                |
| /blog/2012/12/21/ios-uitabelview-didselectrowatindexpath-is-not-called/ | 4         | 4                |
| /blog/2013/09/12/get-json-using-php-curl-from-web-service/              | 4         | 4                |
| /blog/2014/08/23/laravel-4-two-pagination-in-a-single-page/             | 3         | 3                |
| ...                                                                     | ...       | ...              |
+-------------------------------------------------------------------------+-----------+------------------+
```
