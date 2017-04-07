<?php declare(strict_types=1);

namespace GBV\DAIA;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

use Http\Client\HttpClient;

use DAIA\Proxy;
use GBV\ISIL;

use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use Http\Adapter\Guzzle6\Client as GuzzleClient;

/**
 * Build instances of DAIA Servers.
 *
 * @package GBVDAIA 
 */
class ServerFactory
{
    use \Psr\Log\LoggerAwareTrait;

    private $config;
    private $client;

    public function __construct(Config $config, LoggerInterface $logger)
    {
        $this->setLogger($logger);
        $this->client = static::makeClient($logger);

        // additional configuration
        $this->config = $config;
    }

    public static function makeClient(LoggerInterface $logger): HttpClient
    {
        // configure HTTP request logging
        $stack = HandlerStack::create();

        $stack->unshift(Middleware::log(
            $logger,
            new MessageFormatter("{method} {uri} {code}"),
            LogLevel::INFO
        ));

        $stack->unshift(Middleware::log(
            $logger,
            new MessageFormatter("{res_headers}\n{res_body}"),
            LogLevel::DEBUG
        ));

        // setup HTTP client
        return GuzzleClient::createWithConfig([            
            'connect_timeout' => 1.0,
            'timeout' => 3.0,
            'User-Agent' => 'GBVDAIA/0.0.0',
            'handler' => $stack,
        ]);
    }

    public function makeServer(string $path='/'): \DAIA\Server
    {
        if (preg_match('!^/isil/(.+)!', $path, $match) and ISIL::ok($match[1])) {
            $isil = $match[1];
            $path = '/';
        }

        if ($path != '/') {
            $error = new \DAIA\Error(404, 'Nothing found');
            return new \DAIA\ErrorServer($error);
        }
        
        $server = new Server($this->config, $this->logger, $this->client);
        if (isset($isil)) {
            $server->isil = $isil;

            $url = $this->config->proxyUrl($isil);
            if ($url) {
                $server = new Proxy($url, $this->client, $this->logger);
            }
        }

        return $server;
    }
}
