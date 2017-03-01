<?php

include __DIR__.'/../vendor/autoload.php';

$headers = [];
# $headers['Link'] = "<$profile>; rel=\"profile\""; // TODO

try {
    $request = DAIA\Request::fromGlobals();

    // TODO: move into DAIA namespace?
    if ($request->method == 'OPTIONS') {
        $headers['Access-Control-Allow-Headers'] = ['Authorization, Content-Type'];
        $headers['Access-Control-Allow-Methods'] = ['GET, HEAD, OPTIONS'];
        $response = new DAIA\Response(); 
    } 
    else 
    {
        // TODO: move into DAIA\Request
        $path = $_SERVER['PATH_INFO'] ?? '';

        if (!count($request->ids) && $path === '') {
            include 'startseite.php';
            exit;
        }

        // get response from DAIA Service
        $config = new GBV\DAIA\FileConfig('../config', Psr\Log\LogLevel::DEBUG);

        $app = new GBV\DAIA\Service($config);

        if ($path == '') {
            $response = $app->query($request);
        } else {
            $isil = GBV\DAIA\Service::isilFromPath($path);
            if ($isil) {
                $response = $app->query($request, $isil);
            } else {
                throw new DAIA\Error(404, 'not_found', 'Nothing found at this URL');
            }
        }
    }
} catch (DAIA\Error $e) {
    $response = $e;
}

# TODO: add Link header for multiple ids

$response->send($request->method, $headers ?? [], $request->callback ?? '');
