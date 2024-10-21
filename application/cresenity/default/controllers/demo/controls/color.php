<?php

class Controller_Demo_Controls_Color extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Color Picker');
        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Mini Color Picker');
        $div->addMiniColorControl();

        return $app;
    }
}
