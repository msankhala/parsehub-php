<?php

require_once 'config.php';

use PHPUnit\Framework\TestCase;
use Parsehub\Parsehub; // Import the Parsehub class (adjust the namespace as needed)

class ParsehubTest extends TestCase
{
    protected $parsehub;

    public function setUp(): void
    {
        parent::setUp(); // Call the parent class's setUp method

        // Set up the Parsehub object with configuration for testing
        $api_key = API_KEY;
        $this->parsehub = new Parsehub($api_key);
    }

    public function testGetProjectList()
    {
        // req api
        $projectList = $this->parsehub->getProjectList();

        // Check if the expected project list is returned
        $this->assertIsObject($projectList);
        $this->assertIsArray($projectList->projects);
    }
    public function testGetProject()
    {
        // req api
        $project = $this->parsehub->getProject(PROJECT_TOKEN);

        // Check if the expected project is returned
        $this->assertIsObject($project);
        $this->assertIsString($project->title);
    }
    public function testGetLastReadyRunData()
    {
        // req api
        $data = $this->parsehub->getLastReadyRunData(PROJECT_TOKEN);

        // Check if the expected project is returned
        $this->assertIsObject($data);
    }
    public function testGetRunData()
    {
        // req api
        $data = $this->parsehub->getRunData(RUN_TOKEN);

        // Check if the expected data is returned
        $this->assertIsObject($data);
    }
    public function testRunProject()
    {
        // req api
        $data = $this->parsehub->runProject(PROJECT_TOKEN);

        // Check if the expected data is returned
        $this->assertIsObject($data);
    }
    public function testGetRun()
    {
        // req api
        $data = $this->parsehub->getRun(RUN_TOKEN);

        // Check if the expected data is returned
        $this->assertIsObject($data);
    }
    public function testCancelProjectRun()
    {
        // req api
        $data = $this->parsehub->cancelProjectRun(RUN_TOKEN);

        // Check if the expected data is returned
        $this->assertIsObject($data);
    }
    public function testDeleteProjectRun()
    {
        // req api
        $data = $this->parsehub->deleteProjectRun(RUN_TOKEN);

        // Check if the expected data is returned
        $this->assertIsObject($data);
    }
   
}
