<?php

class Controller_Demo_Elements_Chart_Chartist extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Chartist');

        $app->addH5()->add('Line Chart');

        $lineChart = CChart::lineChart();
        $lineChart->addSeries([100, 200, 400, 500, 300, 600], 'Item 1');
        $lineChart->addSeries([400, 500, 800, 300, 500, 100], 'Item 2');
        $lineChart->addSeries([600, 400, 1000, 100, 200, 400], 'Item 3');

        $lineChart->setDataLabels(['A', 'B', 'C', 'D', 'E', 'F']);
        $lineChart->setColors(['#ff0000', '#00ff00', '#0000ff']);
        $lineChart->setLegendPosition(CChart::POSITION_LEFT);
        $app->addDiv()->addChart('Chartist')->setChart($lineChart)
            ->addClass('mb-3');
        // $app->addH5()->add('Bar Chart');
        // $barChart = CChart::barChart();
        // $barChart->addSeries([100, 200, 400, 500, 300, 600], 'Item 1');
        // $barChart->addSeries([400, 500, 800, 300, 500, 100], 'Item 2');
        // $barChart->setColors(['#ff0000', '#00ff00']);
        // $barChart->setDataLabels(['Bar1', 'Bar2', 'Bar3', 'Bar4', 'Bar5', 'Bar6']);
        // $barChart->setTitle('Vertical Bar');
        // $app->addDiv()->addChart('Chartist')->setChart($barChart)->addClass('mb-3');

        // $app->addH5()->add('Pie Chart');

        // $pieChart = CChart::pieChart();
        // $pieChart->addSeries([500, 200, 300, 100]);
        // $pieChart->setDataLabels(['A', 'B', 'C', 'D']);
        // $pieChart->setColors(['#ff0000', '#00ff00', '#0000ff', '#ff00ff']);
        // $pieChart->setTitle('Regular Pie');
        // $app->addDiv()->addChart('Chartist')->setChart($pieChart)->addClass('mb-3');

        return $app;
    }
}
