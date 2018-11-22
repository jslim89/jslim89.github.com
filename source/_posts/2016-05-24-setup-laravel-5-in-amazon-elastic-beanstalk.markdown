---
layout: post
title: "Setup Laravel 5 in Amazon Elastic Beanstalk"
date: 2016-05-24 15:42:55 +0800
comments: true
tags: 
- laravel
- aws
---

## Problem

This is my first time dealing with AWS. Initially, I setup my Laravel project in a normal EC2 instance, and manually install mysql, web server, etc.

Then a friend of mine told me that this kind of infrastructure is not scalable. Eventually I go for Elastic Beanstalk.

## Create an [AWS account](https://aws.amazon.com/)

You need a valid credit card to perform this action. Just follow the instruction will do.

## Create a new app in Elastic Beanstalk

![AWS services](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/aws-services-eb.png)

Then choose **Web server environment**.

![AWS Elastic Beanstalk environment](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/eb-environment.png)

Platform select `PHP`.

![AWS Elastic Beanstalk - environment config](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/eb-environment-config.png)

You can temporary choose **Sample application** first.

_Or you can use command line to create ([see here](http://docs.aws.amazon.com/elasticbeanstalk/latest/dg/eb3-create.html))_

```
$ cd /path/to/project/
$ eb create <environment> -t webapp
```

![AWS Elastic Beanstalk - overview](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/eb-environment-overview.png)

Now you can click on the link above to see your sample page

## Install `eb` in your local

[Follow the instruction here](https://docs.aws.amazon.com/console/elasticbeanstalk/eb-cli-install)

Once you have installed. Run the following command

```
$ eb init
```

You will be prompted to key in AWS credential. [Refer the instruction here](http://docs.aws.amazon.com/general/latest/gr/getting-aws-sec-creds.html)


### Enable ssh login

Go to **Configuration** on your left side, then select **Instances**

![AWS Elastic Beanstalk - ssh login](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/choose-your-ssh-key.png)

If you don't have a key, create it. Download it and put into your `~/.ssh/` directory.

_NOTE: Bare in mind that whenever you ssh in and change something, the changes only temporary, once it scale up and down the changes will be lost_

### Add environment variables

If you notice, there is a file named **.env.sample** in all laravel project, and you need to rename/copy it as **.env** _(without **sample**)_, this is because the configuration cannot be push to git repo, due to security reason.

Now, in elasticbeanstalk, if you ssh in and rename it to **.env**, then when it scale up and down, the **.env** will be lost, and the site may be down. Thus, the you have to add the config settings to environment variables

![AWS Elastic Beanstalk - software configuration](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/eb-environment-software-config.png)

![AWS Elastic Beanstalk - environment variables](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/eb-env-variable.png)

## Configure RDS for your project

Go to **Services** _(in the top left)_, and choose `RDS`, then follow the instruction.

Now go to your instances

![AWS Elastic Beanstalk - RDS instances](/images/posts/2016-05-24-setup-laravel-5-in-amazon-elastic-beanstalk/rds-instances.png)

Get the link and put it in your environment variable.

```
DB_HOST=zzzzz.xxxxxxxxxxxx.ap-southeast-1.rds.amazonaws.com
```

### Deploy

Now, the deployment is pretty simple _(assume you're using git here)_

In your local, `cd` to the project root, then run a command

```
$ eb deploy <environment>
```

It will deploy the latest commit, if you have any changes which not committed yet, it won't be deployed.

#### Mission Completed!!!


## References:

- [Laravel on AWS Elastic Beanstalk – Dev Guide](http://blog.goforyt.com/laravel-aws-elastic-beanstalk-dev-guide/)
- [Laravel Queues with Supervisor on Elasticbeanstalk](http://www.lifeofguenter.de/2015/04/laravel-queues-with-supervisor-on.html)
- [Deploying a Django App to AWS Elastic Beanstalk](https://realpython.com/blog/python/deploying-a-django-app-to-aws-elastic-beanstalk/)
