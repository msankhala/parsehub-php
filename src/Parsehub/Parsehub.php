<?php 
namespace Parsehub;

use Httpful\Request;
use Parsehub\ParsehubProject;
use Parsehub\ParsehubRun;
use JsonMapper;

/**
 * Parsehub wrapper class.
 */
class Parsehub
{
    /**
     * @var string
     */
    private $api_key = '';

    protected $projects = array();

    /**
     * \Httpful\Request object to make restful request.
     * @var \Httpful\Request
     */
    public $RESTful;
    /**
     * constructor.
     */
    public function __construct(\Httpful\Request $RESTful)
    {
        $this->RESTful = $RESTful;
    }


    /**
     * Gets the value of api_key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * Sets the value of api_key.
     *
     * @param string $api_key the api key
     *
     * @return self
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;

        return $this;
    }

    /**
     * Sets the value of projects.
     *
     * @param mixed $projects the projects
     *
     * @return self
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;

        return $this;
    }

    /**
     * Gets the value of projects.
     *
     * @return mixed
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Gets the value of projects.
     *
     * @return mixed
     */
    public function addProject(ParsehubProject $project)
    {
        $this->projects[] = $project;
    }

    public function getCrawlerList()
    {
        $response = $this->RESTful->send();
        $mapper = new JsonMapper();
        $body = json_decode($response->body, true);
        $data = $mapper->mapArray($body['projects'], array(), 'Parsehub\ParsehubProject');
        return $data;
    }
}
