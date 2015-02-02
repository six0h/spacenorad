<?php namespace SpaceNorad;

use SpaceNorad\Config;
use SpaceNorad\Request\NeptuneApi;
use SpaceNorad\Request\CurlRequest;
use SpaceNorad\Repository\GameRepository;
use SpaceNorad\Repository\AttackerFileRepository;
use SpaceNorad\Model\Game;
use SpaceNorad\Model\Player;
use SpaceNorad\Model\Report;

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
                $this->myStars = $game->findMyStars($report->getStars(), $report->getPlayerId());
                $this->enemies = $game->findEnemies($report->getFleets(), $report->getPlayerId());
                $this->attackers = $game->findAttackers($this->enemies, $this->myStars, $report->getPlayerId());

        //        $attackerRecon = new AttackerFileRepository($game);
        //        $attackerRecon->findNewAttackers($this->attackers);

                if(count($this->attackers) > 0 && $this->sendAttackerEmail($this->attackers, $this->myStars, $this->config))
                    exit(0);
            }
        } else {
            echo "Couldn't login";
        }
    }

    private function sendAttackerEmail($attackers, $stars, $config) {
        if(count($attackers) > 0) {
            $text = $this->generateAttackerText($attackers, $stars);
            $message = $this->createEmailMessage("You're being attacked", $text, $config);

            $transport = $this->createSmtpTransportFromConfig($config);
            $mailer = $this->createMailer($transport);
            $numSent = $mailer->send($message);
            if($numSent > 0)
                return true;

            return false;
        }
    }

    private function createMailer($transport) {
        return \Swift_Mailer::newInstance($transport);
    }

    private function createEmailMessage($subject, $body, $config) {
        $message = \Swift_Message::newInstance("You're being attacked");
        if(!isset($config['mailFrom']) || !isset($config['mailTo']))
            exit("Please fill out your smtp config in config.json");

        $message->setFrom($config['mailFrom'])
            ->setTo($config['mailTo'])
            ->setBody($body);

        return $message;
    }

    private function createSmtpTransportFromConfig($config) {
        $transport = \Swift_SmtpTransport::newInstance($config['smtpAddress'], $config['smtpPort'], $config['smtpEncryption'])
            ->setUsername($config['smtpUser'])
            ->setPassword($config['smtpPass']);

        return $transport;
    }

    private function generateAttackerText($attackers, $stars) {
        $text = "You're being attacked" . PHP_EOL;
        foreach($attackers as $attacker) {
            $text = $text . $attacker['n'] . "-" . $attacker['st'] . " - " . $stars[$attacker['o'][0][1]]['n'] . PHP_EOL;
        }

        return $text;
    }
}
