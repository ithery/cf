<?php

class Controller_Demo_Controls_Checkbox extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $checkOptions = [
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three'
        ];

        $app->setTitle('Checkbox');
        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Default Checkbox');
        foreach ($checkOptions as $checkboxKey => $checkboxLabel) {
            $div->addCheckboxControl('check_' . $checkboxKey)->setLabel($checkboxLabel)->setValue($checkboxKey);
        }

        $div->addH5()->add('Checkbox List');
        $div->addCheckboxListControl('check')->setList($checkOptions);

        return $app;
    }
}
