<?php

require_once("vendor/autoload.php");

define("LOGIN_RESOURCE", "/arequest/login");
define("ORDER_RESOURCE", "/grequest/order");
define("BASE_URL", "http://triton.ironhelmet.com");
define("DOMAIN", "triton.ironhelmet.com");
define("COOKIEFILE", "triton-cookie");

$config = getConfig();
if($config["username"] == null || $config["password"] == null || $config["gameNumber"] == null) {
    echo "Please fill out your config file.";
    exit();
}

if(isset($config["tmpDir"]))
    define("STORAGEDIR", $config["tmpDir"]);
else
    define("STORAGEDIR", "/tmp");

if($login = login($config["username"], $config["password"])) {
    $data = getData($config["gameNumber"]);
} else {
    echo "Couldn't login";
}

function getConfig() {
    $fp = @fopen('config.json', 'r');
    $data = fread($fp, filesize('config.json'));

    return json_decode($data, true);
}

function login($username = null, $password = null) {
    if($username == null || $password == null) {
        echo "Please supply a username and a password in config.json";
    }

    $data = [
        "alias" => "$username",
        "password" => "$password",
        "type" => "login",
    ];

    $response = doRequest(LOGIN_RESOURCE, $data, 1);

    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $response, $m);
    saveCookie($m[1]);

    return $response;
}

function getData($gameNumber = null) {
    if($gameNumber == null) {
        echo "You need to set a game number in config.json";
        exit();
    }

    $data = [
        "order" => "full_universe_report",
        "type" => "order",
        "version" => "7",
        "gameNumber" => $gameNumber
    ];


    $response = doRequest(ORDER_RESOURCE, $data, 1);

    return $response;
}

function saveCookie($data) {
    saveFile(COOKIEFILE, $data);
}

function getCookie() {
        return file_get_contents(STORAGEDIR . '/' . COOKIEFILE);
}

function saveFile($filename, $data) {
    $filename = STORAGEDIR . '/' . $filename;
    if(!file_put_contents($filename, $data))
        throw new Exception("Could not write to file {$filename}");
}

function deleteFile($filename) {
    $filename = STORAGEDIR . '/' . $filename;
    if(!unlink($filename))
        echo "Could not delete {$filename}";
}

function doRequest($resource, $data = array(), $returnHeaders = 0) {
    $isLogin = substr_count($resource, 'login');
    $url = BASE_URL . $resource;
    $headers = [
//        "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.85 Safari/537.36",
//        "Accept: application/json, text/javascript, */*; q=0.01",
    ];

    $ch = curl_init($url);

    $cookieFile = STORAGEDIR . '/' . COOKIEFILE;

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, $returnHeaders);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    var_dump($response);

    return $response;
}

?>
