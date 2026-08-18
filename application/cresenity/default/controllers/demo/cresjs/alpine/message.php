<?php

class Controller_Demo_Cresjs_Alpine_Message extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Message');

        $app->addView('demo.page.cresjs.alpine.message', []);

        return $app;
    }
}
