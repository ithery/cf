<?php

class Controller_Demo_Image_Chart_Pie extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Image Pie Chart');

        $pieChart = CChart::pieChart();
        $pieChart->addSeries([500, 200, 300, 100]);
        $pieChart->setDataLabels(['A', 'B', 'C', 'D']);
        $pieChart->setColors(['#ff0000', '#00ff00', '#0000ff', '#ff00ff']);
        $pieChart->setTitle('Regular Pie');
        $imageChart = CImage::chart(500, 200);
        $imageChart->setChart($pieChart);
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri);

        $app->addBr();
        $app->addBr();
        $pieChart->make3D();
        $pieChart->setTitle('3D Pie');
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri);

        return $app;
    }
}
