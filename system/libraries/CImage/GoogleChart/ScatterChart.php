<?php

class CImage_GoogleChart_ScatterChart extends CImage_GoogleChart_Chart {
    public function __construct($width = 200, $height = 200) {
        $this->setDimensions($width, $height);
        $this->setProperty('cht', 's');
    }

    /**
     * Returns the applicable labels.
     *
     * There is no reason to use this function. Please refer to the documentation to know how to use colors and legend.
     *
     * @param mixed $labels
     */
    public function getApplicableLabels($labels) {
        return $labels;
    }

    /**
     * Sets the colors for the chart.
     *
     * It has a different separator than the one in the parent class
     *
     * @param mixed $colors
     */
    public function setColors($colors) {
        $this->setProperty('chco', $this->encodeData($this->getApplicableLabels($colors), '|'));
    }
}
