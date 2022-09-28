<?php

class CImage_GoogleChart_LineChart extends CImage_GoogleChart_Chart {
    public function __construct($width = 200, $height = 200) {
        $this->setProperty('cht', 'lc');
        $this->setDimensions($width, $height);
    }

    public function getUrl() {
        $retStr = parent::getUrl();

        return $retStr;
    }
}
