#!/usr/bin/env php
<?php

require_once('vendor/autoload.php');

define("BASEPATH", dirname(__FILE__));

use SpaceNorad\App;
use SpaceNorad\Config;
use SpaceNorad\Request\NeptuneApi;
use SpaceNorad\Request\CurlRequest;

$config = new Config();
$request = new CurlRequest();
$api = new NeptuneApi($request);

$app = new App($config, $request, $api);
$app->start();

?>
