<?php
namespace Parsehub;

use Parsehub\Interfaces\IRESTful;
use Httpful\Request;

/**
 * Httpful adapter implementing IRESTful interface. Adapter class for phphttpclient.com
 * 
 * @see http://phphttpclient.com/
 */
class AHTTPful implements IRESTful
{
    /**
     * Request objet to make RESTful request.
     * @var Parsehub\Interfaces\IRESTful
     */
    public $request;

    /**
     * jsonmapper objet to make RESTful request.
     * @var JsonMapper
     */
    public $jsonmapper;

    /**
     * Constructor.
     * @param Parsehub\Interfaces\IRESTful $request request object to make RESTful request.
     */
    public function __construct(IRESTful $request = null)
    {
        $this->request = $request;
        $this->jsonmapper = new \JsonMapper();
    }

    /**
     * GET request with restful api.    
     * @param  sting $url     url of REST api.
     * @param  array $options options to use with get request.
     * @return mixed          method can return array, object or string.
     */
    public function get($url, $options = array())
    {
        $response = Request::get($url)->send();
        return $response;
    }

    /**
     * POST request with restful api.    
     * @param  sting $url     url of REST api.
     * @param  array $options options to use with get request.
     * @return mixed          method can return array, object or string.
     */
    public function post($url, $options = array())
    {
        $response = Request::post($url)->body($options['data'])->send();
        return $response;
    }

    /**
     * PUT request with restful api.    
     * @param  sting $url     url of REST api.
     * @param  array $options options to use with get request.
     * @return mixed          method can return array, object or string.
     */
    public function put($url, $options = array())
    {
        $response = Request::put($url)->body($options['data'])->send();
        return $response;
    }

    /**
     * DELETE request with restful api.    
     * @param  sting $url     url of REST api.
     * @param  array $options options to use with get request.
     * @return mixed          method can return array, object or string.
     */
    public function delete($url, $options = array())
    {
        $response = Request::delete($url)->send();
        return $response;
    }

    /**
     * Gets the Request objet to make RESTful request.
     *
     * @return Parsehub\Interfaces\IRESTful
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the Request objet to make RESTful request.
     *
     * @param Parsehub\Interfaces\IRESTful $request the request
     *
     * @return self
     */
    public function setRequest(Parsehub\Interfaces\IRESTful $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Gets the jsonmapper objet to make RESTful request.
     *
     * @return JsonMapper
     */
    public function getJsonmapper()
    {
        return $this->jsonmapper;
    }

    /**
     * Sets the jsonmapper objet to make RESTful request.
     *
     * @param JsonMapper $jsonmapper the jsonmapper
     *
     * @return self
     */
    public function setJsonmapper(JsonMapper $jsonmapper)
    {
        $this->jsonmapper = $jsonmapper;

        return $this;
    }
}
