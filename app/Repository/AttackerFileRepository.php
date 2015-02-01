<?php namespace SpaceNorad\Repository;

class AttackerFileRepository {
    private $dirList;
    private $parentDir = __DIR__;

    function __construct() {
    }

    public function findNewAttackers($attackers) {
    }

    private function getFileList() {
        $currentAttackers = [];
        $dirList = scandir($cacheDir);
    }
}
