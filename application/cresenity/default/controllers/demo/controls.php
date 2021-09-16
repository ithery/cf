<?php

class Controller_Demo_Controls extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Controls');

        $widget = $app->addWidget();
        $form = $widget->addForm();
        $form->addField()->setLabel('Text')->addControl('text-field', 'text')->setPlaceholder('Input here..');
        return $app;
    }
}
