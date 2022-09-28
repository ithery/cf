<?php

class Controller_Demo_Controls_Image extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Image');
        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Normal Image Input');
        $div->addImageControl();

        return $app;
    }
}
