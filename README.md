### ParsehubPhp

Parsehub REST api wrapper class. Use this class to communicate with parsehub.
This class uses [phphttpclient](http://phphttpclient.com) to communicate with parsehub.

#### Features

- Uses [phphttpclient](http://phphttpclient.com) class for making http requests.
- This class also support basic logging using monolog.
- This class use `PSR-0` autoload.

#### Uses

Set you configuration in config.php file. Copy `config.default.php` file and rename it to `config.php` file. Set you `api_key` in this config file.

```php
return $parsehub_config = array(
    'api_key' => '<your-api-key>',
    'api_url' => 'https://www.parsehub.com/api/v2',
    //Set log path default to log/parsehub.log
    'log_path' => __DIR__ . '/../log/parsehub.log',
    'api_version' => '2',
);

```

Autoload Parsehub class:

```php
require_once __DIR__ . '/vendor/autoload.php';

use Parsehub\Parsehub;
```

In your controller you can use `Parsehub` class to get list of all the
`parsehub projects` and `run object` for a `parsehub project` and save 
them in your db. When you get a `parsehub project` information you also get the
`run_list` of that project which you can store in your db.

Get Parsehub projects list:
```php
$parsehub = new Parsehub();
$crawlerList = $parsehub->getCrawlerList();
echo $crawlerList;
```

Get particular Parsehub project, Pass the project_token:
```php
$parsehub = new Parsehub();
$crawler = $parsehub->getCrawler($project_token);
echo $crawler;
```

```php
// Get project_token and run_token from DB.
$project_token = <get project token from db>
$run_token = <get project token from db>
```

Get Last ready run Data for a project:
```php
$parsehub = new Parsehub();
$data =  $parsehub->getLastReadyRunCrawlData($project_token);
print $data;
```

Get data for a particular run, Pass the run token:
```php
$parsehub = new Parsehub();
$data = $parsehub->getCrawlData($run_token);
print $data;
```

Run a parsehub project:
```php
$parsehub = new Parsehub();
$options = array(
    // Skip start_url option if don't want to override starting url configured
    // on parsehub.
    'start_url' => '<starting url at which crawling starts>'
    // Enter comma separated list of keywords to pass into `start_value_override`
    'keywords' => 'iphone case, iphone copy'
    // Set send_email options. Skip to remain this value default.
    'send_email' => 1,
);
$run_obj = $parsehub->runCrawler($project_token, $options);
echo $run_obj;
```
Cancel a parsehub project run:
```php
$parsehub = new Parsehub();
$cancel = $parsehub->cancelCrawlerRun($run_token);
print $cancel;
```
You can check the log in your log file.