<?php declare(strict_types=1);

namespace DAIA;

/**
 * Any kind of HTTP response sent from a DAIA server.
 *
 * See <https://purl.org/NET/DAIA#request-and-response>
 * @package DAIA
 */
abstract class HTTPResponse extends Data
{
    public $language = 'en';
    public $headers = [];

    /**
     * Return HTTP status code.
     */
    abstract public function getStatusCode(): int;

    /**
     * Return HTTP response headers.
     *
     * @param boolean callback whether to use JSONP (no by default)
     * @param string output format 'json' (default) or 'xml'
     */
    public function getHeaders($callback=false, string $format='json'): array
    {
        $headers = $this->headers;

        # TODO: add Link header with additional query ids
        # TODO: add Link header with "<$profile>; rel=\"profile\"";

        $headers['X-DAIA-Version'] = ['1.0.0'];
        $headers['Access-Control-Allow-Origin'] = ['*'];
        $headers['Content-Language'] = [$this->language];

        if ($format == 'xml') {
            $headers['Content-Type'] = ['text/xml'];
        } else {
            if ($callback) {
                $headers['Content-Type'] = ['application/javascript'];
            } else {
                $headers['Content-Type'] = ['application/json; charset=utf-8'];
            }
        }

        ksort($headers);
        return $headers;
    }

    /**
     * Serialize as JSON without headers and language.
     */
    public function jsonSerialize($root=true)
    {
        $data = parent::jsonSerialize($root);
        unset($data->language);
        unset($data->headers);
        return $data;
    }

    /**
     * Return HTTP response body.
     *
     * @param string callback JSONP function name
     * @param string output format 'json' (default) or 'xml'
     */
    public function getBody(string $callback='', string $format='json'): string
    {
        if ($format == 'xml') {
            $writer = new XMLWriter();
            $writer->write($this);
            return $writer->outputMemory();
        } else {
            if ($callback) {
                return "/**/$callback($this);";
            } else {
                return $this->json();
            }
        }
    }

    /**
     * Send as HTTP Response.
     *
     * @param Request to get HTTP request method (GET or HEAD), callback and format
     */
    public function send(Request $request)
    {
        $method   = $request->method ?? 'GET';
        $callback = $request->callback ?? '';

        http_response_code($this->getStatusCode());

        $headers = $this->getHeaders($callback, $request->format);
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value");
            }
        }

        if ($method == 'GET') {
            echo $this->getBody($callback, $request->format);
        }
    }
}
