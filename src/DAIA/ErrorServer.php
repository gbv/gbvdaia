<?php
declare(strict_types=1);

namespace DAIA;

/**
 * A DAIA Server that always returns an error.
 * 
 * @package DAIA
 */
class ErrorServer extends Server
{
    public $error;

    public function __construct(Error $error)
    {
        $this->error = $error;
    }
    
    public function queryImplementation(Request $request): Response
    {
        throw $this->error;
    }
}
