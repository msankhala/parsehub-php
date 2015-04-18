<?php 
namespace Parsehub;

use Httpful\Request as PHPHttpful;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Parsehub wrapper class.
 */
class Parsehub
{
    /**
     * Parsehub api configuration.
     * @var array
     */
    protected static $config = array();

    /**
     * Monolog logger object for basic logging.
     * @var Monolog\Logger
     */
    protected static $logger;

    /**
     * constructor.
     */
    public function __construct()
    {
        if (empty(self::$config)) {
            self::$config = require_once __DIR__ . '/../config.php';
        }

        if (empty(self::$logger)) {
            // create a log channel
            self::$logger = new Logger('ParsehubLogger');
            self::$logger->pushHandler(new StreamHandler(self::$config['log_path']));
        }
    }

    /**
     * Get a data for a particular run of a parsehub project.
     * @param  string $run_token run token for which you want to get data.
     * @return string            json response.
     */
    public function getCrawlData($run_token)
    {
        $url = $this->getCrawlerDataApiUrl($run_token);
        $response = PHPHttpful::get($url)
        ->parseWith(function ($body) {
            // Decode the gzip encoded respose.
            return gzdecode($body);
        })
        ->send();
        $response = $response->body;
        return $response;
    }

    /**
     * Get the last ready run data for a project
     * @param  string $project_token project token to get last ready data for project
     * @return string                json response.
     */
    public function getLastReadyRunCrawlData($project_token)
    {
        $url = $this->getLastReadyRunDataApiUrl($project_token);
        $response = PHPHttpful::get($url)
        ->parseWith(function ($body) {
            // Decode the gzip encoded respose.
            return gzdecode($body);
        })
        ->send();
        $response = $response->body;
        return $response;
    }

    /**
     * Get a run object for a project.
     * @param  string $run_token run token whose run object you want to get.
     * @return string            json response.
     */
    public function getCrawl($run_token)
    {
        $url = $this->getCrawlApiUrl($run_token);
        $response = PHPHttpful::get($url)->send();
        $crawler = $response->body;
        return $crawler;
    }

    /**
     * Get a project detail.
     * @param  string $project_token project token for which project you want 
     *                               to get information.
     * @return string                json response.
     */
    public function getCrawler($project_token)
    {
        $url = $this->getCrawlerApiUrl($project_token);
        $response = PHPHttpful::get($url)->send();
        $crawler = $response->body;
        return $crawler;
    }

    /**
     * Get list of all the parsehub project.
     * @return string json response.
     */
    public function getCrawlerList()
    {
        $url = $this->getCrawlerListApiUrl();
        $response = PHPHttpful::get($url)->send();
        $crawler_list = $response->body;
        return $crawler_list;
    }

    /**
     * Run crawler on parsehub.
     * @param  string $project_token project token which you want to run on parsehub.
     * @param  array  $options       Array of options which you want to pass.
     *                               Options can have 
     *                               start_url = starting url,
     *                               keywords = comma separated list of keywords to search,
     *                               send_email = send email about run status.
     * @return string                run object if run successful otherwise return false.
     */
    public function runCrawler($project_token, $options = array())
    {
        $url = $this->getCrawlerRunApiUrl($project_token);
        $api_key = self::$config['api_key'];

        // Set query parameters to pass to crawler.
        $start_url = isset($options['start_url']) ? $options['start_url'] : '';
        $keywords = isset($options['keywords']) ? explode(',', $options['keywords']) : array();
        $send_email = (isset($options['send_email']) && $options['send_email'] == 1) ? $options['send_email'] : 0;
        $requestbody = 'api_key=' . $api_key;
        if (!empty($start_url)) {
            $requestbody .= '&start_url=' . urlencode($start_url);
        }
        if (!empty($keywords)) {
            $last_keyword_index = count($keywords) - 1;
            $start_value_override = '{"keywords":[';
            foreach ($keywords as $index => $keyword) {
                if ($index === $last_keyword_index) {
                    $start_value_override .= '"' . $keyword . '"';
                } else {
                    $start_value_override .= '"' . $keyword . '"' . ",";
                }
            }
            $start_value_override .= ']}';
            $requestbody .= '&start_value_override=' . urlencode($start_value_override);
        }
        if ($send_email) {
            $requestbody .= '&send_email=' . urlencode($send_email);
        }
        $response = PHPHttpful::post($url)
        ->addHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8')
        ->body($requestbody)
        ->send();
        if ($response->code == 200) {
            self::$logger->info('Crawler run successfully on parsehub with values: ', ['context' => array(
                'start_url' => $start_url,
                'keywords' => $keywords,
                'send_email' => $send_email
            )]);
            $data = $response->body;
            // var_dump("Crawler with project token $project_token started on server successfully. run token is");
            return $data;
        }
        if ($response->code == 401) {
            Log::error('Access denied. Not able to run project on parsehub.');
        }
        if ($response->code == 400) {
            Log::error('Bad request. Not able to cancel project on parsehub.');
        }
    }

