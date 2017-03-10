<?php
/**
 * GBV DAIA application main script.
 *
 * All HTTP queries except static files must be routed to this script.
 */

include __DIR__.'/../vendor/autoload.php';

$request = DAIA\Request::fromGlobals();
    
$path = $_SERVER['PATH_INFO'] ?? '/';

if ( $request->method != 'OPTIONS' && (!$path or $path == '/') and !count($request->ids) ) {
    include 'startseite.php';
    exit;
}

# TODO: catch errors in config_
$config = '../config';
$level  = Psr\Log\LogLevel::DEBUG;
$config = new GBV\DAIA\FileConfig($config, $level);
#$config = new GBV\DAIA\GitConfig($config, $level);

$serverFactory = new GBV\DAIA\ServerFactory($config);
$server = $serverFactory->makeServer($path);

$response = $server->queryResponse($request);

$response->send();
