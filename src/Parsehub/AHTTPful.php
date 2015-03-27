<?php
namespace Parsehub;

use Parsehub\interfaces\IRESTful;
use Httpful\Request;

/**
 * Httpful adapter implementing IRESTful interface. Adapter class for phphttpclient.com
 * 
 * @see http://phphttpclient.com/
 */
class AHTTPFUL implements IRESTful
{
    /**
     * Request objet to make RESTful request.
     * @var HTTPful\Request
     */
    private $request;

    /**
     * Constructor.
     * @param Httpful\Request $request request object to make RESTful request.
     */
    public function __construct(Httpful\Request $request)
    {
        $this->request = $request;
    }
}
