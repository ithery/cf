<?php

class Controller_Demo_Controls_Text extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Text');
        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Normal Text Input');
        $div->addTextControl();

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Text Input With Placeholder');
        $div->addTextControl()->setPlaceholder('My Placeholder');

        return $app;
    }
}
