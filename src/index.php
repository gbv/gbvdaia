<?php

include __DIR__.'/../vendor/autoload.php';

use DAIA\Request;
use DAIA\Error;

// build request

if (function_exists('getallheaders')) {
    $headers = getallheaders();
} else {
	$headers = [];
	foreach ($_SERVER as $name => $value) { 
		if (substr($name, 0, 5) != 'HTTP_') continue;
		$name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
		$headers[$name] = $value;
   } 
}

$request = new Request($_GET, $headers);

# TODO: Automatically answer non-GET requests (HTTP OPTIONS / 405 error response)


// get response from DAIA Service
$config = new GBV\DAIA\FileConfig('daia-config');
$app = new GBV\DAIA\Service($config);

$path = $_SERVER['PATH_INFO'] ?? '';
if ($path == '') {
    $response = $app->query($request);
} else {
    $isil = DAIAService::isilFromPath($path);
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

