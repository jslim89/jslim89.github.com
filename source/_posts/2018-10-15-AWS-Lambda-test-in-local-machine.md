---
title: AWS Lambda test in local machine
date: 2018-10-14 14:58:50
tags:
- aws
- lambda
- node
---

I believe many developer first time deal with AWS Lambda will have this question: "How can I test the code in my machines?"

After keep trying hundreds of times, I found a way to do it.

I will show the example in Node.js

### 1. Setup a new project

```
$ mkdir mylambda
$ cd mylambda/
$ npm init
```

Input all necessary info

```
$ npm install aws-sdk --save
```

### 2. Create a base script

```js
const aws = require('aws-sdk');
var s3 = new aws.S3();

exports.handler = function(event, context, callback) {
  try {
    // your script goes here

    return callback(null, 'Success');
  } catch (error) {
    console.error('Error:', error);
    return callback(null, 'Something wrong');
  }
};
```

### 3. How to test it?

First, we need to know how the `event` & `context` object looks like

Simple, just output to console

```js
console.log('event', JSON.stringify(event));
console.log('context', JSON.stringify(context));
```

Upload to lambda, and trigger it. We need to trigger it for the first time only we know how it looks like.

Let's copy the output for event and make it a file named **event.json** in the project root. Same goes for **context**.

Example output:

**event.json**

```json
{
  "Records": [
    {
      "eventVersion": "2.0",
      "eventSource": "aws:s3",
      "awsRegion": "us-west-2",
      "eventTime": "2018-10-12T08:30:32.715Z",
      "eventName": "ObjectCreated:Put",
      "userIdentity": {
        "principalId": "AWS:AAAABBBCCCCDDDDDAAAAA"
      },
      "requestParameters": {
        "sourceIPAddress": "192.181.181.181"
      },
      "responseElements": {
        "x-amz-request-id": "9999988887777766",
        "x-amz-id-2": "999999oaaaaaaaikkkkkkkOn/fCCCCCCCCAwIIIIIIeSPDSoYYjhhhhhhhhhJ+ptYrktJJJJJJJ="
      },
      "s3": {
        "s3SchemaVersion": "1.0",
        "configurationId": "88888888-4444-3333-2222-111111111111",
        "bucket": {
          "name": "myawesomebucket",
          "ownerIdentity": {
            "principalId": "88888AAAAACCCC"
          },
          "arn": "arn:aws:s3:::myawesomebucket"
        },
        "object": {
          "key": "path/to/your/file.extension",
          "size": 889700,
          "eTag": "88888877777716666666942b5333333e",
          "sequencer": "CCCCCCC77777788888"
        }
      }
    }
  ]
}
```

**context.json**

```json
{
    "callbackWaitsForEmptyEventLoop": true,
    "logGroupName": "/aws/lambda/my-lambda-function",
    "logStreamName": "2018/10/12/[$LATEST]88888887c65cccccccaaaaaa99999999",
    "functionName": "my-lambda-function",
    "memoryLimitInMB": "128",
    "functionVersion": "$LATEST",
    "invokeid": "88888888-4444-3333-2222-111111111111",
    "awsRequestId": "11111111-cccc-aaaa-9999-777777777777",
    "invokedFunctionArn": "arn:aws:lambda:us-west-2:888888999995:function:my-lambda-function"
}
```

### 4. Test run in local

Add this block to the event handler, e.g.

```js
exports.handler = function(event, context, callback) {
  try {
    var s3 = new aws.S3({
      accessKeyId: '<aws key id>',
      secretAccessKey: '<aws secret>',
      region: event.Records[0].awsRegion,
    });
```

Then, at the most bottom, add this

```js
exports.handler(require('./event'), require('./context'), function(err, obj) {
    console.log('error', err);
    console.log('obj', obj);
});
```

Now you can run

```
$ node index.js
```

**NOTE: Remember to remove the code you added in _Section 4_ before upload to production.**

References:

- [AWS Lambda - How to stop retries when there is a failure](https://stackoverflow.com/questions/49069363/aws-lambda-how-to-stop-retries-when-there-is-a-failure/49069568#49069568)
