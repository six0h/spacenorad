<?php namespace SpaceNorad\Repository;

use \SpaceNorad\Model\Game;

class GameRepository implements \ArrayAccess {
    private $games = [];

    public function Parse($games) {
        foreach($games as $game) {
            $this->games[] = Game::Parse($game);
        }
    }

    public function getGames() {
        return $this->games;
    }

    public function offsetSet($offset, $value) {
        $this->games[$offset] = $value;
    }

    public function offsetGet($offset) {
        return $this->games[$offset];
    }

    public function offsetUnset($offset) {
        unset($this->games[$offset]);
    }

    public function offsetExists($offset) {
        return isset($this->game[$offset]);
    }

}
