<?php
use React\EventLoop\Factory;

class Controller_Demo_Module_Bot extends \Cresenity\Demo\Controller {
    public function index() {
        $config = [
            'bot_id' => uniqid(),
        ];
        $bot = CBot::createBot($config);

        $bot->hears('Hello CApp Bot!', function ($bot) {
            $bot->reply('Hello!');
            $bot->ask('Whats your name?', function ($answer, $bot) {
                $bot->say('Welcome ' . $answer->getText());
            });
        });

        $bot->listen();
    }

    public function discord() {
        $loop = Factory::create();

        $bot = CBot::createForDiscord([
            'discord' => [
                'token' => 'ODg2NjY3Njg3MDgwNjMyMzgx.YT47og.RWbJwD4zwSYnaWEkm0ZsbT_sIQk',
            ]
        ], $loop);

        $bot->hears('hello', function ($bot) {
            $bot->reply('Hi there!');
        });

        $loop->run();
    }
}
