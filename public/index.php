<?php
/**
 * GBV DAIA main script to route all HTTP queries except static files through.
 */

include __DIR__.'/../vendor/autoload.php';

$request = DAIA\Request::fromGlobals();
    
$path = $_SERVER['PATH_INFO'] ?? '/';

if ( $request->method != 'OPTIONS' and $path == '/' and !count($request->ids) ) {
    include 'startseite.php';
    exit;
}

# TODO: catch errors during config initialization

$config = '../config';
$level  = Psr\Log\LogLevel::DEBUG;
$config = new GBV\DAIA\FileConfig($config, $level);
#$config = new GBV\DAIA\GitConfig($config, $level);

$factory  = new GBV\DAIA\ServerFactory($config);
$server   = $factory->makeServer($path);
$response = $server->query($request);
    
# TODO: für ausgewählte Bibliotheken: XML-Format erlauben (veraltet)

$response->send();
