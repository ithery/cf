<?php

class CChart_Chart_LineChart extends CChart_ChartAbstract {
    /**
     * @param string $label
     * @param array  $data
     *
     * @return $this
     */
    public function addData($label, array $data) {
        $this->labels[] = $label;
        $this->values[] = $data;
    }
}
