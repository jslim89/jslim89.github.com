---
layout: post
title: "Setup cronjob &amp; queue in Elastic Beanstalk with Laravel"
date: 2016-07-19 10:39:16 +0800
comments: true
categories: 
- aws
- laravel
- php
---

This is basically part-2 continue from [Setup Laravel 5 in Amazon Elastic Beanstalk](/blog/2016/05/24/setup-laravel-5-in-amazon-elastic-beanstalk/).

In most cases, we need to run some long process tasks in background, by either cronjob or queue.

Here I'll demostrate both.

## Create a worker tier

We need to separate normal http operation from long process background tasks. So let's create a worker environment

```
$ eb create <environment> -t worker
```

## Post hook after `eb deploy`

First, create a directory in your project root

```
$ cd /path/to/project
$ mkdir .ebextensions
```

You can [read the official documentation here](http://docs.aws.amazon.com/elasticbeanstalk/latest/dg/ebextensions.html)

_From now onward, I assume the **current directory** is in project root_

### Setup queue with supervisor & sqs

First you need to create a **.config** file

```
packages:
  yum:
    python27-setuptools: []
container_commands:
  01-supervise:
    command: ".ebextensions/supervise.sh"
```

Where the supervise.sh you can [download here](/attachments/posts/2016-07-19-setup-cronjob-and-queue-in-elastic-beanstalk-with-laravel/supervise.sh)

_(credit to [Günter Grodotzki](http://www.lifeofguenter.de/2015/04/laravel-queues-with-supervisor-on.html))_

Once you done this, you must add an environment variable in your elastic beanstalk console _(worker tier only)_ "SUPERVISE=enable" to activate

![AWS Elastic Beanstalk environment](/images/posts/2016-07-19-setup-cronjob-and-queue-in-elastic-beanstalk-with-laravel/eb-environment-variable-supervise.png)

Because you don't want the **webapp** environment to listen to the queue, thus this variable is used to identify which environment need to run this.

#### Create a queue

Create a queue in SQS console, then copy the URL to environment variable, e.g.

```
https://sqs.ap-southeast-1.amazonaws.com/999999999999/laravel-queue
```

For how to use queue in laravel, [refer here](https://laravel.com/docs/master/queues)


### Setup cronjob

Create a file named **cron.config**

```
container_commands:
  01-crontab:
    command: ".ebextensions/cron.sh"
```

Where the cron.sh you can [download here](/attachments/posts/2016-07-19-setup-cronjob-and-queue-in-elastic-beanstalk-with-laravel/cron.sh)
and also [download the crontab](/attachments/posts/2016-07-19-setup-cronjob-and-queue-in-elastic-beanstalk-with-laravel/crontab) _(you may modify if you need)_

Place this 2 files in **.ebextensions**. Again, same as supervise, you need to add `INS_CRONTAB` environment variable in worker tier, and set the value to `enable`.

**One thing to take note**, whenever the environment scale up to multiple server, your crontab may run multiple times, thus, I suggest that
create a table _(e.g. `cronjob`)_ in database, and add 2 attributes: `executed_at` and `completed_at`.

Thus, in your php script, you only run those job with `executed_at` = `null`, to avoid duplication.

## Deploy your app

```
$ eb deploy <worker environment>
```

Done

References:

- [Laravel queues with supervisor on ElasticBeanstalk](http://www.lifeofguenter.de/2015/04/laravel-queues-with-supervisor-on.html)
