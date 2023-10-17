---
title: 'AWS Cognito User Pool custom domain error'
date: 2023-10-18 07:02:53
tags:
- aws
- troubleshoot
---

## One or more aliases specified for the distribution includes an incorrectly configured DNS record that points to another CloudFront distribution.

I was deleting the user pool, and try to recreating a new one with Terraform, then encounter this error:

```
Error: creating Cognito User Pool Domain (xxx.example.com): InvalidParameterException: One or more aliases specified
for the distribution includes an inc orrectly configured DNS record that points to another CloudFront distribution.
You must update the DNS record to correct the problem. For more information, see 
https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/CNAMEs.html#alternate-domain-names-restrictions
(Service: AmazonCloudFront; Status Code: 409; Error Code: CNAMEAlreadyExists; Request ID: 11111111-2222-3333-4444-555555555555; Proxy: null)
```

But I don't see any CloudFront distribution in my AWS console. It took me hours of troubleshooting...

```
$ dig A xxx.example.com +short
xxxxxxxxxxxxxx.cloudfront.net.
```

Eventually, I noticed the my domain is still pointing to a CNAME record. I deleted that record, and re-run the terraform command, and it works well.

Ref: [How do I troubleshoot errors when I create a custom domain in Amazon Cognito?](https://repost.aws/knowledge-center/cognito-custom-domain-errors)

