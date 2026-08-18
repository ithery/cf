<?php

class Controller_Demo_Controls_Autonumeric extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Simple Auto Numeric');
        $div->addAutoNumericControl()->setPlaceholder('Input numeric');

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Auto Numeric with 2 digit decimal');
        $div->addAutoNumericControl()->setDecimalDigit(2);

        return $app;
    }
}
