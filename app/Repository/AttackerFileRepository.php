<?php namespace SpaceNorad\Repository;

class AttackerFileRepository {
    private $dirList;
    private $baseDir;
    private $game;
    private $cacheFiles;

    function __construct($game) {
        $this->game = $game;
        $this->baseDir = dirname(__FILE__);
        $this->cacheFiles = scandir($this->baseDir . '/cache');
        var_dump($cacheFiles);
    }

    public function findNewAttackers($attackers) {
        foreach($attackers as $attacker) {

        }
    }

    private function fileExists() {

    }
}
