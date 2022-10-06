<?php

class Controller_Demo_Cresjs_Progressive extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->title('Progressive');
        $app->addView('demo.page.cresjs.progressive');

        return $app;
    }
}
