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

    public function clearLandedAndFindNewAttackers($fleets) {
        $this->clearLandedAttackers($fleets);
        return $this->findNewAttackers($fleets);
    }

    private function findNewAttackers($fleets) {
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

    private function clearLandedAttackers($fleets) {
        $gameNumber = $this->game->getNumber();
        $fleetIds = [];
        foreach($fleets as $fleet) {
            $fleetIds[] = $fleet['uid'];
        }

        foreach($this->cacheFiles as $file) {
            $pieces = explode('-', $file);
            if(isset($pieces[2])) {
                if(!in_array($file, $fleetIds)) {
                    unlink($this->cacheDir . '/' . $file);
                }
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
