<?php

class CImage_GoogleChart_Pie3DChart extends CImage_GoogleChart_PieChart {
    public function __construct($width = 500, $height = 200) {
        $this->setProperty('cht', 'p3');
        $this->setDimensions($width, $height);
    }
}
