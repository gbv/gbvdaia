<?php
declare(strict_types=1);

namespace DAIA;

class Error extends ResponseData
{
    public $code;
    public $error;
    public $error_description;
    public $error_uri;

    function __construct(int $c, string $e, string $d=NULL, $uri=NULL) {
        $this->code = $c;
        $this->error = $e;
        $this->error_description = $d;
        $this->error_uri = $uri;
    }
}
