<?php

class CImage_Chart_Engine_GoogleEngine extends CImage_Chart_EngineAbstract {
    public function toUri() {
        $chart = $this->builder->getChart();
        $googleChart = null;
        if ($chart instanceof CChart_Chart_BarChart) {
            $googleChart = new CImage_GoogleChart_BarChart($this->builder->getWidth(), $this->builder->getHeight());
        } elseif ($chart instanceof CChart_Chart_PieChart) {
            $googleChart = new CImage_GoogleChart_PieChart($this->builder->getWidth(), $this->builder->getHeight());
        } elseif ($chart instanceof CChart_Chart_LineChart) {
            $googleChart = new CImage_GoogleChart_LineChart($this->builder->getWidth(), $this->builder->getHeight());
        }
        foreach ($chart->getValues() as $value) {
            $googleChart->addDataSet($value);
        }
        if ($chart instanceof CChart_Chart_PieChart) {
            $googleChart->setLabels($chart->getLabels());
        } else {
            $googleChart->setLegend($chart->getLabels());
        }

        return $googleChart->getUrl();
    }
}
