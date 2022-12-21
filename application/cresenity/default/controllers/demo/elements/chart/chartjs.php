<?php

class Controller_Demo_Elements_Chart_Chartjs extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Chart JS');

        $app->addH5()->add('Line Chart');
        $app->addDiv()->addChart();

        return $app;
    }
}
