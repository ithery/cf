<?php

class CImage_Chart_Engine_GoogleEngine extends CImage_Chart_EngineAbstract {
    use CImage_Chart_Trait_UseColorTrait;

    public function toUri() {
        $chart = $this->builder->getChart();
        $googleChart = null;
        if ($chart instanceof CChart_Chart_BarChart) {
            $googleChart = new CImage_GoogleChart_BarChart($this->builder->getWidth(), $this->builder->getHeight());
        // $googleChart->setProperty('chxr', '0,0,600,100|1,0,600,100');
            // $googleChart->setProperty('chxt', 'y');
            // $googleChart->setProperty('chm', 'N,000000,0,,10|N,000000,1,,10');
            // $googleChart->setProperty('chds', '0,600');
        } elseif ($chart instanceof CChart_Chart_PieChart) {
            $googleChart = new CImage_GoogleChart_PieChart($this->builder->getWidth(), $this->builder->getHeight());
        } elseif ($chart instanceof CChart_Chart_LineChart) {
            $googleChart = new CImage_GoogleChart_LineChart($this->builder->getWidth(), $this->builder->getHeight());
            // $googleChart->setProperty('chxr', '0,0,600,100|1,0,600,100');
            // $googleChart->setProperty('chxt', 'y');
            // $googleChart->setProperty('chm', 'N,000000,0,,10|N,000000,1,,10');
            // $googleChart->setProperty('chds', '0,600');
        }
        foreach ($chart->getValues() as $value) {
            $googleChart->addDataSet($value);
        }
        $googleChart->setLabels($chart->getDataLabels());
        $seriesLabels = $chart->getSeriesLabels();
        if (count(array_filter($seriesLabels)) == 0) {
            $seriesLabels = [];
        }
        if (count($seriesLabels) > 0) {
            $googleChart->setLegend($seriesLabels);
        }

        $colors = c::collect($chart->getColors())->map(function ($color) {
            return $this->toRgba($color);
        })->all();

        $googleChart->setColors($colors);

        return $googleChart->getUrl();
    }
}