    /**
     * Cancel a running project.
     * @param  string $run_token run token of a 
     * @return [type]            run token of canceled run.
     */
    public function cancelCrawlerRun($run_token)
    {
        $url = $this->getCrawlerRunCancelApiUrl($run_token);
        $api_key = self::$config['api_key'];
        $requestbody = 'api_key=' . $api_key;

        $response = PHPHttpful::post($url)
        ->addHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8')
        ->body($requestbody)
        ->send();

        if ($response->code == 200) {
            self::$logger->info("Crawler canceled successfully on parsehub with run_token: $run_token");
            $data = $response->body;
            return $data;
        }
        if ($response->code == 401) {
            Log::error('Access denied. Not able to cancel project on parsehub.');
        }
        if ($response->code == 400) {
            Log::error('Bad request. Not able to cancel project on parsehub.');
        }
    }

    /**
     * Get parsehub project list api url.
     * @return string REST api url for parsehub project list.
     */
    public function getCrawlerListApiUrl()
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/projects?api_key=' . $api_key;
        return $url;
    }

    /**
     * Get parsehub project information api url.
     * @param  string $project_token project information api url.
     * @return string                REST api url for parsehub project.
     */
    public function getCrawlerApiUrl($project_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/projects/' . $project_token . '?api_key=' . $api_key;
        return $url;
    }

    /**
     * Get run information api url.
     * @param  string $run_token run token of a particular run.
     * @return string            REST api url for parsehub project run.
     */
    public function getCrawlApiUrl($run_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/runs/' . $run_token . '?api_key=' . $api_key;
        return $url;
    }

    /**
     * Get crawled data for a run.
     * @param  string $run_token run token to get data.
     * @return string            REST api url for parsehub project run data.
     */
    public function getCrawlerDataApiUrl($run_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/runs/' . $run_token . '/data' . '?api_key=' . $api_key;
        return $url;
    }

    /**
     * Get Last ready run api url.
     * @param  string $project_token project for which to get last ready run
     *                               data.
     * @return string                REST api url for project last ready run data.
     */
    public function getLastReadyRunDataApiUrl($project_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/projects/' . $project_token . '/last_ready_run/data' . '?api_key=' . $api_key;
        return $url;
    }

    /**
     * Get crawler run api url
     * @param  string $project_token project token which project you want to
     *                               run.
     * @return string                REST api url for running a parsehub
     *                               project.
     */
    public function getCrawlerRunApiUrl($project_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/projects/' . $project_token . '/run';
        return $url;
    }

    /**
     * Cancel a running parsehub project.
     * @param  string $run_token run token to stop a run.
     * @return string            REST api url to cancel a run.
     */
    public function getCrawlerRunCancelApiUrl($run_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/runs/' . $run_token . '/cancel';
        return $url;
    }
}
