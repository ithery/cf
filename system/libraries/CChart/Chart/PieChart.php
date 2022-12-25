<?php

class CChart_Chart_PieChart extends CChart_ChartAbstract implements CChart_Contract_ChartHave3D {
    protected $is3D = false;

    public function is3D() {
        return $this->is3D;
    }

    public function make3D() {
        $this->is3D = true;

        return $this;
    }

    public function remove3D() {
        $this->is3D = false;

        return $this;
    }
}
