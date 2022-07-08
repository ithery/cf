<?php

class Controller_Demo_Module_Bot extends \Cresenity\Demo\Controller {
    public function index() {
        $bot = CBot::createBot();

        $bot->hears('Hello BotMan!', function ($bot) {
            $bot->reply('Hello!');
            $bot->ask('Whats your name?', function ($answer, $bot) {
                $bot->say('Welcome ' . $answer->getText());
            });
        });

        $bot->listen();
    }
}
