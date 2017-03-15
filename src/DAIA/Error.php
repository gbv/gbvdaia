<?php declare(strict_types=1);

namespace DAIA;

/**
 * A throwable DAIA Error.
 *
 * @package DAIA
 **/
class Error extends \Exception
{
    public $response;

    public function __construct(int $code, string $description=null, $uri=null)
    {        
        $this->response = new ErrorResponse($code, $description, $uri);
    }
}
