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
use SpaceNorad\Controller\AttackerController;
use SpaceNorad\Controller\ResearchController;

class CliApp {

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
        $controller = new ResearchController($this->loadTwig());
        $controller->researchMonitorAction($this->api, $this->config);
//        $controller = new AttackerController();
//        $controller->attackerReportAction($this->api, $this->config);
    }

    public function loadTwig() {
        $path = dirname(__FILE__);
        $loader = new \Twig_Loader_Filesystem("{$path}/Template/");
        $twig = new \Twig_Environment($loader);
        return $twig;
    }
}
