---
layout: post
title: "PHP - create database with PDO"
date: 2014-04-09 21:17:33 +0800
comments: true
categories: 
- php
---

```php
<?php
define('DB_HOST', '127.0.0.1'); // use ip address instead of `localhost`
// existing user that has permission to create database and grant access
define('DB_ROOT_USER', 'root');
define('DB_ROOT_PASS', 'rootpass');

// the database you want to create
$dbname = 'my_new_db';
// specific user for this particular database
$dbuser = 'my_new_db_user';
$dbpass = 'new_dbpassword';

try {
    // login with root user
    $dbh = new PDO('mysql:host='.DB_HOST, DB_ROOT_USER, DB_ROOT_PASS);

    // create database
    $dbh->exec(
        "CREATE DATABASE `$dbname`;
        CREATE USER '$dbuser'@'localhost' IDENTIFIED BY '$dbpass';
        GRANT ALL ON `$dbname`.* TO '$dbuser'@'localhost';
        FLUSH PRIVILEGES;"
    ) 
    or die(print_r($dbh->errorInfo(), true));

    // use database
    $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.$dbname, DB_ROOT_USER, DB_ROOT_PASS);

    // optional: import existing sql file if you have
    $imported = $dbh->exec(file_get_contents('existingdata.sql'));
    if ($imported === false) { // even if success, it may also return some code
        die(print_r($dbh->errorInfo(), true));
    }

} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}
```

_References:_

* _[Can I create a database using PDO in PHP](http://stackoverflow.com/questions/2583707/can-i-create-a-database-using-pdo-in-php/6549440#6549440)_
* _[Troubleshooting “No such file or directory” when running `php app/console doctrine:schema:create`](http://stackoverflow.com/questions/6259424/troubleshooting-no-such-file-or-directory-when-running-php-app-console-doctri/9251924#9251924)_
* _[PHP PDO. error number '00000' when query is correct](http://stackoverflow.com/questions/11813911/php-pdo-error-number-00000-when-query-is-correct/11813915#11813915)_
