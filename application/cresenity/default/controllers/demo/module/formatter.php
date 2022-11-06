<?php

class Controller_Demo_Module_Formatter extends \Cresenity\Demo\Controller {
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
        $widgetFormatCurrency = $app->addWidget()->setTitle('Format Currency')->addClass('mb-3');
        $widgetFormatCurrency->add($globalValue . ' => ' . c::formatter()->formatCurrency($globalValue));

        // format date
        $widgetFormatDate = $app->addWidget()->setTitle('Format Date')->addClass('mb-3');
        $widgetFormatDate->add($dateNow . ' => ' . c::formatter()->formatDate($dateNow, 'd/m/Y'));

        // format datetime
        $widgetFormatDateTime = $app->addWidget()->setTitle('Format Date Time')->addClass('mb-3');
        $widgetFormatDateTime->add($dateNow . ' => ' . c::formatter()->formatDatetime($dateNow, 'd/m/Y g:i A'));

        // format decimal
        $widgetFormatDecimal = $app->addWidget()->setTitle('Format Decimal')->addClass('mb-3');
        $widgetFormatDecimal->add($globalValue . ' => ' . c::formatter()->formatDecimal($globalValue, 2, ',', '.'));

        // format number
        $widgetFormatNumber = $app->addWidget()->setTitle('Format Number')->addClass('mb-3');
        $widgetFormatNumber->add($globalValue . ' => ' . c::formatter()->formatNumber($globalValue, ',', '.'));

        return $app;
    }
}
