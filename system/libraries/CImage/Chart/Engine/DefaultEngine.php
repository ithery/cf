<?php

class CImage_Chart_Engine_DefaultEngine extends CImage_Chart_EngineAbstract {
    use CImage_Chart_Trait_UseColorTrait;

    public function toUri() {
        $width = $this->builder->getWidth();
        $height = $this->builder->getHeight();
        $topMargin = $this->builder->getTopMargin();
        $bottomMargin = $this->builder->getBottomMargin();
        $leftMargin = $this->builder->getLeftMargin();
        $rightMargin = $this->builder->getRightMargin();
        $chart = $this->builder->getChart();
        $seriesLabels = $chart->getSeriesLabels();
        if (count(array_filter($seriesLabels)) == 0) {
            $seriesLabels = [];
        }
        $dataLabels = $chart->getDataLabels();
        $data = CImage_Chart::createData();
        $labelSerieName = 'capp-label-' . uniqid();
        $chartLegendShow = $chart->isShowLegend() && count($seriesLabels) > 0;
        $legendPosition = $chart->getLegendPosition();
        $colors = $chart->getColors();

        $legendFormat = [
            'style' => CImage_Chart_Constant::LEGEND_NOBORDER,
            'mode' => $legendPosition == CChart::POSITION_LEFT | $legendPosition == CChart::POSITION_RIGHT ? CImage_Chart_Constant::LEGEND_VERTICAL : CImage_Chart_Constant::LEGEND_HORIZONTAL,
            'margin' => 5,
        ];
        foreach ($chart->getValues() as $index => $value) {
            $serieName = carr::get($seriesLabels, $index, 'series-' . $index);

            $data->addPoints($value, $serieName);
            $data->setSerieWeight($serieName, 0);
            $data->setSerieTicks($serieName, 0);
            if ($chart instanceof CChart_Chart_PieChart) {
                foreach ($value as $valueIndex => $val) {
                    $color = carr::get($colors, $valueIndex, null);
                    if ($color) {
                        $data->palette[$valueIndex] = $this->toRgbaArray($color);
                    }
                }
            } else {
                $color = carr::get($colors, $index, null);
                if ($color) {
                    $data->setPalette([$serieName], $this->toRgbaArray($color));
                }
            }
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
        $fontFormat = [
            'r' => 100,
            'g' => 100,
            'b' => 100,
            'fontSize' => 8,
        ];
        $image->setFontProperties($fontFormat);
        $titleHeight = 0;
        $title = $chart->getTitle();
        if ($title) {
            $image->drawText($width / 2, 0, $title, ['fontSize' => 10, 'align' => CImage_Chart_Constant::TEXT_ALIGN_TOPMIDDLE]);
        }

        if (!($chart instanceof CChart_Chart_PieChart)) {
            if ($chartLegendShow) {
                $legendPosition = $chart->getLegendPosition();
                $legendBoundaries = $image->getLegendBoundaries($legendX, $legendY, $legendFormat);
                $offsetTop = carr::get($legendBoundaries, 't') * -1;
                $offsetLeft = carr::get($legendBoundaries, 'l') * -1;
                $legendHeight = carr::get($legendBoundaries, 'b') - carr::get($legendBoundaries, 't');
                $legendWidth = carr::get($legendBoundaries, 'r') - carr::get($legendBoundaries, 'l');
                if ($legendPosition == CChart::POSITION_BOTTOM) {
                    $legendY = $height - ($legendHeight + ($offsetTop));
                    $legendX = ($width / 2) - (($legendWidth / 2) - ($offsetLeft * 5));
                    $graphBottom = min($graphBottom, $legendY - $offsetTop);
                }
                if ($legendPosition == CChart::POSITION_TOP) {
                    $legendY = $offsetTop;
                    $legendX = ($width / 2) - (($legendWidth / 2) - ($offsetLeft * 5));

                    $graphTop = max($graphTop, $legendY + $legendHeight + $offsetTop);
                }
                if ($legendPosition == CChart::POSITION_RIGHT) {
                    $legendX = $width - ($legendWidth - ($offsetLeft));
                    $legendY = ($height / 2) - (($legendHeight / 2) - ($offsetTop));

                    $graphRight = min($graphRight, $legendX - $offsetLeft);
                }
                if ($legendPosition == CChart::POSITION_LEFT) {
                    $legendX = $offsetLeft;
                    $legendY = ($height / 2) - (($legendHeight / 2) - ($offsetTop));

                    $graphLeft = max($graphLeft, $legendX + $legendWidth + $offsetLeft);
                }
            }

            $image->setGraphArea($graphLeft, $graphTop, $graphRight, $graphBottom);
            $scaleOptions = [
                'drawSubTicks' => true,
                'mode' => CImage_Chart_Constant::SCALE_MODE_FLOATING,
                'xReleasePercent' => 0,
                //'yMargin'=>CImage_Chart_Constant::AUTO,
            ];

            $image->drawScale($scaleOptions);
            if ($chartLegendShow) {
                $image->drawLegend($legendX, $legendY, $legendFormat);
            }
        }

        if ($chart instanceof CChart_Chart_BarChart) {
            $image->drawBarChart(['displayValues' => false]);
        } elseif ($chart instanceof CChart_Chart_PieChart) {
            $centerX = $width / 2;
            $centerY = $height / 2;
            $radius = min($centerX, $centerY) - 10;
            $chartOptions = [
                //'radius' => $radius,
                'drawLabels' => true,
                'border' => true,
                'labelStacked' => true,
                'valuePosition' => CImage_Chart_Constant::PIE_VALUE_NATURAL
            ];
            $image->setGraphArea($graphLeft, $graphTop, $graphRight, $graphBottom);
            $pieChart = CImage_Chart::createPie($image, $data);
            if ($chart->is3D()) {
                $pieChart->draw3DPie($centerX, $centerY, $chartOptions);
            } else {
                $pieChart->draw2DPie($centerX, $centerY, $chartOptions);
            }
        } elseif ($chart instanceof CChart_Chart_LineChart) {
            $image->drawLineChart(['displayValues' => false]);
        }

        return $image->toDataURI();
    }
}
