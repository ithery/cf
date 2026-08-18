<?php

class Controller_Demo_Module_Geo extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = c::app();

        $app->title('Geo IP Address');

        $code = 'CGeo::ip()->getClientIP();';
        $app->addDiv()->addClass('my-2 console')->add($code);

        $result = CGeo::ip()->getClientIP();
        $app->addDiv()->addClass('my-2 json-container')->add($result);

        $code = 'CGeo::ip()->getLocation(CGeo::ip()->getClientIP());';
        $app->addDiv()->addClass('my-2 console')->add($code);

        $result = CGeo::ip()->getLocation();
        $app->addDiv()->addClass('my-2 json-container')->add($result);

        return $app;
    }
}
