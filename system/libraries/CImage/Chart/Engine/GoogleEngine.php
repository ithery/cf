<?php

class CImage_Chart_Engine_GoogleEngine extends CImage_Chart_EngineAbstract {
    use CImage_Chart_Trait_UseColorTrait;

    public function toUri() {
        $chart = $this->builder->getChart();
        $googleChart = null;
        if ($chart instanceof CChart_Chart_BarChart) {
            $type = 'g';
            $direction = $chart->getDirection() == CChart::DIRECTION_VERTICAL ? 'v' : 'h';
            $googleChart = new CImage_GoogleChart_BarChart($this->builder->getWidth(), $this->builder->getHeight(), $type, $direction);
        } elseif ($chart instanceof CChart_Chart_PieChart) {
            $googleChart = new CImage_GoogleChart_PieChart($this->builder->getWidth(), $this->builder->getHeight());
        } elseif ($chart instanceof CChart_Chart_LineChart) {
            $googleChart = new CImage_GoogleChart_LineChart($this->builder->getWidth(), $this->builder->getHeight());

            // $googleChart->setProperty('chm', 'N,000000,0,,10|N,000000,1,,10');
        }

        if ($chart instanceof CChart_Contract_ChartHaveAxis) {
            $chartAxis = [];
            if ($chart->getXAxis()) {
                $chartAxis[] = 'x';
            }
            if ($chart->getYAxis()) {
                $chartAxis[] = 'y';
            }
            if (count($chartAxis) > 0) {
                $googleChart->setProperty('chxt', implode(',', $chartAxis));
            }
            $googleChart->setProperty('chds', 'a');
        }
        foreach ($chart->getValues() as $value) {
            $googleChart->addDataSet($value);
        }
        if ($chart instanceof CChart_Contract_ChartHaveDirection && $chart->getDirection() == CChart::DIRECTION_HORIZONTAL) {
            $property = '1:|' . implode('|', $chart->getDataLabels()) . '';
            $googleChart->setProperty('chxl', $property);
        } else {
            $googleChart->setLabels($chart->getDataLabels());
        }

        $seriesLabels = $chart->getSeriesLabels();
        if (count(array_filter($seriesLabels)) == 0) {
            $seriesLabels = [];
        }

        if ($chart->isShowLegend() && count($seriesLabels) > 0) {
            $legendPosition = $chart->getLegendPosition();
            $googleChart->setLegend($seriesLabels);
            $googleChart->setLegendPosition($legendPosition);
        }

        $colors = c::collect($chart->getColors())->map(function ($color) {
            return $this->toRgba($color);
        })->all();

        $googleChart->setColors($colors);

        if ($chart->getTitle()) {
            $googleChart->setTitle($chart->getTitle());
        }
        if ($chart instanceof CChart_Contract_ChartHave3D) {
            if ($chart->is3D()) {
                $googleChart->set3D(true, false);
            }
        }
        $googleChart->setChartMargins([
            $this->builder->getLeftMargin(),
            $this->builder->getRightMargin(),
            $this->builder->getTopMargin(),
            $this->builder->getBottomMargin()
        ]);

        return $googleChart->getUrl();
    }
}
