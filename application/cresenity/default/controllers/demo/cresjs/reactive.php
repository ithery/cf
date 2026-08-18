<?php

class Controller_Demo_Cresjs_Reactive extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->title('Reactive');

        $app->addView('demo/page/cresjs/reactive');

        return $app;
    }
}
