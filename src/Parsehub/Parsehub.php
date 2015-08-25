<?php
namespace Parsehub;

use Httpful\Request as PHPHttpful;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Parsehub\Exception\HttpException;

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
    public function __construct($api_key, $api_url = '', $logpath = '')
    {
        if (empty(self::$config)) {
            $logpath = empty($logpath) ? __DIR__ . '/../../log/parsehub.log' : $logpath;
            $api_url = empty($api_url) ? 'https://www.parsehub.com/api/v2' : $api_url;
            self::$config = array(
                'api_key' => $api_key,
                'log_path' => $logpath,
                'api_url' => $api_url,
            );
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

        //check url is valid and accessable
        $status = $this->getHttpStatusCode($url);
        if ($status >= 400) {
            throw new HttpException($this->getHttpStatusMessage($status), $status);
        }

        $response = PHPHttpful::get($url)
        ->parseWith(function ($body) {
            // Decode the gzip encoded respose.
            return gzdecode($body);
        })
        ->send();
        if ($this->isResponseValid($response)) {
            $response = $response->body;
            return $response;
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

        //check url is valid and accessable
        $status = $this->getHttpStatusCode($url);
        if ($status >= 400) {
            throw new HttpException($this->getHttpStatusMessage($status), $status);
        }
        
        $response = PHPHttpful::get($url)
        ->parseWith(function ($body) {
            // Decode the gzip encoded respose.
            return gzdecode($body);
        })
        ->send();

        if ($this->isResponseValid($response)) {
            $response = $response->body;
            return $response;
        }
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
        if ($this->isResponseValid($response)) {
            $run = $response->body;
            return $run;
        }
        return $response;
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
        if ($this->isResponseValid($response)) {
            $project = $response->body;
            return $project;
        }
        return $response;
    }

    /**
     * Get list of all the parsehub project.
     * @return string json response.
     */
    public function getProjectList()
    {
        $url = $this->getProjectListApiUrl();
        $response = PHPHttpful::get($url)->send();
        if ($this->isResponseValid($response)) {
            $project_list = $response->body;
            return $project_list;
        }
        return $response;
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
                    $start_value_override .= '"' . $keyword . '"' . "]";
                } else {
                    $start_value_override .= '"' . $keyword . '"' . ",";
                }
            }
            if (isset($options['page_limit'])) {
                $start_value_override .= ',' . '"page_limit":' . $options['page_limit'];
            }
            $start_value_override .= '}';
            $requestbody .= '&start_value_override=' . urlencode($start_value_override);
        }
        if ($send_email) {
            $requestbody .= '&send_email=' . urlencode($send_email);
        }
        $response = PHPHttpful::post($url)
        ->addHeader('Content-Type', 'application/x-www-form-urlencoded; charset=utf-8')
        ->body($requestbody)
        ->send();

        if ($this->isResponseValid($response)) {
            $run_object = json_decode($response->body);
            // If this is new run then parsehub return complete run object.
            if (isset($run_object->project_token) && isset($run_object->run_token)) {
                self::$logger->info('Project run successfully on parsehub with values: ', ['context' => array(
                    'start_url' => $start_url,
                    'keywords' => $keywords,
                    'send_email' => $send_email,
                    'run_token' => $run_object->run_token,
                    'project_token' => $run_object->project_token,
                )]);
                // If crawler is running already for the same value then
                // parsehub return run object only with run_token value.
            } elseif (isset($run_object->run_token)) {
                self::$logger->info('Project already running on parsehub with same values: ', ['context' => array(
                    'start_url' => $start_url,
                    'keywords' => $keywords,
                    'send_email' => $send_email,
                    'run_token' => $run_object->run_token,
                )]);
            } else {
                self::$logger->info('Unable to start project on parsehub: ', ['context' => array(
                    'start_url' => $start_url,
                    'keywords' => $keywords,
                    'project_token' => $project_token,
                    'api_key' => $api_key,
                    'requestbody_body' => $requestbody,
                )]);
            }
            $data = $response->body;
            return $data;
        }
        return $response;
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

        if ($this->isResponseValid($response)) {
            self::$logger->info("Project run canceled successfully on parsehub with run_token: $run_token");
            $data = $response->body;
            return $data;
        }
        return $response;
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
        if ($this->isResponseValid($response)) {
            self::$logger->info("Project run deleted successfully on parsehub of run_token $run_token");
            $data = $response->body;
            return $data;
        }

    }

    public function isResponseValid($response)
    {
        switch ($response->code) {
            case 200:
                return true;
                break;

            case 400:
                self::$logger->error($this->getHttpStatusMessage($response->code));
                break;

            case 401:
                self::$logger->error($this->getHttpStatusMessage($response->code));
                return $response;
                break;

            case 403:
                self::$logger->error($this->getHttpStatusMessage($response->code));
                return $response;
                break;

            default:
                # code...
                break;
        }
        return false;
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

    /**
    * Get http status code of url
    * @param string $url url to check
    * @return integer http status
    */
    public function getHttpStatusCode($url)
    {
        $handler = curl_init($url);
        curl_setopt($handler,  CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handler, CURLOPT_NOBODY, true);
        curl_exec($handler);
        return curl_getinfo($handler, CURLINFO_HTTP_CODE);
    }

    /**
    * get Http status message
    * @param integer $status http status code
    * @return string status message
    */
    public function getHttpStatusMessage($status)
    {
        switch ($status) {
            case 400:
                return 'Bad request. Not able to get data from parsehub.';
                break;

            case 401:
                return 'Unauthorized access. Not able to get data from parsehub. Please check api key.';
                break;

            case 403:
                return 'Forbidden. Not able to get data from parsehub. Please check api key.';
                break;
        }
    }
}
