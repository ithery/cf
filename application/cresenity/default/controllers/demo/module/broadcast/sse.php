<?php

class Controller_Demo_Module_Broadcast_Sse extends \Cresenity\Demo\Controller {
    protected $bot;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = c::app();
        $app->setTitle('SSE');

        $app->addView('demo.page.module.broadcast.sse');

        return $app;
    }

    public function send() {
        CBroadcast::manager()->driver('sse')->broadcast(['event-stream'], 'NewEvent', [
            'message' => 'Hello World',
        ]);

        return c::response()->json([
            'status' => 'ok',
        ]);
    }
}
