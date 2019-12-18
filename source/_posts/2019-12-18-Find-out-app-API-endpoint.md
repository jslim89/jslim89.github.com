---
title: Find out app API endpoint
date: 2019-12-18 23:02:23
tags:
- hacking
- mitm
- proxy
---

Have you ever think of, to find out any app API endpoint?

I'm just know about this. I will be doing this in iPhone.

## Download mitmproxy

[mitmproxy](https://mitmproxy.org/) is free & open source.

```
$ brew install mitmproxy
```

once installed, run it

```
$ mitmweb
```

it will open up your browser, you will see this

![mitmweb](/images/posts/2019-12-18-Find-out-app-API-endpoint/mitmproxy-web.png)

## Configure in iPhone

![iPhone proxy](/images/posts/2019-12-18-Find-out-app-API-endpoint/iphone-proxy.jpeg)

1. Go to **Settings** -> **Wi-Fi**, select the Wi-Fi you currently connected. Make sure it's same network with your Mac
2. Set it to manual
3. Set the IP of your Mac _(you can find it from `ifconfig` in terminal)_

Open Safari in your iPhone

![special URL mitm.it](/images/posts/2019-12-18-Find-out-app-API-endpoint/mitm.it.jpeg)

1. Click on the Apple icon, and install certificate
2. Go to **Settings** -> **General** -> **Profile**, install `mitmproxy` profile
3. Go to **Settings** -> **General** -> **About** -> **Certificate Trust Settings**, enable full trust for root certificates for `mitmproxy`

## Test it out

All settings is done, now you can try to open an app

![mitm request/response](/images/posts/2019-12-18-Find-out-app-API-endpoint/mitm-request.png)

You can see the request header, and response as well
