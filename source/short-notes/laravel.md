---
layout: page
title: PHP - Laravel
permalink: /short-notes/laravel/
date: 2020-05-24 21:13:51
comments: false
sharing: true
footer: true
---

https://laravel.com/

### Laravel 4

#### Add custom helper functions with some helper files

```sh
# create a helpers folder
$ mkdir /path/to/laravel/app/helpers

# create a helper file
$ touch /path/to/laravel/app/helpers/debug.php
```

Edit **/path/to/laravel/app/start/global.php**, and add this to the end of file. This is to include it so you can use it anywhere

```php
<?php
...

/*
|--------------------------------------------------------------------------
| Require Helper Files
|--------------------------------------------------------------------------
|
*/
foreach(glob(app_path().'/helpers/*.php') as $h_file) require $h_file;
```

In **/path/to/laravel/app/helpers/debug.php**

```php
if (!function_exists('my_debug')) {
    function my_debug($obj) {
        echo '<pre>';
        print_r($obj);
        echo '</pre>';
    }
}
```

##### Reference:

- [What are the best practices and best places for laravel 4 helpers or basic functions?](http://stackoverflow.com/questions/17088917/what-are-the-best-practices-and-best-places-for-laravel-4-helpers-or-basic-funct/17091089#17091089)

---

#### Add custom config file

Some time we have use a lot of third party API which need to keep the API key. So we can create a custom config file to store that.

```sh
$ touch /path/to/laravel/app/config/custom.php
```

Put content to the file. i.e.

```php
<?php
return array(
    'api' => array(
        'google_map' => array(
            'key' => '98fgy8sy3SDF8f37*#7f',
            'other_param' => 'foobar',
        ),
    ),
);
```

Can retrieve it by

```php
$google_config = \Config::get('custom.api.google_map');
echo 'KEY: ' . $google_config['key'];
echo 'Other: ' . $google_config['other_param'];
```

##### Reference:

- [Configuration](http://codehappy.daylerees.com/configuration)

---

#### Specify `alias` on `JOIN`

**On SQL**

```sql
SELECT table.title, table.desc, cu.first_name, mu.first_name
FROM table
LEFT JOIN user cu ON cu.id = table.created_by  // user who create
LEFT JOIN user mu ON mu.id = table.modified_by // user who modify
```

**Laravel Query Builder**

```php
DB::table('table')
    ->leftJoin('user AS cu', 'cu.id', '=', 'table.created_by')
    ->leftJoin('user AS mu', 'mu.id', '=', 'table.modified_by')
    ->select(
        'table.title'
        , 'table.desc'
        , 'cu.first_name'
        , 'mu.first_name'
    )
    ->get();
```

##### Reference:

- [Laravel join queries AS](http://stackoverflow.com/questions/14318205/laravel-join-queries-as/14320265#14320265)

---

#### Check is POST request

```
if (\Request::isMethod('POST')) {
    // confirmed this is POST request
}
```

---

#### Filter `csrf` for few functions only

```php
$this->beforeFilter('csrf', array('on' => 'post', 'only' => array('func1', 'func2')));
```

---

#### Show the last executed query

```php
$queries = DB::getQueryLog();
$last_query = end($queries);
```

OR a better way

```php
$query->toSql();
```

##### References:

- [Get the query executed in Laravel 3/4](http://stackoverflow.com/questions/14536165/get-the-query-executed-in-laravel-3-4/14536215#14536215)
- [How do I get the query builder to output its raw SQL query as a string?](http://stackoverflow.com/questions/18236294/how-do-i-get-the-query-builder-to-output-its-raw-sql-query-as-a-string/20382987#20382987)

---

#### Pagination check has page

Check whether the pagination has other pages

```php
<?php if ($obj_list->getTotal() > $obj_list->count(): ?>
    <div class="pagination-wrapper">
        <?php echo $obj_list->appends(array(
                'param1' => 'value1',
                'param2' => 'value2',
            ))->links(); ?>
    </div>
<?php endif; ?>
```

If the total objects is more than retrieved objects, that mean it has other pages.

---

#### Query builder multiple ORDER BY

```php
\DB::table('tablename')
    ->orderBy('col_1', 'desc')
    ->orderBy('col_2', 'asc');
```

Just have to invoke multiple times

##### Reference:

- [How to use order by for multiple columns in laravel 4?](http://stackoverflow.com/questions/17006309/how-to-use-order-by-for-multiple-columns-in-laravel-4/17006377#17006377)

---

#### Eloquent get pivot/bridge table attributes

In model class

```php
 return $this->belongsToMany('Bar', 'pivot_table')->withPivot(array(
     'pivot_attr_1',
     'pivot_attr_2',
     'pivot_attr_3',
     'pivot_attr_4',
     'pivot_attr_5',
 ));
```

Usage:

```php
foreach ($foo->bars as $bar) {
    echo $bar->pivot->pivot_attr_1;
}
```

##### Reference:

- [Querying pivot attributes in Eloquent](http://stackoverflow.com/questions/20120360/querying-pivot-attributes-in-eloquent)

---

#### Save pivot table data

```php
foreach ($foo->bars as $bar) {
    $bar->pivot->pivot_attr_1 = 'new value';
    $bar->pivot->pivot_attr_2 = 'special value';
    $bar->pivot->save();
}
```

##### Reference:

- [laravel4 update additional columns in pivot table](http://stackoverflow.com/questions/19083779/laravel4-update-additional-columns-in-pivot-table/19084126#19084126)

---

#### Eloquent: Sort in child table

Example

**user**

```
+-------------------+------------------+------+-----+---------+----------------+
| Field             | Type             | Null | Key | Default | Extra          |
+-------------------+------------------+------+-----+---------+----------------+
| id                | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| name              | varchar(128)     | NO   |     | NULL    |                |
| email             | varchar(128)     | NO   |     | NULL    |                |
+-------------------+------------------+------+-----+---------+----------------+
```

**post**

```
+-------------------+------------------+------+-----+---------+----------------+
| Field             | Type             | Null | Key | Default | Extra          |
+-------------------+------------------+------+-----+---------+----------------+
| id                | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| title             | varchar(128)     | NO   |     | NULL    |                |
| user_id           | int(10) unsigned | NO   |     | 0       |                |
| published_date    | datetime         | YES  |     | NULL    |                |
+-------------------+------------------+------+-----+---------+----------------+
```

In this case we want to sort posts belongs to the user

```php
$posts = $user->posts->sortBy('published_date');
```

##### Reference:

- [Laravel - Eloquent ORM](http://laravel.com/docs/eloquent#working-with-pivot-tables)

---

#### `hasMany` for long table name

Example

**table**

```
+-------------------+------------------+------+-----+---------+----------------+
| Field             | Type             | Null | Key | Default | Extra          |
+-------------------+------------------+------+-----+---------+----------------+
| id                | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| name              | varchar(128)     | NO   |     | NULL    |                |
+-------------------+------------------+------+-----+---------+----------------+
```

**table_foo**

```
+-------------------+------------------+------+-----+---------+----------------+
| Field             | Type             | Null | Key | Default | Extra          |
+-------------------+------------------+------+-----+---------+----------------+
| id                | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| title             | varchar(128)     | NO   |     | NULL    |                |
| table_id          | int(10) unsigned | NO   |     | 0       |                |
+-------------------+------------------+------+-----+---------+----------------+
```

In **Table.php**, use camel-case instead of underscore, e.g.

```php
public function tableFoos() {
    return $this->hasMany('TableFoo');
}
```

otherwise it won't works.

##### Reference:

- [Issue with Laravel hasMany relation](https://stackoverflow.com/questions/22390822/issue-with-laravel-hasmany-relation/23485131#23485131)

---

#### Outer .htaccess for shared hosting

In shared hosting, usually we can't configure the virtual host, one of the way is to create another **.htaccess** in the project root

```sh
project
  ├── app
  ├── artisan
  ├── bootstrap
  ├── composer.json
  ├── composer.lock
  ├── composer.phar
  ├── phpunit.xml
  ├── public
  │   ├── favicon.ico
  │   ├── index.php
  │   ├── packages
  │   ├── robots.txt
  │   └── .htaccess # original
  ├── server.php
  ├── vendor
  └── .htaccess # Add this
```

Add the content below to **project/.htaccess**

```apache
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{REQUEST_URI} !^public
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

To redirect all request to **public** directory

##### Reference:

- [Laravel 4 on a shared host](http://driesvints.com/blog/laravel-4-on-a-shared-host/)

---

#### Redirect to external URL

```php
return Redirect::away('https://www.facebook.com');
```

##### Reference:

- [Laravel 4: Redirect to a given url](https://stackoverflow.com/questions/18626492/laravel-4-redirect-to-a-given-url/18626541#18626541)

---

#### Eloquent filter joined table

```
Transaction table
+----+-------------+---------+
| id | borrower_id | book_id |
+----+-------------+---------+
|  1 |      1      |    1    |
+----+-------------+---------+

Book table
+----+----------+
| id | owner_id |
+----+----------+
|  1 |    1     |
+----+----------+
```

Let say want to retrieve those transation that related to a particular
user, regardless he/she is owner or borrower

```php
$query
    ->where('borrower_id', '=', $user_id)
    ->book()->where('owner_id', '=', $user_id)
;
```

---

### Laravel 5

#### MassAssignmentException exception in seeder

```php
public function run()
{
    Eloquent::unguard(); // <----- Add this

    // ...
}
```

##### Reference:

- [MassAssignmentException in Laravel](http://stackoverflow.com/questions/22280136/massassignmentexception-in-laravel/25566046#25566046)

---

#### Get remote IP address

```php
<?php
$request->server->get('HTTP_X_FORWARDED_FOR', $request->ip())
```

---

#### Get database table last insert ID without insert record

```php
<?php
$table_status = \DB::select('SHOW TABLE STATUS FROM ' . env('DB_DATABASE') . ' WHERE Name = "table_name"');
if (empty($table_status)) {
	throw new \Exception('Table not found');
}
$last_id = $table_status[0]->Auto_increment;
```

---

#### `COUNT` aggregated values with multiple conditions

Example, get the book borrowed for today, this week, this month, etc. in 1 query

```php
<?php
$today = Carbon::now()->toDateString();
$week_start = Carbon::now()->startOfWeek()->toDateString();
$week_end = Carbon::now()->endOfWeek()->toDateString();
$month_start = Carbon::now()->startOfMonth()->toDateString();
$month_end = Carbon::now()->endOfMonth()->toDateString();

$books = Book::select([
	'books.*',
	\DB::raw('COUNT(DISTINCT user_books.id) AS total_borrows'),
	\DB::raw('COUNT(DISTINCT IF(user_books.date = "' . $today . '", user_books.id, NULL)) AS today_borrows'),
	\DB::raw('COUNT(DISTINCT IF(user_books.date >= "' . $week_start . '" AND user_books.date <= "' . $week_end . '", user_books.id, NULL)) AS week_borrows'),
	\DB::raw('COUNT(DISTINCT IF(user_books.date >= "' . $month_start . '" AND user_books.date <= "' . $month_end . '", user_books.id, NULL)) AS month_borrows'),
])->join('user_books', 'user_books.book_id', '=', 'books.id')
	->groupBy('books.id')
	->get();
```

---

### Laravel 6

#### Get total count from a `GROUP BY` query

```php
<?php
\DB::query()->fromSub(function ($query) {
    $query->from('table_child')->groupBy('col_id');
}, 'sq')->count();
```

##### Reference:

- [How to select from subquery using Laravel Query Builder?](https://stackoverflow.com/questions/24823915/how-to-select-from-subquery-using-laravel-query-builder/52772444#52772444)

---

#### Set cache-control to S3

Update **config/filesystems.php**

```php
's3' => [
    'driver' => 's3',
    'key' => env('AMAZON_S3_KEY'),
    'secret' => env('AMAZON_S3_SECRET'),
    'region' => env('AMAZON_S3_REGION'),
    'bucket' => env('AMAZON_S3_BUCKET'),
    'options' => [
        'CacheControl' => 'max-age=2592000', // 2592000 seconds = 30 days
    ]
],
```

##### Reference:

- [Set default Cache-Control header for all objects uploaded using Laravel](https://dev.to/brunofernandes/setting-cache-control-header-for-all-amazon-s3-objects-5cga)

---

#### Generate dummy data

```
$ php artisan make:factory ProductFactory --model="App\\Models\\Product"
```

Edit the factory file **database/factories/ProductFactory.php**

```
<?php
use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    $user_id = random_int(1, 100);
    return [
        'description' => $faker->text,
        'price' => random_int(1, 10) * 100,
        'user_id' => $user_id,
        'created_by' => $user_id,
    ];
});
```

Trigger the php code with tinker

```
$ php artisan tinker
```

Generate data

```
Psy Shell v0.9.9 (PHP 7.3.6 — cli) by Justin Hileman
>>> factory(App\Models\Product::class, 100)->create();
```

##### Reference:

- [Laravel 6 Faker – Generate Fake Data Using Faker Example](https://www.tutsmake.com/laravel-faker-generate-fake-data-using-faker-tutorial-example/)
