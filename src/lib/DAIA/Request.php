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
    public $method;

    public function __construct(array $get=[], array $headers=[], string $method='GET')
    {
        // request parameters
        $this->ids = isset($get['id']) ? explode('|', $get['id']) : [];
        $this->format = $get['format'] ?? 'json';
        $this->callback = $get['callback'] ?? '';
        if (!preg_match('/^[a-z_][a-z0-9_]*/i', $this->callback)) {
            $this->callback = null;
        }
        $this->accessToken = $get['access_token'] ?? null;
        $this->patron = $get['patron'] ?? null;
        $this->patronType = $get['patron-type'] ?? null;

        // request headers
        $this->accept = $headers['Accept'] ?? 'application/json';
        $this->language = $headers['Accept-Language'] ?? null;
        $this->authorization = $headers['Authorization'] ?? null;
        $this->origin = $headers['Origin'] ?? null;

        // request method (GET, HEAD, OPTIONS)
        $this->method = $method;
    }

    // TODO: add fromPSR7 or refactor the whole class to use PSR-7

    public static function fromHTTP()
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            $headers = [];
            foreach ($_SERVER as $name => $value) { 
                if (substr($name, 0, 5) != 'HTTP_') continue;
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
           } 
        }

        return new Request($_GET, $headers, $_SERVER['REQUEST_METHOD']);
    }
}
