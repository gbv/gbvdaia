<?php
declare(strict_types=1);

namespace DAIA;

class ThrownError extends \Exception
{
    public $error;

    public function __construct(int $code, string $description=null, $uri=null)
    {        
        $this->error = new Error($code, $description, $uri);
    }
}
