<?php

class CElement_Component_Chart_ChartAbstract {
    /**
     * @var CChart_ChartAbstract
     */
    protected $chart;

    protected $engine;

    public function __construct(CChart_ChartAbstract $chart, $engine = 'chartjs') {
        $this->chart = $chart;
        $this->engine = $engine;
    }
}
