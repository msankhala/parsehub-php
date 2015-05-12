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
     * @todo Find a better way to manage config file.
     */
    public function __construct()
    {
        if (empty(self::$config)) {
            $confilg_file_path = __DIR__ . '/../config.php';
            $default_config_file = __DIR__ . '/../config.default.php';
            // Each time you update package you have to re-create config.php
            // file again because Composer will delete whole package along with
            // config file on updating this package and autoload generate will
            // fail. So including default config file.
            self::$config = require_once file_exists($confilg_file_path) ?  $confilg_file_path : $default_config_file;
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
    public function getRunData($run_token)
    {
        $url = $this->getRunDataApiUrl($run_token);
        $response = PHPHttpful::get($url)
        ->parseWith(function ($body) {
            // Decode the gzip encoded respose.
            return gzdecode($body);
        })
        ->send();
        if ($response->code == 200) {
            $response = $response->body;
            return $response;
        }
        if ($response->code == 401) {
            self::$logger->error('Access denied. Not able to get data from parsehub.');
        }
        if ($response->code == 400) {
            self::$logger->error('Bad request. Not able to get data from parsehub.');
        }
        return $response;
    }

    /**
     * Get the last ready run data for a project
     * @param  string $project_token project token to get last ready data for project
     * @return string                json response.
     */
    public function getLastReadyRunData($project_token)
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
    public function getRun($run_token)
    {
        $url = $this->getRunApiUrl($run_token);
        $response = PHPHttpful::get($url)->send();
        $run = $response->body;
        return $run;
    }

    /**
     * Get a project detail.
     * @param  string $project_token project token for which project you want 
     *                               to get information.
     * @return string                json response.
     */
    public function getProject($project_token, $offset = null)
    {
        $url = $this->getProjectApiUrl($project_token, $offset);
        $response = PHPHttpful::get($url)->send();
        $project = $response->body;
        return $project;
    }

    /**
     * Get list of all the parsehub project.
     * @return string json response.
     */
    public function getProjectList()
    {
        $url = $this->getProjectListApiUrl();
        $response = PHPHttpful::get($url)->send();
        $project_list = $response->body;
        return $project_list;
    }

    /**
     * Run Project on parsehub.
     * @param  string $project_token project token which you want to run on parsehub.
     * @param  array  $options       Array of options which you want to pass.
     *                               Options can have 
     *                               start_url = starting url,
     *                               keywords = comma separated list of keywords to search,
     *                               send_email = send email about run status.
     * @return string                run object if run successful otherwise return false.
     */
    public function runProject($project_token, $options = array())
    {
        $url = $this->getProjectRunApiUrl($project_token);
        $api_key = self::$config['api_key'];

        // Set query parameters to pass to Project.
        $start_url = isset($options['start_url']) ? $options['start_url'] : '';
        $keywords = isset($options['keywords']) ? explode(',', $options['keywords']) : array();
        $keywords = array_map('trim', $keywords);
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
            $run_object = json_decode($response->body);
            if (isset($run_object->start_time)) {
                self::$logger->info('Project run successfully on parsehub with values: ', ['context' => array(
                    'start_url' => $start_url,
                    'keywords' => $keywords,
                    'send_email' => $send_email,
                    'run_token' => $run_object->run_token,
                )]);
            } else {
                self::$logger->info('Project already running on parsehub with same values: ', ['context' => array(
                    'start_url' => $start_url,
                    'keywords' => $keywords,
                    'send_email' => $send_email,
                    'run_token' => $run_object->run_token,
                )]);
            }
            $data = $response->body;
            return $data;
        }
        if ($response->code == 401) {
            self::$logger->error('Access denied. Not able to run project on parsehub.');
        }
        if ($response->code == 400) {
            self::$logger->error('Bad request. Not able to run project on parsehub.');
        }
    }

    /**
     * Cancel a running project.
     * @param  string $run_token run token of a project run.
     * @return [type]            run token of canceled run.
     */
    public function cancelProjectRun($run_token)
    {
        $url = $this->getProjectRunCancelApiUrl($run_token);
        $api_key = self::$config['api_key'];
        $requestbody = 'api_key=' . $api_key;

        $response = PHPHttpful::post($url)
        ->addHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8')
        ->body($requestbody)
        ->send();

        if ($response->code == 200) {
            self::$logger->info("Project run canceled successfully on parsehub with run_token: $run_token");
            $data = $response->body;
            return $data;
        }
        if ($response->code == 401) {
            self::$logger->error('Access denied. Not able to cancel project on parsehub.');
        }
        if ($response->code == 400) {
            self::$logger->error('Bad request. Not able to cancel project on parsehub.');
        }
    }

    /**
     * Delete a project run. This cancels a run if running, and deletes the run
     * and its data.
     * @param  string $run_token run token of a project run.
     * @return string            json response with run token that run was
     *                                deleted.
     */
    public function deleteProjectRun($run_token)
    {
        $url = $this->getProjectRunDeleteApiUrl($run_token);
        $api_key = self::$config['api_key'];

        $response = PHPHttpful::delete($url)
        ->send();

        if ($response->code == 200) {
            self::$logger->info("Project deleted successfully on parsehub with run_token: $run_token");
            $data = $response->body;
            return $data;
        }
        if ($response->code == 401) {
            self::$logger->error('Access denied. Not able to delete project on parsehub.');
        }
        if ($response->code == 400) {
            self::$logger->error('Bad request. Not able to delete project on parsehub.');
        }
    }

    /**
     * Get parsehub project list api url.
     * @return string REST api url for parsehub project list.
     */
    public function getProjectListApiUrl()
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
    public function getProjectApiUrl($project_token, $offset = null)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/projects/' . $project_token . '?api_key=' . $api_key;
        if (isset($offset) && is_numeric($offset)) {
            $url .= '&offset=' . $offset;
        }
        return $url;
    }

    /**
     * Get run information api url.
     * @param  string $run_token run token of a particular run.
     * @return string            REST api url for parsehub project run.
     */
    public function getRunApiUrl($run_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/runs/' . $run_token . '?api_key=' . $api_key;
        return $url;
    }

    /**
     * Get project run data for a run.
     * @param  string $run_token run token to get data.
     * @return string            REST api url for parsehub project run data.
     */
    public function getRunDataApiUrl($run_token)
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
     * Get Project run api url
     * @param  string $project_token project token which project you want to
     *                               run.
     * @return string                REST api url for running a parsehub
     *                               project.
     */
    public function getProjectRunApiUrl($project_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/projects/' . $project_token . '/run';
        return $url;
    }

    /**
     * Get Cancel a running parsehub project api url.
     * @param  string $run_token run token to stop a run.
     * @return string            REST api url to cancel a run.
     */
    public function getProjectRunCancelApiUrl($run_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/runs/' . $run_token . '/cancel';
        return $url;
    }

    /**
     * Get Delete a parsehub project run api url.
     * @param  string $run_token run token to stop a run.
     * @return string            REST api url to cancel a run.
     */
    public function getProjectRunDeleteApiUrl($run_token)
    {
        $api_key = self::$config['api_key'];
        $api_url = self::$config['api_url'];
        $url = $api_url . '/runs/' . $run_token . '?api_key=' . $api_key;
        return $url;
    }
}
