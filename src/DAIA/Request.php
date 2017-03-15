<?php
declare(strict_types=1);

namespace DAIA;

/**
 * Query parameters and request headers sent to a DAIA server.
 *
 * See <https://purl.org/NET/DAIA#request-and-response>
 * @package DAIA
 */
class Request extends Data
{
    public $method = 'GET'; // GET, HEAD, OPTIONS
    public $ids;
    public $format;
    public $callback;
    public $patron;
    public $patronType;
    public $accessToken;
    public $accept;
    public $language;
    public $authorization;
    public $origin;

    /**
     * Create a new HTTP GET Request.
     *
     * @param array|string query    query id or query parameters
     * @param array        headers  request headers
     */
    public function __construct($query='', array $headers=[])
    {
        // TODO: make this private            

        // request parameters
        if (!is_array($query)) {
            $query = ['id'=>(string)$query];
        }
        $this->ids = array_values(array_filter(explode('|', $query['id'] ?? ''),
                                   function ($s) {
                                       return $s !== '';
                                   }));
        $this->format = $query['format'] ?? 'json';
        $this->callback = $query['callback'] ?? '';
        if (!preg_match('/^[a-z_][a-z0-9_]*/i', $this->callback)) {
            $this->callback = null;
        }
        $this->accessToken = $query['access_token'] ?? null;
        $this->patron = $query['patron'] ?? null;
        $this->patronType = $query['patron-type'] ?? null;

        // request headers
        $this->accept = $headers['Accept'] ?? 'application/json';
        $this->language = $headers['Accept-Language'] ?? null;
        $this->authorization = $headers['Authorization'] ?? null;
        $this->origin = $headers['Origin'] ?? null;
    }

    /**
     * Create a Request populated from superglobals `$_SERVER` and `$_GET`.
     */
    public static function fromGlobals(array $server=null, array $get=null): Request
    {
        $server = $server ?? $_SERVER;
        
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            $headers = [];
            foreach ($server as $name => $value) {
                if (substr($name, 0, 5) != 'HTTP_') {
                    continue;
                }
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$name] = $value;
            }
        }

        $method = $server['REQUEST_METHOD'] ?? 'GET';
        return static::buildRequest($method, $get ?? $_GET, $headers);
    }

    /**
     * Create a Request from PSR-7 ServerRequestInterface.
     *
     * @throws TypeError unless the argument is instance of Psr\Http\Message\ServerRequestInterface
     */
    public static function fromPsr7($request): Request
    {
        // This class does not require PSR-7 so we cannot use type hinting
        $expect = "Psr\Http\Message\ServerRequestInterface";
        $method = __METHOD__;
        if (!$request instanceof $expect) {
            $type = gettype($request);
            if ($type == 'object') {
                $type = "instance of ".get_class($request);
            }
            throw new \TypeError("Argument 1 passed to $method must be an instance of $expect, $type given");
        }

        $headers = array_map(function ($h) {
            return $h[0];
        }, $request->getHeaders());

        return static::buildRequest($request->getMethod(), $request->getQueryParams(), $headers);
    }

    private static function buildRequest($method, $query, $headers): Request
    {
        $request = new Request($query, $headers);
        $request->method = $method;
        return $request;
    }
}
