<?php

class CChart_Chart_LineChart extends CChart_ChartAbstract implements CChart_Contract_ChartHaveAxis {
    private $xAxis = true;

    private $yAxis = true;

    public function setXAxis($option) {
        $this->xAxis = $option;
    }

    public function setYAxis($option) {
        $this->yAxis = $option;
    }

    public function getXAxis() {
        return $this->xAxis;
    }

    public function getYAxis() {
        return $this->yAxis;
    }
}
