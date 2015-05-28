### ParsehubPhp

Parsehub REST api wrapper class. Use this class to communicate with parsehub.
This class uses [phphttpclient](http://phphttpclient.com) to communicate with parsehub.

### installation
You can either download, clone this repo or install via composer:

```php
composer require msankhala/parsehub-php
```

#### Features

- Uses [phphttpclient](http://phphttpclient.com) class for making http requests.
- This class also support basic logging using monolog.
- This class use `PSR-0` autoload.

#### Uses

Create Parsehub class Object to communicate with Parsehub, pass the `api_key`
to parsehub class constructor. You can optionally pass `api_url` and `log_path`
log file path as second and third arguments.

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
$api_key = <your-api-key>;
$parsehub = new Parsehub($api_key);
$projectList = $parsehub->getProjectList();
echo $projectList;
```

```php
// Get project_token and run_token from DB.
$project_token = <get project token from db>
$run_token = <get project token from db>
```

Get particular Parsehub project, Pass the project_token:
```php
$parsehub = new Parsehub($api_key);
$project = $parsehub->getProject($project_token);
echo $project;
```

Get Last ready run Data for a project:
```php
$parsehub = new Parsehub($api_key);
$data =  $parsehub->getLastReadyRunData($project_token);
print $data;
```

Get data for a particular run, Pass the run token:
```php
$parsehub = new Parsehub($api_key);
$data = $parsehub->getRunData($run_token);
print $data;
```

Get a particular run, Pass the run token:
```php
$parsehub = new Parsehub($api_key);
$run = $parsehub->getRun($run_token);
print $run;
```

Run a parsehub project:
```php
$parsehub = new Parsehub($api_key);
$options = array(
    // Skip start_url option if don't want to override starting url configured
    // on parsehub.
    'start_url' => '<starting url at which crawling starts>'
    // Enter comma separated list of keywords to pass into `start_value_override`
    'keywords' => 'iphone case, iphone copy'
    // Set send_email options. Skip to remain this value default.
    'send_email' => 1,
);
$run_obj = $parsehub->runProject($project_token, $options);
echo $run_obj;
```
Cancel a parsehub project run:
```php
$parsehub = new Parsehub($api_key);
$cancel = $parsehub->cancelProjectRun($run_token);
print $cancel;
```

Delete a parsehub project run, This will delete the project run and data of that
run so be careful when using this method, once data deleted for a run, are not
recoverable:
```php
$parsehub = new Parsehub($api_key);
$cancel = $parsehub->deleteProjectRun($run_token);
print $cancel;
```
**You can check the log in your log file.**
