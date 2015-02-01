<?php namespace SpaceNorad;

class Config implements \ArrayAccess {
    private $config = [];

    function __construct() {
        $this->getConfig();
    }

    private function getConfig() {
        $dir = dirname(dirname(__FILE__));
        $configFile = $dir . '/config.json';
        $fp = @fopen($configFile , 'r');
        $data = fread($fp, filesize($configFile));

        $config = json_decode($data, true);
        $this->validateConfig($config);

        $this->config = $config;
    }

    private function validateConfig($config) {
        if($config["username"] == null || $config["password"] == null || $config["gameNumber"] == null) {
            echo "Please fill out your config file.";
            exit();
        }
    }

    public function offsetSet($offset, $value) {
        $this->config[$offset] = $value;
    }

    public function offsetGet($offset) {
        return $this->config[$offset];
    }

    public function offsetExists($offset) {
        return isset($this->config[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->config[$offset]);
    }

}
