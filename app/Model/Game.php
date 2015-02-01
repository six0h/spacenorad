<?php namespace SpaceNorad\Model;

class Game {

    private $number,
            $name,
            $creator,
            $turn_based,
            $maxPlayers,
            $players,
            $version,
            $config,
            $fleets,
            $stars,
            $myStars,
            $enemies,
            $attackers;

    public  $report;

    public static function Parse($infoArray) {
        $game = new Game();
        foreach($infoArray as $k => $v) {
            $game->set($k, $v);
        }

        return $game;
    }

    public function set($k, $v) {
        $this->$k = $v;
    }

    public function get($k) {
        return $this->$k;
    }

    public function getNumber() {
        return $this->number;
    }

    public function getName() {
        return $this->name;
    }

    public function getPlayers() {
        return $this->players;
    }

    public function getCreator() {
        return $this->creator;
    }

    public function isTurnBased() {
        return $this->turn_based;
    }

    public function getMaxPlayers() {
        return $this->maxPlayers;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getReport() {
        return $this->report;
    }

    public function setReport($report) {
        $this->report = $report;
    }

    public function findAttackers($enemies, $stars, $myId) {
        $attackers = [];
        foreach($enemies as $enemy) {
            if(isset($enemy['o'][0][1])) {
                $target = $enemy['o'][0][1];
                if(isset($stars[$target])) {
                    $attackers[] = $enemy;
                }
            }
        }
        return $attackers;
    }

    public function findMyStars($stars, $myId) {
        $myStars = [];
        foreach($stars as $star) {
            if($star["puid"] == $myId) {
                $myStars[$star["uid"]] = $star;
            }
        }
        return $myStars;
    }

    public function findEnemies($fleets, $myId) {
        $enemies = [];
        foreach($fleets as $fleet) {
            if($fleet['puid'] != $myId) {
                $enemies[$fleet["uid"]] = $fleet;
            }
        }
        return $enemies;
    }

}
