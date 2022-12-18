<?php

class Controller_Demo_Image_Chart_Bar extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Image Bar Chart');

        $barChart = CChart::barChart();
        $barChart->addData('A', [100, 200, 400, 500, 300, 600]);
        $barChart->addData('B', [400, 500, 800, 300, 500, 100]);

        $imageChart = CImage::chart(500, 200);
        $imageChart->setChart($barChart);
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri);

        return $app;
    }
}
