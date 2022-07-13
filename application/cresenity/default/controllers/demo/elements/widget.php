<?php

class Controller_Demo_Elements_Widget extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Widget');
        $app->addWidget()->setTitle('Widget Title');

        return $app;
    }
}
