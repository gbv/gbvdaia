<?php
declare(strict_types=1);

namespace DAIA;

class Request
{
    public $ids;
    public $format;
    public $callback;
    public $patron;
    public $patronType;
    public $accessToken;
    public $accept;
    public $language;
    public $authorization;

    function __construct(array $get=[], array $headers=[])
    {

        # request parameters

        $this->ids = isset($get['id']) ? explode('|', $get['id']) : [];

        $this->format = $get['format'] ?? 'json';

        $this->callback = $get['callback'] ?? '';
        if (!preg_match('/^[a-z_][a-z0-9_]*/i', $this->callback)) {
            $this->callback = null;
        }

        $this->accessToken = $get['access_token'] ?? null;

        $this->patron = $get['patron'] ?? null;

        $this->patronType = $get['patron-type'] ?? null;


        # request headers

        $this->accept = $headers['Accept'] ?? 'application/json';

        $this->language = $headers['Accept-Language'] ?? null;

        $this->authorization = $headers['Authorization'] ?? null;
    }
}
