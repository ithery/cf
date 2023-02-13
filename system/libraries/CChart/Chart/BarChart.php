<?php

class CChart_Chart_BarChart extends CChart_Chart_LineChart implements CChart_Contract_ChartHaveDirection {
    protected $direction = CChart::DIRECTION_VERTICAL;

    public function setDirection($direction) {
        $this->direction = $direction;

        return $this;
    }

    public function getDirection() {
        return $this->direction;
    }
}
