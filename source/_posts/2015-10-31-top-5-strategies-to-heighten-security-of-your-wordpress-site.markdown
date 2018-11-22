---
layout: post
title: "Top 5 Strategies to Heighten Security of Your WordPress Site"
date: 2015-10-31 00:01:09 +0800
comments: true
tags: 
- wordpress
- security
---

## Author Bio :

Sophia is a renowned WordPress developer by profession. If you're about to [Hire WordPress developer](http://www.wordprax.com/services/hire-wordpress-developers), then you can get in touch with her. Sophia already has multiple WordPress-related articles under her name.

Despite investing a significant amount of time and efforts, your WordPress website may still be vulnerable to attacks. In fact, on an average nearly 20,000 sites are identified as hacked every day. Unfortunately, you won't find any “One size Fits All” approach that can help make a WordPress website secure from getting hacked. But, there are a few simple to follow strategies that can help harden your WordPress site security. In this post, I've listed 5 simple yet crucial strategies that will safeguard your site against potential threats.


### 1. Limit The Use Of the Term “WordPress” On Your Site

While having a WordPress site can help establish your business as a strong online identity, the irony is that very mention of the terminology “WordPress” on your site can put it at risk. That's because, the immense popularity of the WordPress CMS makes it a favorite target for hackers. Malicious users can launch brute force attacks, if they'll identify the WordPress version you're running, or the WP theme or plugins you have installed. Of course, you can completely avoid using the term WordPress on your website, but try to limit its use.

You can deal with such an issue by changing some default permalinks of your WordPress site. Doing so, will help keep your site safe against brute force attacks, SQL-injection and other attacks.


### 2. Don't Forget to Change Default Administrator Name

![Strong password](http://jslim89.github.com/images/posts/2015-10-31-top-5-strategies-to-heighten-security-of-your-wordpress-site/password.png)

One common mistake that site owners tend to make is that they overlook or forget to change the default username (i.e. admin) for their WordPress account. Keep in mind that not changing the administrator name makes the brute force successful, as hackers just need to guess your password to break into your site admin interface.

You just need to create a new admin user for changing your website default user name. Once you've created a new admin user account, make sure to delete the original default admin user from your admin panel.


### 3. Use an Updated WordPress Version

![Updated version of WordPress](http://jslim89.github.com/images/posts/2015-10-31-top-5-strategies-to-heighten-security-of-your-wordpress-site/updated.jpg)

Each new WordPress release comes with several new and improved features. More importantly, the latest version boasts security fixes to resolve bugs found in previous WP version. Thus, running your site on an up-to-date WordPress version will not just help add many more features, but will also help fix security loopholes and issues.

For example, in order to keep WordPress sites secure the latest WordPress version 4.3 has introduced a new approach for generating secure passwords. When using this new version, you won't be receiving passwords through email instead you will be getting a password reset link. The best part is that WordPress will automatically create a password for every new user profile.


### 4. Correct File Permissions is Crucial For Website Security

Not using the correct right permissions for your website files and directories can impose a threat to your website security. Wondering why? As you may know, you can write on your WordPress site from the server. This often creates a problem when your site runs on a shared server. Such kind of server hosts multiple sites, and thus there is a greater possibility that your files can be accessed by some other site owner. You won't face any problem if your file permission allows users to read it. However, avoid assigning write permission on your file, or some malicious user may write faulty code, making it vulnerable to attacks.

With the following commands, you can protect all your WordPress files and folders safe and accessible only to authenticated users:

```
$ find /path/to/your/wordpress/install/ -type d -exec chmod 755 {} \;
$ find /path/to/your/wordpress/install/ -type f -exec chmod 644 {} \;
```


### 5. Use Plugins To Address Specific Security Issues

Last but definitely not the least, there is a plugin available online that can help meet your needs, including measures to keep your website protected against attacks. Here are 3 great and highly useful plugins that ensures in making your site

- All In One WP Security And Firewall: This is an excellent plugin that helps monitor a WordPress site for vulnerabilities. That's not it! The plugin enforces latest security techniques to handle such vulnerabilities. Interestingly enough, it makes use of exceptional “security points grading system” that helps analyze if you're protecting your site accurately on the basis of security features you've activated or not.
- WP Security Audit Log: Wouldn't it be better if you can stop possible hacker attacks from happening on your WordPress site? This can be achieved with the help of WP Security Audit Log plugin. It helps to store an audit log including the changes and activities that helps improve WordPress productivity and stop hacker attacks. Security log is an extremely useful plugin for professionals and site owners to track changes that takes place on multiple website.
- Google Authenticator: Two step login authentication has proved an effective technique that hardens website security by adding an extra layer of security to it. Using such a technique requires users to enter an authenticated code that they receive on their mobile phone along with a username and password to login into the website's admin interface. For this purpose, you can make use of the Google Authenticator plugin. It provides an app that enables two-way authentication on Android, or iPhone or Blackberry device.


### Conclusion

So, that's it! Hope that reading the post will help you find the suitable ways to increase your website security to a great extent. However, make sure to create a backup of your site before making any update and changes to the WordPress core files, as it will help you restore your original website in case it is being hacked and damaged by hackers.
