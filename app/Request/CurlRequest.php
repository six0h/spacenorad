<?php namespace SpaceNorad\Request;

class CurlRequest {

    private $url,
            $data;

    function __construct() {
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data = array()) {
        $this->data = $data;
    }

    public function send($returnHeaders = 0) {

        $fields_string = http_build_query($this->data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, $returnHeaders);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_COOKIEJAR, BASEPATH . '/cache/cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, BASEPATH . '/cache/cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

        if(!$response = curl_exec($ch)) {
            echo curl_error($ch);
        }

        return $response;
    }

}
