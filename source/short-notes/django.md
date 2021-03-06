---
layout: page
title: Python - Django
permalink: /short-notes/django/
date: 2020-05-24 21:13:51
comments: false
sharing: true
footer: true
---

https://www.djangoproject.com/

#### URL configure get 404

First, the URL file will be loaded by setting in **project_dir/project/settings.py**

```py
ROOT_URLCONF = 'project.urls'
```

Assumed that you already have an app named **myapp**

Include the URL config file from your app

Edit **project_dir/project/urls.py**

```py
...
urlpatterns = patterns('',
    ...
    # This is to include the urls.py from myapp
    # if you add a trailing slash, will get 404 error
    # i.e. (r'^$/', include('myapp.urls')),
    (r'^$', include('myapp.urls')),
    ...
)
...
```

##### Reference:

- [URL dispatcher](https://docs.djangoproject.com/en/dev/topics/http/urls/)

---

#### Get COOKIE

```py
request.COOKIES.get('key', 'default')
```

##### Reference:

- [Testing for cookie existence in Django](http://stackoverflow.com/questions/1466732/testing-for-cookie-existence-in-django#answers)

---

#### Model date comparison

```py
from datetime import date

user = User.objects.get(id=1)
if user.accessed_date == date.today():
    # do something
else:
    # do other thing
```
