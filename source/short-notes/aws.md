---
layout: page
title: AWS
permalink: /short-notes/aws/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

https://aws.amazon.com/

#### Configure aws CLI

##### OS X

You need to have [homebrew](http://brew.sh/) installed

```
$ brew install awscli
$ aws configure
```

then put in your access ID & key _(don't know where to get, read [IAM doc](http://docs.aws.amazon.com/IAM/latest/UserGuide/introduction.html))_

##### Reference:

- [Bash with AWS CLI - unable to locate credentials](http://stackoverflow.com/questions/31425838/bash-with-aws-cli-unable-to-locate-credentials/31426381#31426381)

---

#### Delete all pending queue items

```
$ aws sqs purge-queue --queue-url https://sqs.ap-southeast-1.amazonaws.com/123456789012/queue_name
```

##### Reference:

- [AWS purge-queue](https://docs.aws.amazon.com/cli/latest/reference/sqs/purge-queue.html)

---

#### Use `aws` command with specific credentials

We can set multiple credentials in **~/.aws/credentials**

```
[default]
aws_access_key_id=ABCDEFGHIJKLMNOPQRSTUVWXYZ
aws_secret_access_key=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

[pf1]
aws_access_key_id=ZYXWVUTSRQPONMLKJIHGFEDCBA
aws_secret_access_key=yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy

[pf2]
aws_access_key_id=ABCDEFGHIJKLMESBM7VFCXU
aws_secret_access_key=zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz
```

We can specify with `--profile` option. e.g.

```
$ aws s3 ls --profile pf2
```

OR

Run any command with specific aws profile

```
$ AWS_PROFILE=pf2 python3 download_s3.py
```

##### Reference:

- [Support for --profile for selecting IAM credentials](https://github.com/awslabs/aws-sam-cli/issues/27)

----

#### S3 copy wildcard (*)

let say we want to achieve

```
$ cp /path/to/files/wanted* s3://bucket/new_folder/
```

The correct way should be

```
$ aws s3 cp /path/to/files/ s3://bucket/new_folder/ --exclude "*" --include "wanted*" --recursive
```

##### Reference:

- [How do i use wildcards to copy group of files in AWS CLI?](https://intellipaat.com/community/525/how-do-i-use-wildcards-to-copy-group-of-files-in-aws-cli)

----

#### Get the top 10 lines from a large S3 file

```
aws s3api get-object --bucket my-bucket --key path/to/large-file.csv --range bytes=0-10000 /dev/stdout | head -10
```


#### S3 bucket policy deny all but allow only a single role

```json
{
	"Version": "2012-10-17",
	"Statement": [
		{
			"Effect": "Deny",
			"NotPrincipal": {
				"AWS": [
					"arn:aws:iam::444455556666:root"
				]
			},
			"Action": "s3:*",
			"Resource": ["arn:aws:s3:::BUCKETNAME", "arn:aws:s3:::BUCKETNAME/*"],
            "Condition": {
                "ArnNotEquals": {
                    "aws:PrincipalArn": "arn:aws:iam::444455556666:role/the-allowed-role-name"
                }
            }
		}
	]
}
```

##### Reference:

- [AWS JSON policy elements: NotPrincipal](https://docs.aws.amazon.com/IAM/latest/UserGuide/reference_policies_elements_notprincipal.html)

