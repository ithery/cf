<?php

class Controller_Demo_Cresjs_Style extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->title('Cres Style');
        //force to true
        CConfig::repository()->set('cresjs.style.enable', true);
        $app->addView('demo/page/cresjs/style');

        return $app;
    }
}
