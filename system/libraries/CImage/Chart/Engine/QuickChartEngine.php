<?php

class CImage_Chart_Engine_QuickChartEngine extends CImage_Chart_EngineAbstract {
    use CImage_Chart_Trait_UseColorTrait;

    public function toUri() {
        $chart = $this->builder->getChart();
        $quickChart = null;
        if ($chart instanceof CChart_Chart_BarChart) {
            $type = 'g';
            $direction = $chart->getDirection() == CChart::DIRECTION_VERTICAL ? 'v' : 'h';
            $quickChart = new CImage_QuickChart_BarChart($this->builder->getWidth(), $this->builder->getHeight(), $type, $direction);
        } elseif ($chart instanceof CChart_Chart_PieChart) {
            $quickChart = new CImage_QuickChart_PieChart($this->builder->getWidth(), $this->builder->getHeight());
        } elseif ($chart instanceof CChart_Chart_LineChart) {
            $quickChart = new CImage_QuickChart_LineChart($this->builder->getWidth(), $this->builder->getHeight());

            // $quickChart->setProperty('chm', 'N,000000,0,,10|N,000000,1,,10');
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
                $quickChart->setProperty('chxt', implode(',', $chartAxis));
            }
            $quickChart->setProperty('chds', 'a');
        }
        foreach ($chart->getValues() as $value) {
            $quickChart->addDataSet($value);
        }
        if ($chart instanceof CChart_Contract_ChartHaveDirection && $chart->getDirection() == CChart::DIRECTION_HORIZONTAL) {
            $property = '1:|' . implode('|', $chart->getDataLabels()) . '';
            $quickChart->setProperty('chxl', $property);
        } else {
            $quickChart->setLabels($chart->getDataLabels());
        }

        $seriesLabels = $chart->getSeriesLabels();
        if (count(array_filter($seriesLabels)) == 0) {
            $seriesLabels = [];
        }

        if ($chart->isShowLegend() && count($seriesLabels) > 0) {
            $legendPosition = $chart->getLegendPosition();
            $quickChart->setLegend($seriesLabels);
            $quickChart->setLegendPosition($legendPosition);
        }

        $colors = c::collect($chart->getColors())->map(function ($color) {
            return $this->toRgba($color);
        })->all();

        $quickChart->setColors($colors);

        if ($chart->getTitle()) {
            $quickChart->setTitle($chart->getTitle());
        }
        if ($chart instanceof CChart_Contract_ChartHave3D) {
            if ($chart->is3D()) {
                $quickChart->set3D(true, false);
            }
        }
        $quickChart->setChartMargins([
            $this->builder->getLeftMargin(),
            $this->builder->getRightMargin(),
            $this->builder->getTopMargin(),
            $this->builder->getBottomMargin()
        ]);

        return $quickChart->getUrl();
    }
}
