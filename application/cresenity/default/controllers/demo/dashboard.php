<?php

class Controller_Demo_Dashboard extends \Cresenity\Demo\Controller {
    public function index() {
        return c::app()->setTitle('Dashboard');
    }
}
