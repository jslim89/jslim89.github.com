---
layout: post
title: "Create Facebook page app using Django with Nginx"
date: 2013-04-01 21:31
comments: true
categories: 
- setup-configuration
- python
- django
---

This is continue from [previous post](http://jslim89.github.com/blog/2013/03/29/setup-django-slash-mysql-in-ubuntu-server-in-vmware-fusion/).

## Enable SSL
As we know that Facebook app required SSL, so first we must self-sign a certificate

```
# Generate a private key
$ openssl genrsa -des3 -out server.key 1024

# Generate a CSR (Certificate Signing Request)
$ openssl req -new -key server.key -out server.csr

# Remove Passphrase from key
$ cp server.key server.key.orig
$ openssl rsa -in server.key.orig -out server.key

# Generate a self-signed certificate
$ openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt

# Keep the key and cert to a directory
$ sudo mv server.key /etc/ssl/private/yoursite.com.key
$ sudo mv server.crt /etc/ssl/certs/yoursite.com.crt
```

Configure Nginx - Edit **/etc/nginx/sites-available/django.conf**

```
server {
    listen 80;
    ...
}
...
# Add here
server {
    listen 443;
    ssl on;
    ssl_certificate /etc/ssl/certs/yoursite.com.crt
    ssl_certificate_key /etc/ssl/private/yoursite.com.key

    server_name https://test.local.domain;
    access_log /home/username/public_html/projectname/logs/access.log;
    error_log /home/username/public_html/projectname/logs/error.log;

    location /static/ {
        alias /home/username/public_html/projectname/static/;
        expires 30d;
    }

    location /media/ {
        alias /home/username/public_html/projectname/media/;
        expires 30d;
    }

    location / {
        fastcgi_pass 127.0.0.1:8000;
        fastcgi_split_path_info ^()(.*)$;
        include /etc/nginx/fastcgi_params;
        fastcgi_param PATH_INFO $fastcgi_script_name;
        fastcgi_pass_header Authorization;
        fastcgi_intercept_errors off;
    }
}
```
This basically listen to 1 more port, otherwise if you access via **https** will not found.

Add those path that you want it to use **https**  
Add a file in **/home/username/public_html/projectname/middleware.py**
```py
from django.http import HttpResponsePermanentRedirect
from django.conf import settings

class SecureRequiredMiddleware(object):
    def __init__(self):
        self.paths = getattr(settings, 'SECURE_REQUIRED_PATHS')
        self.enabled = self.paths and getattr(settings, 'HTTPS_SUPPORT')

    def process_request(self, request):
        if self.enabled and not request.is_secure():
            for path in self.paths:
                if request.get_full_path().startswith(path):
                    request_url = request.build_absolute_uri(request.get_full_path())
                    secure_url = request_url.replace('http://', 'https://')
                    return HttpResponsePermanentRedirect(secure_url)
        return None
```

In **/home/username/public_html/projectname/projectname/settings.py**
```py
MIDDLEWARE_CLASSES = (
    ...
    # the line below if not comment out, 403 error will occur and I still haven't figure it out
    # 'django.middleware.csrf.CsrfViewMiddleware',
    ...
    'middleware.SecureRequiredMiddleware',
)
...
# HTTPS MIDDLEWARE CONFIG
HTTPS_SUPPORT = True
SECURE_REQUIRE_PATHS = (
    '/',
    '/admin/',
    # you can add in more path here if you want the path to use https
)
```

## Create home view

Edit **/home/username/public_html/projectname/newapp/views.py**
```py
from django.template.loader import get_template
from django.template import Context
from django.http import HttpResponse
import datetime

def index(request):
    now = datetime.datetime.now()
    t = get_template('index.html')
    # pass a dynamic value to the view
    html = t.render(Context({'special_date': now}))
    return HttpResponse(html)
```

In **/home/username/public_html/projectname/newapp/urls.py**
```py
from django.conf.urls import patterns, url
from newapp import views

urlpatterns = patterns('newapp',
    url(r'^$', views.index)
)
```

In **/home/username/public_html/projectname/projectname/urls.py**, this is the main URL setting
which will load the URLs in **newapp**
```py
...
urlpatterns = patterns('',
    (r'^$', include('newapp.urls')),
    ...
)
...
```

### Create a template for home page

First, add the template dir to **/home/username/public_html/projectname/projectname/settings.py**
```py
import os.path
...
TEMPLATE_DIRS = (
    ...
    # in case that it is run on windows, so have to change to forward slash no matter how
    os.path.join(os.path.dirname(__file__), 'newapp/templates'.replace('\\', '/')),
)
```

Create a directory to keep all templates related to **newapp**
```
$ mkdir /home/username/public_html/projectname/newapp/templates
$ touch /home/username/public_html/projectname/newapp/templates/index.html
```

In case that you want to keep your **css** or **js** or **image** files, so create a directory for them
```
$ mkdir /home/username/public_html/projectname/static
$ mkdir /home/username/public_html/projectname/static/css
$ mkdir /home/username/public_html/projectname/static/js
$ mkdir /home/username/public_html/projectname/static/images

# Add a stylesheet
$ touch /home/username/public_html/projectname/static/css/styles.css
```

In **/home/username/public_html/projectname/projectname/settings.py**

```py
...
STATIC_URL = '/static/'
...
STATICFILES_DIRS = (
    ...
    # just keep all static file here
    '/home/username/public_html/projectname/static',
)
```

Edit the **index.html**

```
<!DOCTYPE html>
<html>
    <head>
        <!-- load the function -->
        <title>Hello World</title>
        <link rel="stylesheet" type="text/css" href="/static/css/styles.css"/>
    </head>
    <body>
        <h1>Hello world</h1>
        <p>The special date is {{ special_date|date:'F j, Y' }}.</p>
    </body>
</html>
```

## Integrate to Facebook
**Assumed that you already have an App in Facebook as well as a Facebook Page**

Configure the setting

{% img http://jslim89.github.com/images/posts/2013-04-01-create-facebook-page-app-using-django-with-nginx/facebook-app-setting.png facebook setting %}

**Remember to check both _Website with Facebook Login_ and _Page Tab_**

Then add the app to your Facebook Page by type in the URL **https://www.facebook.com/dialog/pagetab?app_id=YOUR_APP_ID&next=YOUR_URL**

Congrat!!! You successfully added your app to Facebook Page.

References:

* _[How to create a self-signed SSL Certificate ...](http://www.akadia.com/services/ssh_test_certificate.html)_
* _[Requiring https for certain paths in Django](http://www.redrobotstudios.com/blog/2010/02/06/requiring-https-for-certain-paths-in-django/)_
