<?php
declare(strict_types=1);

namespace DAIA;

class Error extends ResponseData
{
    public $code;
    public $error;
    public $error_description;
    public $error_uri;

    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function __construct(int $code, string $description=null, $uri=null)
    {
        $this->code = $code;
        $this->error = static::$errors[$code] ?? 'unknown';
        $this->error_description = $description;
        $this->error_uri = $uri;
    }

    private static $errors = [
        400 => 'invalid_request',
        401 => 'invalid_grant',
        403 => 'insufficient_scope',
        404 => 'not_found',
        405 => 'invalid_request',
        422 => 'invalid_request',
        500 => 'internal_error',
        501 => 'not_implemented',
        502 => 'bad_gateway',
        503 => 'service_unavailable',
        504 => 'gateway_timeout'
    ];
}
