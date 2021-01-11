<?php
class CImage_GoogleChart_MeterChart extends CImage_GoogleChart_Chart {
    /**
     * Google-o-Meter Chart constructor.
     *
     * Please see documentation for specia usage of functions setVisibleAxes(), addAxisLabel(), and setColors().
     *
     * @param mixed $width
     * @param mixed $height
     */
    public function __construct($width = 200, $height = 200) {
        $this->setDimensions($width, $height);
        $this->setProperty('cht', 'gom');
    }

    public function getApplicableLabels($labels) {
        return array_splice($labels, 0, count($this->values[0]));
    }

    /**
     * Sets the labels for each arrow
     *
     * You can obtain the same result of this function by setting visible axis x and adding the labels on that axis.
     *
     * @param mixed $labels
     */
    public function setLabels($labels) {
        $this->setProperty('chl', urlencode($this->encodeData($this->getApplicableLabels($labels), '|')));
    }
}
