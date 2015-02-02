<?php namespace SpaceNorad;

use SpaceNorad\Config;
use SpaceNorad\Request\NeptuneApi;
use SpaceNorad\Request\CurlRequest;
use SpaceNorad\Repository\GameRepository;
use SpaceNorad\Repository\AttackerFileRepository;
use SpaceNorad\Model\Game;
use SpaceNorad\Model\Player;
use SpaceNorad\Model\Report;
use SpaceNorad\Service\Mail\Mailer;
use SpaceNorad\Service\Mail\Message;

class App {

    private $config, // Config
            $request, // CurlRequest
            $api, // NeptuneApi
            $myStars,
            $enemies,
            $attackers;

    function __construct(Config $config, CurlRequest $request, NeptuneApi $api) {
        $this->config = $config;
        $this->request = $request;
        $this->api = $api;
    }

    public function start() {
        if($this->api->login($this->config["username"], $this->config["password"])) {
            $player = Player::Parse($this->api->getPlayerInfo()[1]);

            $gameRepository = new GameRepository();
            $gameRepository->Parse($player->getGames());

            foreach($gameRepository->getGames() as $game) {
                $response = $this->api->getGameReport($game->getNumber());
                $report = Report::Parse($response['report']);
                $game->setReport($report);

                $myStars = $game->findMyStars($report->getStars(), $report->getPlayerId());
                $enemies = $game->findEnemies($report->getFleets(), $report->getPlayerId());
                $attackers = $game->findAttackers($enemies, $myStars, $report->getPlayerId());

                $attackerRecon = new AttackerFileRepository($game);
                $newAttackers = $attackerRecon->findNewAttackers($attackers);

                if(!$this->sendAttackerEmail($newAttackers, $myStars, $this->config))
                    exit("Could not send email");
            }
        } else {
            echo "Couldn't login";
        }
    }

    private function sendAttackerEmail($attackers, $stars, $config) {
        if(count($attackers) > 0) {
            $attackerMessage = $this->generateAttackerText($attackers, $stars);
            $message = Message::create("You're being attacked!", $attackerMessage, $config['mailFrom'], $config['mailTo']);
            $mailer = new Mailer($config);
            $numSent = $mailer->send($message);
            if($numSent > 0)
                return true;

            return false;
        }
    }

    private function generateAttackerText($attackers, $stars) {
        $text = "You're being attacked" . PHP_EOL;
        foreach($attackers as $attacker) {
            $text = $text . $attacker['n'] . "-" . $attacker['st'] . " - " . $stars[$attacker['o'][0][1]]['n'] . PHP_EOL;
        }

        return $text;
    }
}
