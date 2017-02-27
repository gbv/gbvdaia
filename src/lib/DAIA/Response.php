<?php
declare(strict_types=1);

namespace DAIA;

class Response extends ResponseData
{
    public $document = [];
    public $institution;
    public $timestamp;

    public function __construct()
    {
        $this->timestamp = date("c", time()); 
    }

    public function send(array $options=[])
    {
        static::sendJSON($this, $options);
    }

    public static function sendJSON(Data $data, array $options=[])
    {
        static::sendCommonHeaders($options);

        if ($options['link'] ?? FALSE) {
            header('Link: ' . $options['link']);
        }

        if ($options['callback'] ?? FALSE) {
            header('Content-Type: application/javascript');
            echo "/**/".$options['callback']."($this);";
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo $data->json();
        }
    }

    public static function handleHTTPMethods(Request $request, array $options=[])
    {
        if ($request->method == 'GET' or $request->method == 'HEAD') {
            return;
        }

        if ($request->method == 'OPTIONS') {
            static::sendCommonHeaders($options);
            header('Access-Control-Allow-Headers: Authorization, Content-Type');
            header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
        } else {
            $error = new Error(405, 'invalid_request', 'Unexpected HTTP verb');
            $error->send($options);
        }
        
        exit;
    }

    public static function sendCommonHeaders(array $options=[])
    {
        header('X-DAIA-Version: 1.0.0');
        header('Access-Control-Allow-Origin: *');

        if ($options['language'] ?? FALSE) {
            header('Content-Language: ' . $options['language']);
        }

        if ($options['profile'] ?? FALSE) {
            header('Link: <' . $options['profile'] . '>; rel="profile"');
        }
    }
}
