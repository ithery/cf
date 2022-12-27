<?php

class Controller_Demo_Module_Transform extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = c::app();
        $app->title('Formatter');

        // initilize variable
        $globalValue = 10000;
        $dateNow = CCarbon::now();

        $app->addP()->add('Demo Formatter with value ' . $globalValue . ' and datetime is now');

        // format currency
        $widgetFormatCurrency = $app->addWidget()->setTitle('Transform Format Currency')->addClass('mb-3');
        $widgetFormatCurrency->add($globalValue . ' => ' . c::manager()->transform()->call('formatCurrency', $globalValue));

        // format distance
        $widgetFormatDistance = $app->addWidget()->setTitle('Transform Format Distance')->addClass('mb-3');
        $widgetFormatDistance->add($globalValue . ' => ' . c::manager()->transform()->call('formatDistance', $globalValue));

        // format bytes
        $widgetFormatByte = $app->addWidget()->setTitle('Transform Format Byte')->addClass('mb-3');
        $widgetFormatByte->add($globalValue . ' => ' . c::manager()->transform()->call('formatByte', $globalValue));

        return $app;
    }
}
