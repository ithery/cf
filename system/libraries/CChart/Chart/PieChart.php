<?php

class CChart_Chart_PieChart extends CChart_ChartAbstract {
    /**
     * @param string $label
     * @param int    $data
     *
     * @return $this
     */
    public function addData($label, $data) {
        $this->labels[] = $label;
        if (count($this->values) == 0) {
            $this->values[] = [];
        }
        $this->values[0][] = $data;
    }
}
