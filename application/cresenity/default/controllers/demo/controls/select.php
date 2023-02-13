<?php

class Controller_Demo_Controls_Select extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $selectOptions = [
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three'
        ];

        $app->setTitle('Select');
        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Default Select');
        $div->addSelectControl()->setList($selectOptions);

        return $app;
    }
}
