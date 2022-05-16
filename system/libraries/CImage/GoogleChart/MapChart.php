<?php

class CImage_GoogleChart_MapChart extends CImage_GoogleChart_Chart {
    /**
     * Map Chart constructor.
     *
     * Maximum size for a map is 440x220, this is the defaul size.
     *
     * @param mixed $width
     * @param mixed $height
     */
    public function __construct($width = 440, $height = 220) {
        $this->setDimensions($width, $height);
        $this->setProperty('cht', 't');
    }

    /**
     * Geographical area shown in the chart.
     *
     * @param string $zoomArea One of the following values: africa, asia, europe, middle_east, south_america, usa, world
     */
    public function setZoomArea($zoomArea) {
        $this->setProperty('chtm', $zoomArea);
    }

    /**
     * A list of countries or states to which you are applying values.
     *
     * @param array $stateCodes These are a set of two-character codes. Use either of the following types (you cannot mix types):
     *                          ISO 3166-1-alpha-2 codes for countries, {@link http://www.iso.org/iso/english_country_names_and_code_elements}
     *                          USA state code
     */
    public function setStateCodes($stateCodes) {
        $this->setProperty('chld', $this->encodeData($stateCodes, ''));
    }

    /**
     * Specifies the colors of the chart
     *
     * @param string $defaultColor   The color of regions that do not have data assigned. An RRGGBB format
     *                               hexadecimal number. Suggested value is BEBEBE (medium gray).
     * @param array  $gradientColors Optional. The colors corresponding to the gradient values in the data
     *                               format range. RRGGBB format hexadecimal numbers. The default values are
     *                               0000FF and FF0000.
     */
    public function setColors($defaultColor, $gradientColors = ['0000FF', 'FF0000']) {
        $gradientColors = (func_num_args() > 1) ? func_get_arg(1) : ['0000FF', 'FF0000'];
        $this->setProperty('chco', $this->encodeData(array_merge([$defaultColor], $gradientColors), ','));
    }

    public function getApplicableLabels($labels) {
        return array_splice($labels, 0, count($this->values[0]));
    }
}
