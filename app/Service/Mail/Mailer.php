<?php namespace SpaceNorad\Service\Mail;

class Mailer {

    private $transport,
            $mailer;

    function __construct($config) {
        $this->transport = $this->createSmtpTransportFromConfig($config);
        $this->mailer = $this->createMailer($this->transport);
    }

    public function send($message) {
        return $this->mailer->send($message);
    }

    public function init($config) {
        return $mailer;
    }

    private function createMailer($transport) {
        return \Swift_Mailer::newInstance($transport);
    }

    private function createSmtpTransportFromConfig($config) {
        $transport = \Swift_SmtpTransport::newInstance($config['smtpAddress'], $config['smtpPort'], $config['smtpEncryption'])
            ->setUsername($config['smtpUser'])
            ->setPassword($config['smtpPass']);

        return $transport;
    }

}
