<?php
declare(strict_types=1);

namespace DAIA;

abstract class ResponseData extends Data
{
    private $language = 'en';

    /**
     * Return HTTP status code.
     */
    abstract public function getStatusCode(): int;

    /**
     * Return HTTP response headers.
     *
     * @param boolean callback whether to use JSONP (no by default)
     */
    public function getHeaders($callback=false): array
    {
        $headers = [];

        $headers['X-DAIA-Version'] = ['1.0.0'];
        $headers['Access-Control-Allow-Origin'] = ['*'];
        $headers['Content-Language'] = [$this->language];

        if ($callback) {
            $headers['Content-Type'] = ['application/javascript'];
        } else {
            $headers['Content-Type'] = ['application/json; charset=utf-8'];
        }

        return $headers;
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    /**
     * Return HTTP response body.
     *
     * @param string callback JSONP function name
     */
    public function getBody(string $callback=''): string
    {
        if ($callback) {
            return "/**/$callback($this);";
        } else {
            return $this->json();
        }
    }

    /**
     * Send as HTTP Response.
     *
     * @param string method   HTTP request method (GET or HEAD)
     * @param array  headers  additional HTTP response headers
     * @param string callback JSONP function name
     */
    public function send(string $method='GET', array $headers=[], string $callback='')
    {
        http_response_code($this->getStatusCode());

        $this->sendHeaders($this->getHeaders($callback));
        $this->sendHeaders($headers);

        if ($method == 'GET') {
            echo $this->getBody($callback);
        }
    }

    private static function sendHeaders(array $headers) {
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value");
            }
        }
    }
}
