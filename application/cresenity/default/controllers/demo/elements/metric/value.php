<?php

class Controller_Demo_Elements_Metric_Value extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $divRow = $app->addDiv()->addClass('row');
        $divCol = $divRow->addDiv()->addClass('col-md-3');

        $valueMetric = $divCol->addValueMetric()->setIcon('ti ti-layers');
        $valueMetric->setLabel('Country')->setValue(\Cresenity\Demo\Model\Country::count());

        $divCol = $divRow->addDiv()->addClass('col-md-3');

        $valueMetric = $divCol->addValueMetric()->setIcon('ti ti-user');
        $valueMetric->setLabel('ISG Avg')->setValue(\Cresenity\Demo\Model\Country::avg('isg'));

        return $app;
    }
}
