<?php namespace SpaceNorad\Request;

class NeptuneApi {

    const LOGIN_RESOURCE = "/arequest/login";
    const ORDER_RESOURCE = "/grequest/order";
    const PLAYER_RESOURCE = "/mrequest/init_player";
    const BASE_URL = "http://triton.ironhelmet.com";

    private $request;

    function __construct(CurlRequest $request) {
        $this->request = $request;
    }

    public function login($username = null, $password = null) {
        if($username == null || $password == null) {
            echo "Please supply a username and a password in config.json";
        }

        $data = [
            "alias" => "$username",
            "password" => "$password",
            "type" => "login",
        ];

        $this->request->setData($data);
        $this->request->setUrl(self::BASE_URL . self::LOGIN_RESOURCE);
        return json_decode($this->request->send(), true);
    }

    public function getPlayerInfo() {
        $data = [
            "type" => "init_player"
        ];

        $this->request->setData($data);
        $this->request->setUrl(self::BASE_URL . self::PLAYER_RESOURCE);
        return json_decode($this->request->send(), true);
    }

    public function getGameReport($gameNumber = null) {
        if($gameNumber == null) {
            echo "You need to set a game number in config.json";
            exit();
        }

        $data = [
            "type" => "order",
            "order" => "full_universe_report",
            "version" => "7",
            "game_number" => $gameNumber,
        ];

        $this->request->setData($data);
        $this->request->setUrl(self::BASE_URL . self::ORDER_RESOURCE);
        return json_decode($this->request->send(), true);
    }

}
