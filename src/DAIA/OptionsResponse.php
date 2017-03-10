<?php
declare(strict_types=1);

namespace DAIA;

/**
 * HTTP response to an OPTIONS request.
 *
 * See <https://purl.org/NET/DAIA#request-and-response>
 * @package DAIA
 */
class OptionsResponse extends ResponseData    
{
    public function __construct()
    {
        $this->headers = [
            'Access-Control-Allow-Headers' => ['Authorization, Content-Type'],
            'Access-Control-Allow-Methods' => ['GET, HEAD, OPTIONS']
        ];
    }

    public function getStatusCode(): int
    {
        return 200;
    }
}
