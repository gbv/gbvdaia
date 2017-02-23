<?php
declare(strict_types=1);

namespace DAIA;

class Response extends ResponseData
{
    public $document = [];
    public $institution;
    public $timestamp;

    function __construct() {
        $this->timestamp = date("c", time()); 
    }

    function send(array $options=[]) {
        static::sendJSON($this, $options);
    }

    static function sendJSON(Data $data, array $options=[]) {
        header('X-DAIA-Version: 1.0.0');
        header('Access-Control-Allow-Origin: *');

        if ($options['language'] ?? FALSE) {
            header('Content-Language: ' . $options['language']);
        }

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
}
