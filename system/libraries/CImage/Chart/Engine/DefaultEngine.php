<?php

class CImage_Chart_Engine_DefaultEngine extends CImage_Chart_EngineAbstract {
    public function toUri() {
        $width = $this->builder->getWidth();
        $height = $this->builder->getHeight();
        $topMargin = $this->builder->getTopMargin();
        $bottomMargin = $this->builder->getBottomMargin();
        $leftMargin = $this->builder->getLeftMargin();
        $rightMargin = $this->builder->getRightMargin();
        $chart = $this->builder->getChart();
        $seriesLabels = $chart->getSeriesLabels();
        $dataLabels = $chart->getDataLabels();
        $data = CImage_Chart::createData();
        $labelSerieName = 'capp-label-' . uniqid();
        $chartLegendShow = $chart->isShowLegend() && count($seriesLabels) > 0;
        $legendPosition = $chart->getLegendPosition();

        $legendFormat = [
            'style' => CImage_Chart_Constant::LEGEND_NOBORDER,
            'mode' => $legendPosition == CChart::POSITION_LEFT | $legendPosition == CChart::POSITION_LEFT ? CImage_Chart_Constant::LEGEND_VERTICAL : CImage_Chart_Constant::LEGEND_HORIZONTAL,
            'margin' => 0,
        ];
        foreach ($chart->getValues() as $index => $value) {
            $serieName = carr::get($seriesLabels, $index, 'series-' . $index);
            $data->addPoints($value, $serieName);
            $data->setSerieWeight($serieName, 0);
            $data->setSerieTicks($serieName, 0);
        }
        if (count($dataLabels) > 0) {
            $data->addPoints($dataLabels, $labelSerieName);
            $data->setAbscissa($labelSerieName);
        }

        $image = CImage_Chart::createImage($width, $height, $data, false);
        //calculate legend position
        $legendX = 0;
        $legendY = 0;
        $legendWidth = 0;
        $legendHeight = 0;
        $graphLeft = $leftMargin;
        $graphTop = $topMargin;
        $graphRight = $width - $rightMargin;
        $graphBottom = $height - $bottomMargin;
        if ($chartLegendShow) {
            $legendPosition = $chart->getLegendPosition();
            $legendBoundaries = $image->getLegendBoundaries($legendX, $legendY, $legendFormat);
            $offsetTop = carr::get($legendBoundaries, 't') * -1;
            $offsetLeft = carr::get($legendBoundaries, 'l') * -1;
            $legendHeight = carr::get($legendBoundaries, 'b') - carr::get($legendBoundaries, 't');
            $legendWidth = carr::get($legendBoundaries, 'r') - carr::get($legendBoundaries, 'l');
            if ($legendPosition == CChart::POSITION_BOTTOM) {
                $legendY = $height - ($legendHeight + ($offsetTop));
                $legendX = ($width / 2) - ($legendWidth / 2);
                $graphBottom = $legendY - $offsetTop * 4;
            }
        }
        $fontFormat = [

        ];
        $image->setFontProperties([
            'r'=>100,
            'g'=>100,
            'b'=>100,
            'fontSize'=>8,
        ]);
        $image->setGraphArea($graphLeft, $graphTop, $graphRight, $graphBottom);
        $scaleOptions = [
            'drawSubTicks' => true,
            'mode'=>CImage_Chart_Constant::SCALE_MODE_FLOATING,
            'xReleasePercent'=>0,
            //'yMargin'=>CImage_Chart_Constant::AUTO,
        ];
        $image->drawScale($scaleOptions);

        if ($chartLegendShow) {
            $image->drawLegend($legendX, $legendY, $legendFormat);
        }

        if ($chart instanceof CChart_Chart_BarChart) {
        } elseif ($chart instanceof CChart_Chart_PieChart) {
        } elseif ($chart instanceof CChart_Chart_LineChart) {
            $image->drawLineChart(['displayValues'=>false]);
        }

        return $image->toDataURI();
    }
}
