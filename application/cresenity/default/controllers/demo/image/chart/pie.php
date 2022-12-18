<?php

class Controller_Demo_Image_Chart_Pie extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Image Pie Chart');

        $pieChart = CChart::pieChart();
        $pieChart->addData('A', 500);
        $pieChart->addData('B', 200);
        $pieChart->addData('C', 300);
        $pieChart->addData('D', 100);

        $imageChart = CImage::chart(500, 200);
        $imageChart->setChart($pieChart);
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri);

        return $app;
    }
}
