<?php
namespace Parsehub;

/**
 * Parsehub project run class.
 */
class ParsehubRun
{
    /**
     * Project run token.
     * @var string
     */
    public $run_token;

    /**
     * The status of the run. It can be one of initialized, running, cancelled, complete, or error. 
     * @var string
     */
    public $status;

    /**
     * Project token.
     * @var string
     * @see \Parsehub\ParsehubProject
     */
    
    public $project_token;

    /**
     * Starting time of crawler.
     * @var \Datetime
     */
    public $start_time;

    /**
     * End time of crawler.
     * @var \Datetime
     */
    public $end_time;

    /**
     * Whether data is ready or not.
     * @var boolean
     */
    public $data_ready;

    /**
     * Starting url for crawler.
     * @var string
     */
    public $start_url;

    /**
     * Starting values for parsehub project to run. This value is used for specifying the search keyword on marketplaces.
     * @var string
     */
    public $start_value;

    /**
     * md5 hash for crawler.
     * @var string
     */
    public $md5sum;

    /**
     * Number of pages crawled by crawler.
     * @var int
     */
    public $pages;

    public function __construct()
    {
        # code...
    }

    /**
     * Gets the Number of pages crawled by crawler.
     *
     * @return int
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Sets the Number of pages crawled by crawler.
     *
     * @param int $pages the pages
     *
     * @return self
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Gets the Project run token.
     *
     * @return string
     */
    public function getRunToken()
    {
        return $this->run_token;
    }

    /**
     * Sets the Project run token.
     *
     * @param string $run_token the run token
     *
     * @return self
     */
    public function setRunToken($run_token)
    {
        $this->run_token = $run_token;

        return $this;
    }

    /**
     * Gets the The status of the run. It can be one of initialized, running, cancelled, complete, or error.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the The status of the run. It can be one of initialized, running, cancelled, complete, or error.
     *
     * @param string $status the status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Gets the Project token.
     *
     * @return string
     */
    public function getProjectToken()
    {
        return $this->project_token;
    }

    /**
     * Sets the Project token.
     *
     * @param string $project_token the project token
     *
     * @return self
     */
    public function setProjectToken($project_token)
    {
        $this->project_token = $project_token;

        return $this;
    }

    /**
     * Gets the Starting time of crawler.
     *
     * @return Datetime
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Sets the Starting time of crawler.
     *
     * @param Datetime $start_time the start time
     *
     * @return self
     */
    public function setStartTime(Datetime $start_time)
    {
        $this->start_time = $start_time;

        return $this;
    }

    /**
     * Gets the End time of crawler.
     *
     * @return Datetime
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Sets the End time of crawler.
     *
     * @param Datetime $end_time the end time
     *
     * @return self
     */
    public function setEndTime(Datetime $end_time)
    {
        $this->end_time = $end_time;

        return $this;
    }

    /**
     * Gets the Whether data is ready or not.
     *
     * @return boolean
     */
    public function getDataReady()
    {
        return $this->data_ready;
    }

    /**
     * Sets the Whether data is ready or not.
     *
     * @param boolean $data_ready the data ready
     *
     * @return self
     */
    public function setDataReady($data_ready)
    {
        $this->data_ready = $data_ready;

        return $this;
    }

    /**
     * Gets the Starting url for crawler.
     *
     * @return string
     */
    public function getStartUrl()
    {
        return $this->start_url;
    }

    /**
     * Sets the Starting url for crawler.
     *
     * @param string $start_url the start url
     *
     * @return self
     */
    public function setStartUrl($start_url)
    {
        $this->start_url = $start_url;

        return $this;
    }

    /**
     * Gets the Starting values for parsehub project to run. This value is used for specifying the search keyword on marketplaces.
     *
     * @return array
     */
    public function getStartValue()
    {
        return json_decode($this->start_value, true);
    }

    /**
     * Sets the Starting values for parsehub project to run. This value is used for specifying the search keyword on marketplaces.
     *
     * @param string $start_value the start value
     *
     * @return self
     */
    public function setStartValue(string $start_value)
    {
        $this->start_value = $start_value;

        return $this;
    }

    /**
     * Gets the md5 hash for crawler.
     *
     * @return string
     */
    public function getMd5sum()
    {
        return $this->md5sum;
    }

    /**
     * Sets the md5 hash for crawler.
     *
     * @param string $md5sum the md5sum
     *
     * @return self
     */
    public function setMd5sum($md5sum)
    {
        $this->md5sum = $md5sum;

        return $this;
    }
}
