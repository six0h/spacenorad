<?php namespace SpaceNorad\Service\Mail;

class Message {

    public static function create($subject, $body, $from, $to) {
        $message = \Swift_Message::newInstance($subject);
        if(!isset($from) || !isset($to))
            exit("Please provide 'from' and 'to' addresses in your mail message.");

        $message->setFrom($from)
            ->setTo($to)
            ->setBody($body);

        return $message;
    }

}
