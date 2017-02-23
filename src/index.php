<?php

include __DIR__.'/../vendor/autoload.php';

use DAIA\Request;
use DAIA\Error;

$request = new Request($_GET, getallheaders());

# TODO: HTTP OPTIONS request and 405 response

$app = new GBVDAIA();

$path = $_SERVER['PATH_INFO'] ?? '';
if ($path == '') {
    $response = $app->query($request);
} else {
    $isil = GBVDAIA::isilFromPath($path);
    if ($isil) {
        $response = $app->query($request, $isil);
    } else {
        $response = new Error(404, 'not_found', 'Nothing found at this URL');
    }
}

$response->send([
    'callback' => $request->callback,
    'language' => 'de', # TODO: configure?
    # TODO: add Link header for multiple ids
]);

