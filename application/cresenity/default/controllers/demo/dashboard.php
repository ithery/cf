<?php

class Controller_Demo_Dashboard extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Dashboard');


        return $app;
    }
}
