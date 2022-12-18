<?php

class Controller_Demo_Image_Chart_Line extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Image Line Chart');

        $lineChart = CChart::lineChart();
        $lineChart->addData('A', [100, 200, 400, 500, 300, 600]);
        $lineChart->addData('B', [400, 500, 800, 300, 500, 100]);

        $imageChart = CImage::chart(500, 200);
        $imageChart->setChart($lineChart);
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri);

        return $app;
    }
}
