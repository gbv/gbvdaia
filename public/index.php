<?php

include __DIR__.'/../vendor/autoload.php';

/*
// initialize logger
use Monolog\Logger;

// log warnings and errors to STDERR by default
$logStream = new \Monolog\Handler\StreamHandler('php://stderr', Logger::WARNING)
$logger->pushHandler($stream);
 */

use DAIA\Request;
use DAIA\Error;

// build request

$request = Request::fromHTTP();

$options = [ 'language' => 'de' ]; # TODO: profile.json

// Respond to HTTP OPTIONS and non-GET/HEAD requests
DAIA\Response::handleHTTPMethods($request, $options);


$path = $_SERVER['PATH_INFO'] ?? '';

if (!count($request->ids) && $path === '') {
    include 'startseite.php';
    exit;
}


// get response from DAIA Service
$config = new GBV\DAIA\FileConfig('daia-config');
$app = new GBV\DAIA\Service($config);

if ($path == '') {
    $response = $app->query($request);
} else {
    $isil = GBV\DAIA\Service::isilFromPath($path);
    if ($isil) {
        $response = $app->query($request, $isil);
    } else {
        $response = new Error(404, 'not_found', 'Nothing found at this URL');
    }
}

# TODO: support HEAD request

$response->send([
    'callback' => $request->callback,
    'language' => 'de', # TODO: configure?
    # TODO: add Link header for multiple ids
]);