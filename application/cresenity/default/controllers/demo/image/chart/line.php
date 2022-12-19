<?php

class Controller_Demo_Image_Chart_Line extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Image Line Chart');

        $lineChart = CChart::lineChart();
        $lineChart->addSeries([100, 200, 400, 500, 300, 600], 'Item 1');
        $lineChart->addSeries([400, 500, 800, 300, 500, 100], 'Item 2');

        $lineChart->setDataLabels(['A', 'B', 'C', 'D', 'E', 'F']);
        $lineChart->setColors(['#ff0000', '#00ff00']);

        $imageChart = CImage::chart(500, 200);
        $imageChart->setChart($lineChart);

        $app->addH5()->add('With Google Engine');

        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri);

        return $app;
    }
}
