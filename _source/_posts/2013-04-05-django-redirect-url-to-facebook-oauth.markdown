---
layout: post
title: "Django - Redirect URL to facebook oAuth"
date: 2013-04-05 12:11
comments: true
categories: 
- python
- django
---

In your **views.py**

```py
from django.http import HttpResponse
from projectname import settings
from urllib import urlencode

def index(request):
    params = {
        'client_id': settings.FACEBOOK_APP_ID,
        'redirect_uri': 'https://www.facebook.com/yourappname/app_' + settings.FACEBOOK_APP_ID,
        'scope': 'email,user_birthday,offline_access,publish_stream',
    }
    redirect_url = 'https://www.facebook.com/dialog/oauth/?' + urlencode(params)
    redirect_code = """
        <script type="text/javascript">
        top.location.href = '%s';
        </script>
    """ % redirect_url;

    return HttpResponse(redirect_code, mimetype='text/html')
```

Make sure you add Facebook details to **projectname/settings.py**

```py
...
FACEBOOK_APP_ID = '12332358235'
FACEBOOK_APP_SECRET = '2839fsd9fh29hg897eyr8g'
```

One more thing, be sure in your facebook app setting **Website with Facebook Login** set to your app URL

![facebook setting](http://jslim89.github.com/images/posts/2013-04-05-django-redirect-url-to-facebook-oauth/facebook-login-setting.png)

Hurray! You've done

_References:_

* _[How to redirect the url after logging into Facebook?](http://stackoverflow.com/questions/5730545/how-to-redirect-the-url-after-logging-into-facebook#answers)_
* _[OAuth Dialog](https://developers.facebook.com/docs/reference/dialogs/oauth/)_
