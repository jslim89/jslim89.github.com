---
title: How to add more hosts to phpMyAdmin
date: 2018-07-20 22:18:40
tags:
- php
- mysql
- mysql-client
---

If want to add in more hosts in phpMyAdmin, just edit the file **config.inc.php**

Replace the following content

```php
/* Authentication type */
$cfg['Servers'][$i]['auth_type'] = 'cookie';
/* Server parameters */
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;
```

with this

```php
/**
 * Servers configuration
 */
$i = 0;

/**
 * First server
 */
$hosts = [
    [
        'verbose' => 'Local DB',
        'host' => '127.0.0.1',
        'user' => 'user_1',
        'password' => 'pass_1',
    ],
    [
        'verbose' => 'Staging server',
        'host' => '123.123.123.123',
        'user' => 'user_2',
        'password' => 'pass_2',
    ],
    [
        'verbose' => 'Production DB',
        'host' => '12.12.12.12',
        'user' => 'user_3',
        'password' => 'pass_3',
    ],
];
for ($x = 0; $x < count($hosts); $x++) {
    $i++;
    $cfg['Servers'][$i] = array_merge($hosts[$x], [
        /* Authentication type */
        'auth_type' => 'config',
        /* Server parameters */
        'compress' => false,
        'AllowNoPassword' => true,
    ]);

/**
 * phpMyAdmin configuration storage settings.
 */

/* User used to manipulate with storage */
// $cfg['Servers'][$i]['controlhost'] = '';
// $cfg['Servers'][$i]['controlport'] = '';
// $cfg['Servers'][$i]['controluser'] = 'pma';
// $cfg['Servers'][$i]['controlpass'] = 'pmapass';

/* Storage database and tables */
// $cfg['Servers'][$i]['pmadb'] = 'phpmyadmin';
// $cfg['Servers'][$i]['bookmarktable'] = 'pma__bookmark';
// $cfg['Servers'][$i]['relation'] = 'pma__relation';
// $cfg['Servers'][$i]['table_info'] = 'pma__table_info';
// $cfg['Servers'][$i]['table_coords'] = 'pma__table_coords';
// $cfg['Servers'][$i]['pdf_pages'] = 'pma__pdf_pages';
// $cfg['Servers'][$i]['column_info'] = 'pma__column_info';
// $cfg['Servers'][$i]['history'] = 'pma__history';
// $cfg['Servers'][$i]['table_uiprefs'] = 'pma__table_uiprefs';
// $cfg['Servers'][$i]['tracking'] = 'pma__tracking';
// $cfg['Servers'][$i]['userconfig'] = 'pma__userconfig';
// $cfg['Servers'][$i]['recent'] = 'pma__recent';
// $cfg['Servers'][$i]['favorite'] = 'pma__favorite';
// $cfg['Servers'][$i]['users'] = 'pma__users';
// $cfg['Servers'][$i]['usergroups'] = 'pma__usergroups';
// $cfg['Servers'][$i]['navigationhiding'] = 'pma__navigationhiding';
// $cfg['Servers'][$i]['savedsearches'] = 'pma__savedsearches';
// $cfg['Servers'][$i]['central_columns'] = 'pma__central_columns';
// $cfg['Servers'][$i]['designer_settings'] = 'pma__designer_settings';
// $cfg['Servers'][$i]['export_templates'] = 'pma__export_templates';
} // <------- REMEMBER TO CLOSE
```
