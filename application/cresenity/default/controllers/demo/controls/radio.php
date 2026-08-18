<?php

class Controller_Demo_Controls_Radio extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $checkOptions = [
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three'
        ];

        $app->setTitle('Radio');
        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Default Radio');
        foreach ($checkOptions as $checkboxKey => $checkboxLabel) {
            $div->addRadioControl()->setName('my-radio')->setLabel($checkboxLabel)->setValue($checkboxKey);
        }

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Radio List');
        $div->addRadioListControl()->setName('my-radio-list')->setList($checkOptions);

        // $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        // $div->addH5()->add('Radio Buttons');
        // $div->addRadioListControl()->setName('my-radio-list')->setList($checkOptions);

        return $app;
    }
}
