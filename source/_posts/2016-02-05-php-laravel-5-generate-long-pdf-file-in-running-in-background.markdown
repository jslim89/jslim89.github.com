---
layout: post
title: "PHP Laravel 5 generate long PDF file in running in background"
date: 2016-02-05 11:44:45 +0800
comments: true
categories: 
- laravel
- php
- cronjob
---

If you have come across a problem with generating a large PDF file in your Laravel application, probably is not a good idea for the user to wait.

One of the solution is to process the PDF generation in background.

## Pre-requisite

- [wkhtmltopdf](http://wkhtmltopdf.org/)
- cronjob

## 1. Create a table for cronjob task

Let's name it `cronjob` table

```php
<?php
Schema::create('cronjob', function($table) {
    $table->increments('id');
    $table->string('type', 40);
    $table->text('data')->nullable();
    $table->integer('user_id')->unsigned()->default(0); // user who created the cron
    $table->datetime('executed_at')->nullable(); // the execution start time
    $table->datetime('completed_at')->nullable(); // the complete timestamp
});
```

Don't forget also create a model class, **Cronjob.php**

```php
<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cronjob extends BaseModel {

    use SoftDeletes;

    protected $table = 'cronjob';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
```

_P/S: In my case, I put all models into **app/Models/** directory, it depends on where you keep them_

## 2. Download wkhtmltopdf

Download it here http://wkhtmltopdf.org/downloads.html

Extract the tarball, only copy the binary to your project

```
$ mkdir /path/to/project/bin # create a folder to keep the binary
$ mv /path/to/wkhtmltox/bin/wkhtmltopdf /path/to/project/bin/wkhtmltopdf
```

## 3. Create a cronjob

```
$ crontab -e
```

```
* * * * * /usr/bin/php /path/to/project/artisan schedule:run >> /dev/null 2>&1
```

Bare in mind that Laravel has it's own [scheduler](https://laravel.com/docs/master/scheduling).

Now create a file in **/path/to/project/app/Console/Command/GeneratePDF.php**

```php
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Cronjob;

class GeneratePDF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_pdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate pdf from html';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get all cron that not yet complete
        $jobs = Cronjob::where('type', $this->signature)
            ->whereNull('completed_at')
            ->get();

        foreach ($jobs as $job) {

            $job->executed_at = date('Y-m-d H:i:s');
            $job->save();

            $this->_generatePdf($job);

            // mark it as completed
            $job->completed_at = date('Y-m-d H:i:s');
            $job->save();
        }
    }

    protected function _generatePdf(Cronjob $job)
    {
        $data = json_decode($job->data, 1);

        $path = storage_path('files/users/' . $job->user_id . '/pdf');
        @mkdir($path, 0755, true);

        $html_file = storage_path('files/users/' . $job->user_id . '/html/' . $data['html']);

        $file = 'your-awesome-pdf-file.pdf';

        $cmd_output = exec(base_path('bin/wkhtmltopdf') . ' ' . $html_file . ' ' . $path . '/' . $file);

        unlink($html_file); // remove temporary html file
    }
}
```

Also, don't forget to add it to **/path/to/project/app/Console/Kernel.php**

```php
<?php
protected $commands = [
    ...
    \App\Console\Commands\GeneratePDF::class,
];

...

protected function schedule(Schedule $schedule)
{
    ...

    $schedule->command('generate_pdf')
        ->everyMinute()
        ->withoutOverlapping()
        ->sendOutputTo(storage_path('logs/generate_pdf.log'))
    ;
}
```

Now it will run this every minute.

## 4. In your controller

Now let's do the code in your controller

```php
<?php
public function generatePDF(Request $request)
{
    ...

    $html = view('module.awesome.pdf', [
        'data' => $data,
    ])->render();

    $path = storage_path('files/users/' . \Auth::user()->id . '/html');
    @mkdir($path, 0755, true);
    $filename = 'awesome-html-file.html';
    file_put_contents($path . '/' . $filename, $html); // save to a temporary html file

    // schedule generate pdf in cronjob
    $cronjob = new Cronjob();
    $cronjob->type = 'generate_pdf';
    $cronjob->data = json_encode([
        'html' => $filename,
    ]);
    $cronjob->user_id = \Auth::user()->id; // keep the current user identity, because in cron, it won't know which user is current user
    $cronjob->save();

    return redirect()->back()
        ->with('alert.success', 'PDF is currently processing in background. Please refresh the page later.');
}
```

DONE :)
