---
layout: page
title: Python - Boto
permalink: /short-notes/python-boto/
date: 2020-06-04 21:13:51
comments: false
sharing: true
footer: true
---

AWS client for Python
https://boto3.amazonaws.com/v1/documentation/api/latest/index.html

#### Set specific AWS profile

```
$ cat ~/.aws/credentials 
[default]
aws_access_key_id = ABCDEFGHIJKLMNOPQRST
aws_secret_access_key = 12345678904lqM+abcdefghijklmnopViX+abcde

[foobar]
aws_access_key_id = TSRQPONMLKJIHGFEDCBA
aws_secret_access_key = edcba+XiVponmlkjihgfedcba+Mql40987654321
aws_session_token = xxxxxxxxxxxx
```

Let say I want to use `foobar` profile as default

```
echo "AWS_PROFILE=foobar" >> ~/.bashrc
```

##### References:

- [Using profiles in Boto3](https://devopslife.io/using-profiles-in-boto3/)
