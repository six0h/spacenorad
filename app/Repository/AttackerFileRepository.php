<?php namespace SpaceNorad\Repository;

class AttackerFileRepository {
    private $dirList;
    private $game;
    private $cacheDir;
    private $cacheFiles;

    function __construct($game) {
        $this->game = $game;
        $this->cacheDir = dirname(dirname(dirname(__FILE__))) . '/cache';
        $this->cacheFiles = scandir($this->cacheDir);
    }

    public function findNewAttackers($fleets) {
        $gameNumber = $this->game->getNumber();
        $newAttackers = [];
        foreach($fleets as $fleet) {
            if(!$this->attackerFileExists($gameNumber, $fleet['uid'])) {
                $this->createFile($gameNumber, $fleet['uid']);
                $newAttackers[] = $fleet;
            }
        }

        return $newAttackers;
    }

    public function clearLandedAttackers($fleets) {
        $gameNumber = $this->game->getNumber();
        $fleetIds = [];
        foreach($fleets as $fleet) {
            $fleetIds[] = $fleet['uid'];
        }

        foreach($cacheFiles as $file) {
            $pieces = explode('-', $file);
            if(!in_array($pieces[2], $fleetIds)) {
                unlink($this->cacheDir . '/' . $file);
            }
        }
    }

    private function createFile($gameNumber, $fleetId) {
        $fileName = "{$this->cacheDir}/{$gameNumber}-attack-{$fleetId}";
        $fp = fopen($fileName, 'w');
        if(!$fp)
            exit("Could not create cache file {$fileName}");
    }

    private function attackerFileExists($gameNumber, $fleetId) {
        return in_array("{$gameNumber}-attack-{$fleetId}", $this->cacheFiles);
    }
}
