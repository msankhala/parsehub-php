<?php
$loader = require_once __DIR__ . '/vendor/autoload.php';
// $url = "http://api.openweathermap.org/data/2.5/weather?lat=22.569719&lon=88.36972";
$url = 'https://www.parsehub.com/api/v2/projects?' . 'api_key=tS-CGrbH1aWL0xaHyb4TrHkm';

use Httpful\Request;
use Parsehub\Parsehub;
use Parsehub\ParsehubProject;
use Parsehub\ParsehubRun;

// Get Parsehub projects listing.
$request =  Request::get($url);
 
// $parsehub = new Parsehub($request);
$parsehub = new Parsehub();
$response = $parsehub->getCrawlerList();
var_dump($response);

// Get Parsehub Run for a specific marketplace.
// $url = 'https://www.parsehub.com/api/v2/runs/tsVm3nyGvXK42dFQbWijmHu-?api_key=tS-CGrbH1aWL0xaHyb4TrHkm';
// $request =  Request::get($url);
// $response = $request->send();
// $mapper = new JsonMapper();
// $body = json_decode($response->body);
// $data = $mapper->map($body, new ParsehubRun());
// var_dump($data);

// Get the project project information.
// $url = 'https://www.parsehub.com/api/v2/projects/tlYxbRYjuF9ieNvMieZhCF7Y?api_key=tS-CGrbH1aWL0xaHyb4TrHkm';
// $request =  Request::get($url);
// $response = $request->send();
// $mapper = new JsonMapper();
// $body = json_decode($response->body);
// $data = $mapper->map($body, new ParsehubProject());
// // var_dump($data);
// var_dump($data->getLastRun()->getRunToken());
// var_dump($data->getLastRun()->getStartValue());
