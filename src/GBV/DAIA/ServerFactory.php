<?php declare(strict_types=1);

namespace GBV\DAIA;

use GBV\ISIL;
use Psr\Log\LoggerInterface;

/**
 * Build instances of DAIA Servers.
 *
 * @package GBVDAIA 
 */
class ServerFactory
{
    use \Psr\Log\LoggerAwareTrait;

    private $config;

    public function __construct(Config $config, LoggerInterface $logger=null)
    {
        $this->setLogger($logger ?? new Logger());
        $this->config = $config;
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
        
        $server = new Server($this->config, $this->logger);
        if (isset($isil)) {
            $server->isil = $isil;
        }

        return $server;
    }
}
