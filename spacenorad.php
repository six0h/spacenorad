<?php

require_once('vendor/autoload.php');

define("BASEPATH", __DIR__);

use SpaceNorad\CliApp;
use SpaceNorad\Config;
use SpaceNorad\Request\NeptuneApi;
use SpaceNorad\Request\CurlRequest;

$config = new Config();
$request = new CurlRequest();
$api = new NeptuneApi($request);

if (php_sapi_name() == "cli") {
    $app = new CliApp($config, $request, $api);
    $app->start();
} else {
    $app = new WebApp($config, $request, $api);
    $app->start();
}

?>
