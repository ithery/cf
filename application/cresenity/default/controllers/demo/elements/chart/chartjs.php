<?php

class Controller_Demo_Elements_Chart_Chartjs extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Chart JS');

        $row = $app->addDiv()->addClass('row');

        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Line Chart');
        $lineChart = CChart::lineChart();
        $lineChart->addSeries([100, 200, 400, 500, 300, 600], 'Item 1');
        $lineChart->addSeries([400, 500, 800, 300, 500, 100], 'Item 2');
        $lineChart->addSeries([600, 400, 1000, 100, 200, 400], 'Item 3');
        $lineChart->setDataLabels(['A', 'B', 'C', 'D', 'E', 'F']);
        $lineChart->setColors(['#f87171', '#6ee7b7', '#93c5fd']);
        $lineChart->setLegendPosition(CChart::POSITION_LEFT);
        $widget->addChart()->setChart($lineChart);

        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Bar Chart');
        $barChart = CChart::barChart();
        $barChart->addSeries([100, 200, 400, 500, 300, 600], 'Item 1');
        $barChart->addSeries([400, 500, 800, 300, 500, 100], 'Item 2');
        $barChart->setColors(['#fca5a5', '#86efac']);
        $barChart->setDataLabels(['Bar1', 'Bar2', 'Bar3', 'Bar4', 'Bar5', 'Bar6']);
        $barChart->setTitle('Vertical Bar');
        $widget->addChart()->setChart($barChart);

        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Pie Chart');
        $pieChart = CChart::pieChart();
        $pieChart->addSeries([500, 200, 300, 100]);
        $pieChart->setDataLabels(['A', 'B', 'C', 'D']);
        $pieChart->setColors(['#fca5a5', '#86efac', '#93c5fd', '#d8b4fe']);
        $pieChart->setTitle('Regular Pie');
        $widget->addChart()->setChart($pieChart);

        return $app;
    }
}
