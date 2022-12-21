<?php

class Controller_Demo_Elements_Chart_Chartjs extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Chart JS');

        $app->addH5()->add('Line Chart');
        $data1 = [100, 200, 400, 500, 300, 600];
        $data2 = [400, 500, 800, 300, 500, 100];
        $data3 = [600, 400, 1000, 100, 200, 400];

        $lineChart = $app->addDiv()->addLineChart();
        $lineChart->addSeries($data1, 'Data 1');
        $lineChart->addSeries($data2, 'Data 2');
        $lineChart->addSeries($data3, 'Data 3');

        $lineChart->setDataLabels(['A', 'B', 'C', 'D', 'E', 'F']);
        $lineChart->setColors(['#ff0000', '#00ff00', '#0000ff']);
        //$lineChart->setLegendPosition(CChart::POSITION_LEFT);

        return $app;
    }
}
