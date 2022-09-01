<?php
use React\EventLoop\Factory;

class Controller_Demo_Module_Bot extends \Cresenity\Demo\Controller {
    /**
     * @var CBot_Bot
     */
    protected $bot;

    public function __construct() {
        parent::__construct();
        $this->bot = CBot::createBot([
            'driver' => 'web',
            'bot_id' => 'web'
        ]);
    }

    public function index() {
        $app = c::app();
        $app->setTitle('Chat Bot');

        $app->addView('demo.page.module.bot');

        return $app;
    }

    public function frame() {
        return c::view('demo.page.module.bot.frame');
    }

    public function api() {
        $this->bot->hears('{message}', function ($botman, $message) {
            if ($message == 'Hi') {
                $this->askName($botman);
            } else {
                $botman->reply("Write 'Hi' for testing...");
            }
        });

        $this->bot->listen();
    }

    public function askName(CBot_Bot $botman) {
        $botman->ask('Hello! What is your Name?', function (CBot_Message_Incoming_Answer $answer) {
            /** @var CBot_Message_Conversation_InlineConversation $this */
            $name = $answer->getText();

            $this->say('Nice to meet you ' . $name);
        });
    }
}
