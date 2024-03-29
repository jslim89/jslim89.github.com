---
layout: page
title: MySQL/Maria DB
permalink: /short-notes/mysql/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

### MySQL

#### Create user and grant permission

```sql
# Create a database called 'db_foo'
CREATE DATABASE db_foo;
# Create a user 'foo' which only allowed access via 'localhost'
# i.e. mysql -u foo -p
CREATE USER 'foo'@'localhost' IDENTIFIED BY 'password';
# Grant all privileges to user 'foo' only for database 'db_foo'
# if want to allowed for ALL database, just change 'db_foo.*' to '*.*'
GRANT ALL PRIVILEGES ON db_foo.* TO 'foo'@'localhost' WITH GRANT OPTION;
# Create a user 'foo' which allowed access remotely
# i.e. mysql -u foo -h 192.168.1.xxx -p
CREATE USER 'foo'@'%' IDENTIFIED BY 'another_or_same_password';
# Grant all privileges to user 'foo' only for database 'db_foo'
GRANT ALL PRIVILEGES ON db_foo.* TO 'foo'@'%' WITH GRANT OPTION;
```

##### Reference:

- [MySQL: 6.3.2. Adding User Accounts](http://dev.mysql.com/doc/refman/5.5/en//adding-users.html)

---

#### Dump database to sql file

```sh
$ mysqldump -u root -p database_name > filename.sql
# enter password
```

`-u` - refer to user (normally use root)  
`-p` - will prompt you password

---

#### Select current DateTime

```sql
mysql > SELECT NOW();
```

Output:

```
+---------------------+
| NOW()               |
+---------------------+
| 2012-12-11 17:28:30 |
+---------------------+
1 row in set (0.00 sec)
```

##### Reference:

- [Mysql/Php - Current date and time](http://stackoverflow.com/questions/3618401/mysql-php-current-date-and-time#answers)

---

#### String concatenation with IF condition

This is to avoid the **extra comma** appear if some of the component doesn't exist.

```sql
SELECT CONCAT(
    address
    , IF(address2 IS NULL OR address2 = '', '', CONCAT(', ', address2))
    , IF(address3 IS NULL OR address3 = '', '', CONCAT(', ', address3))
    , IF(postcode IS NULL OR postcode = '', '', CONCAT(', ', postcode))
    , IF(state IS NULL OR state = '', '', CONCAT(', ', state))
) AS full_address
FROM what_ever;
```

##### Reference:

- [MySQL Forums :: Views :: IF and CONCAT](http://forums.mysql.com/read.php?100,94227,94227)

---

#### Dump a specific table

```sh
$ mysqldump -u user -p db_name table_name1 table_name2 > file.sql
```

##### Reference:

- [How do you mysqldump specific table(s)?](http://dba.stackexchange.com/questions/9306/how-do-you-mysqldump-specific-tables#answer-9309)

---

#### Get a specific opponent from explode equivalent string

```sql
SELECT SUBSTRING_INDEX(
    SUBSTRING_INDEX('123-456-789-abc-this_is_what_we_want-xyz', '-',5)
    , '-', -1
);
```

Look at the inner `SUBSTRING_INDEX`, we explode by **-** _(dash)_, so the **5** is to take from the **1st** to **5th** opponent.  
So we get **123-456-789-abc-this_is_what_we_want**.

Now the outer `SUBSTRING_INDEX` is to get **1** opponent start from the left, which is the **last** opponent, is what we want **this_is_what_we_want**.

---

#### Migrate data form table A to table B

```sql
INSERT INTO table_b (col_1, col_2, col_3)
(SELECT col_1, col_2, col_3
FROM table_a
WHERE col_1 = 'foo');
```

##### Reference:

- [MySQL : perform a big data migration between tables](http://dba.stackexchange.com/questions/24116/mysql-perform-a-big-data-migration-between-tables)

---

#### `printf` like in MySQL

```sql
mysql> SELECT LPAD(`id` , 4, '0') FROM `table_name` LIMIT 10;
+---------------------+
| LPAD(`id` , 4, '0') |
+---------------------+
| 0001                |
| 0002                |
| 0003                |
| 0004                |
| 0005                |
| 0006                |
| 0007                |
| 0008                |
| 0009                |
| 0010                |
+---------------------+
```

Which is similar to `printf("%04d", value);`

##### Reference:

- [printf or similar inside an SQL query?](http://stackoverflow.com/questions/7266031/printf-or-similar-inside-an-sql-query/7266053#7266053)

---

#### Copy a table structure from a database to another database

```sql
mysql> CREATE TABLE source_database.foo_table LIKE dest_database.foo_table;
```

##### Reference:

- [Copy an existing MySQL table to a new table](http://www.tech-recipes.com/rx/1487/copy-an-existing-mysql-table-to-a-new-table/)

---

#### Add root password to MySql in XAMPP

```sh
# initially was no password, so can login directly
$ /Applications/XAMPP/xamppfiles/bin/mysql -u root
```

```mysql
mysql> UPDATE mysql.user SET Password=PASSWORD('MyNewPass') WHERE User='root';
mysql> FLUSH PRIVILEGES;
```

Edit the phpMyAdmin config, otherwise could not be login

```sh
$ sudo vi /Applications/XAMPP/xamppfiles/phpmyadmin/config.inc.php
```

Change the password

```php
...
/* Authentication type */
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = 'MyNewPass'; /* this line */
...
```

##### Reference:

- [C.5.4.1. How to Reset the Root Password](http://dev.mysql.com/doc/refman/5.0/en/resetting-permissions.html)

---

#### Auto-input password on mysql shell command

Typical way, it will prompt for password

```sh
$ mysql -u root -p
Enter password:
```

Another way, it you passed the password through the input argument

```sh
# username: root
# password: MyAwesomePassword
$ mysql -uroot -pMyAwesomePassword
```

##### Reference:

- [BASH script not reading input mysql password [duplicate]](http://stackoverflow.com/questions/13649364/bash-script-not-reading-input-mysql-password/13649419#13649419)

---

#### Rename a column with FULLTEXT index

```sql
ALTER TABLE tablename ADD column_new_name VARCHAR(100) AFTER column_old_name;
ALTER TABLE tablename MODIFY column_new_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci; /* make sure the collation is same as column_old_name */
/* copy the data from column_old_name to column_new_name */
UPDATE tablename SET column_new_name = column_old_name;
ALTER TABLE tablename ADD FULLTEXT(column_new_name);
/* Delete old column */
alter TABLE tablename DROP COLUMN column_old_name;
```

---

#### Update all dates by addeing 30 minutes

```sql
UPDATE `tablename` SET `created_date` = DATE_ADD(`created_date` , INTERVAL 30 MINUTE ) WHERE `column` = 'value'
```

##### Reference:

- [adding 30 minutes to datetime php/mysql](http://stackoverflow.com/questions/1436827/adding-30-minutes-to-datetime-php-mysql/1436856#1436856)

---

#### Check is MySQL user exists

```sql
SELECT `User`, `Host`
FROM `mysql`.`user`
WHERE `User` = 'foouser'
```

##### Reference:

- [How do I search to see if a MySQL user exists on the system?](http://dba.stackexchange.com/questions/682/how-do-i-search-to-see-if-a-mysql-user-exists-on-the-system/answer-685#answer-685)

---

#### Row size too large

Error shown is

```
SQLSTATE[42000]: Syntax error or access violation: 1118 Row size too large (> 8126). Changing some columns to TEXT or BLOB or using ROW_FORMAT=DYNAMIC or ROW_FORMAT=COMPRESSED may help. In current row format, BLOB prefix of 768 bytes is stored inline.
```

The table here contains many columns with `TEXT` data type, each column has a long long paragraph, thus this error rised

**Solution**

Edit the file `my.cnf` _(for XAMPP in Mac is on `/Applications/XAMPP/xamppfiles/etc/my.cnf`)_

```
innodb_file_per_table
innodb_file_format = Barracuda
```

Add the content above to `[mysqld]` section. Then run sql

```sql
ALTER TABLE myawesome_table ENGINE=InnoDB ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8; 
```

Restart XAMPP server

##### Reference:

- [Issue with maximum row size in MySQL](http://serverfault.com/questions/326836/issue-with-maximum-row-size-in-mysql/327222#327222)

---

#### Date '0000-00-00' error on MySQL 5.7

Cannot even update the value

```
UPDATE table SET birthday = null WHERE birthday = '0000-00-00';

#1292 - Incorrect date value: '0000-00-00' for column 'birthday' at row 1
```

But it works in this way

```
UPDATE table SET birthday = null WHERE CAST(birthday AS CHAR(10)) = '0000-00-00';
```

##### Reference:

- [MySQL Incorrect datetime value: '0000-00-00 00:00:00'](http://stackoverflow.com/questions/35565128/mysql-incorrect-datetime-value-0000-00-00-000000/37780259#37780259)

---

#### Export a query to a csv file

```
mysql -h 123.123.123.123 -u db_username -p db_name < custom-query.sql > output.tsv
```

##### Reference:

- [How can I output MySQL query results in CSV format?](https://stackoverflow.com/questions/356578/how-can-i-output-mysql-query-results-in-csv-format/2601837#2601837)

---

### MariaDB

#### mysqld start failed

```
$ sudo rm /var/lib/mysql/{ib_logfile0,ib_logfile1,aria_log_control}
$ sudo /etc/init.d/mysql restart
```

##### Reference:

- [Cannot start mysqld after switching to MariaDB](http://wood1978.dyndns.org/~wood/wordpress/2013/03/26/cannot-start-mysqld-after-switching-to-mariadb/)
