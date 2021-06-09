<?php

class CImage_Chart_Processor_Parameter {
    use CTrait_HasOptions;
    protected $width;
    protected $height;
    protected $haveLabel;
    protected $dataLabel;
    protected $defaultDataLabel;
    protected $dataSeries;

    public function __construct($options) {
        $this->options = $options;
        $this->dataSeries = [];
        $this->parseSize();
    }

    protected function parseData() {
        $chartData = $this->getOption('chd', '');
        if (cstr::startsWith($chartData, 't:')) {
            $chartData = substr($chartData, 2);
            $chartDataArray = explode('|', $chartData);
            foreach ($chartDataArray as $chda) {
                $seriesArray = explode(',', $chda);
                if ($this->defaultDataLabel === null) {
                    $this->defaultDataLabel = $seriesArray;
                }
                $this->dataSeries[] = $seriesArray;
            }
        }
    }

    protected function parseLabel() {
    }

    protected function parseSize() {
        $chartSize = $this->getOption('chs', '250x250');
        $chartSizeArray = explode('x', $chartSize);
        $chartWidth = carr::get($chartSizeArray, 0, '250');
        $chartHeight = carr::get($chartSizeArray, 1, '250');
        if (!is_numeric($chartWidth)) {
            $chartWidth = '250';
        }
        if (!is_numeric($chartHeight)) {
            $chartHeight = '250';
        }
        $this->width = $chartWidth;
        $this->height = $chartWidth;
    }
}
