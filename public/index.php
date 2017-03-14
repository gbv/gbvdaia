<?php
/**
 * GBV DAIA main script to route all HTTP queries except static files through.
 */

// core configuration

// write logfiles to this directory
$LOG_DIR   = 'log';

// PSR-3 log level (DEBUG, INFO, NOTICE, WARNING, ERROR)
$LOG_LEVEL = 'WARNING';

// where to read configuration from
$CONFIG_DIR = '../config'; 

// git remote to fetch configuration from
$GIT_REMOTE  = null; # 'https://github.com/gbv/daia-config.git';

if (file_exists('../config.php')) {
    include 'config.php';
}


include __DIR__.'/../vendor/autoload.php';

$LOG_DIR=null;
$logger = new GBV\DAIA\Logger($LOG_DIR, $LOG_LEVEL);

$request = DAIA\Request::fromGlobals();
    
$path = $_SERVER['PATH_INFO'] ?? '/';

if ( $request->method != 'OPTIONS' and $path == '/' and !count($request->ids) ) {
    include 'startseite.php';
    exit;
}

# TODO: catch errors during initialization

if ($GIT_REMOTE) {
    $config = new GBV\DAIA\GitConfig($CONFIG_DIR, $GIT_REMOTE, $logger);
} else {
    $config = new GBV\DAIA\FileConfig($CONFIG_DIR, $logger);
}

$factory  = new GBV\DAIA\ServerFactory($config, $logger);
$server   = $factory->makeServer($path);

$response = $server->query($request);
    
# TODO: für ausgewählte Bibliotheken: XML-Format erlauben (veraltet)

$response->send();
