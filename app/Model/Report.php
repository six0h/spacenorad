<?php namespace SpaceNorad\Model;

class Report {
    private $playerId,
            $fleets,
            $stars,
            $players;

    function __construct() {
    }

    public static function Parse($reportJson) {
        $report = new Report();
        $report->fleets = $reportJson['fleets'];
        $report->stars = $reportJson['stars'];
        $report->players = $reportJson['players'];
        $report->parseReport();

        $report->setPlayerId($reportJson['player_uid']);
        return $report;
    }

    public function setPlayerId($id) {
        $this->playerId = $id;
    }

    public function getPlayerId() {
        return $this->playerId;
    }

    public function getStars() {
        return $this->stars;
    }

    public function getPlayers() {
        return $this->players;
    }

    public function getFleets() {
        return $this->fleets;
    }

    public function parseReport() {
        $this->fleets = $this->parseUnits('fleets');
        $this->stars = $this->parseUnits('stars');
        $this->players = $this->parseUnits('players', false);
    }

    private function parseUnits($unitType, $assocArray = true) {
        $arrUnits = [];

        if(!isset($this->$unitType)) {
            exit("Sorry, {$unitType} does not exist");
        }

        foreach($this->$unitType as $unit) {
            if($assocArray) {
                $arrUnits[$unit['uid']] = $unit;
            } else {
                $arrUnits[] = $unit;
            }
        }
        return $arrUnits;
    }
}
