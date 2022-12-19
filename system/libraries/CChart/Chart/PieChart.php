<?php

class CChart_Chart_PieChart extends CChart_ChartAbstract implements CChart_Contract_ChartHave3D {
    protected $is3D = false;

    public function get3D() {
        return $this->is3D;
    }

    public function set3D($is3D = true) {
        $this->is3D = $is3D;

        return $this;
    }
}
