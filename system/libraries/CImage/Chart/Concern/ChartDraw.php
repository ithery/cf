<?php
use CImage_Chart_Constant as Constant;
use CImage_Chart_Helper as Helper;

trait CImage_Chart_Concern_ChartDraw {
    /**
     * Draw a plot chart
     *
     * @param array $format
     */
    public function drawPlotChart(array $format = []) {
        $plotSize = isset($format['plotSize']) ? $format['plotSize'] : null;
        $plotBorder = isset($format['plotBorder']) ? $format['plotBorder'] : false;
        $borderR = isset($format['borderR']) ? $format['borderR'] : 50;
        $borderG = isset($format['borderG']) ? $format['borderG'] : 50;
        $borderB = isset($format['borderB']) ? $format['borderB'] : 50;
        $borderalpha = isset($format['borderalpha']) ? $format['borderalpha'] : 30;
        $borderSize = isset($format['borderSize']) ? $format['borderSize'] : 2;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        $displayValues = isset($format['displayValues']) ? $format['displayValues'] : false;
        $displayOffset = isset($format['displayOffset']) ? $format['displayOffset'] : 4;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $displayR = isset($format['displayR']) ? $format['displayR'] : 0;
        $displayG = isset($format['displayG']) ? $format['displayG'] : 0;
        $displayB = isset($format['displayB']) ? $format['displayB'] : 0;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                if (isset($serie['weight'])) {
                    $serieWeight = $serie['weight'] + 2;
                } else {
                    $serieWeight = 2;
                }
                if ($plotSize != null) {
                    $serieWeight = $plotSize;
                }
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = (int) $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                if ($surrounding != null) {
                    $borderR = $r + $surrounding;
                    $borderG = $g + $surrounding;
                    $borderB = $b + $surrounding;
                }
                if (isset($serie['picture'])) {
                    $picture = $serie['picture'];
                    list($picWidth, $picHeight, $picType) = $this->getPicInfo($picture);
                } else {
                    $picture = null;
                    $picOffset = 0;
                }
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie['axis'];
                $shape = $serie['shape'];
                $mode = $data['axis'][$axisId]['display'];
                $format = $data['axis'][$axisId]['format'];
                $unit = $data['axis'][$axisId]['unit'];
                if (isset($serie['description'])) {
                    $serieDescription = $serie['description'];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY($serie['data'], ['axisId' => $serie['axis']]);
                $this->dataSet->data['series'][$serieName]['xOffset'] = 0;
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    if ($picture != null) {
                        $picOffset = $picHeight / 2;
                        $serieWeight = 0;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    foreach ($posArray as $key => $y) {
                        if ($displayValues) {
                            $this->drawText(
                                $x,
                                $y - $displayOffset - $serieWeight - $borderSize - $picOffset,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit),
                                [
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE
                                ]
                            );
                        }
                        if ($y != Constant::VOID) {
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                    'CIRCLE',
                                    floor($x) . ',' . floor($y) . ',' . $serieWeight,
                                    $this->toHTMLColor($r, $g, $b),
                                    $serieDescription,
                                    $this->scaleFormat(
                                        $serie['data'][$key],
                                        $mode,
                                        $format,
                                        $unit
                                    )
                                );
                            }
                            if ($picture != null) {
                                $this->drawFromPicture(
                                    $picType,
                                    $picture,
                                    $x - $picWidth / 2,
                                    $y - $picHeight / 2
                                );
                            } else {
                                $this->drawShape(
                                    $x,
                                    $y,
                                    $shape,
                                    $serieWeight,
                                    $plotBorder,
                                    $borderSize,
                                    $r,
                                    $g,
                                    $b,
                                    $alpha,
                                    $borderR,
                                    $borderG,
                                    $borderB,
                                    $borderalpha
                                );
                            }
                        }
                        $x = $x + $xStep;
                    }
                } else {
                    if ($xDivs == 0) {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    if ($picture != null) {
                        $picOffset = $picWidth / 2;
                        $serieWeight = 0;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    foreach ($posArray as $key => $x) {
                        if ($displayValues) {
                            $this->drawText(
                                $x + $displayOffset + $serieWeight + $borderSize + $picOffset,
                                $y,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit),
                                [
                                    'Angle' => 270,
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE
                                ]
                            );
                        }
                        if ($x != Constant::VOID) {
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                    'CIRCLE',
                                    floor($x) . ',' . floor($y) . ',' . $serieWeight,
                                    $this->toHTMLColor($r, $g, $b),
                                    $serieDescription,
                                    $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                );
                            }
                            if ($picture != null) {
                                $this->drawFromPicture(
                                    $picType,
                                    $picture,
                                    $x - $picWidth / 2,
                                    $y - $picHeight / 2
                                );
                            } else {
                                $this->drawShape(
                                    $x,
                                    $y,
                                    $shape,
                                    $serieWeight,
                                    $plotBorder,
                                    $borderSize,
                                    $r,
                                    $g,
                                    $b,
                                    $alpha,
                                    $borderR,
                                    $borderG,
                                    $borderB,
                                    $borderalpha
                                );
                            }
                        }
                        $y = $y + $yStep;
                    }
                }
            }
        }
    }

    /**
     * Draw a spline chart
     *
     * @param array $format
     */
    public function drawSplineChart(array $format = []) {
        $breakVoid = isset($format['breakVoid']) ? $format['breakVoid'] : true;
        $voidTicks = isset($format['voidTicks']) ? $format['voidTicks'] : 4;
        $breakR = isset($format['breakR']) ? $format['breakR'] : null; // 234
        $breakG = isset($format['breakG']) ? $format['breakG'] : null; // 55
        $breakB = isset($format['breakB']) ? $format['breakB'] : null; // 26
        $displayValues = isset($format['displayValues']) ? $format['displayValues'] : false;
        $displayOffset = isset($format['displayOffset']) ? $format['displayOffset'] : 2;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $displayR = isset($format['displayR']) ? $format['displayR'] : 0;
        $displayG = isset($format['displayG']) ? $format['displayG'] : 0;
        $displayB = isset($format['displayB']) ? $format['displayB'] : 0;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;
        $ImageMapPlotSize = isset($format['imageMapPlotSize']) ? $format['imageMapPlotSize'] : 5;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                $weight = $serie['weight'];
                if ($breakR == null) {
                    $breakSettings = [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'alpha' => $alpha,
                        'ticks' => $voidTicks
                    ];
                } else {
                    $breakSettings = [
                        'r' => $breakR,
                        'g' => $breakG,
                        'b' => $breakB,
                        'alpha' => $alpha,
                        'ticks' => $voidTicks,
                        'weight' => $weight
                    ];
                }
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie['axis'];
                $mode = $data['axis'][$axisId]['display'];
                $format = $data['axis'][$axisId]['format'];
                $unit = $data['axis'][$axisId]['unit'];
                if (isset($serie['description'])) {
                    $serieDescription = $serie['description'];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisId' => $serie['axis']]
                );
                $this->dataSet->data['series'][$serieName]['xOffset'] = 0;
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    $wayPoints = [];
                    $force = $xStep / 5;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $lastGoodY = null;
                    $lastGoodX = null;
                    $lastX = 1;
                    $lastY = 1;
                    foreach ($posArray as $key => $y) {
                        if ($displayValues) {
                            $this->drawText(
                                $x,
                                $y - $displayOffset,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit),
                                [
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE
                                ]
                            );
                        }
                        if ($recordImageMap && $y != Constant::VOID) {
                            $this->addToImageMap(
                                'CIRCLE',
                                floor($x) . ',' . floor($y) . ',' . $ImageMapPlotSize,
                                $this->toHTMLColor($r, $g, $b),
                                $serieDescription,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                            );
                        }
                        if ($y == Constant::VOID && $lastY != null) {
                            $this->drawSpline(
                                $wayPoints,
                                [
                                    'Force' => $force,
                                    'r' => $r,
                                    'g' => $g,
                                    'b' => $b,
                                    'alpha' => $alpha,
                                    'ticks' => $ticks,
                                    'weight' => $weight
                                ]
                            );
                            $wayPoints = [];
                        }
                        if ($y != Constant::VOID && $lastY == null && $lastGoodY != null && !$breakVoid) {
                            $this->drawLine($lastGoodX, $lastGoodY, $x, $y, $breakSettings);
                        }
                        if ($y != Constant::VOID) {
                            $wayPoints[] = [$x, $y];
                        }
                        if ($y != Constant::VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($y == Constant::VOID) {
                            $y = null;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        $x = $x + $xStep;
                    }
                    $this->drawSpline(
                        $wayPoints,
                        [
                            'Force' => $force,
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha,
                            'ticks' => $ticks,
                            'weight' => $weight
                        ]
                    );
                } else {
                    if ($xDivs == 0) {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    $wayPoints = [];
                    $force = $yStep / 5;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $lastGoodY = null;
                    $lastGoodX = null;
                    $lastX = 1;
                    $lastY = 1;
                    foreach ($posArray as $key => $x) {
                        if ($displayValues) {
                            $this->drawText(
                                $x + $displayOffset,
                                $y,
                                $this->scaleFormat(
                                    $serie['data'][$key],
                                    $mode,
                                    $format,
                                    $unit
                                ),
                                [
                                    'Angle' => 270,
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE
                                ]
                            );
                        }
                        if ($recordImageMap && $x != Constant::VOID) {
                            $this->addToImageMap(
                                'CIRCLE',
                                floor($x) . ',' . floor($y) . ',' . $ImageMapPlotSize,
                                $this->toHTMLColor($r, $g, $b),
                                $serieDescription,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                            );
                        }
                        if ($x == Constant::VOID && $lastX != null) {
                            $this->drawSpline(
                                $wayPoints,
                                [
                                    'Force' => $force,
                                    'r' => $r,
                                    'g' => $g,
                                    'b' => $b,
                                    'alpha' => $alpha,
                                    'ticks' => $ticks,
                                    'weight' => $weight
                                ]
                            );
                            $wayPoints = [];
                        }
                        if ($x != Constant::VOID && $lastX == null && $lastGoodX != null && !$breakVoid) {
                            $this->drawLine($lastGoodX, $lastGoodY, $x, $y, $breakSettings);
                        }
                        if ($x != Constant::VOID) {
                            $wayPoints[] = [$x, $y];
                        }
                        if ($x != Constant::VOID) {
                            $lastGoodX = $x;
                            $lastGoodY = $y;
                        }
                        if ($x == Constant::VOID) {
                            $x = null;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        $y = $y + $yStep;
                    }
                    $this->drawSpline(
                        $wayPoints,
                        [
                            'Force' => $force,
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha,
                            'ticks' => $ticks,
                            'weight' => $weight
                        ]
                    );
                }
            }
        }
    }

    /**
     * Draw a filled spline chart
     *
     * @param array $format
     */
    public function drawFilledSplineChart(array $format = []) {
        $displayValues = isset($format['displayValues']) ? $format['displayValues'] : false;
        $displayOffset = isset($format['displayOffset']) ? $format['displayOffset'] : 2;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $displayR = isset($format['displayR']) ? $format['displayR'] : 0;
        $displayG = isset($format['displayG']) ? $format['displayG'] : 0;
        $displayB = isset($format['displayB']) ? $format['displayB'] : 0;
        $aroundZero = isset($format['aroundZero']) ? $format['aroundZero'] : true;
        $threshold = isset($format['Threshold']) ? $format['Threshold'] : null;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie['axis'];
                $mode = $data['axis'][$axisId]['display'];
                $format = $data['axis'][$axisId]['format'];
                $unit = $data['axis'][$axisId]['unit'];
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisId' => $serie['axis']]
                );
                if ($aroundZero) {
                    $yZero = $this->scaleComputeY(0, ['axisId' => $serie['axis']]);
                }
                if ($threshold != null) {
                    foreach ($threshold as $key => $params) {
                        $threshold[$key]['minX'] = $this->scaleComputeY(
                            $params['min'],
                            ['axisId' => $serie['axis']]
                        );
                        $threshold[$key]['maxX'] = $this->scaleComputeY(
                            $params['max'],
                            ['axisId' => $serie['axis']]
                        );
                    }
                }
                $this->dataSet->data['series'][$serieName]['xOffset'] = 0;
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    $wayPoints = [];
                    $force = $xStep / 5;
                    if (!$aroundZero) {
                        $yZero = $this->graphAreaY2 - 1;
                    }
                    if ($yZero > $this->graphAreaY2 - 1) {
                        $yZero = $this->graphAreaY2 - 1;
                    }
                    if ($yZero < $this->graphAreaY1 + 1) {
                        $yZero = $this->graphAreaY1 + 1;
                    }
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    foreach ($posArray as $key => $y) {
                        if ($displayValues) {
                            $this->drawText(
                                $x,
                                $y - $displayOffset,
                                $this->scaleFormat(
                                    $serie['data'][$key],
                                    $mode,
                                    $format,
                                    $unit
                                ),
                                [
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE
                                ]
                            );
                        }
                        if ($y == Constant::VOID) {
                            $area = $this->drawSpline(
                                $wayPoints,
                                ['force' => $force, 'PathOnly' => true]
                            );
                            if (count($area)) {
                                foreach ($area as $key => $points) {
                                    $Corners = [];
                                    $Corners[] = $area[$key][0]['x'];
                                    $Corners[] = $yZero;
                                    foreach ($points as $subKey => $point) {
                                        if ($subKey == count($points) - 1) {
                                            $Corners[] = $point['x'] - 1;
                                        } else {
                                            $Corners[] = $point['x'];
                                        }
                                        $Corners[] = $point['y'] + 1;
                                    }
                                    $Corners[] = $points[$subKey]['x'] - 1;
                                    $Corners[] = $yZero;
                                    $this->drawPolygonChart(
                                        $Corners,
                                        [
                                            'r' => $r,
                                            'g' => $g,
                                            'b' => $b,
                                            'alpha' => $alpha / 2,
                                            'noBorder' => true,
                                            'Threshold' => $threshold
                                        ]
                                    );
                                }
                                $this->drawSpline(
                                    $wayPoints,
                                    [
                                        'Force' => $force,
                                        'r' => $r,
                                        'g' => $g,
                                        'b' => $b,
                                        'alpha' => $alpha,
                                        'ticks' => $ticks
                                    ]
                                );
                            }
                            $wayPoints = [];
                        } else {
                            $wayPoints[] = [$x, $y - .5]; /* -.5 for AA visual fix */
                        }
                        $x = $x + $xStep;
                    }
                    $area = $this->drawSpline($wayPoints, ['force' => $force, 'PathOnly' => true]);
                    if (count($area)) {
                        foreach ($area as $key => $points) {
                            $Corners = [];
                            $Corners[] = $area[$key][0]['x'];
                            $Corners[] = $yZero;
                            foreach ($points as $subKey => $point) {
                                if ($subKey == count($points) - 1) {
                                    $Corners[] = $point['x'] - 1;
                                } else {
                                    $Corners[] = $point['x'];
                                }
                                $Corners[] = $point['y'] + 1;
                            }
                            $Corners[] = $points[$subKey]['x'] - 1;
                            $Corners[] = $yZero;
                            $this->drawPolygonChart(
                                $Corners,
                                [
                                    'r' => $r,
                                    'g' => $g,
                                    'b' => $b,
                                    'alpha' => $alpha / 2,
                                    'noBorder' => true,
                                    'Threshold' => $threshold
                                ]
                            );
                        }
                        $this->drawSpline(
                            $wayPoints,
                            [
                                'Force' => $force,
                                'r' => $r,
                                'g' => $g,
                                'b' => $b,
                                'alpha' => $alpha,
                                'ticks' => $ticks
                            ]
                        );
                    }
                } else {
                    if ($xDivs == 0) {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    $wayPoints = [];
                    $force = $yStep / 5;
                    if (!$aroundZero) {
                        $yZero = $this->graphAreaX1 + 1;
                    }
                    if ($yZero > $this->graphAreaX2 - 1) {
                        $yZero = $this->graphAreaX2 - 1;
                    }
                    if ($yZero < $this->graphAreaX1 + 1) {
                        $yZero = $this->graphAreaX1 + 1;
                    }
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    foreach ($posArray as $key => $x) {
                        if ($displayValues) {
                            $this->drawText(
                                $x + $displayOffset,
                                $y,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit),
                                [
                                    'Angle' => 270,
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE
                                ]
                            );
                        }
                        if ($x == Constant::VOID) {
                            $area = $this->drawSpline(
                                $wayPoints,
                                ['force' => $force, 'PathOnly' => true]
                            );
                            if (count($area)) {
                                foreach ($area as $key => $points) {
                                    $Corners = [];
                                    $Corners[] = $yZero;
                                    $Corners[] = $area[$key][0]['y'];
                                    foreach ($points as $subKey => $point) {
                                        if ($subKey == count($points) - 1) {
                                            $Corners[] = $point['x'] - 1;
                                        } else {
                                            $Corners[] = $point['x'];
                                        }
                                        $Corners[] = $point['y'];
                                    }
                                    $Corners[] = $yZero;
                                    $Corners[] = $points[$subKey]['y'] - 1;
                                    $this->drawPolygonChart(
                                        $Corners,
                                        [
                                            'r' => $r,
                                            'g' => $g,
                                            'b' => $b,
                                            'alpha' => $alpha / 2,
                                            'noBorder' => true,
                                            'Threshold' => $threshold
                                        ]
                                    );
                                }
                                $this->drawSpline(
                                    $wayPoints,
                                    [
                                        'Force' => $force,
                                        'r' => $r,
                                        'g' => $g,
                                        'b' => $b,
                                        'alpha' => $alpha,
                                        'ticks' => $ticks
                                    ]
                                );
                            }
                            $wayPoints = [];
                        } else {
                            $wayPoints[] = [$x, $y];
                        }
                        $y = $y + $yStep;
                    }
                    $area = $this->drawSpline(
                        $wayPoints,
                        ['force' => $force, 'PathOnly' => true]
                    );
                    if (count($area)) {
                        foreach ($area as $key => $points) {
                            $Corners = [];
                            $Corners[] = $yZero;
                            $Corners[] = $area[$key][0]['y'];
                            foreach ($points as $subKey => $point) {
                                if ($subKey == count($points) - 1) {
                                    $Corners[] = $point['x'] - 1;
                                } else {
                                    $Corners[] = $point['x'];
                                }
                                $Corners[] = $point['y'];
                            }
                            $Corners[] = $yZero;
                            $Corners[] = $points[$subKey]['y'] - 1;
                            $this->drawPolygonChart(
                                $Corners,
                                [
                                    'r' => $r,
                                    'g' => $g,
                                    'b' => $b,
                                    'alpha' => $alpha / 2,
                                    'noBorder' => true,
                                    'Threshold' => $threshold
                                ]
                            );
                        }
                        $this->drawSpline(
                            $wayPoints,
                            [
                                'Force' => $force,
                                'r' => $r,
                                'g' => $g,
                                'b' => $b,
                                'alpha' => $alpha,
                                'ticks' => $ticks
                            ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Draw a line chart
     *
     * @param array $format
     */
    public function drawLineChart(array $format = []) {
        $breakVoid = isset($format['breakVoid']) ? $format['breakVoid'] : true;
        $voidTicks = isset($format['voidTicks']) ? $format['voidTicks'] : 4;
        $breakR = isset($format['breakR']) ? $format['breakR'] : null;
        $breakG = isset($format['breakG']) ? $format['breakG'] : null;
        $breakB = isset($format['breakB']) ? $format['breakB'] : null;
        $displayValues = isset($format['displayValues']) ? $format['displayValues'] : false;
        $displayOffset = isset($format['displayOffset']) ? $format['displayOffset'] : 2;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $displayR = isset($format['displayR']) ? $format['displayR'] : 0;
        $displayG = isset($format['displayG']) ? $format['displayG'] : 0;
        $displayB = isset($format['displayB']) ? $format['displayB'] : 0;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;
        $ImageMapPlotSize = isset($format['imageMapPlotSize']) ? $format['imageMapPlotSize'] : 5;
        $forceColor = isset($format['forceColor']) ? $format['forceColor'] : false;
        $forceR = isset($format['forceR']) ? $format['forceR'] : 0;
        $forceG = isset($format['forceG']) ? $format['forceG'] : 0;
        $forceB = isset($format['forceB']) ? $format['forceB'] : 0;
        $forcealpha = isset($format['forcealpha']) ? $format['forcealpha'] : 100;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                $weight = $serie['weight'];
                if ($forceColor) {
                    $r = $forceR;
                    $g = $forceG;
                    $b = $forceB;
                    $alpha = $forcealpha;
                }
                if ($breakR == null) {
                    $breakSettings = [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'alpha' => $alpha,
                        'ticks' => $voidTicks,
                        'weight' => $weight
                    ];
                } else {
                    $breakSettings = [
                        'r' => $breakR,
                        'g' => $breakG,
                        'b' => $breakB,
                        'alpha' => $alpha,
                        'ticks' => $voidTicks,
                        'weight' => $weight
                    ];
                }
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie['axis'];
                $mode = $data['axis'][$axisId]['display'];
                $format = carr::get($data, 'axis.' . $axisId . '.format');
                $unit = carr::get($data, 'axis.' . $axisId . '.unit');
                if (isset($serie['description'])) {
                    $serieDescription = $serie['description'];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisId' => $serie['axis']]
                );
                $this->dataSet->data['series'][$serieName]['xOffset'] = 0;
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    $lastX = null;
                    $lastY = null;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $lastGoodY = null;
                    $lastGoodX = null;
                    foreach ($posArray as $key => $y) {
                        if ($displayValues && $serie['data'][$key] != Constant::VOID) {
                            if ($serie['data'][$key] > 0) {
                                $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                                $offset = $displayOffset;
                            } else {
                                $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                                $offset = -$displayOffset;
                            }

                            $this->drawText(
                                $x,
                                $y - $offset - $weight,
                                $this->scaleFormat(
                                    $serie['data'][$key],
                                    $mode,
                                    $format,
                                    $unit
                                ),
                                [
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => $align
                                ]
                            );
                        }
                        if ($recordImageMap && $y != Constant::VOID) {
                            $this->addToImageMap(
                                'CIRCLE',
                                floor($x) . ',' . floor($y) . ',' . $ImageMapPlotSize,
                                $this->toHTMLColor($r, $g, $b),
                                $serieDescription,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                            );
                        }
                        if ($y != Constant::VOID && $lastX != null && $lastY != null) {
                            $this->drawLine(
                                $lastX,
                                $lastY,
                                $x,
                                $y,
                                [
                                    'r' => $r,
                                    'g' => $g,
                                    'b' => $b,
                                    'alpha' => $alpha,
                                    'ticks' => $ticks,
                                    'weight' => $weight
                                ]
                            );
                        }
                        if ($y != Constant::VOID && $lastY == null && $lastGoodY != null && !$breakVoid) {
                            $this->drawLine(
                                $lastGoodX,
                                $lastGoodY,
                                $x,
                                $y,
                                $breakSettings
                            );
                            $lastGoodY = null;
                        }
                        if ($y != Constant::VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($y == Constant::VOID) {
                            $y = null;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        $x = $x + $xStep;
                    }
                } else {
                    if ($xDivs == 0) {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    $lastX = null;
                    $lastY = null;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $lastGoodY = null;
                    $lastGoodX = null;
                    foreach ($posArray as $key => $x) {
                        if ($displayValues && $serie['data'][$key] != Constant::VOID) {
                            $this->drawText(
                                $x + $displayOffset + $weight,
                                $y,
                                $this->scaleFormat(
                                    $serie['data'][$key],
                                    $mode,
                                    $format,
                                    $unit
                                ),
                                [
                                    'Angle' => 270,
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE
                                ]
                            );
                        }
                        if ($recordImageMap && $x != Constant::VOID) {
                            $this->addToImageMap(
                                'CIRCLE',
                                floor($x) . ',' . floor($y) . ',' . $ImageMapPlotSize,
                                $this->toHTMLColor($r, $g, $b),
                                $serieDescription,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                            );
                        }
                        if ($x != Constant::VOID && $lastX != null && $lastY != null) {
                            $this->drawLine(
                                $lastX,
                                $lastY,
                                $x,
                                $y,
                                [
                                    'r' => $r,
                                    'g' => $g,
                                    'b' => $b,
                                    'alpha' => $alpha,
                                    'ticks' => $ticks,
                                    'weight' => $weight
                                ]
                            );
                        }
                        if ($x != Constant::VOID && $lastX == null && $lastGoodY != null && !$breakVoid) {
                            $this->drawLine(
                                $lastGoodX,
                                $lastGoodY,
                                $x,
                                $y,
                                $breakSettings
                            );
                            $lastGoodY = null;
                        }
                        if ($x != Constant::VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($x == Constant::VOID) {
                            $x = null;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        $y = $y + $yStep;
                    }
                }
            }
        }
    }

    /**
     * Draw a zone chart
     *
     * @param string $serieA
     * @param string $serieB
     * @param array  $format
     *
     * @return null|integer
     */
    public function drawZoneChart($serieA, $serieB, array $format = []) {
        $axisId = isset($format['axisId']) ? $format['axisId'] : 0;
        $lineR = isset($format['lineR']) ? $format['lineR'] : 150;
        $lineG = isset($format['lineG']) ? $format['lineG'] : 150;
        $lineB = isset($format['lineB']) ? $format['lineB'] : 150;
        $linealpha = isset($format['linealpha']) ? $format['linealpha'] : 50;
        $lineTicks = isset($format['lineTicks']) ? $format['lineTicks'] : 1;
        $areaR = isset($format['areaR']) ? $format['areaR'] : 150;
        $areaG = isset($format['areaG']) ? $format['areaG'] : 150;
        $areaB = isset($format['areaB']) ? $format['areaB'] : 150;
        $areaalpha = isset($format['areaalpha']) ? $format['areaalpha'] : 5;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        if (!isset($data['series'][$serieA]['data']) || !isset($data['series'][$serieB]['data'])
        ) {
            return 0;
        }
        $serieAData = $data['series'][$serieA]['data'];
        $serieBData = $data['series'][$serieB]['data'];
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        $mode = $data['axis'][$axisId]['display'];
        $format = $data['axis'][$axisId]['format'];
        $posArrayA = $this->scaleComputeY($serieAData, ['axisId' => $axisId]);
        $posArrayB = $this->scaleComputeY($serieBData, ['axisId' => $axisId]);
        if (count($posArrayA) != count($posArrayB)) {
            return 0;
        }
        if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
            if ($xDivs == 0) {
                $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
            } else {
                $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
            }
            $x = $this->graphAreaX1 + $xMargin;
            $lastX = null;
            $lastY = null;
            $lastY1 = null;
            $lastY2 = null;
            $boundsA = [];
            $boundsB = [];
            foreach ($posArrayA as $key => $y1) {
                $y2 = $posArrayB[$key];
                $boundsA[] = $x;
                $boundsA[] = $y1;
                $boundsB[] = $x;
                $boundsB[] = $y2;
                $lastX = $x;
                $lastY1 = $y1;
                $lastY2 = $y2;
                $x = $x + $xStep;
            }
            $bounds = array_merge($boundsA, $this->reversePlots($boundsB));
            $this->drawPolygonChart(
                $bounds,
                [
                    'r' => $areaR,
                    'g' => $areaG,
                    'b' => $areaB,
                    'alpha' => $areaalpha
                ]
            );
            for ($i = 0; $i <= count($boundsA) - 4; $i = $i + 2) {
                $this->drawLine(
                    $boundsA[$i],
                    $boundsA[$i + 1],
                    $boundsA[$i + 2],
                    $boundsA[$i + 3],
                    [
                        'r' => $lineR,
                        'g' => $lineG,
                        'b' => $lineB,
                        'alpha' => $linealpha,
                        'ticks' => $lineTicks
                    ]
                );
                $this->drawLine(
                    $boundsB[$i],
                    $boundsB[$i + 1],
                    $boundsB[$i + 2],
                    $boundsB[$i + 3],
                    [
                        'r' => $lineR,
                        'g' => $lineG,
                        'b' => $lineB,
                        'alpha' => $linealpha,
                        'ticks' => $lineTicks
                    ]
                );
            }
        } else {
            if ($xDivs == 0) {
                $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
            } else {
                $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
            }
            $y = $this->graphAreaY1 + $xMargin;
            $lastX = null;
            $lastY = null;
            $lastX1 = null;
            $lastX2 = null;
            $boundsA = [];
            $boundsB = [];
            foreach ($posArrayA as $key => $x1) {
                $x2 = $posArrayB[$key];
                $boundsA[] = $x1;
                $boundsA[] = $y;
                $boundsB[] = $x2;
                $boundsB[] = $y;
                $lastY = $y;
                $lastX1 = $x1;
                $lastX2 = $x2;
                $y = $y + $yStep;
            }
            $bounds = array_merge($boundsA, $this->reversePlots($boundsB));
            $this->drawPolygonChart(
                $bounds,
                ['r' => $areaR, 'g' => $areaG, 'b' => $areaB, 'alpha' => $areaalpha]
            );
            for ($i = 0; $i <= count($boundsA) - 4; $i = $i + 2) {
                $this->drawLine(
                    $boundsA[$i],
                    $boundsA[$i + 1],
                    $boundsA[$i + 2],
                    $boundsA[$i + 3],
                    [
                        'r' => $lineR,
                        'g' => $lineG,
                        'b' => $lineB,
                        'alpha' => $linealpha,
                        'ticks' => $lineTicks
                    ]
                );
                $this->drawLine(
                    $boundsB[$i],
                    $boundsB[$i + 1],
                    $boundsB[$i + 2],
                    $boundsB[$i + 3],
                    [
                        'r' => $lineR,
                        'g' => $lineG,
                        'b' => $lineB,
                        'alpha' => $linealpha,
                        'ticks' => $lineTicks
                    ]
                );
            }
        }
    }

    /**
     * Draw a step chart
     *
     * @param array $format
     */
    public function drawStepChart(array $format = []) {
        $breakVoid = isset($format['breakVoid']) ? $format['breakVoid'] : false;
        $reCenter = isset($format['reCenter']) ? $format['reCenter'] : true;
        $voidTicks = isset($format['voidTicks']) ? $format['voidTicks'] : 4;
        $breakR = isset($format['breakR']) ? $format['breakR'] : null;
        $breakG = isset($format['breakG']) ? $format['breakG'] : null;
        $breakB = isset($format['breakB']) ? $format['breakB'] : null;
        $displayValues = isset($format['displayValues']) ? $format['displayValues'] : false;
        $displayOffset = isset($format['displayOffset']) ? $format['displayOffset'] : 2;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $displayR = isset($format['displayR']) ? $format['displayR'] : 0;
        $displayG = isset($format['displayG']) ? $format['displayG'] : 0;
        $displayB = isset($format['displayB']) ? $format['displayB'] : 0;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;
        $ImageMapPlotSize = isset($format['imageMapPlotSize']) ? $format['imageMapPlotSize'] : 5;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        $xStep = 0;
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                $weight = $serie['weight'];
                if (isset($serie['description'])) {
                    $serieDescription = $serie['description'];
                } else {
                    $serieDescription = $serieName;
                }
                if ($breakR == null) {
                    $breakSettings = [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'alpha' => $alpha,
                        'ticks' => $voidTicks,
                        'weight' => $weight
                    ];
                } else {
                    $breakSettings = [
                        'r' => $breakR,
                        'g' => $breakG,
                        'b' => $breakB,
                        'alpha' => $alpha,
                        'ticks' => $voidTicks,
                        'weight' => $weight
                    ];
                }
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie['axis'];
                $mode = $data['axis'][$axisId]['display'];
                $format = $data['axis'][$axisId]['format'];
                $unit = $data['axis'][$axisId]['unit'];
                $color = [
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                    'alpha' => $alpha,
                    'ticks' => $ticks,
                    'weight' => $weight
                ];
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisId' => $serie['axis']]
                );
                $this->dataSet->data['series'][$serieName]['xOffset'] = 0;
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    $lastX = null;
                    $lastY = null;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $lastGoodY = null;
                    $lastGoodX = null;
                    $Init = false;
                    foreach ($posArray as $key => $y) {
                        if ($displayValues && $serie['data'][$key] != Constant::VOID) {
                            if ($y <= $lastY) {
                                $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                                $offset = $displayOffset;
                            } else {
                                $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                                $offset = -$displayOffset;
                            }
                            $this->drawText(
                                $x,
                                $y - $offset - $weight,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit),
                                ['r' => $displayR, 'g' => $displayG, 'b' => $displayB, 'align' => $align]
                            );
                        }
                        if ($y != Constant::VOID && $lastX != null && $lastY != null) {
                            $this->drawLine($lastX, $lastY, $x, $lastY, $color);
                            $this->drawLine($x, $lastY, $x, $y, $color);
                            if ($reCenter && $x + $xStep < $this->graphAreaX2 - $xMargin) {
                                $this->drawLine($x, $y, $x + $xStep, $y, $color);
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                        'RECT',
                                        sprintf(
                                            '%s,%s,%s,%s',
                                            floor($x - $ImageMapPlotSize),
                                            floor($y - $ImageMapPlotSize),
                                            floor($x + $xStep + $ImageMapPlotSize),
                                            floor($y + $ImageMapPlotSize)
                                        ),
                                        $this->toHTMLColor($r, $g, $b),
                                        $serieDescription,
                                        $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                    );
                                }
                            } else {
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                        'RECT',
                                        sprintf(
                                            '%s,%s,%s,%s',
                                            floor($lastX - $ImageMapPlotSize),
                                            floor($lastY - $ImageMapPlotSize),
                                            floor($x + $ImageMapPlotSize),
                                            floor($lastY + $ImageMapPlotSize)
                                        ),
                                        $this->toHTMLColor($r, $g, $b),
                                        $serieDescription,
                                        $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                    );
                                }
                            }
                        }
                        if ($y != Constant::VOID && $lastY == null && $lastGoodY != null && !$breakVoid) {
                            if ($reCenter) {
                                $this->drawLine($lastGoodX + $xStep, $lastGoodY, $x, $lastGoodY, $breakSettings);
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                        'RECT',
                                        sprintf(
                                            '%s,%s,%s,%s',
                                            floor($lastGoodX + $xStep - $ImageMapPlotSize),
                                            floor($lastGoodY - $ImageMapPlotSize),
                                            floor($x + $ImageMapPlotSize),
                                            floor($lastGoodY + $ImageMapPlotSize)
                                        ),
                                        $this->toHTMLColor($r, $g, $b),
                                        $serieDescription,
                                        $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                    );
                                }
                            } else {
                                $this->drawLine($lastGoodX, $lastGoodY, $x, $lastGoodY, $breakSettings);
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                        'RECT',
                                        sprintf(
                                            '%s,%s,%s,%s',
                                            floor($lastGoodX - $ImageMapPlotSize),
                                            floor($lastGoodY - $ImageMapPlotSize),
                                            floor($x + $ImageMapPlotSize),
                                            floor($lastGoodY + $ImageMapPlotSize)
                                        ),
                                        $serieDescription,
                                        $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                    );
                                }
                            }
                            $this->drawLine($x, $lastGoodY, $x, $y, $breakSettings);
                            $lastGoodY = null;
                        } elseif (!$breakVoid && $lastGoodY == null && $y != Constant::VOID) {
                            $this->drawLine($this->graphAreaX1 + $xMargin, $y, $x, $y, $breakSettings);
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                    'RECT',
                                    sprintf(
                                        '%s,%s,%s,%s',
                                        floor($this->graphAreaX1 + $xMargin - $ImageMapPlotSize),
                                        floor($y - $ImageMapPlotSize),
                                        floor($x + $ImageMapPlotSize),
                                        floor($y + $ImageMapPlotSize)
                                    ),
                                    $this->toHTMLColor($r, $g, $b),
                                    $serieDescription,
                                    $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                );
                            }
                        }
                        if ($y != Constant::VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($y == Constant::VOID) {
                            $y = null;
                        }
                        if (!$Init && $reCenter) {
                            $x = $x - $xStep / 2;
                            $Init = true;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        if ($lastX < $this->graphAreaX1 + $xMargin) {
                            $lastX = $this->graphAreaX1 + $xMargin;
                        }
                        $x = $x + $xStep;
                    }
                    if ($reCenter) {
                        $this->drawLine($lastX, $lastY, $this->graphAreaX2 - $xMargin, $lastY, $color);
                        if ($recordImageMap) {
                            $this->addToImageMap(
                                'RECT',
                                sprintf(
                                    '%s,%s,%s,%s',
                                    floor($lastX - $ImageMapPlotSize),
                                    floor($lastY - $ImageMapPlotSize),
                                    floor($this->graphAreaX2 - $xMargin + $ImageMapPlotSize),
                                    floor($lastY + $ImageMapPlotSize)
                                ),
                                $this->toHTMLColor($r, $g, $b),
                                $serieDescription,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                            );
                        }
                    }
                } else {
                    if ($xDivs == 0) {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    $lastX = null;
                    $lastY = null;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $lastGoodY = null;
                    $lastGoodX = null;
                    $Init = false;
                    foreach ($posArray as $key => $x) {
                        if ($displayValues && $serie['data'][$key] != Constant::VOID) {
                            if ($x >= $lastX) {
                                $align = Constant::TEXT_ALIGN_MIDDLELEFT;
                                $offset = $displayOffset;
                            } else {
                                $align = Constant::TEXT_ALIGN_MIDDLERIGHT;
                                $offset = -$displayOffset;
                            }
                            $this->drawText(
                                $x + $offset + $weight,
                                $y,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit),
                                [
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => $align
                                ]
                            );
                        }
                        if ($x != Constant::VOID && $lastX != null && $lastY != null) {
                            $this->drawLine($lastX, $lastY, $lastX, $y, $color);
                            $this->drawLine($lastX, $y, $x, $y, $color);
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                    'RECT',
                                    sprintf(
                                        '%s,%s,%s,%s',
                                        floor($lastX - $ImageMapPlotSize),
                                        floor($lastY - $ImageMapPlotSize),
                                        floor($lastX + $xStep + $ImageMapPlotSize),
                                        floor($y + $ImageMapPlotSize)
                                    ),
                                    $this->toHTMLColor($r, $g, $b),
                                    $serieDescription,
                                    $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                );
                            }
                        }
                        if ($x != Constant::VOID && $lastX == null && $lastGoodY != null && !$breakVoid) {
                            $this->drawLine(
                                $lastGoodX,
                                $lastGoodY,
                                $lastGoodX,
                                $lastGoodY + $yStep,
                                $color
                            );
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                    'RECT',
                                    sprintf(
                                        '%s,%s,%s,%s',
                                        floor($lastGoodX - $ImageMapPlotSize),
                                        floor($lastGoodY - $ImageMapPlotSize),
                                        floor($lastGoodX + $ImageMapPlotSize),
                                        floor($lastGoodY + $yStep + $ImageMapPlotSize)
                                    ),
                                    $this->toHTMLColor($r, $g, $b),
                                    $serieDescription,
                                    $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                );
                            }
                            $this->drawLine(
                                $lastGoodX,
                                $lastGoodY + $yStep,
                                $lastGoodX,
                                $y,
                                $breakSettings
                            );
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                    'RECT',
                                    sprintf(
                                        '%s,%s,%s,%s',
                                        floor($lastGoodX - $ImageMapPlotSize),
                                        floor($lastGoodY + $yStep - $ImageMapPlotSize),
                                        floor($lastGoodX + $ImageMapPlotSize),
                                        floor($yStep + $ImageMapPlotSize)
                                    ),
                                    $this->toHTMLColor($r, $g, $b),
                                    $serieDescription,
                                    $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                );
                            }
                            $this->drawLine($lastGoodX, $y, $x, $y, $breakSettings);
                            $lastGoodY = null;
                        } elseif ($x != Constant::VOID && $lastGoodY == null && !$breakVoid) {
                            $this->drawLine($x, $this->graphAreaY1 + $xMargin, $x, $y, $breakSettings);
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                    'RECT',
                                    sprintf(
                                        '%s,%s,%s,%s',
                                        floor($x - $ImageMapPlotSize),
                                        floor($this->graphAreaY1 + $xMargin - $ImageMapPlotSize),
                                        floor($x + $ImageMapPlotSize),
                                        floor($y + $ImageMapPlotSize)
                                    ),
                                    $this->toHTMLColor($r, $g, $b),
                                    $serieDescription,
                                    $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                );
                            }
                        }
                        if ($x != Constant::VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($x == Constant::VOID) {
                            $x = null;
                        }
                        if (!$Init && $reCenter) {
                            $y = $y - $yStep / 2;
                            $Init = true;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        if ($lastY < $this->graphAreaY1 + $xMargin) {
                            $lastY = $this->graphAreaY1 + $xMargin;
                        }
                        $y = $y + $yStep;
                    }
                    if ($reCenter) {
                        $this->drawLine($lastX, $lastY, $lastX, $this->graphAreaY2 - $xMargin, $color);
                        if ($recordImageMap) {
                            $this->addToImageMap(
                                'RECT',
                                sprintf(
                                    '%s,%s,%s,%s',
                                    floor($lastX - $ImageMapPlotSize),
                                    floor($lastY - $ImageMapPlotSize),
                                    floor($lastX + $ImageMapPlotSize),
                                    floor($this->graphAreaY2 - $xMargin + $ImageMapPlotSize)
                                ),
                                $this->toHTMLColor($r, $g, $b),
                                $serieDescription,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Draw a step chart
     *
     * @param array $format
     */
    public function drawFilledStepChart(array $format = []) {
        $reCenter = isset($format['reCenter']) ? $format['reCenter'] : true;
        $displayValues = isset($format['displayValues']) ? $format['displayValues'] : false;
        $displayOffset = isset($format['displayOffset']) ? $format['displayOffset'] : 2;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $forceTransparency = isset($format['forceTransparency']) ? $format['forceTransparency'] : null;
        $displayR = isset($format['displayR']) ? $format['displayR'] : 0;
        $displayG = isset($format['displayG']) ? $format['displayG'] : 0;
        $displayB = isset($format['displayB']) ? $format['displayB'] : 0;
        $aroundZero = isset($format['aroundZero']) ? $format['aroundZero'] : true;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie['axis'];
                $format = $data['axis'][$axisId]['format'];
                $color = ['r' => $r, 'g' => $g, 'b' => $b];
                if ($forceTransparency != null) {
                    $color['alpha'] = $forceTransparency;
                } else {
                    $color['alpha'] = $alpha;
                }
                $posArray = $this->scaleComputeY($serie['data'], ['axisId' => $serie['axis']]);
                $yZero = $this->scaleComputeY(0, ['axisId' => $serie['axis']]);
                $this->dataSet->data['series'][$serieName]['xOffset'] = 0;
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($yZero > $this->graphAreaY2 - 1) {
                        $yZero = $this->graphAreaY2 - 1;
                    }
                    if ($yZero < $this->graphAreaY1 + 1) {
                        $yZero = $this->graphAreaY1 + 1;
                    }
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    $lastX = null;
                    $lastY = null;
                    if (!$aroundZero) {
                        $yZero = $this->graphAreaY2 - 1;
                    }
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $lastGoodY = null;
                    $lastGoodX = null;
                    $points = [];
                    $Init = false;
                    foreach ($posArray as $key => $y) {
                        if ($y == Constant::VOID && $lastX != null && $lastY != null && count($points)) {
                            $points[] = $lastX;
                            $points[] = $lastY;
                            $points[] = $x;
                            $points[] = $lastY;
                            $points[] = $x;
                            $points[] = $yZero;
                            $this->drawPolygon($points, $color);
                            $points = [];
                        }
                        if ($y != Constant::VOID && $lastX != null && $lastY != null) {
                            if (count($points)) {
                                $points[] = $lastX;
                                $points[] = $yZero;
                            }
                            $points[] = $lastX;
                            $points[] = $lastY;
                            $points[] = $x;
                            $points[] = $lastY;
                            $points[] = $x;
                            $points[] = $y;
                        }
                        if ($y != Constant::VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($y == Constant::VOID) {
                            $y = null;
                        }
                        if (!$Init && $reCenter) {
                            $x = $x - $xStep / 2;
                            $Init = true;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        if ($lastX < $this->graphAreaX1 + $xMargin) {
                            $lastX = $this->graphAreaX1 + $xMargin;
                        }
                        $x = $x + $xStep;
                    }
                    if ($reCenter) {
                        $points[] = $lastX + $xStep / 2;
                        $points[] = $lastY;
                        $points[] = $lastX + $xStep / 2;
                        $points[] = $yZero;
                    } else {
                        $points[] = $lastX;
                        $points[] = $yZero;
                    }
                    $this->drawPolygon($points, $color);
                } else {
                    if ($yZero < $this->graphAreaX1 + 1) {
                        $yZero = $this->graphAreaX1 + 1;
                    }
                    if ($yZero > $this->graphAreaX2 - 1) {
                        $yZero = $this->graphAreaX2 - 1;
                    }
                    if ($xDivs == 0) {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    $lastX = null;
                    $lastY = null;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $lastGoodY = null;
                    $lastGoodX = null;
                    $points = [];
                    foreach ($posArray as $key => $x) {
                        if ($x == Constant::VOID && $lastX != null && $lastY != null && count($points)) {
                            $points[] = $lastX;
                            $points[] = $lastY;
                            $points[] = $lastX;
                            $points[] = $y;
                            $points[] = $yZero;
                            $points[] = $y;
                            $this->drawPolygon($points, $color);
                            $points = [];
                        }
                        if ($x != Constant::VOID && $lastX != null && $lastY != null) {
                            if (count($points)) {
                                $points[] = $yZero;
                                $points[] = $lastY;
                            }
                            $points[] = $lastX;
                            $points[] = $lastY;
                            $points[] = $lastX;
                            $points[] = $y;
                            $points[] = $x;
                            $points[] = $y;
                        }
                        if ($x != Constant::VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($x == Constant::VOID) {
                            $x = null;
                        }
                        if ($lastX == null && $reCenter) {
                            $y = $y - $yStep / 2;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        if ($lastY < $this->graphAreaY1 + $xMargin) {
                            $lastY = $this->graphAreaY1 + $xMargin;
                        }
                        $y = $y + $yStep;
                    }
                    if ($reCenter) {
                        $points[] = $lastX;
                        $points[] = $lastY + $yStep / 2;
                        $points[] = $yZero;
                        $points[] = $lastY + $yStep / 2;
                    } else {
                        $points[] = $yZero;
                        $points[] = $lastY;
                    }
                    $this->drawPolygon($points, $color);
                }
            }
        }
    }

    /**
     * Draw an area chart
     *
     * @param array $format
     */
    public function drawAreaChart(array $format = []) {
        $displayValues = isset($format['displayValues']) ? $format['displayValues'] : false;
        $displayOffset = isset($format['displayOffset']) ? $format['displayOffset'] : 2;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $displayR = isset($format['displayR']) ? $format['displayR'] : 0;
        $displayG = isset($format['displayG']) ? $format['displayG'] : 0;
        $displayB = isset($format['displayB']) ? $format['displayB'] : 0;
        $forceTransparency = isset($format['forceTransparency']) ? $format['forceTransparency'] : 25;
        $aroundZero = isset($format['aroundZero']) ? $format['aroundZero'] : true;
        $threshold = isset($format['Threshold']) ? $format['Threshold'] : null;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie['axis'];
                $mode = $data['axis'][$axisId]['display'];
                $format = $data['axis'][$axisId]['format'];
                $unit = $data['axis'][$axisId]['unit'];
                $posArray = $this->scaleComputeY($serie['data'], ['axisId' => $serie['axis']]);
                $yZero = $this->scaleComputeY(0, ['axisId' => $serie['axis']]);
                if ($threshold != null) {
                    foreach ($threshold as $key => $params) {
                        $threshold[$key]['minX'] = $this->scaleComputeY(
                            $params['min'],
                            ['axisId' => $serie['axis']]
                        );
                        $threshold[$key]['maxX'] = $this->scaleComputeY(
                            $params['max'],
                            ['axisId' => $serie['axis']]
                        );
                    }
                }
                $this->dataSet->data['series'][$serieName]['xOffset'] = 0;
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($yZero > $this->graphAreaY2 - 1) {
                        $yZero = $this->graphAreaY2 - 1;
                    }
                    $areas = [];
                    $areaID = 0;
                    $areas[$areaID][] = $this->graphAreaX1 + $xMargin;
                    if ($aroundZero) {
                        $areas[$areaID][] = $yZero;
                    } else {
                        $areas[$areaID][] = $this->graphAreaY2 - 1;
                    }
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    $lastX = null;
                    $lastY = null;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    foreach ($posArray as $key => $y) {
                        if ($displayValues && $serie['data'][$key] != Constant::VOID) {
                            if ($serie['data'][$key] > 0) {
                                $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                                $offset = $displayOffset;
                            } else {
                                $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                                $offset = -$displayOffset;
                            }
                            $this->drawText(
                                $x,
                                $y - $offset,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit),
                                ['r' => $displayR, 'g' => $displayG, 'b' => $displayB, 'align' => $align]
                            );
                        }
                        if ($y == Constant::VOID && isset($areas[$areaID])) {
                            if ($lastX == null) {
                                $areas[$areaID][] = $x;
                            } else {
                                $areas[$areaID][] = $lastX;
                            }
                            if ($aroundZero) {
                                $areas[$areaID][] = $yZero;
                            } else {
                                $areas[$areaID][] = $this->graphAreaY2 - 1;
                            }
                            $areaID++;
                        } elseif ($y != Constant::VOID) {
                            if (!isset($areas[$areaID])) {
                                $areas[$areaID][] = $x;
                                if ($aroundZero) {
                                    $areas[$areaID][] = $yZero;
                                } else {
                                    $areas[$areaID][] = $this->graphAreaY2 - 1;
                                }
                            }
                            $areas[$areaID][] = $x;
                            $areas[$areaID][] = $y;
                        }
                        $lastX = $x;
                        $x = $x + $xStep;
                    }
                    $areas[$areaID][] = $lastX;
                    if ($aroundZero) {
                        $areas[$areaID][] = $yZero;
                    } else {
                        $areas[$areaID][] = $this->graphAreaY2 - 1;
                    }
                    /* Handle shadows in the areas */
                    if ($this->shadow) {
                        $shadowArea = [];
                        foreach ($areas as $key => $points) {
                            $shadowArea[$key] = [];
                            foreach ($points as $key2 => $value) {
                                if ($key2 % 2 == 0) {
                                    $shadowArea[$key][] = $value + $this->shadowX;
                                } else {
                                    $shadowArea[$key][] = $value + $this->shadowY;
                                }
                            }
                        }
                        foreach ($shadowArea as $key => $points) {
                            $this->drawPolygonChart(
                                $points,
                                [
                                    'r' => $this->shadowR,
                                    'g' => $this->shadowG,
                                    'b' => $this->shadowB,
                                    'alpha' => $this->shadowA
                                ]
                            );
                        }
                    }
                    $alpha = $forceTransparency != null ? $forceTransparency : $alpha;
                    $color = [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'alpha' => $alpha,
                        'Threshold' => $threshold
                    ];
                    foreach ($areas as $key => $points) {
                        $this->drawPolygonChart($points, $color);
                    }
                } else {
                    if ($yZero < $this->graphAreaX1 + 1) {
                        $yZero = $this->graphAreaX1 + 1;
                    }
                    if ($yZero > $this->graphAreaX2 - 1) {
                        $yZero = $this->graphAreaX2 - 1;
                    }
                    $areas = [];
                    $areaID = 0;
                    if ($aroundZero) {
                        $areas[$areaID][] = $yZero;
                    } else {
                        $areas[$areaID][] = $this->graphAreaX1 + 1;
                    }
                    $areas[$areaID][] = $this->graphAreaY1 + $xMargin;
                    if ($xDivs == 0) {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    $lastX = null;
                    $lastY = null;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    foreach ($posArray as $key => $x) {
                        if ($displayValues && $serie['data'][$key] != Constant::VOID) {
                            if ($serie['data'][$key] > 0) {
                                $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                                $offset = $displayOffset;
                            } else {
                                $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                                $offset = -$displayOffset;
                            }
                            $this->drawText(
                                $x + $offset,
                                $y,
                                $this->scaleFormat($serie['data'][$key], $mode, $format, $unit),
                                [
                                    'Angle' => 270,
                                    'r' => $displayR,
                                    'g' => $displayG,
                                    'b' => $displayB,
                                    'align' => $align
                                ]
                            );
                        }
                        if ($x == Constant::VOID && isset($areas[$areaID])) {
                            if ($aroundZero) {
                                $areas[$areaID][] = $yZero;
                            } else {
                                $areas[$areaID][] = $this->graphAreaX1 + 1;
                            }
                            if ($lastY == null) {
                                $areas[$areaID][] = $y;
                            } else {
                                $areas[$areaID][] = $lastY;
                            }
                            $areaID++;
                        } elseif ($x != Constant::VOID) {
                            if (!isset($areas[$areaID])) {
                                if ($aroundZero) {
                                    $areas[$areaID][] = $yZero;
                                } else {
                                    $areas[$areaID][] = $this->graphAreaX1 + 1;
                                }
                                $areas[$areaID][] = $y;
                            }
                            $areas[$areaID][] = $x;
                            $areas[$areaID][] = $y;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        $y = $y + $yStep;
                    }
                    if ($aroundZero) {
                        $areas[$areaID][] = $yZero;
                    } else {
                        $areas[$areaID][] = $this->graphAreaX1 + 1;
                    }
                    $areas[$areaID][] = $lastY;
                    /* Handle shadows in the areas */
                    if ($this->shadow) {
                        $shadowArea = [];
                        foreach ($areas as $key => $points) {
                            $shadowArea[$key] = [];
                            foreach ($points as $key2 => $value) {
                                if ($key2 % 2 == 0) {
                                    $shadowArea[$key][] = $value + $this->shadowX;
                                } else {
                                    $shadowArea[$key][] = $value + $this->shadowY;
                                }
                            }
                        }
                        foreach ($shadowArea as $key => $points) {
                            $this->drawPolygonChart(
                                $points,
                                [
                                    'r' => $this->shadowR,
                                    'g' => $this->shadowG,
                                    'b' => $this->shadowB,
                                    'alpha' => $this->shadowA
                                ]
                            );
                        }
                    }
                    $alpha = $forceTransparency != null ? $forceTransparency : $alpha;
                    $color = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'Threshold' => $threshold];
                    foreach ($areas as $key => $points) {
                        $this->drawPolygonChart($points, $color);
                    }
                }
            }
        }
    }

    /**
     * Draw a bar chart
     *
     * @param array $format
     */
    public function drawBarChart(array $format = []) {
        $floating0Serie = isset($format['floating0Serie']) ? $format['floating0Serie'] : null;
        $floating0Value = isset($format['floating0Value']) ? $format['floating0Value'] : null;
        $draw0Line = isset($format['draw0Line']) ? $format['draw0Line'] : false;
        $displayValues = isset($format['displayValues']) ? $format['displayValues'] : false;
        $displayOffset = isset($format['displayOffset']) ? $format['displayOffset'] : 2;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $displayFont = isset($format['displayFont']) ? $format['displayFont'] : $this->fontName;
        $displaySize = isset($format['displaySize']) ? $format['displaySize'] : $this->fontSize;
        $displayPos = isset($format['displayPos']) ? $format['displayPos'] : Constant::LABEL_POS_OUTSIDE;
        $displayShadow = isset($format['displayShadow']) ? $format['displayShadow'] : true;
        $displayR = isset($format['displayR']) ? $format['displayR'] : 0;
        $displayG = isset($format['displayG']) ? $format['displayG'] : 0;
        $displayB = isset($format['displayB']) ? $format['displayB'] : 0;
        $aroundZero = isset($format['aroundZero']) ? $format['aroundZero'] : true;
        $Interleave = isset($format['interleave']) ? $format['interleave'] : .5;
        $rounded = isset($format['rounded']) ? $format['rounded'] : false;
        $roundRadius = isset($format['roundRadius']) ? $format['roundRadius'] : 4;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        $borderR = isset($format['borderR']) ? $format['borderR'] : -1;
        $borderG = isset($format['borderG']) ? $format['borderG'] : -1;
        $borderB = isset($format['borderB']) ? $format['borderB'] : -1;
        $gradient = isset($format['gradient']) ? $format['gradient'] : false;
        $gradientMode = isset($format['gradientMode']) ? $format['gradientMode'] : Constant::GRADIENT_SIMPLE;
        $gradientalpha = isset($format['gradientalpha']) ? $format['gradientalpha'] : 20;
        $gradientStartR = isset($format['gradientStartR']) ? $format['gradientStartR'] : 255;
        $gradientStartG = isset($format['gradientStartG']) ? $format['gradientStartG'] : 255;
        $gradientStartB = isset($format['gradientStartB']) ? $format['gradientStartB'] : 255;
        $gradientEndR = isset($format['gradientEndR']) ? $format['gradientEndR'] : 0;
        $gradientEndG = isset($format['gradientEndG']) ? $format['gradientEndG'] : 0;
        $gradientEndB = isset($format['gradientEndB']) ? $format['gradientEndB'] : 0;
        $txtMargin = isset($format['TxtMargin']) ? $format['TxtMargin'] : 6;
        $overrideColors = isset($format['overrideColors']) ? $format['overrideColors'] : null;
        $overrideSurrounding = isset($format['overrideSurrounding']) ? $format['overrideSurrounding'] : 30;
        $InnerSurrounding = isset($format['innerSurrounding']) ? $format['innerSurrounding'] : null;
        $InnerborderR = isset($format['innerborderR']) ? $format['innerborderR'] : -1;
        $InnerborderG = isset($format['innerborderG']) ? $format['innerborderG'] : -1;
        $InnerborderB = isset($format['innerborderB']) ? $format['innerborderB'] : -1;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        if ($overrideColors != null) {
            $overrideColors = $this->validatePalette($overrideColors, $overrideSurrounding);
            $this->dataSet->saveExtendedData('Palette', $overrideColors);
        }
        $restoreShadow = $this->shadow;
        $seriesCount = $this->countDrawableSeries();
        $CurrentSerie = 0;
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                if ($surrounding != null) {
                    $borderR = $r + $surrounding;
                    $borderG = $g + $surrounding;
                    $borderB = $b + $surrounding;
                }
                if ($InnerSurrounding != null) {
                    $InnerborderR = $r + $InnerSurrounding;
                    $InnerborderG = $g + $InnerSurrounding;
                    $InnerborderB = $b + $InnerSurrounding;
                }
                if ($InnerborderR == -1) {
                    $InnerColor = null;
                } else {
                    $InnerColor = [
                        'r' => $InnerborderR,
                        'g' => $InnerborderG,
                        'b' => $InnerborderB
                    ];
                }
                $color = [
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                    'alpha' => $alpha,
                    'borderR' => $borderR,
                    'borderG' => $borderG,
                    'borderB' => $borderB
                ];
                $axisId = $serie['axis'];
                $mode = $data['axis'][$axisId]['display'];
                $format = $data['axis'][$axisId]['format'];
                $unit = $data['axis'][$axisId]['unit'];
                if (isset($serie['description'])) {
                    $serieDescription = $serie['description'];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisId' => $serie['axis']]
                );
                if ($floating0Value != null) {
                    $yZero = $this->scaleComputeY(
                        $floating0Value,
                        ['axisId' => $serie['axis']]
                    );
                } else {
                    $yZero = $this->scaleComputeY(0, ['axisId' => $serie['axis']]);
                }
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($yZero > $this->graphAreaY2 - 1) {
                        $yZero = $this->graphAreaY2 - 1;
                    }
                    if ($yZero < $this->graphAreaY1 + 1) {
                        $yZero = $this->graphAreaY1 + 1;
                    }
                    if ($xDivs == 0) {
                        $xStep = 0;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    if ($aroundZero) {
                        $y1 = $yZero;
                    } else {
                        $y1 = $this->graphAreaY2 - 1;
                    }
                    if ($xDivs == 0) {
                        $xSize = ($this->graphAreaX2 - $this->graphAreaX1) / ($seriesCount + $Interleave);
                    } else {
                        $xSize = ($xStep / ($seriesCount + $Interleave));
                    }
                    $xOffset = -($xSize * $seriesCount) / 2 + $CurrentSerie * $xSize;
                    if ($x + $xOffset <= $this->graphAreaX1) {
                        $xOffset = $this->graphAreaX1 - $x + 1;
                    }
                    $this->dataSet->data['series'][$serieName]['xOffset'] = $xOffset + $xSize / 2;
                    if ($rounded || $borderR != -1) {
                        $xSpace = 1;
                    } else {
                        $xSpace = 0;
                    }
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $id = 0;
                    foreach ($posArray as $key => $y2) {
                        if ($floating0Serie != null) {
                            if (isset($data['series'][$floating0Serie]['data'][$key])) {
                                $value = $data['series'][$floating0Serie]['data'][$key];
                            } else {
                                $value = 0;
                            }
                            $yZero = $this->scaleComputeY($value, ['axisId' => $serie['axis']]);
                            if ($yZero > $this->graphAreaY2 - 1) {
                                $yZero = $this->graphAreaY2 - 1;
                            }
                            if ($yZero < $this->graphAreaY1 + 1) {
                                $yZero = $this->graphAreaY1 + 1;
                            }
                            if ($aroundZero) {
                                $y1 = $yZero;
                            } else {
                                $y1 = $this->graphAreaY2 - 1;
                            }
                        }
                        if ($overrideColors != null) {
                            if (isset($overrideColors[$id])) {
                                $color = [
                                    'r' => $overrideColors[$id]['r'],
                                    'g' => $overrideColors[$id]['g'],
                                    'b' => $overrideColors[$id]['b'],
                                    'alpha' => $overrideColors[$id]['alpha'],
                                    'borderR' => $overrideColors[$id]['borderR'],
                                    'borderG' => $overrideColors[$id]['borderG'],
                                    'borderB' => $overrideColors[$id]['borderB']
                                ];
                            } else {
                                $color = $this->getRandomColor();
                            }
                        }
                        if ($y2 != Constant::VOID) {
                            $barHeight = $y1 - $y2;
                            if ($serie['data'][$key] == 0) {
                                $this->drawLine(
                                    $x + $xOffset + $xSpace,
                                    $y1,
                                    $x + $xOffset + $xSize - $xSpace,
                                    $y1,
                                    $color
                                );
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                        'RECT',
                                        sprintf(
                                            '%s,%s,%s,%s',
                                            floor($x + $xOffset + $xSpace),
                                            floor($y1 - 1),
                                            floor($x + $xOffset + $xSize - $xSpace),
                                            floor($y1 + 1)
                                        ),
                                        $this->toHTMLColor($r, $g, $b),
                                        $serieDescription,
                                        $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                    );
                                }
                            } else {
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                        'RECT',
                                        sprintf(
                                            '%s,%s,%s,%s',
                                            floor($x + $xOffset + $xSpace),
                                            floor($y1),
                                            floor($x + $xOffset + $xSize - $xSpace),
                                            floor($y2)
                                        ),
                                        $this->toHTMLColor($r, $g, $b),
                                        $serieDescription,
                                        $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                    );
                                }
                                if ($rounded) {
                                    $this->drawRoundedFilledRectangle(
                                        $x + $xOffset + $xSpace,
                                        $y1,
                                        $x + $xOffset + $xSize - $xSpace,
                                        $y2,
                                        $roundRadius,
                                        $color
                                    );
                                } else {
                                    $this->drawFilledRectangle(
                                        $x + $xOffset + $xSpace,
                                        $y1,
                                        $x + $xOffset + $xSize - $xSpace,
                                        $y2,
                                        $color
                                    );
                                    if ($InnerColor != null) {
                                        $this->drawRectangle(
                                            $x + $xOffset + $xSpace + 1,
                                            min($y1, $y2) + 1,
                                            $x + $xOffset + $xSize - $xSpace - 1,
                                            max($y1, $y2) - 1,
                                            $InnerColor
                                        );
                                    }
                                    if ($gradient) {
                                        $this->shadow = false;
                                        if ($gradientMode == Constant::GRADIENT_SIMPLE) {
                                            if ($serie['data'][$key] >= 0) {
                                                $gradienColor = [
                                                    'StartR' => $gradientStartR,
                                                    'StartG' => $gradientStartG,
                                                    'StartB' => $gradientStartB,
                                                    'endR' => $gradientEndR,
                                                    'endG' => $gradientEndG,
                                                    'endB' => $gradientEndB,
                                                    'alpha' => $gradientalpha
                                                ];
                                            } else {
                                                $gradienColor = [
                                                    'StartR' => $gradientEndR,
                                                    'StartG' => $gradientEndG,
                                                    'StartB' => $gradientEndB,
                                                    'endR' => $gradientStartR,
                                                    'endG' => $gradientStartG,
                                                    'endB' => $gradientStartB,
                                                    'alpha' => $gradientalpha
                                                ];
                                            }
                                            $this->drawGradientArea(
                                                $x + $xOffset + $xSpace,
                                                $y1,
                                                $x + $xOffset + $xSize - $xSpace,
                                                $y2,
                                                Constant::DIRECTION_VERTICAL,
                                                $gradienColor
                                            );
                                        } elseif ($gradientMode == Constant::GRADIENT_EFFECT_CAN) {
                                            $gradienColor1 = [
                                                'StartR' => $gradientEndR,
                                                'StartG' => $gradientEndG,
                                                'StartB' => $gradientEndB,
                                                'endR' => $gradientStartR,
                                                'endG' => $gradientStartG,
                                                'endB' => $gradientStartB,
                                                'alpha' => $gradientalpha
                                            ];
                                            $gradienColor2 = [
                                                'StartR' => $gradientStartR,
                                                'StartG' => $gradientStartG,
                                                'StartB' => $gradientStartB,
                                                'endR' => $gradientEndR,
                                                'endG' => $gradientEndG,
                                                'endB' => $gradientEndB,
                                                'alpha' => $gradientalpha
                                            ];
                                            $xSpan = floor($xSize / 3);
                                            $this->drawGradientArea(
                                                $x + $xOffset + $xSpace,
                                                $y1,
                                                $x + $xOffset + $xSpan - $xSpace,
                                                $y2,
                                                Constant::DIRECTION_HORIZONTAL,
                                                $gradienColor1
                                            );
                                            $this->drawGradientArea(
                                                $x + $xOffset + $xSpan + $xSpace,
                                                $y1,
                                                $x + $xOffset + $xSize - $xSpace,
                                                $y2,
                                                Constant::DIRECTION_HORIZONTAL,
                                                $gradienColor2
                                            );
                                        }
                                        $this->shadow = $restoreShadow;
                                    }
                                }
                                if ($draw0Line) {
                                    $line0Color = ['r' => 0, 'g' => 0, 'b' => 0, 'alpha' => 20];
                                    if (abs($y1 - $y2) > 3) {
                                        $line0Width = 3;
                                    } else {
                                        $line0Width = 1;
                                    }
                                    if ($y1 - $y2 < 0) {
                                        $line0Width = -$line0Width;
                                    }
                                    $this->drawFilledRectangle(
                                        $x + $xOffset + $xSpace,
                                        floor($y1),
                                        $x + $xOffset + $xSize - $xSpace,
                                        floor($y1) - $line0Width,
                                        $line0Color
                                    );
                                    $this->drawLine(
                                        $x + $xOffset + $xSpace,
                                        floor($y1),
                                        $x + $xOffset + $xSize - $xSpace,
                                        floor($y1),
                                        $line0Color
                                    );
                                }
                            }
                            if ($displayValues && $serie['data'][$key] != Constant::VOID) {
                                if ($displayShadow) {
                                    $this->shadow = true;
                                }
                                $caption = $this->scaleFormat($serie['data'][$key], $mode, $format, $unit);
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 90, $caption);
                                $txtHeight = $txtPos[0]['y'] - $txtPos[1]['y'] + $txtMargin;
                                if ($displayPos == Constant::LABEL_POS_INSIDE && abs($txtHeight) < abs($barHeight)) {
                                    $CenterX = (($x + $xOffset + $xSize - $xSpace) - ($x + $xOffset + $xSpace)) / 2 + $x + $xOffset + $xSpace;
                                    $CenterY = ($y2 - $y1) / 2 + $y1;
                                    $this->drawText(
                                        $CenterX,
                                        $CenterY,
                                        $caption,
                                        [
                                            'r' => $displayR,
                                            'g' => $displayG,
                                            'b' => $displayB,
                                            'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE,
                                            'fontSize' => $displaySize,
                                            'Angle' => 90
                                        ]
                                    );
                                } else {
                                    if ($serie['data'][$key] >= 0) {
                                        $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                                        $offset = $displayOffset;
                                    } else {
                                        $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                                        $offset = -$displayOffset;
                                    }
                                    $this->drawText(
                                        $x + $xOffset + $xSize / 2,
                                        $y2 - $offset,
                                        $this->scaleFormat($serie['data'][$key], $mode, $format, $unit),
                                        [
                                            'r' => $displayR,
                                            'g' => $displayG,
                                            'b' => $displayB,
                                            'align' => $align,
                                            'fontSize' => $displaySize
                                        ]
                                    );
                                }
                                $this->shadow = $restoreShadow;
                            }
                        }
                        $x = $x + $xStep;
                        $id++;
                    }
                } else {
                    if ($yZero < $this->graphAreaX1 + 1) {
                        $yZero = $this->graphAreaX1 + 1;
                    }
                    if ($yZero > $this->graphAreaX2 - 1) {
                        $yZero = $this->graphAreaX2 - 1;
                    }
                    if ($xDivs == 0) {
                        $yStep = 0;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    if ($aroundZero) {
                        $x1 = $yZero;
                    } else {
                        $x1 = $this->graphAreaX1 + 1;
                    }
                    if ($xDivs == 0) {
                        $ySize = ($this->graphAreaY2 - $this->graphAreaY1) / ($seriesCount + $Interleave);
                    } else {
                        $ySize = ($yStep / ($seriesCount + $Interleave));
                    }
                    $yOffset = -($ySize * $seriesCount) / 2 + $CurrentSerie * $ySize;
                    if ($y + $yOffset <= $this->graphAreaY1) {
                        $yOffset = $this->graphAreaY1 - $y + 1;
                    }
                    $this->dataSet->data['series'][$serieName]['xOffset'] = $yOffset + $ySize / 2;
                    if ($rounded || $borderR != -1) {
                        $ySpace = 1;
                    } else {
                        $ySpace = 0;
                    }
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $id = 0;
                    foreach ($posArray as $key => $x2) {
                        if ($floating0Serie != null) {
                            if (isset($data['series'][$floating0Serie]['data'][$key])) {
                                $value = $data['series'][$floating0Serie]['data'][$key];
                            } else {
                                $value = 0;
                            }
                            $yZero = $this->scaleComputeY($value, ['axisId' => $serie['axis']]);
                            if ($yZero < $this->graphAreaX1 + 1) {
                                $yZero = $this->graphAreaX1 + 1;
                            }
                            if ($yZero > $this->graphAreaX2 - 1) {
                                $yZero = $this->graphAreaX2 - 1;
                            }
                            if ($aroundZero) {
                                $x1 = $yZero;
                            } else {
                                $x1 = $this->graphAreaX1 + 1;
                            }
                        }
                        if ($overrideColors != null) {
                            if (isset($overrideColors[$id])) {
                                $color = [
                                    'r' => $overrideColors[$id]['r'],
                                    'g' => $overrideColors[$id]['g'],
                                    'b' => $overrideColors[$id]['b'],
                                    'alpha' => $overrideColors[$id]['alpha'],
                                    'borderR' => $overrideColors[$id]['borderR'],
                                    'borderG' => $overrideColors[$id]['borderG'],
                                    'borderB' => $overrideColors[$id]['borderB']
                                ];
                            } else {
                                $color = $this->getRandomColor();
                            }
                        }
                        if ($x2 != Constant::VOID) {
                            $barWidth = $x2 - $x1;
                            if ($serie['data'][$key] == 0) {
                                $this->drawLine(
                                    $x1,
                                    $y + $yOffset + $ySpace,
                                    $x1,
                                    $y + $yOffset + $ySize - $ySpace,
                                    $color
                                );
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                        'RECT',
                                        sprintf(
                                            '%s,%s,%s,%s',
                                            floor($x1 - 1),
                                            floor($y + $yOffset + $ySpace),
                                            floor($x1 + 1),
                                            floor($y + $yOffset + $ySize - $ySpace)
                                        ),
                                        $this->toHTMLColor($r, $g, $b),
                                        $serieDescription,
                                        $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                    );
                                }
                            } else {
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                        'RECT',
                                        sprintf(
                                            '%s,%s,%s,%s',
                                            floor($x1),
                                            floor($y + $yOffset + $ySpace),
                                            floor($x2),
                                            floor($y + $yOffset + $ySize - $ySpace)
                                        ),
                                        $this->toHTMLColor($r, $g, $b),
                                        $serieDescription,
                                        $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                    );
                                }
                                if ($rounded) {
                                    $this->drawRoundedFilledRectangle(
                                        $x1 + 1,
                                        $y + $yOffset + $ySpace,
                                        $x2,
                                        $y + $yOffset + $ySize - $ySpace,
                                        $roundRadius,
                                        $color
                                    );
                                } else {
                                    $this->drawFilledRectangle(
                                        $x1,
                                        $y + $yOffset + $ySpace,
                                        $x2,
                                        $y + $yOffset + $ySize - $ySpace,
                                        $color
                                    );
                                    if ($InnerColor != null) {
                                        $this->drawRectangle(
                                            min($x1, $x2) + 1,
                                            $y + $yOffset + $ySpace + 1,
                                            max($x1, $x2) - 1,
                                            $y + $yOffset + $ySize - $ySpace - 1,
                                            $InnerColor
                                        );
                                    }
                                    if ($gradient) {
                                        $this->shadow = false;
                                        if ($gradientMode == Constant::GRADIENT_SIMPLE) {
                                            if ($serie['data'][$key] >= 0) {
                                                $gradienColor = [
                                                    'StartR' => $gradientStartR,
                                                    'StartG' => $gradientStartG,
                                                    'StartB' => $gradientStartB,
                                                    'endR' => $gradientEndR,
                                                    'endG' => $gradientEndG,
                                                    'endB' => $gradientEndB,
                                                    'alpha' => $gradientalpha
                                                ];
                                            } else {
                                                $gradienColor = [
                                                    'StartR' => $gradientEndR,
                                                    'StartG' => $gradientEndG,
                                                    'StartB' => $gradientEndB,
                                                    'endR' => $gradientStartR,
                                                    'endG' => $gradientStartG,
                                                    'endB' => $gradientStartB,
                                                    'alpha' => $gradientalpha
                                                ];
                                            }
                                            $this->drawGradientArea(
                                                $x1,
                                                $y + $yOffset + $ySpace,
                                                $x2,
                                                $y + $yOffset + $ySize - $ySpace,
                                                Constant::DIRECTION_HORIZONTAL,
                                                $gradienColor
                                            );
                                        } elseif ($gradientMode == Constant::GRADIENT_EFFECT_CAN) {
                                            $gradienColor1 = [
                                                'StartR' => $gradientEndR,
                                                'StartG' => $gradientEndG,
                                                'StartB' => $gradientEndB,
                                                'endR' => $gradientStartR,
                                                'endG' => $gradientStartG,
                                                'endB' => $gradientStartB,
                                                'alpha' => $gradientalpha
                                            ];
                                            $gradienColor2 = [
                                                'StartR' => $gradientStartR,
                                                'StartG' => $gradientStartG,
                                                'StartB' => $gradientStartB,
                                                'endR' => $gradientEndR,
                                                'endG' => $gradientEndG,
                                                'endB' => $gradientEndB,
                                                'alpha' => $gradientalpha
                                            ];
                                            $ySpan = floor($ySize / 3);
                                            $this->drawGradientArea(
                                                $x1,
                                                $y + $yOffset + $ySpace,
                                                $x2,
                                                $y + $yOffset + $ySpan - $ySpace,
                                                Constant::DIRECTION_VERTICAL,
                                                $gradienColor1
                                            );
                                            $this->drawGradientArea(
                                                $x1,
                                                $y + $yOffset + $ySpan,
                                                $x2,
                                                $y + $yOffset + $ySize - $ySpace,
                                                Constant::DIRECTION_VERTICAL,
                                                $gradienColor2
                                            );
                                        }
                                        $this->shadow = $restoreShadow;
                                    }
                                }
                                if ($draw0Line) {
                                    $line0Color = ['r' => 0, 'g' => 0, 'b' => 0, 'alpha' => 20];
                                    if (abs($x1 - $x2) > 3) {
                                        $line0Width = 3;
                                    } else {
                                        $line0Width = 1;
                                    }
                                    if ($x2 - $x1 < 0) {
                                        $line0Width = -$line0Width;
                                    }
                                    $this->drawFilledRectangle(
                                        floor($x1),
                                        $y + $yOffset + $ySpace,
                                        floor($x1) + $line0Width,
                                        $y + $yOffset + $ySize - $ySpace,
                                        $line0Color
                                    );
                                    $this->drawLine(
                                        floor($x1),
                                        $y + $yOffset + $ySpace,
                                        floor($x1),
                                        $y + $yOffset + $ySize - $ySpace,
                                        $line0Color
                                    );
                                }
                            }
                            if ($displayValues && $serie['data'][$key] != Constant::VOID) {
                                if ($displayShadow) {
                                    $this->shadow = true;
                                }
                                $caption = $this->scaleFormat($serie['data'][$key], $mode, $format, $unit);
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 0, $caption);
                                $txtWidth = $txtPos[1]['x'] - $txtPos[0]['x'] + $txtMargin;
                                if ($displayPos == Constant::LABEL_POS_INSIDE && abs($txtWidth) < abs($barWidth)) {
                                    $CenterX = ($x2 - $x1) / 2 + $x1;
                                    $CenterY = (($y + $yOffset + $ySize - $ySpace) - ($y + $yOffset + $ySpace)) / 2 + ($y + $yOffset + $ySpace);
                                    $this->drawText(
                                        $CenterX,
                                        $CenterY,
                                        $caption,
                                        [
                                            'r' => $displayR,
                                            'g' => $displayG,
                                            'b' => $displayB,
                                            'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE,
                                            'fontSize' => $displaySize
                                        ]
                                    );
                                } else {
                                    if ($serie['data'][$key] >= 0) {
                                        $align = Constant::TEXT_ALIGN_MIDDLELEFT;
                                        $offset = $displayOffset;
                                    } else {
                                        $align = Constant::TEXT_ALIGN_MIDDLERIGHT;
                                        $offset = -$displayOffset;
                                    }
                                    $this->drawText(
                                        $x2 + $offset,
                                        $y + $yOffset + $ySize / 2,
                                        $caption,
                                        [
                                            'r' => $displayR,
                                            'g' => $displayG,
                                            'b' => $displayB,
                                            'align' => $align,
                                            'fontSize' => $displaySize
                                        ]
                                    );
                                }
                                $this->shadow = $restoreShadow;
                            }
                        }
                        $y = $y + $yStep;
                        $id++;
                    }
                }
                $CurrentSerie++;
            }
        }
    }

    /**
     * Draw a bar chart
     *
     * @param array $format
     */
    public function drawStackedBarChart(array $format = []) {
        $displayValues = isset($format['displayValues']) ? $format['displayValues'] : false;
        $displayOrientation = isset($format['displayOrientation']) ? $format['displayOrientation'] : Constant::ORIENTATION_AUTO;
        $displayRound = isset($format['displayRound']) ? $format['displayRound'] : 0;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $displayFont = isset($format['displayFont']) ? $format['displayFont'] : $this->fontName;
        $displaySize = isset($format['displaySize']) ? $format['displaySize'] : $this->fontSize;
        $displayR = isset($format['displayR']) ? $format['displayR'] : 0;
        $displayG = isset($format['displayG']) ? $format['displayG'] : 0;
        $displayB = isset($format['displayB']) ? $format['displayB'] : 0;
        $Interleave = isset($format['interleave']) ? $format['interleave'] : .5;
        $rounded = isset($format['rounded']) ? $format['rounded'] : false;
        $roundRadius = isset($format['roundRadius']) ? $format['roundRadius'] : 4;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        $borderR = isset($format['borderR']) ? $format['borderR'] : -1;
        $borderG = isset($format['borderG']) ? $format['borderG'] : -1;
        $borderB = isset($format['borderB']) ? $format['borderB'] : -1;
        $gradient = isset($format['gradient']) ? $format['gradient'] : false;
        $gradientMode = isset($format['gradientMode']) ? $format['gradientMode'] : Constant::GRADIENT_SIMPLE;
        $gradientalpha = isset($format['gradientalpha']) ? $format['gradientalpha'] : 20;
        $gradientStartR = isset($format['gradientStartR']) ? $format['gradientStartR'] : 255;
        $gradientStartG = isset($format['gradientStartG']) ? $format['gradientStartG'] : 255;
        $gradientStartB = isset($format['gradientStartB']) ? $format['gradientStartB'] : 255;
        $gradientEndR = isset($format['gradientEndR']) ? $format['gradientEndR'] : 0;
        $gradientEndG = isset($format['gradientEndG']) ? $format['gradientEndG'] : 0;
        $gradientEndB = isset($format['gradientEndB']) ? $format['gradientEndB'] : 0;
        $InnerSurrounding = isset($format['innerSurrounding']) ? $format['innerSurrounding'] : null;
        $InnerborderR = isset($format['innerborderR']) ? $format['innerborderR'] : -1;
        $InnerborderG = isset($format['innerborderG']) ? $format['innerborderG'] : -1;
        $InnerborderB = isset($format['innerborderB']) ? $format['innerborderB'] : -1;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;
        $fontFactor = isset($format['fontFactor']) ? $format['fontFactor'] : 8;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_STACKED;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        $restoreShadow = $this->shadow;
        $lastX = [];
        $lastY = [];
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = 255;
                    $displayG = 255;
                    $displayB = 255;
                }
                if ($surrounding != null) {
                    $borderR = $r + $surrounding;
                    $borderG = $g + $surrounding;
                    $borderB = $b + $surrounding;
                }
                if ($InnerSurrounding != null) {
                    $InnerborderR = $r + $InnerSurrounding;
                    $InnerborderG = $g + $InnerSurrounding;
                    $InnerborderB = $b + $InnerSurrounding;
                }
                if ($InnerborderR == -1) {
                    $InnerColor = null;
                } else {
                    $InnerColor = [
                        'r' => $InnerborderR,
                        'g' => $InnerborderG,
                        'b' => $InnerborderB
                    ];
                }
                $axisId = $serie['axis'];
                $mode = $data['axis'][$axisId]['display'];
                $format = $data['axis'][$axisId]['format'];
                $unit = $data['axis'][$axisId]['unit'];
                if (isset($serie['description'])) {
                    $serieDescription = $serie['description'];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisId' => $serie['axis']],
                    true
                );
                $yZero = $this->scaleComputeY(0, ['axisId' => $serie['axis']]);
                $this->dataSet->data['series'][$serieName]['xOffset'] = 0;
                $color = [
                    'TransCorner' => true,
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                    'alpha' => $alpha,
                    'borderR' => $borderR,
                    'borderG' => $borderG,
                    'borderB' => $borderB
                ];
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($yZero > $this->graphAreaY2 - 1) {
                        $yZero = $this->graphAreaY2 - 1;
                    }
                    if ($yZero > $this->graphAreaY2 - 1) {
                        $yZero = $this->graphAreaY2 - 1;
                    }
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    $xSize = ($xStep / (1 + $Interleave));
                    $xOffset = -($xSize / 2);
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    foreach ($posArray as $key => $height) {
                        if ($height != Constant::VOID && $serie['data'][$key] != 0) {
                            if ($serie['data'][$key] > 0) {
                                $pos = '+';
                            } else {
                                $pos = '-';
                            }
                            if (!isset($lastY[$key])) {
                                $lastY[$key] = [];
                            }
                            if (!isset($lastY[$key][$pos])) {
                                $lastY[$key][$pos] = $yZero;
                            }
                            $y1 = $lastY[$key][$pos];
                            $y2 = $y1 - $height;
                            if (($rounded || $borderR != -1) && ($pos == '+' && $y1 != $yZero)) {
                                $ySpaceUp = 1;
                            } else {
                                $ySpaceUp = 0;
                            }
                            if (($rounded || $borderR != -1) && ($pos == '-' && $y1 != $yZero)) {
                                $ySpaceDown = 1;
                            } else {
                                $ySpaceDown = 0;
                            }
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                    'RECT',
                                    sprintf(
                                        '%s,%s,%s,%s',
                                        floor($x + $xOffset),
                                        floor($y1 - $ySpaceUp + $ySpaceDown),
                                        floor($x + $xOffset + $xSize),
                                        floor($y2)
                                    ),
                                    $this->toHTMLColor($r, $g, $b),
                                    $serieDescription,
                                    $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                );
                            }
                            if ($rounded) {
                                $this->drawRoundedFilledRectangle(
                                    $x + $xOffset,
                                    $y1 - $ySpaceUp + $ySpaceDown,
                                    $x + $xOffset + $xSize,
                                    $y2,
                                    $roundRadius,
                                    $color
                                );
                            } else {
                                $this->drawFilledRectangle(
                                    $x + $xOffset,
                                    $y1 - $ySpaceUp + $ySpaceDown,
                                    $x + $xOffset + $xSize,
                                    $y2,
                                    $color
                                );
                                if ($InnerColor != null) {
                                    $restoreShadow = $this->shadow;
                                    $this->shadow = false;
                                    $this->drawRectangle(
                                        min($x + $xOffset + 1, $x + $xOffset + $xSize),
                                        min($y1 - $ySpaceUp + $ySpaceDown, $y2) + 1,
                                        max($x + $xOffset + 1, $x + $xOffset + $xSize) - 1,
                                        max($y1 - $ySpaceUp + $ySpaceDown, $y2) - 1,
                                        $InnerColor
                                    );
                                    $this->shadow = $restoreShadow;
                                }
                                if ($gradient) {
                                    $this->shadow = false;
                                    if ($gradientMode == Constant::GRADIENT_SIMPLE) {
                                        $gradientColor = [
                                            'StartR' => $gradientStartR,
                                            'StartG' => $gradientStartG,
                                            'StartB' => $gradientStartB,
                                            'endR' => $gradientEndR,
                                            'endG' => $gradientEndG,
                                            'endB' => $gradientEndB,
                                            'alpha' => $gradientalpha
                                        ];
                                        $this->drawGradientArea(
                                            $x + $xOffset,
                                            $y1 - 1 - $ySpaceUp + $ySpaceDown,
                                            $x + $xOffset + $xSize,
                                            $y2 + 1,
                                            Constant::DIRECTION_VERTICAL,
                                            $gradientColor
                                        );
                                    } elseif ($gradientMode == Constant::GRADIENT_EFFECT_CAN) {
                                        $gradientColor1 = [
                                            'StartR' => $gradientEndR,
                                            'StartG' => $gradientEndG,
                                            'StartB' => $gradientEndB,
                                            'endR' => $gradientStartR,
                                            'endG' => $gradientStartG,
                                            'endB' => $gradientStartB,
                                            'alpha' => $gradientalpha
                                        ];
                                        $gradientColor2 = [
                                            'StartR' => $gradientStartR,
                                            'StartG' => $gradientStartG,
                                            'StartB' => $gradientStartB,
                                            'endR' => $gradientEndR,
                                            'endG' => $gradientEndG,
                                            'endB' => $gradientEndB,
                                            'alpha' => $gradientalpha
                                        ];
                                        $xSpan = floor($xSize / 3);
                                        $this->drawGradientArea(
                                            $x + $xOffset - .5,
                                            $y1 - .5 - $ySpaceUp + $ySpaceDown,
                                            $x + $xOffset + $xSpan,
                                            $y2 + .5,
                                            Constant::DIRECTION_HORIZONTAL,
                                            $gradientColor1
                                        );
                                        $this->drawGradientArea(
                                            $x + $xSpan + $xOffset - .5,
                                            $y1 - .5 - $ySpaceUp + $ySpaceDown,
                                            $x + $xOffset + $xSize,
                                            $y2 + .5,
                                            Constant::DIRECTION_HORIZONTAL,
                                            $gradientColor2
                                        );
                                    }
                                    $this->shadow = $restoreShadow;
                                }
                            }
                            if ($displayValues) {
                                $barHeight = abs($y2 - $y1) - 2;
                                $barWidth = $xSize + ($xOffset / 2) - $fontFactor;
                                $caption = $this->scaleFormat(
                                    round($serie['data'][$key], $displayRound),
                                    $mode,
                                    $format,
                                    $unit
                                );
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 0, $caption);
                                $txtHeight = abs($txtPos[2]['y'] - $txtPos[0]['y']);
                                $txtWidth = abs($txtPos[1]['x'] - $txtPos[0]['x']);
                                $xCenter = (($x + $xOffset + $xSize) - ($x + $xOffset)) / 2 + $x + $xOffset;
                                $yCenter = (($y2) - ($y1 - $ySpaceUp + $ySpaceDown)) / 2 + $y1 - $ySpaceUp + $ySpaceDown;
                                $done = false;
                                if ($displayOrientation == Constant::ORIENTATION_HORIZONTAL || $displayOrientation == Constant::ORIENTATION_AUTO
                                ) {
                                    if ($txtHeight < $barHeight && $txtWidth < $barWidth) {
                                        $this->drawText(
                                            $xCenter,
                                            $yCenter,
                                            $this->scaleFormat(
                                                $serie['data'][$key],
                                                $mode,
                                                $format,
                                                $unit
                                            ),
                                            [
                                                'r' => $displayR,
                                                'g' => $displayG,
                                                'b' => $displayB,
                                                'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE,
                                                'fontSize' => $displaySize,
                                                'fontName' => $displayFont
                                            ]
                                        );
                                        $done = true;
                                    }
                                }
                                if ($displayOrientation == Constant::ORIENTATION_VERTICAL || ($displayOrientation == Constant::ORIENTATION_AUTO && !$done)
                                ) {
                                    if ($txtHeight < $barWidth && $txtWidth < $barHeight) {
                                        $this->drawText(
                                            $xCenter,
                                            $yCenter,
                                            $this->scaleFormat(
                                                $serie['data'][$key],
                                                $mode,
                                                $format,
                                                $unit
                                            ),
                                            [
                                                'r' => $displayR,
                                                'g' => $displayG,
                                                'b' => $displayB,
                                                'Angle' => 90,
                                                'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE,
                                                'fontSize' => $displaySize,
                                                'fontName' => $displayFont
                                            ]
                                        );
                                    }
                                }
                            }
                            $lastY[$key][$pos] = $y2;
                        }
                        $x = $x + $xStep;
                    }
                } else {
                    if ($yZero < $this->graphAreaX1 + 1) {
                        $yZero = $this->graphAreaX1 + 1;
                    }
                    if ($yZero > $this->graphAreaX2 - 1) {
                        $yZero = $this->graphAreaX2 - 1;
                    }
                    if ($xDivs == 0) {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    $ySize = $yStep / (1 + $Interleave);
                    $yOffset = -($ySize / 2);
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    foreach ($posArray as $key => $width) {
                        if ($width != Constant::VOID && $serie['data'][$key] != 0) {
                            if ($serie['data'][$key] > 0) {
                                $pos = '+';
                            } else {
                                $pos = '-';
                            }
                            if (!isset($lastX[$key])) {
                                $lastX[$key] = [];
                            }
                            if (!isset($lastX[$key][$pos])) {
                                $lastX[$key][$pos] = $yZero;
                            }
                            $x1 = $lastX[$key][$pos];
                            $x2 = $x1 + $width;
                            if (($rounded || $borderR != -1) && ($pos == '+' && $x1 != $yZero)) {
                                $xSpaceLeft = 2;
                            } else {
                                $xSpaceLeft = 0;
                            }
                            if (($rounded || $borderR != -1) && ($pos == '-' && $x1 != $yZero)) {
                                $xSpaceRight = 2;
                            } else {
                                $xSpaceRight = 0;
                            }
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                    'RECT',
                                    sprintf(
                                        '%s,%s,%s,%s',
                                        floor($x1 + $xSpaceLeft),
                                        floor($y + $yOffset),
                                        floor($x2 - $xSpaceRight),
                                        floor($y + $yOffset + $ySize)
                                    ),
                                    $this->toHTMLColor($r, $g, $b),
                                    $serieDescription,
                                    $this->scaleFormat($serie['data'][$key], $mode, $format, $unit)
                                );
                            }
                            if ($rounded) {
                                $this->drawRoundedFilledRectangle(
                                    $x1 + $xSpaceLeft,
                                    $y + $yOffset,
                                    $x2 - $xSpaceRight,
                                    $y + $yOffset + $ySize,
                                    $roundRadius,
                                    $color
                                );
                            } else {
                                $this->drawFilledRectangle(
                                    $x1 + $xSpaceLeft,
                                    $y + $yOffset,
                                    $x2 - $xSpaceRight,
                                    $y + $yOffset + $ySize,
                                    $color
                                );
                                if ($InnerColor != null) {
                                    $restoreShadow = $this->shadow;
                                    $this->shadow = false;
                                    $this->drawRectangle(
                                        min($x1 + $xSpaceLeft, $x2 - $xSpaceRight) + 1,
                                        min($y + $yOffset, $y + $yOffset + $ySize) + 1,
                                        max($x1 + $xSpaceLeft, $x2 - $xSpaceRight) - 1,
                                        max($y + $yOffset, $y + $yOffset + $ySize) - 1,
                                        $InnerColor
                                    );
                                    $this->shadow = $restoreShadow;
                                }
                                if ($gradient) {
                                    $this->shadow = false;
                                    if ($gradientMode == Constant::GRADIENT_SIMPLE) {
                                        $gradientColor = [
                                            'StartR' => $gradientStartR,
                                            'StartG' => $gradientStartG,
                                            'StartB' => $gradientStartB,
                                            'endR' => $gradientEndR,
                                            'endG' => $gradientEndG,
                                            'endB' => $gradientEndB,
                                            'alpha' => $gradientalpha
                                        ];
                                        $this->drawGradientArea(
                                            $x1 + $xSpaceLeft,
                                            $y + $yOffset,
                                            $x2 - $xSpaceRight,
                                            $y + $yOffset + $ySize,
                                            Constant::DIRECTION_HORIZONTAL,
                                            $gradientColor
                                        );
                                    } elseif ($gradientMode == Constant::GRADIENT_EFFECT_CAN) {
                                        $gradientColor1 = [
                                            'StartR' => $gradientEndR,
                                            'StartG' => $gradientEndG,
                                            'StartB' => $gradientEndB,
                                            'endR' => $gradientStartR,
                                            'endG' => $gradientStartG,
                                            'endB' => $gradientStartB,
                                            'alpha' => $gradientalpha
                                        ];
                                        $gradientColor2 = [
                                            'StartR' => $gradientStartR,
                                            'StartG' => $gradientStartG,
                                            'StartB' => $gradientStartB,
                                            'endR' => $gradientEndR,
                                            'endG' => $gradientEndG,
                                            'endB' => $gradientEndB,
                                            'alpha' => $gradientalpha
                                        ];
                                        $ySpan = floor($ySize / 3);
                                        $this->drawGradientArea(
                                            $x1 + $xSpaceLeft,
                                            $y + $yOffset,
                                            $x2 - $xSpaceRight,
                                            $y + $yOffset + $ySpan,
                                            Constant::DIRECTION_VERTICAL,
                                            $gradientColor1
                                        );
                                        $this->drawGradientArea(
                                            $x1 + $xSpaceLeft,
                                            $y + $yOffset + $ySpan,
                                            $x2 - $xSpaceRight,
                                            $y + $yOffset + $ySize,
                                            Constant::DIRECTION_VERTICAL,
                                            $gradientColor2
                                        );
                                    }
                                    $this->shadow = $restoreShadow;
                                }
                            }
                            if ($displayValues) {
                                $barWidth = abs($x2 - $x1) - $fontFactor;
                                $barHeight = $ySize + ($yOffset / 2) - $fontFactor / 2;
                                $caption = $this->scaleFormat(
                                    round($serie['data'][$key], $displayRound),
                                    $mode,
                                    $format,
                                    $unit
                                );
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 0, $caption);
                                $txtHeight = abs($txtPos[2]['y'] - $txtPos[0]['y']);
                                $txtWidth = abs($txtPos[1]['x'] - $txtPos[0]['x']);
                                $xCenter = ($x2 - $x1) / 2 + $x1;
                                $yCenter = (($y + $yOffset + $ySize) - ($y + $yOffset)) / 2 + $y + $yOffset;
                                $done = false;
                                if ($displayOrientation == Constant::ORIENTATION_HORIZONTAL || $displayOrientation == Constant::ORIENTATION_AUTO
                                ) {
                                    if ($txtHeight < $barHeight && $txtWidth < $barWidth) {
                                        $this->drawText(
                                            $xCenter,
                                            $yCenter,
                                            $this->scaleFormat(
                                                $serie['data'][$key],
                                                $mode,
                                                $format,
                                                $unit
                                            ),
                                            [
                                                'r' => $displayR,
                                                'g' => $displayG,
                                                'b' => $displayB,
                                                'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE,
                                                'fontSize' => $displaySize,
                                                'fontName' => $displayFont
                                            ]
                                        );
                                        $done = true;
                                    }
                                }
                                if ($displayOrientation == Constant::ORIENTATION_VERTICAL || ($displayOrientation == Constant::ORIENTATION_AUTO && !$done)
                                ) {
                                    if ($txtHeight < $barWidth && $txtWidth < $barHeight) {
                                        $this->drawText(
                                            $xCenter,
                                            $yCenter,
                                            $this->scaleFormat(
                                                $serie['data'][$key],
                                                $mode,
                                                $format,
                                                $unit
                                            ),
                                            [
                                                'r' => $displayR,
                                                'g' => $displayG,
                                                'b' => $displayB,
                                                'Angle' => 90,
                                                'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE,
                                                'fontSize' => $displaySize,
                                                'fontName' => $displayFont
                                            ]
                                        );
                                    }
                                }
                            }
                            $lastX[$key][$pos] = $x2;
                        }
                        $y = $y + $yStep;
                    }
                }
            }
        }
    }

    /**
     * Draw a stacked area chart
     *
     * @param array $format
     */
    public function drawStackedAreaChart(array $format = []) {
        $drawLine = isset($format['drawLine']) ? $format['drawLine'] : false;
        $lineSurrounding = isset($format['lineSurrounding']) ? $format['lineSurrounding'] : null;
        $lineR = isset($format['lineR']) ? $format['lineR'] : Constant::VOID;
        $lineG = isset($format['lineG']) ? $format['lineG'] : Constant::VOID;
        $lineB = isset($format['lineB']) ? $format['lineB'] : Constant::VOID;
        $linealpha = isset($format['linealpha']) ? $format['linealpha'] : 100;
        $drawPlot = isset($format['drawPlot']) ? $format['drawPlot'] : false;
        $plotRadius = isset($format['plotRadius']) ? $format['plotRadius'] : 2;
        $plotBorder = isset($format['plotBorder']) ? $format['plotBorder'] : 1;
        $plotBorderSurrounding = isset($format['plotBorderSurrounding']) ? $format['plotBorderSurrounding'] : null;
        $plotborderR = isset($format['plotborderR']) ? $format['plotborderR'] : 0;
        $plotborderG = isset($format['plotborderG']) ? $format['plotborderG'] : 0;
        $plotborderB = isset($format['plotborderB']) ? $format['plotborderB'] : 0;
        $plotBorderalpha = isset($format['plotBorderalpha']) ? $format['plotBorderalpha'] : 50;
        $forceTransparency = isset($format['forceTransparency']) ? $format['forceTransparency'] : null;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_STACKED;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        $restoreShadow = $this->shadow;
        $this->shadow = false;
        /* Build the offset data series */
        $overallOffset = [];
        $serieOrder = [];
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $serieOrder[] = $serieName;
                foreach ($serie['data'] as $key => $value) {
                    if ($value == Constant::VOID) {
                        $value = 0;
                    }
                    if ($value >= 0) {
                        $sign = '+';
                    } else {
                        $sign = '-';
                    }
                    if (!isset($overallOffset[$key]) || !isset($overallOffset[$key][$sign])) {
                        $overallOffset[$key][$sign] = 0;
                    }
                    if ($sign == '+') {
                        $data['series'][$serieName]['data'][$key] = $value + $overallOffset[$key][$sign];
                    } else {
                        $data['series'][$serieName]['data'][$key] = $value - $overallOffset[$key][$sign];
                    }
                    $overallOffset[$key][$sign] = $overallOffset[$key][$sign] + abs($value);
                }
            }
        }
        $serieOrder = array_reverse($serieOrder);
        foreach ($serieOrder as $key => $serieName) {
            $serie = $data['series'][$serieName];
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                if ($forceTransparency != null) {
                    $alpha = $forceTransparency;
                }
                $color = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha];
                if ($lineSurrounding != null) {
                    $lineColor = [
                        'r' => $r + $lineSurrounding,
                        'g' => $g + $lineSurrounding,
                        'b' => $b + $lineSurrounding,
                        'alpha' => $alpha
                    ];
                } elseif ($lineR != Constant::VOID) {
                    $lineColor = [
                        'r' => $lineR,
                        'g' => $lineG,
                        'b' => $lineB,
                        'alpha' => $linealpha
                    ];
                } else {
                    $lineColor = $color;
                }
                if ($plotBorderSurrounding != null) {
                    $plotBorderColor = [
                        'r' => $r + $plotBorderSurrounding,
                        'g' => $g + $plotBorderSurrounding,
                        'b' => $b + $plotBorderSurrounding,
                        'alpha' => $plotBorderalpha
                    ];
                } else {
                    $plotBorderColor = [
                        'r' => $plotborderR,
                        'g' => $plotborderG,
                        'b' => $plotborderB,
                        'alpha' => $plotBorderalpha
                    ];
                }
                $axisId = $serie['axis'];
                $format = $data['axis'][$axisId]['format'];
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisId' => $serie['axis']],
                    true
                );
                $yZero = $this->scaleComputeY(0, ['axisId' => $serie['axis']]);
                $this->dataSet->data['series'][$serieName]['xOffset'] = 0;
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($yZero < $this->graphAreaY1 + 1) {
                        $yZero = $this->graphAreaY1 + 1;
                    }
                    if ($yZero > $this->graphAreaY2 - 1) {
                        $yZero = $this->graphAreaY2 - 1;
                    }
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $plots = [];
                    $plots[] = $x;
                    $plots[] = $yZero;
                    foreach ($posArray as $key => $height) {
                        if ($height != Constant::VOID) {
                            $plots[] = $x;
                            $plots[] = $yZero - $height;
                        }
                        $x = $x + $xStep;
                    }
                    $plots[] = $x - $xStep;
                    $plots[] = $yZero;
                    $this->drawPolygon($plots, $color);
                    $this->shadow = $restoreShadow;
                    if ($drawLine) {
                        for ($i = 2; $i <= count($plots) - 6; $i = $i + 2) {
                            $this->drawLine(
                                $plots[$i],
                                $plots[$i + 1],
                                $plots[$i + 2],
                                $plots[$i + 3],
                                $lineColor
                            );
                        }
                    }
                    if ($drawPlot) {
                        for ($i = 2; $i <= count($plots) - 4; $i = $i + 2) {
                            if ($plotBorder != 0) {
                                $this->drawFilledCircle(
                                    $plots[$i],
                                    $plots[$i + 1],
                                    $plotRadius + $plotBorder,
                                    $plotBorderColor
                                );
                            }
                            $this->drawFilledCircle($plots[$i], $plots[$i + 1], $plotRadius, $color);
                        }
                    }
                    $this->shadow = false;
                } elseif ($data['orientation'] == Constant::SCALE_POS_TOPBOTTOM) {
                    if ($yZero < $this->graphAreaX1 + 1) {
                        $yZero = $this->graphAreaX1 + 1;
                    }
                    if ($yZero > $this->graphAreaX2 - 1) {
                        $yZero = $this->graphAreaX2 - 1;
                    }
                    if ($xDivs == 0) {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $yStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $plots = [];
                    $plots[] = $yZero;
                    $plots[] = $y;
                    foreach ($posArray as $key => $height) {
                        if ($height != Constant::VOID) {
                            $plots[] = $yZero + $height;
                            $plots[] = $y;
                        }
                        $y = $y + $yStep;
                    }
                    $plots[] = $yZero;
                    $plots[] = $y - $yStep;
                    $this->drawPolygon($plots, $color);
                    $this->shadow = $restoreShadow;
                    if ($drawLine) {
                        for ($i = 2; $i <= count($plots) - 6; $i = $i + 2) {
                            $this->drawLine(
                                $plots[$i],
                                $plots[$i + 1],
                                $plots[$i + 2],
                                $plots[$i + 3],
                                $lineColor
                            );
                        }
                    }
                    if ($drawPlot) {
                        for ($i = 2; $i <= count($plots) - 4; $i = $i + 2) {
                            if ($plotBorder != 0) {
                                $this->drawFilledCircle(
                                    $plots[$i],
                                    $plots[$i + 1],
                                    $plotRadius + $plotBorder,
                                    $plotBorderColor
                                );
                            }
                            $this->drawFilledCircle($plots[$i], $plots[$i + 1], $plotRadius, $color);
                        }
                    }
                    $this->shadow = false;
                }
            }
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * @param array $points
     * @param array $format
     *
     * @return null|integer
     */
    public function drawPolygonChart(array $points, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $noFill = isset($format['noFill']) ? $format['noFill'] : false;
        $noBorder = isset($format['noBorder']) ? $format['noBorder'] : false;
        $borderR = isset($format['borderR']) ? $format['borderR'] : $r;
        $borderG = isset($format['borderG']) ? $format['borderG'] : $g;
        $borderB = isset($format['borderB']) ? $format['borderB'] : $b;
        $borderalpha = isset($format['borderalpha']) ? $format['borderalpha'] : $alpha / 2;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        $threshold = isset($format['Threshold']) ? $format['Threshold'] : null;
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        $restoreShadow = $this->shadow;
        $this->shadow = false;
        $allIntegers = true;
        for ($i = 0; $i <= count($points) - 2; $i = $i + 2) {
            if (Helper::getFirstDecimal($points[$i + 1]) != 0) {
                $allIntegers = false;
            }
        }
        /* Convert polygon to segments */
        $segments = [];
        for ($i = 2; $i <= count($points) - 2; $i = $i + 2) {
            $segments[] = [
                'X1' => $points[$i - 2],
                'Y1' => $points[$i - 1],
                'X2' => $points[$i],
                'Y2' => $points[$i + 1]
            ];
        }
        $segments[] = [
            'X1' => $points[$i - 2],
            'Y1' => $points[$i - 1],
            'X2' => $points[0],
            'Y2' => $points[1]
        ];
        /* Simplify straight lines */
        $result = [];
        $inHorizon = false;
        $lastX = Constant::VOID;
        foreach ($segments as $key => $pos) {
            if ($pos['y1'] != $pos['y2']) {
                if ($inHorizon) {
                    $inHorizon = false;
                    $result[] = [
                        'X1' => $lastX,
                        'Y1' => $pos['y1'],
                        'X2' => $pos['x1'],
                        'Y2' => $pos['y1']
                    ];
                }
                $result[] = [
                    'X1' => $pos['x1'],
                    'Y1' => $pos['y1'],
                    'X2' => $pos['x2'],
                    'Y2' => $pos['y2']
                ];
            } else {
                if (!$inHorizon) {
                    $inHorizon = true;
                    $lastX = $pos['x1'];
                }
            }
        }
        $segments = $result;
        /* Do we have something to draw */
        if (!count($segments)) {
            return 0;
        }
        /* For segments debugging purpose */
        //foreach($segments as $key => $pos)
        // echo $pos["x1"].",".$pos["y1"].",".$pos["x2"].",".$pos["y2"]."\r\n";
        /* Find out the min & max Y boundaries */
        $minY = Constant::OUT_OF_SIGHT;
        $maxY = Constant::OUT_OF_SIGHT;
        foreach ($segments as $key => $coords) {
            if ($minY == Constant::OUT_OF_SIGHT || $minY > min($coords['y1'], $coords['y2'])) {
                $minY = min($coords['y1'], $coords['y2']);
            }
            if ($maxY == Constant::OUT_OF_SIGHT || $maxY < max($coords['y1'], $coords['y2'])) {
                $maxY = max($coords['y1'], $coords['y2']);
            }
        }
        if ($allIntegers) {
            $yStep = 1;
        } else {
            $yStep = .5;
        }
        $minY = floor($minY);
        $maxY = floor($maxY);
        /* Scan each Y lines */
        $defaultColor = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        $debugLine = 0;
        $debugColor = $this->allocateColor($this->picture, 255, 0, 0, 100);
        $minY = floor($minY);
        $maxY = floor($maxY);
        $yStep = 1;
        if (!$noFill) {
            //if ($debugLine ) { $minY = $debugLine; $maxY = $debugLine; }
            for ($y = $minY; $y <= $maxY; $y = $y + $yStep) {
                $intersections = [];
                $lastSlope = null;
                $restoreLast = '-';
                foreach ($segments as $key => $coords) {
                    $x1 = $coords['x1'];
                    $x2 = $coords['x2'];
                    $y1 = $coords['y1'];
                    $y2 = $coords['y2'];
                    if (min($y1, $y2) <= $y && max($y1, $y2) >= $y) {
                        if ($y1 == $y2) {
                            $x = $x1;
                        } else {
                            $x = $x1 + (($y - $y1) * $x2 - ($y - $y1) * $x1) / ($y2 - $y1);
                        }
                        $x = floor($x);
                        if ($x2 == $x1) {
                            $slope = '!';
                        } else {
                            $slopeC = ($y2 - $y1) / ($x2 - $x1);
                            if ($slopeC == 0) {
                                $slope = '=';
                            } elseif ($slopeC > 0) {
                                $slope = '+';
                            } elseif ($slopeC < 0) {
                                $slope = '-';
                            }
                        }
                        if (!is_array($intersections)) {
                            $intersections[] = $x;
                        } elseif (!in_array($x, $intersections)) {
                            $intersections[] = $x;
                        } elseif (in_array($x, $intersections)) {
                            if ($y == $debugLine) {
                                echo $slope . '/' . $lastSlope . '(' . $x . ') ';
                            }
                            if ($slope == '=' && $lastSlope == '-') {
                                $intersections[] = $x;
                            }
                            if ($slope != $lastSlope && $lastSlope != '!' && $lastSlope != '=') {
                                $intersections[] = $x;
                            }
                            if ($slope != $lastSlope && $lastSlope == '!' && $slope == '+') {
                                $intersections[] = $x;
                            }
                        }
                        if (is_array($intersections) && in_array($x, $intersections) && $lastSlope == '=' && ($slope == '-')
                        ) {
                            $intersections[] = $x;
                        }
                        $lastSlope = $slope;
                    }
                }
                if ($restoreLast != '-') {
                    $intersections[] = $restoreLast;
                    echo '@' . $y . "\r\n";
                }
                if (is_array($intersections)) {
                    sort($intersections);
                    if ($y == $debugLine) {
                        print_r($intersections);
                    }
                    /* Remove null plots */
                    $result = [];
                    for ($i = 0; $i <= count($intersections) - 1; $i = $i + 2) {
                        if (isset($intersections[$i + 1])) {
                            if ($intersections[$i] != $intersections[$i + 1]) {
                                $result[] = $intersections[$i];
                                $result[] = $intersections[$i + 1];
                            }
                        }
                    }
                    if (is_array($result)) {
                        $intersections = $result;
                        $lastX = Constant::OUT_OF_SIGHT;
                        foreach ($intersections as $key => $x) {
                            if ($lastX == Constant::OUT_OF_SIGHT) {
                                $lastX = $x;
                            } elseif ($lastX != Constant::OUT_OF_SIGHT) {
                                if (Helper::getFirstDecimal($lastX) > 1) {
                                    $lastX++;
                                }
                                $color = $defaultColor;
                                if ($threshold != null) {
                                    foreach ($threshold as $key => $parameters) {
                                        if ($y <= $parameters['minX'] && $y >= $parameters['maxX']
                                        ) {
                                            if (isset($parameters['r'])) {
                                                $r = $parameters['r'];
                                            } else {
                                                $r = 0;
                                            }
                                            if (isset($parameters['g'])) {
                                                $g = $parameters['g'];
                                            } else {
                                                $g = 0;
                                            }
                                            if (isset($parameters['b'])) {
                                                $b = $parameters['b'];
                                            } else {
                                                $b = 0;
                                            }
                                            if (isset($parameters['alpha'])) {
                                                $alpha = $parameters['alpha'];
                                            } else {
                                                $alpha = 100;
                                            }
                                            $color = $this->allocateColor(
                                                $this->picture,
                                                $r,
                                                $g,
                                                $b,
                                                $alpha
                                            );
                                        }
                                    }
                                }
                                imageline($this->picture, $lastX, $y, $x, $y, $color);
                                if ($y == $debugLine) {
                                    imageline($this->picture, $lastX, $y, $x, $y, $debugColor);
                                }
                                $lastX = Constant::OUT_OF_SIGHT;
                            }
                        }
                    }
                }
            }
        }
        /* Draw the polygon border, if required */
        if (!$noBorder) {
            foreach ($segments as $key => $coords) {
                $this->drawLine(
                    $coords['x1'],
                    $coords['y1'],
                    $coords['x2'],
                    $coords['y2'],
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $borderalpha,
                        'threshold' => $threshold
                    ]
                );
            }
        }
        $this->shadow = $restoreShadow;
    }
}
