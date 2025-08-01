<?php

class Controller_Demo_Cresjs_Confirm extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->title('Confirm');

        $app->addAction()->setLabel('Test Confirm')->addClass('btn btn-primary')->setConfirm();
        $app->addView('demo/page/cresjs/confirm');

        return $app;
    }
}
