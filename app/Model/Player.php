<?php namespace SpaceNorad\Model;

class Player {
    private $subscribed_until,
            $games_in,
            $complete_games,
            $user_email,
            $games_third,
            $badges,
            $owned_games,
            $allow_email,
            $user_id,
            $games_won,
            $games_complete,
            $nagged,
            $logout_url,
            $score,
            $open_games,
            $avatar,
            $credits,
            $galactic_news,
            $created,
            $email_verified,
            $local_account,
            $alias,
            $karma,
            $games_second,
            $dollars_paid,
            $referring_campaign;

    public static function Parse($playerInfo) {
        $player = new Player();
        foreach($playerInfo as $k => $v) {
            $player->$k = $v;
        }

        return $player;
    }

    public function get($k) {
        return $this->$k;
    }

    public function getGames() {
        return $this->open_games;
    }

}
