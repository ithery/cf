<?php

class CImage_Chart_Engine_GoogleEngine extends CImage_Chart_EngineAbstract {
    public function toUri() {
        $chart = $this->builder->getChart();
        if ($chart instanceof CChart_Chart_PieChart) {
            $googleChart = new CImage_GoogleChart_PieChart($this->builder->getWidth(), $this->builder->getHeight());
        }
        foreach ($chart->getValues() as $value) {
            $googleChart->addDataSet($value);
        }

        $googleChart->setLabels($chart->getLabels());

        return $googleChart->getUrl();
    }
}
