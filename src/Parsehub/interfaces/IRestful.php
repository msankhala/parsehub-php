<?php
namespace Parsehub\interfaces;

/**
 * Interface providing common method which adpater can implement.
 */
interface IRESTful
{

    /**
     * GET request with restful api.    
     * @param  sting $url     url of REST api.
     * @param  array $options options to use with get request.
     * @return mixed          method can return array, object or string.
     */
    public function get($url, $options = array());

    /**
     * POST request with restful api.    
     * @param  sting $url     url of REST api.
     * @param  array $options options to use with get request.
     * @return mixed          method can return array, object or string.
     */
    public function post($url, $options = array());

    /**
     * PUT request with restful api.    
     * @param  sting $url     url of REST api.
     * @param  array $options options to use with get request.
     * @return mixed          method can return array, object or string.
     */
    public function put($url, $options = array());

    /**
     * DELETE request with restful api.    
     * @param  sting $url     url of REST api.
     * @param  array $options options to use with get request.
     * @return mixed          method can return array, object or string.
     */
    public function delete($url, $options = array());
}
