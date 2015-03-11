<?php namespace SpaceNorad\Controller;

use \SpaceNorad\Model\Player;
use \SpaceNorad\Model\Report;
use \SpaceNorad\Repository\GameRepository;

class ResearchController {
    private $twig;

    function __construct($twig) {
        $this->twig = $twig;
    }

    public function researchMonitorAction($api, $config) {
        if($api->login($config["username"], $config["password"])) {
            $player = Player::Parse($api->getPlayerInfo()[1]);

            $gameRepository = new GameRepository();
            $gameRepository->Parse($player->getGames());

            $gameResearch = [];
            foreach($gameRepository->getGames() as $game) {
                $response = $api->getGameReport($game->getNumber());
                $report = Report::Parse($response['report']);
                $game->setReport($report);

                $research = [];
                $research['gameName'] = $game->get('name');
                $research['gameNumber'] = $game->get('number');

                $playerStars = [];
                foreach($report->stars as $star) {
                    $starPUID = $star['puid'];
                    if(isset($playerStars[$starPUID]))
                        $playerStars[$starPUID]++;
                    else
                        $playerStars[$starPUID] = 1;
                }

                $research['starCount'] = $playerStars;
            }
        } else {
            echo "Couldn't login";
        }
    }
}
