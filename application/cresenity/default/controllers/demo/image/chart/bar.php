<?php

class Controller_Demo_Image_Chart_Bar extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Image Bar Chart');

        $barChart = CChart::barChart();
        $barChart->addSeries([100, 200, 400, 500, 300, 600], 'Item 1');
        $barChart->addSeries([400, 500, 800, 300, 500, 100], 'Item 2');
        $barChart->setColors(['#ff0000', '#00ff00']);
        $barChart->setDataLabels(['Bar1', 'Bar2', 'Bar3', 'Bar4', 'Bar5', 'Bar6']);
        $barChart->setTitle('Vertical Bar');
        $imageChart = CImage::chart(500, 200);
        $imageChart->setChart($barChart);
        $app->addH5()->add('Default Engine');
        $imageChart->setEngine('default');
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri)->addClass('mb-3');

        $app->addH5()->add('With Google Engine');
        $imageChart->setEngine('google');
        $barChart->setDirection(CChart::DIRECTION_VERTICAL);
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri)->addClass('mb-3');
        $app->addBr();
        $app->addBr();
        $barChart->setDirection(CChart::DIRECTION_HORIZONTAL);
        $imageChart->setSize('500', '500');
        $barChart->setTitle('Horizontal Bar');
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri)->addClass('mb-3');

        return $app;
    }
}
