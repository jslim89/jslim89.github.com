---
layout: post
title: "Python script to simulate multiple concurrent requests to web application"
date: 2016-06-29 17:03:14 +0800
comments: true
tags: 
- python
- webapp
---

I first created this script to heavy test the web application hosted in elastic beanstalk.

Why **python**? Just for fun :)

Let's begin

First, define a main function for the python script

```python
import datetime
import time

def main():
    for i in range(0, 20):
        print datetime.datetime.now()

        # http requests goes here

        time.sleep(1) # every 1 second submit http requests

if __name__ == "__main__":
    main()
```

Then create a function to perform http requests

```python
...
import requests
import re
import random

def http_test():

    # define a base url
    base_url = 'http://example.com'

    # Assumed that your web application need to login
    url = base_url + '/auth/login'
    payload = {
        'username': 'root',
        'password': 'root',
    }
    res = requests.post(url, data=payload)

    # we need the session cookie
    cookies = res.cookies

    # generate sales report
    url = base_url + '/reports/sales'

    # need to pass the cookies in order to tell the server that I'm "that" person
    # this is GET request, because need to get the CSRF token before can do a POST
    res = requests.get(url, cookies=cookies)
    html = res.text.encode('utf-8')
    # the "pattern" is depends on how you construct your html page
    pattern = 'name="csrf-token" content="(\w+)"'
    matches = re.findall(pattern, html)
    token = matches[0]

    # POST to generate sales report
    payload = {
        'date': '2016-06-27',
        '_token': token,
        'sales_id': random.randint(1, 30),
    }
    res = requests.post(url, cookies=cookies, data=payload)

...
```

_If you don't understand why need the cookie, you can refer [Session hijacking: facebook.com example](/blog/2016/03/29/session-hijacking-facebook-dot-com-example/)._

Note that the above function `http_test()` actually perform **POST login**, **GET sales report**, **POST sales report**

Now in your `main` function, replace `# http requests goes here` with `http_test()`

Make the python script executable

```
$ chmod +x http_requests.py
```

if you run this script, means 1 client with 20 _(for loop)_ x 3 requests, which equals to 60 requests one after one.

What if want to make it 100 concurrent connections?

Simply create another shell script, let's name it **run.sh**

```
for i in {1..100}
do
    echo "Requests ($i) begin"
    ./http_requests.py &
done
```

also make it executable

```
$ chmod +x run.sh
```

Then run it

```
$ ./run.sh
```

Now you can simulate 100 concurrent connections with each 60 requests.

You can download the scripts here:

- [run.sh](/attachments/posts/2016-06-29-python-script-to-simulate-multiple-concurrent-requests-to-web-application/run.sh)
- [http_requests.py](/attachments/posts/2016-06-29-python-script-to-simulate-multiple-concurrent-requests-to-web-application/http_requests.py)

References:

- [How to start multiple processes in Bash](http://stackoverflow.com/questions/5238103/how-to-start-multiple-processes-in-bash/5238146#5238146)
