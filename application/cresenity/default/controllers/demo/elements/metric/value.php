<?php

class Controller_Demo_Elements_Metric_Value extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $divRow = $app->addDiv()->addClass('row');
        $divCol = $divRow->addClass('col-md-4');
        $divCol->addValueMetric();

        return $app;
    }
}
