<?php namespace SpaceNorad\Controller;

use \SpaceNorad\Model\Player;
use \SpaceNorad\Model\Report;
use SpaceNorad\Service\Mail\Mailer;
use SpaceNorad\Service\Mail\Message;
use \SpaceNorad\Repository\GameRepository;
use \SpaceNorad\Repository\AttackerFileRepository;

class AttackerController {

    public function attackerReportAction($api, $config) {
        if($api->login($config["username"], $config["password"])) {
            $player = Player::Parse($api->getPlayerInfo()[1]);

            $gameRepository = new GameRepository();
            $gameRepository->Parse($player->getGames());

            foreach($gameRepository->getGames() as $game) {
                $response = $api->getGameReport($game->getNumber());
                $report = Report::Parse($response['report']);
                $game->setReport($report);

                $myStars = $game->findMyStars($report->getStars(), $report->getPlayerId());
                $enemies = $game->findEnemies($report->getFleets(), $report->getPlayerId());
                $allAttackers = $game->findAttackers($enemies, $myStars, $report->getPlayerId());

                $attackerRecon = new AttackerFileRepository($game);
                $newAttackers = $attackerRecon->clearLandedAndFindNewAttackers($allAttackers);

                $this->sendAttackerEmail($newAttackers, $myStars, $config);
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
