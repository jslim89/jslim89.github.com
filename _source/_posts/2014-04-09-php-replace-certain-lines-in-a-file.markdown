---
layout: post
title: "PHP - replace certain lines in a file"
date: 2014-04-09 22:24:07 +0800
comments: true
categories: 
- php
---

I've come across such a scenario, to duplicate a project when a new user sign up, some of the settings in `config.php` have to change accordingly.

Example file content:

**config.php**

```php
<?php
define('DATABASE_NAME', 'my_db');

define('DATABASE_USER', 'my_db_user');

define('DATABASE_PASSWORD', 'my_db_pass');

define('CLIENT_NAME', '');

define('CLIENT_KEY','');

define...
```

```php
<?php
$client = ....;
$client_key = ...;
$config_file = $client.'/config.php';
$newcontent = '';
// open the file
$fp = fopen($config_file, 'r');
if ($fp) {
    // read the content line by line
    while (($line = fgets($fp)) !== false) {
        if (preg_match('/CLIENT_NAME/', $line)) {
            // e.g. want to replace the client name
            $newcontent .= 'define(\'CLIENT_NAME\', \'' . $client . '\');'.PHP_EOL;
        } else if (preg_match('/CLIENT_KEY/', $line)) {
            // e.g. want to replace client secret key
            $newcontent .= 'define(\'CLIENT_KEY\', \'' . $client_key . '\');'.PHP_EOL;
        } else { // otherwise just append the line
            $newcontent .= $line;
        }
    }
}
fclose($fp);
// replace the file with new content
file_put_contents($config_file, $newcontent);
```
