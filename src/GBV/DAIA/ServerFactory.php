<?php
declare(strict_types=1);

namespace GBV\DAIA;

use GBV\ISIL;

/**
 * Build instances of DAIA Servers.
 *
 * @package GBVDAIA 
 */
class ServerFactory
{
    private $config;

    public function __construct(Config $config)
    {
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
        
        $server = new Server($this->config);
        $server->isil = $isil;

        return $server;
    }
}
