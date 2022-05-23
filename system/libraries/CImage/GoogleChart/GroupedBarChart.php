<?php

class CImage_GoogleChart_GroupedBarChart extends CImage_GoogleChart_BarChart {
    public function __construct($width = 200, $height = 200) {
        $this->setChartType('g', 'v');
        $this->setDimensions($width, $height);
    }

    public function setHorizontal($isHorizontal = true) {
        if ($isHorizontal) {
            $this->setChartType('g', 'h');
        } else {
            $this->setChartType('g', 'v');
        }
    }
}
