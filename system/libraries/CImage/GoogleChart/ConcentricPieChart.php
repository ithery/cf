<?php

class CImage_GoogleChart_ConcentricPieChart extends CImage_GoogleChart_PieChart {
    public function __construct($width = 350, $height = 200) {
        $this->setProperty('cht', 'pc');
        $this->setDimensions($width, $height);
    }

    /**
     * Returns the applicable labels for the chart.
     *
     * This function counts recursively the numeber of values in the $values array.
     *
     * @param mixed $labels
     *
     * @return array Applicable labels
     */
    public function getApplicableLabels($labels) {
        return array_splice($labels, 0, count($this->values, COUNT_RECURSIVE));
    }

    /**
     * Adds the legend for Concentric Pie Charts.
     *
     * Run an instance of this function for each data set.
     *
     * @param array $labels
     */
    public function addLegend($labels) {
        $this->setProperty('chdl', urlencode($this->encodeData($this->getApplicableLabels($labels), '|')), true);
    }
}
