---
title: Setup OpenVPN in AWS for RDS access
date: 2019-06-07 13:17:41
tags:
  - aws
  - rds
  - openvpn
  - server
---

When we use AWS, often we will use RDS, for security reason, is better to not to expose to public.

![RDS settings](/images/posts/2019-06-07-Setup-OpenVPN-in-AWS/rds-settings.png)

What if we want to access via MySQL client? Here's why we need VPN.

## 1. Create an EC2 instance

After you created an EC2 instance _(I chose Ubuntu 18.04)_, then create an elastic IP and associate with this instance.

Because we don't want risk the IP to change when the instance restarted.

![AWS Elastic IP](/images/posts/2019-06-07-Setup-OpenVPN-in-AWS/aws-ec2-elastic-ip.png)

## 2. [Follow this post](https://www.cyberciti.biz/faq/howto-setup-openvpn-server-on-ubuntu-linux-14-04-or-16-04-lts/).

```
ubuntu@ip-172-xxx-xxx-xxx:~$ sudo bash openvpn-install.sh

Welcome to this OpenVPN "road warrior" installer!

I need to ask you a few questions before starting the setup.
You can leave the default options and just press enter if you are ok with them.

First, provide the IPv4 address of the network interface you want OpenVPN
listening to.
IP address: 172.xxx-xxx-xxx

This server is behind NAT. What is the public IPv4 address or hostname?
Public IP address / hostname: 54.yyy.yyy.yyy

Which protocol do you want for OpenVPN connections?
   1) UDP (recommended)
   2) TCP
Protocol [1-2]: 1

What port do you want OpenVPN listening to?
Port: 1194

Which DNS do you want to use with the VPN?
   1) Current system resolvers
   2) 1.1.1.1
   3) Google
   4) OpenDNS
   5) Verisign
DNS [1-5]: 1

Finally, tell me your name for the client certificate.
Please, use one word only, no special characters.
Client name: js

Okay, that was all I needed. We are ready to set up your OpenVPN server now.
Press any key to continue...
```

The first time, enter the private IP _(should be auto populated)_, then 2nd time is the elastic IP.

Now, you still won't be able to connect to VPN yet.

## 3. Create a security group

Because the new instance doesn't open some ports by default, now let create.
In the inbound tab:

| Type            | Protocol | Port Range | Source    | Description |
| --------------- | -------- | ---------- | --------- | ----------- |
| Custom UDP Rule | UDP      | 1194       | 0.0.0.0/0 |             |
| SSH             | TCP      | 22         | 0.0.0.0/0 |             |
| Custom TCP Rule | TCP      | 943        | 0.0.0.0/0 |             |
| HTTPS           | TCP      | 443        | 0.0.0.0/0 |             |

Then go back to instance menu, select the OpenVPN instance, and associate the VPN security group.

![EC2 change security group](/images/posts/2019-06-07-Setup-OpenVPN-in-AWS/ec2-change-security-group.png)

## 4. Update RDS security group

In the inbound tab

| Type         | Protocol | Port Range | Source             | Description |
| ------------ | -------- | ---------- | ------------------ | ----------- |
| MYSQL/Aurora | TCP      | 3306       | 172.xxx.xxx.0/24   | Web App     |
| MYSQL/Aurora | TCP      | 3306       | 172.xxx.xxx.xxx/32 | OpenVPN     |

After this, you should be able to connect your local to VPN, and connect MySQL client to RDS.

## References:

- [How you can use OpenVPN to safely access private AWS resources](https://www.freecodecamp.org/news/how-you-can-use-openvpn-to-safely-access-private-aws-resources-f904cd24f890/)
