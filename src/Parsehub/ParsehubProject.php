<?php
namespace Parsehub;

use Httpful\Request;
use Parsehub\ParsehubRun;

/**
 * Parsehub Project class.
 */
class ParsehubProject
{
    /**
     * Title or name of parsehub project.
     * @var string
     */
    public $title = '';

    /**
     * Project token.
     * @var string
     */
    public $token;

    /**
     * Starting values for parsehub project to run. This value is used for specifying the search keyword on marketplaces.
     * @var string
     */

    public $templates_json;
    /**
     * main template name. Starting template for parsehub.
     * @var string
     */
    public $main_template;

    /**
     * Starting url for crawling.
     * @var string
     */
    public $main_site;

    /**
     * options for parsehub carwler.
     * @var string
     */
    public $options_json;

    /**
     * Run object
     * @var \Parsehub\ParsehubRun
     */
    public $last_run;

    /**
     * Last ready Run object
     * @var \Parsehub\ParsehubRun
     */
    public $last_ready_run;

    /**
     * Array of Run object.
     * @var \Parsehub\ParsehubRun[]
     */
    public $run_list;

    /**
     * constructor.
     */
    public function __construct($title = '')
    {
        if (!empty($title)) {
            # get the project token for this title from db.
            # $this->token = (token from db)
        }
    }

    /**
     * Gets the Title or name of parsehub project.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the Title or name of parsehub project.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the Project token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the Project token.
     *
     * @param string $token the token
     *
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Gets the main template name. Starting template for parsehub.
     *
     * @return string
     */
    public function getMainTemplate()
    {
        return $this->main_template;
    }

    /**
     * Sets the main template name. Starting template for parsehub.
     *
     * @param string $main_template the main template
     *
     * @return self
     */
    public function setMainTemplate($main_template)
    {
        $this->main_template = $main_template;

        return $this;
    }

    /**
     * Gets the Starting url for crawling.
     *
     * @return string
     */
    public function getMainSite()
    {
        return $this->main_site;
    }

    /**
     * Sets the Starting url for crawling.
     *
     * @param string $main_site the main site
     *
     * @return self
     */
    public function setMainSite($main_site)
    {
        $this->main_site = $main_site;

        return $this;
    }

    /**
     * Gets the options for parsehub carwler.
     *
     * @return array
     */
    public function getOptionsJson()
    {
        return $this->options_json;
    }

    /**
     * Sets the options for parsehub carwler.
     *
     * @param array $options_json the options json
     *
     * @return self
     */
    public function setOptionsJson(array $options_json)
    {
        $this->options_json = $options_json;

        return $this;
    }

    /**
     * Gets the Run object.
     *
     * @return Parsehub\ParsehubRun
     */
    public function getLastRun()
    {
        return $this->last_run;
    }

    /**
     * Sets the Run object.
     *
     * @param Parsehub\ParsehubRun $last_run the last run
     *
     * @return self
     */
    public function setLastRun(Parsehub\ParsehubRun $last_run)
    {
        $this->last_run = $last_run;

        return $this;
    }

    /**
     * Gets the Last ready Run object.
     *
     * @return Parsehub\ParsehubRun
     */
    public function getLastReadyRun()
    {
        return $this->last_ready_run;
    }

    /**
     * Sets the Last ready Run object.
     *
     * @param Parsehub\ParsehubRun $last_ready_run the last ready run
     *
     * @return self
     */
    public function setLastReadyRun(Parsehub\ParsehubRun $last_ready_run)
    {
        $this->last_ready_run = $last_ready_run;

        return $this;
    }

    /**
     * Gets the Array of Run object.
     *
     * @return \Parsehub\ParsehubRun[]
     */
    public function getRunList()
    {
        return $this->run_list;
    }

    /**
     * Sets the Array of Run object.
     *
     * @param \Parsehub\ParsehubRun[] $run_list the run list
     *
     * @return self
     */
    protected function setRunList($run_list)
    {
        $this->run_list = $run_list;

        return $this;
    }

    public function getCrawlerData()
    {

    }

    /**
     * Gets the Starting values for parsehub project to run. This value is used for specifying the search keyword on marketplaces.
     *
     * @return array
     */
    public function getTemplatesJson()
    {
        return json_decode($this->templates_json);
    }

    /**
     * Sets the Starting values for parsehub project to run. This value is used for specifying the search keyword on marketplaces.
     *
     * @param string $templates_json the templates json
     *
     * @return self
     */
    public function setTemplatesJson($templates_json)
    {
        $this->templates_json = $templates_json;

        return $this;
    }
}
