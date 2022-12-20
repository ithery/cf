<?php

class Controller_Demo_Image_Chart_Line extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Image Line Chart');

        $lineChart = CChart::lineChart();
        $lineChart->addSeries([100, 200, 400, 500, 300, 600], 'Item 1');
        $lineChart->addSeries([400, 500, 800, 300, 500, 100], 'Item 2');
        $lineChart->addSeries([600, 400, 1000, 100, 200, 400], 'Item 3');

        $lineChart->setDataLabels(['A', 'B', 'C', 'D', 'E', 'F']);
        $lineChart->setColors(['#ff0000', '#00ff00', '#0000ff']);
        $lineChart->setLegendPosition(CChart::POSITION_BOTTOM);
        $imageChart = CImage::chart(500, 200)->setMargin(50, 50, 50, 50);

        $imageChart->setChart($lineChart);

        $app->addH5()->add('Default Engine');
        $imageChart->setEngine('default');
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri);

        $app->addH5()->add('With Google Engine');
        $imageChart->setEngine('google');
        $uri = $imageChart->toUri();
        $app->addImg()->setSrc($uri);

        return $app;
    }
}
