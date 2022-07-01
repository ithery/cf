<?php

class Controller_Demo_Elements_Table extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Table');

        return $app;
    }
}
