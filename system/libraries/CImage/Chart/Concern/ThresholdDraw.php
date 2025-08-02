<?php
use CImage_Chart_Constant as Constant;

trait CImage_Chart_Concern_ThresholdDraw {
    /**
     * Draw an X threshold
     *
     * @param mixed $value
     * @param array $format
     *
     * @return array|null|integer
     */
    public function drawXThreshold($value, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 255;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 50;
        $weight = isset($format['weight']) ? $format['weight'] : null;
        $ticks = isset($format['ticks']) ? $format['ticks'] : 6;
        $wide = isset($format['wide']) ? $format['wide'] : false;
        $wideFactor = isset($format['wideFactor']) ? $format['wideFactor'] : 5;
        $writeCaption = isset($format['writeCaption']) ? $format['writeCaption'] : false;
        $caption = isset($format['caption']) ? $format['caption'] : null;
        $captionAlign = isset($format['captionAlign']) ? $format['captionAlign'] : Constant::CAPTION_LEFT_TOP;
        $captionOffset = isset($format['captionOffset']) ? $format['captionOffset'] : 5;
        $captionR = isset($format['captionR']) ? $format['captionR'] : 255;
        $captionG = isset($format['captionG']) ? $format['captionG'] : 255;
        $captionB = isset($format['captionB']) ? $format['captionB'] : 255;
        $captionalpha = isset($format['captionalpha']) ? $format['captionalpha'] : 100;
        $drawBox = isset($format['drawBox']) ? $format['drawBox'] : true;
        $drawBoxBorder = isset($format['drawBoxBorder']) ? $format['drawBoxBorder'] : false;
        $borderOffset = isset($format['borderOffset']) ? $format['borderOffset'] : 3;
        $boxRounded = isset($format['boxRounded']) ? $format['boxRounded'] : true;
        $roundedRadius = isset($format['roundedRadius']) ? $format['roundedRadius'] : 3;
        $boxR = isset($format['boxR']) ? $format['boxR'] : 0;
        $boxG = isset($format['boxG']) ? $format['boxG'] : 0;
        $boxB = isset($format['boxB']) ? $format['boxB'] : 0;
        $boxalpha = isset($format['boxalpha']) ? $format['boxalpha'] : 30;
        $boxSurrounding = isset($format['boxSurrounding']) ? $format['boxSurrounding'] : '';
        $boxborderR = isset($format['boxborderR']) ? $format['boxborderR'] : 255;
        $boxborderG = isset($format['boxborderG']) ? $format['boxborderG'] : 255;
        $boxborderB = isset($format['boxborderB']) ? $format['boxborderB'] : 255;
        $boxBorderalpha = isset($format['boxBorderalpha']) ? $format['boxBorderalpha'] : 100;
        $valueIsLabel = isset($format['valueIsLabel']) ? $format['valueIsLabel'] : false;
        $data = $this->dataSet->getData();
        $abscissaMargin = $this->getAbscissaMargin($data);
        $xScale = $this->scaleGetXSettings();
        if (is_array($value)) {
            foreach ($value as $key => $id) {
                $this->drawXThreshold($id, $format);
            }
            return 0;
        }
        if ($valueIsLabel) {
            $format['valueIsLabel'] = false;
            foreach ($data['series'][$data['abscissa']]['data'] as $key => $serieValue) {
                if ($serieValue == $value) {
                    $this->drawXThreshold($key, $format);
                }
            }
            return 0;
        }
        $captionSettings = [
            'DrawBox' => $drawBox,
            'DrawBoxBorder' => $drawBoxBorder,
            'borderOffset' => $borderOffset,
            'BoxRounded' => $boxRounded,
            'RoundedRadius' => $roundedRadius,
            'BoxR' => $boxR,
            'BoxG' => $boxG,
            'BoxB' => $boxB,
            'Boxalpha' => $boxalpha,
            'BoxSurrounding' => $boxSurrounding,
            'BoxborderR' => $boxborderR,
            'BoxborderG' => $boxborderG,
            'BoxborderB' => $boxborderB,
            'BoxBorderalpha' => $boxBorderalpha,
            'r' => $captionR,
            'g' => $captionG,
            'b' => $captionB,
            'alpha' => $captionalpha
        ];
        if ($caption == null) {
            $caption = $value;
            if (isset($data['abscissa']) && isset($data['series'][$data['abscissa']]['data'][$value])
            ) {
                $caption = $data['series'][$data['abscissa']]['data'][$value];
            }
        }
        if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
            $xStep = (($this->graphAreaX2 - $this->graphAreaX1) - $xScale[0] * 2) / $xScale[1];
            $xPos = $this->graphAreaX1 + $xScale[0] + $xStep * $value;
            $yPos1 = $this->graphAreaY1 + $data['yMargin'];
            $yPos2 = $this->graphAreaY2 - $data['yMargin'];
            if ($xPos >= $this->graphAreaX1 + $abscissaMargin && $xPos <= $this->graphAreaX2 - $abscissaMargin
            ) {
                $this->drawLine(
                    $xPos,
                    $yPos1,
                    $xPos,
                    $yPos2,
                    [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'alpha' => $alpha,
                        'ticks' => $ticks,
                        'weight' => $weight
                    ]
                );
                if ($wide) {
                    $this->drawLine(
                        $xPos - 1,
                        $yPos1,
                        $xPos - 1,
                        $yPos2,
                        [
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha / $wideFactor,
                            'ticks' => $ticks
                        ]
                    );
                    $this->drawLine(
                        $xPos + 1,
                        $yPos1,
                        $xPos + 1,
                        $yPos2,
                        [
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha / $wideFactor,
                            'ticks' => $ticks
                        ]
                    );
                }
                if ($writeCaption) {
                    if ($captionAlign == Constant::CAPTION_LEFT_TOP) {
                        $y = $yPos1 + $captionOffset;
                        $captionSettings['align'] = Constant::TEXT_ALIGN_TOPMIDDLE;
                    } else {
                        $y = $yPos2 - $captionOffset;
                        $captionSettings['align'] = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                    }
                    $this->drawText($xPos, $y, $caption, $captionSettings);
                }
                return ['x' => $xPos];
            }
        } elseif ($data['orientation'] == Constant::SCALE_POS_TOPBOTTOM) {
            $xStep = (($this->graphAreaY2 - $this->graphAreaY1) - $xScale[0] * 2) / $xScale[1];
            $xPos = $this->graphAreaY1 + $xScale[0] + $xStep * $value;
            $yPos1 = $this->graphAreaX1 + $data['yMargin'];
            $yPos2 = $this->graphAreaX2 - $data['yMargin'];
            if ($xPos >= $this->graphAreaY1 + $abscissaMargin && $xPos <= $this->graphAreaY2 - $abscissaMargin
            ) {
                $this->drawLine(
                    $yPos1,
                    $xPos,
                    $yPos2,
                    $xPos,
                    [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'alpha' => $alpha,
                        'ticks' => $ticks,
                        'weight' => $weight
                    ]
                );
                if ($wide) {
                    $this->drawLine(
                        $yPos1,
                        $xPos - 1,
                        $yPos2,
                        $xPos - 1,
                        [
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha / $wideFactor,
                            'ticks' => $ticks
                        ]
                    );
                    $this->drawLine(
                        $yPos1,
                        $xPos + 1,
                        $yPos2,
                        $xPos + 1,
                        [
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha / $wideFactor,
                            'ticks' => $ticks
                        ]
                    );
                }
                if ($writeCaption) {
                    if ($captionAlign == Constant::CAPTION_LEFT_TOP) {
                        $y = $yPos1 + $captionOffset;
                        $captionSettings['align'] = Constant::TEXT_ALIGN_MIDDLELEFT;
                    } else {
                        $y = $yPos2 - $captionOffset;
                        $captionSettings['align'] = Constant::TEXT_ALIGN_MIDDLERIGHT;
                    }
                    $this->drawText($y, $xPos, $caption, $captionSettings);
                }
                return ['x' => $xPos];
            }
        }
    }

    /**
     * Draw an X threshold area
     *
     * @param mixed $value1
     * @param mixed $value2
     * @param array $format
     *
     * @return array|null
     */
    public function drawXThresholdArea($value1, $value2, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 255;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 20;
        $border = isset($format['border']) ? $format['border'] : true;
        $borderR = isset($format['borderR']) ? $format['borderR'] : $r;
        $borderG = isset($format['borderG']) ? $format['borderG'] : $g;
        $borderB = isset($format['borderB']) ? $format['borderB'] : $b;
        $borderalpha = isset($format['borderalpha']) ? $format['borderalpha'] : $alpha + 20;
        $borderTicks = isset($format['borderTicks']) ? $format['borderTicks'] : 2;
        $areaName = isset($format['areaName']) ? $format['areaName'] : null;
        $NameAngle = isset($format['nameAngle']) ? $format['nameAngle'] : Constant::ZONE_NAME_ANGLE_AUTO;
        $NameR = isset($format['nameR']) ? $format['nameR'] : 255;
        $NameG = isset($format['nameG']) ? $format['nameG'] : 255;
        $NameB = isset($format['nameB']) ? $format['nameB'] : 255;
        $Namealpha = isset($format['namealpha']) ? $format['namealpha'] : 100;
        $disableShadowOnArea = isset($format['disableShadowOnArea']) ? $format['disableShadowOnArea'] : true;
        $restoreShadow = $this->shadow;
        if ($disableShadowOnArea && $this->shadow) {
            $this->shadow = false;
        }
        if ($borderalpha > 100) {
            $borderalpha = 100;
        }
        $data = $this->dataSet->getData();
        $xScale = $this->scaleGetXSettings();
        if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
            $xStep = (($this->graphAreaX2 - $this->graphAreaX1) - $xScale[0] * 2) / $xScale[1];
            $xPos1 = $this->graphAreaX1 + $xScale[0] + $xStep * $value1;
            $xPos2 = $this->graphAreaX1 + $xScale[0] + $xStep * $value2;
            $yPos1 = $this->graphAreaY1 + $data['yMargin'];
            $yPos2 = $this->graphAreaY2 - $data['yMargin'];
            if ($xPos1 < $this->graphAreaX1 + $xScale[0]) {
                $xPos1 = $this->graphAreaX1 + $xScale[0];
            }
            if ($xPos1 > $this->graphAreaX2 - $xScale[0]) {
                $xPos1 = $this->graphAreaX2 - $xScale[0];
            }
            if ($xPos2 < $this->graphAreaX1 + $xScale[0]) {
                $xPos2 = $this->graphAreaX1 + $xScale[0];
            }
            if ($xPos2 > $this->graphAreaX2 - $xScale[0]) {
                $xPos2 = $this->graphAreaX2 - $xScale[0];
            }
            $this->drawFilledRectangle(
                $xPos1,
                $yPos1,
                $xPos2,
                $yPos2,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
            if ($border) {
                $this->drawLine(
                    $xPos1,
                    $yPos1,
                    $xPos1,
                    $yPos2,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $borderalpha,
                        'ticks' => $borderTicks
                    ]
                );
                $this->drawLine(
                    $xPos2,
                    $yPos1,
                    $xPos2,
                    $yPos2,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $borderalpha,
                        'ticks' => $borderTicks
                    ]
                );
            }
            if ($areaName != null) {
                $xPos = ($xPos2 - $xPos1) / 2 + $xPos1;
                $yPos = ($yPos2 - $yPos1) / 2 + $yPos1;
                if ($NameAngle == Constant::ZONE_NAME_ANGLE_AUTO) {
                    $txtPos = $this->getTextBox(
                        $xPos,
                        $yPos,
                        $this->fontName,
                        $this->fontSize,
                        0,
                        $areaName
                    );
                    $txtWidth = $txtPos[1]['x'] - $txtPos[0]['x'];
                    $NameAngle = 90;
                    if (abs($xPos2 - $xPos1) > $txtWidth) {
                        $NameAngle = 0;
                    }
                }
                $this->shadow = $restoreShadow;
                $this->drawText(
                    $xPos,
                    $yPos,
                    $areaName,
                    [
                        'r' => $NameR,
                        'g' => $NameG,
                        'b' => $NameB,
                        'alpha' => $Namealpha,
                        'Angle' => $NameAngle,
                        'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE
                    ]
                );
                if ($disableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ['x1' => $xPos1, 'X2' => $xPos2];
        } elseif ($data['orientation'] == Constant::SCALE_POS_TOPBOTTOM) {
            $xStep = (($this->graphAreaY2 - $this->graphAreaY1) - $xScale[0] * 2) / $xScale[1];
            $xPos1 = $this->graphAreaY1 + $xScale[0] + $xStep * $value1;
            $xPos2 = $this->graphAreaY1 + $xScale[0] + $xStep * $value2;
            $yPos1 = $this->graphAreaX1 + $data['yMargin'];
            $yPos2 = $this->graphAreaX2 - $data['yMargin'];
            if ($xPos1 < $this->graphAreaY1 + $xScale[0]) {
                $xPos1 = $this->graphAreaY1 + $xScale[0];
            }
            if ($xPos1 > $this->graphAreaY2 - $xScale[0]) {
                $xPos1 = $this->graphAreaY2 - $xScale[0];
            }
            if ($xPos2 < $this->graphAreaY1 + $xScale[0]) {
                $xPos2 = $this->graphAreaY1 + $xScale[0];
            }
            if ($xPos2 > $this->graphAreaY2 - $xScale[0]) {
                $xPos2 = $this->graphAreaY2 - $xScale[0];
            }
            $this->drawFilledRectangle(
                $yPos1,
                $xPos1,
                $yPos2,
                $xPos2,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
            if ($border) {
                $this->drawLine(
                    $yPos1,
                    $xPos1,
                    $yPos2,
                    $xPos1,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $borderalpha,
                        'ticks' => $borderTicks
                    ]
                );
                $this->drawLine(
                    $yPos1,
                    $xPos2,
                    $yPos2,
                    $xPos2,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $borderalpha,
                        'ticks' => $borderTicks
                    ]
                );
            }
            if ($areaName != null) {
                $xPos = ($xPos2 - $xPos1) / 2 + $xPos1;
                $yPos = ($yPos2 - $yPos1) / 2 + $yPos1;
                $this->shadow = $restoreShadow;
                $this->drawText(
                    $yPos,
                    $xPos,
                    $areaName,
                    [
                        'r' => $NameR,
                        'g' => $NameG,
                        'b' => $NameB,
                        'alpha' => $Namealpha,
                        'Angle' => 0,
                        'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE
                    ]
                );
                if ($disableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ['x1' => $xPos1, 'X2' => $xPos2];
        }
    }

    /**
     * Draw an Y threshold with the computed scale
     *
     * @param mixed $value
     * @param array $format
     *
     * @return array|int
     */
    public function drawThreshold($value, array $format = []) {
        $axisId = isset($format['axisId']) ? $format['axisId'] : 0;
        $r = isset($format['r']) ? $format['r'] : 255;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 50;
        $weight = isset($format['weight']) ? $format['weight'] : null;
        $ticks = isset($format['ticks']) ? $format['ticks'] : 6;
        $wide = isset($format['wide']) ? $format['wide'] : false;
        $wideFactor = isset($format['wideFactor']) ? $format['wideFactor'] : 5;
        $writeCaption = isset($format['writeCaption']) ? $format['writeCaption'] : false;
        $caption = isset($format['caption']) ? $format['caption'] : null;
        $captionAlign = isset($format['captionAlign']) ? $format['captionAlign'] : Constant::CAPTION_LEFT_TOP;
        $captionOffset = isset($format['captionOffset']) ? $format['captionOffset'] : 10;
        $captionR = isset($format['captionR']) ? $format['captionR'] : 255;
        $captionG = isset($format['captionG']) ? $format['captionG'] : 255;
        $captionB = isset($format['captionB']) ? $format['captionB'] : 255;
        $captionalpha = isset($format['captionalpha']) ? $format['captionalpha'] : 100;
        $drawBox = isset($format['drawBox']) ? $format['drawBox'] : true;
        $drawBoxBorder = isset($format['drawBoxBorder']) ? $format['drawBoxBorder'] : false;
        $borderOffset = isset($format['borderOffset']) ? $format['borderOffset'] : 5;
        $boxRounded = isset($format['boxRounded']) ? $format['boxRounded'] : true;
        $roundedRadius = isset($format['roundedRadius']) ? $format['roundedRadius'] : 3;
        $boxR = isset($format['boxR']) ? $format['boxR'] : 0;
        $boxG = isset($format['boxG']) ? $format['boxG'] : 0;
        $boxB = isset($format['boxB']) ? $format['boxB'] : 0;
        $boxalpha = isset($format['boxalpha']) ? $format['boxalpha'] : 20;
        $boxSurrounding = isset($format['boxSurrounding']) ? $format['boxSurrounding'] : '';
        $boxborderR = isset($format['boxborderR']) ? $format['boxborderR'] : 255;
        $boxborderG = isset($format['boxborderG']) ? $format['boxborderG'] : 255;
        $boxborderB = isset($format['boxborderB']) ? $format['boxborderB'] : 255;
        $boxBorderalpha = isset($format['boxBorderalpha']) ? $format['boxBorderalpha'] : 100;
        $NoMargin = isset($format['noMargin']) ? $format['noMargin'] : false;
        if (is_array($value)) {
            foreach ($value as $key => $id) {
                $this->drawThreshold($id, $format);
            }
            return 0;
        }
        $captionSettings = [
            'DrawBox' => $drawBox,
            'DrawBoxBorder' => $drawBoxBorder,
            'borderOffset' => $borderOffset,
            'BoxRounded' => $boxRounded,
            'RoundedRadius' => $roundedRadius,
            'BoxR' => $boxR,
            'BoxG' => $boxG,
            'BoxB' => $boxB,
            'Boxalpha' => $boxalpha,
            'BoxSurrounding' => $boxSurrounding,
            'BoxborderR' => $boxborderR,
            'BoxborderG' => $boxborderG,
            'BoxborderB' => $boxborderB,
            'BoxBorderalpha' => $boxBorderalpha,
            'r' => $captionR,
            'g' => $captionG,
            'b' => $captionB,
            'alpha' => $captionalpha
        ];
        $data = $this->dataSet->getData();
        $abscissaMargin = $this->getAbscissaMargin($data);
        if ($NoMargin) {
            $abscissaMargin = 0;
        }
        if (!isset($data['axis'][$axisId])) {
            return -1;
        }
        if ($caption == null) {
            $caption = $value;
        }
        if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
            $yPos = $this->scaleComputeY($value, ['axisId' => $axisId]);
            if ($yPos >= $this->graphAreaY1 + $data['axis'][$axisId]['margin'] && $yPos <= $this->graphAreaY2 - $data['axis'][$axisId]['margin']
            ) {
                $x1 = $this->graphAreaX1 + $abscissaMargin;
                $x2 = $this->graphAreaX2 - $abscissaMargin;
                $this->drawLine(
                    $x1,
                    $yPos,
                    $x2,
                    $yPos,
                    [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'alpha' => $alpha,
                        'ticks' => $ticks,
                        'weight' => $weight
                    ]
                );
                if ($wide) {
                    $this->drawLine(
                        $x1,
                        $yPos - 1,
                        $x2,
                        $yPos - 1,
                        [
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha / $wideFactor,
                            'ticks' => $ticks
                        ]
                    );
                    $this->drawLine(
                        $x1,
                        $yPos + 1,
                        $x2,
                        $yPos + 1,
                        [
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha / $wideFactor,
                            'ticks' => $ticks
                        ]
                    );
                }
                if ($writeCaption) {
                    if ($captionAlign == Constant::CAPTION_LEFT_TOP) {
                        $x = $x1 + $captionOffset;
                        $captionSettings['align'] = Constant::TEXT_ALIGN_MIDDLELEFT;
                    } else {
                        $x = $x2 - $captionOffset;
                        $captionSettings['align'] = Constant::TEXT_ALIGN_MIDDLERIGHT;
                    }
                    $this->drawText($x, $yPos, $caption, $captionSettings);
                }
            }
            return ['y' => $yPos];
        }
        if ($data['orientation'] == Constant::SCALE_POS_TOPBOTTOM) {
            $xPos = $this->scaleComputeY($value, ['axisId' => $axisId]);
            if ($xPos >= $this->graphAreaX1 + $data['axis'][$axisId]['margin'] && $xPos <= $this->graphAreaX2 - $data['axis'][$axisId]['margin']
            ) {
                $y1 = $this->graphAreaY1 + $abscissaMargin;
                $y2 = $this->graphAreaY2 - $abscissaMargin;
                $this->drawLine(
                    $xPos,
                    $y1,
                    $xPos,
                    $y2,
                    [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'alpha' => $alpha,
                        'ticks' => $ticks,
                        'weight' => $weight
                    ]
                );
                if ($wide) {
                    $this->drawLine(
                        $xPos - 1,
                        $y1,
                        $xPos - 1,
                        $y2,
                        [
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha / $wideFactor,
                            'ticks' => $ticks
                        ]
                    );
                    $this->drawLine(
                        $xPos + 1,
                        $y1,
                        $xPos + 1,
                        $y2,
                        [
                            'r' => $r,
                            'g' => $g,
                            'b' => $b,
                            'alpha' => $alpha / $wideFactor,
                            'ticks' => $ticks
                        ]
                    );
                }
                if ($writeCaption) {
                    if ($captionAlign == Constant::CAPTION_LEFT_TOP) {
                        $y = $y1 + $captionOffset;
                        $captionSettings['align'] = Constant::TEXT_ALIGN_TOPMIDDLE;
                    } else {
                        $y = $y2 - $captionOffset;
                        $captionSettings['align'] = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                    }
                    $captionSettings['align'] = Constant::TEXT_ALIGN_TOPMIDDLE;
                    $this->drawText($xPos, $y, $caption, $captionSettings);
                }
            }
            return ['y' => $xPos];
        }
    }

    /**
     * Draw a threshold with the computed scale
     *
     * @param mixed $value1
     * @param mixed $value2
     * @param array $format
     *
     * @return array|int|null
     */
    public function drawThresholdArea($value1, $value2, array $format = []) {
        $axisId = isset($format['axisId']) ? $format['axisId'] : 0;
        $r = isset($format['r']) ? $format['r'] : 255;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 20;
        $border = isset($format['border']) ? $format['border'] : true;
        $borderR = isset($format['borderR']) ? $format['borderR'] : $r;
        $borderG = isset($format['borderG']) ? $format['borderG'] : $g;
        $borderB = isset($format['borderB']) ? $format['borderB'] : $b;
        $borderalpha = isset($format['borderalpha']) ? $format['borderalpha'] : $alpha + 20;
        $borderTicks = isset($format['borderTicks']) ? $format['borderTicks'] : 2;
        $areaName = isset($format['areaName']) ? $format['areaName'] : null;
        $NameAngle = isset($format['nameAngle']) ? $format['nameAngle'] : Constant::ZONE_NAME_ANGLE_AUTO;
        $NameR = isset($format['nameR']) ? $format['nameR'] : 255;
        $NameG = isset($format['nameG']) ? $format['nameG'] : 255;
        $NameB = isset($format['nameB']) ? $format['nameB'] : 255;
        $Namealpha = isset($format['namealpha']) ? $format['namealpha'] : 100;
        $disableShadowOnArea = isset($format['disableShadowOnArea']) ? $format['disableShadowOnArea'] : true;
        $NoMargin = isset($format['noMargin']) ? $format['noMargin'] : false;
        if ($value1 > $value2) {
            list($value1, $value2) = [$value2, $value1];
        }
        $restoreShadow = $this->shadow;
        if ($disableShadowOnArea && $this->shadow) {
            $this->shadow = false;
        }
        if ($borderalpha > 100) {
            $borderalpha = 100;
        }
        $data = $this->dataSet->getData();
        $abscissaMargin = $this->getAbscissaMargin($data);
        if ($NoMargin) {
            $abscissaMargin = 0;
        }
        if (!isset($data['axis'][$axisId])) {
            return -1;
        }
        if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
            $xPos1 = $this->graphAreaX1 + $abscissaMargin;
            $xPos2 = $this->graphAreaX2 - $abscissaMargin;
            $yPos1 = $this->scaleComputeY($value1, ['axisId' => $axisId]);
            $yPos2 = $this->scaleComputeY($value2, ['axisId' => $axisId]);
            if ($yPos1 < $this->graphAreaY1 + $data['axis'][$axisId]['margin']) {
                $yPos1 = $this->graphAreaY1 + $data['axis'][$axisId]['margin'];
            }
            if ($yPos1 > $this->graphAreaY2 - $data['axis'][$axisId]['margin']) {
                $yPos1 = $this->graphAreaY2 - $data['axis'][$axisId]['margin'];
            }
            if ($yPos2 < $this->graphAreaY1 + $data['axis'][$axisId]['margin']) {
                $yPos2 = $this->graphAreaY1 + $data['axis'][$axisId]['margin'];
            }
            if ($yPos2 > $this->graphAreaY2 - $data['axis'][$axisId]['margin']) {
                $yPos2 = $this->graphAreaY2 - $data['axis'][$axisId]['margin'];
            }
            $this->drawFilledRectangle(
                $xPos1,
                $yPos1,
                $xPos2,
                $yPos2,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
            if ($border) {
                $this->drawLine(
                    $xPos1,
                    $yPos1,
                    $xPos2,
                    $yPos1,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $borderalpha,
                        'ticks' => $borderTicks
                    ]
                );
                $this->drawLine(
                    $xPos1,
                    $yPos2,
                    $xPos2,
                    $yPos2,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $borderalpha,
                        'ticks' => $borderTicks
                    ]
                );
            }
            if ($areaName != null) {
                $xPos = ($xPos2 - $xPos1) / 2 + $xPos1;
                $yPos = ($yPos2 - $yPos1) / 2 + $yPos1;
                $this->shadow = $restoreShadow;
                $this->drawText(
                    $xPos,
                    $yPos,
                    $areaName,
                    [
                        'r' => $NameR,
                        'g' => $NameG,
                        'b' => $NameB,
                        'alpha' => $Namealpha,
                        'Angle' => 0,
                        'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE
                    ]
                );
                if ($disableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ['y1' => $yPos1, 'Y2' => $yPos2];
        } elseif ($data['orientation'] == Constant::SCALE_POS_TOPBOTTOM) {
            $yPos1 = $this->graphAreaY1 + $abscissaMargin;
            $yPos2 = $this->graphAreaY2 - $abscissaMargin;
            $xPos1 = $this->scaleComputeY($value1, ['axisId' => $axisId]);
            $xPos2 = $this->scaleComputeY($value2, ['axisId' => $axisId]);
            if ($xPos1 < $this->graphAreaX1 + $data['axis'][$axisId]['margin']) {
                $xPos1 = $this->graphAreaX1 + $data['axis'][$axisId]['margin'];
            }
            if ($xPos1 > $this->graphAreaX2 - $data['axis'][$axisId]['margin']) {
                $xPos1 = $this->graphAreaX2 - $data['axis'][$axisId]['margin'];
            }
            if ($xPos2 < $this->graphAreaX1 + $data['axis'][$axisId]['margin']) {
                $xPos2 = $this->graphAreaX1 + $data['axis'][$axisId]['margin'];
            }
            if ($xPos2 > $this->graphAreaX2 - $data['axis'][$axisId]['margin']) {
                $xPos2 = $this->graphAreaX2 - $data['axis'][$axisId]['margin'];
            }
            $this->drawFilledRectangle(
                $xPos1,
                $yPos1,
                $xPos2,
                $yPos2,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
            if ($border) {
                $this->drawLine(
                    $xPos1,
                    $yPos1,
                    $xPos1,
                    $yPos2,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $borderalpha,
                        'ticks' => $borderTicks
                    ]
                );
                $this->drawLine(
                    $xPos2,
                    $yPos1,
                    $xPos2,
                    $yPos2,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $borderalpha,
                        'ticks' => $borderTicks
                    ]
                );
            }
            if ($areaName != null) {
                $xPos = ($yPos2 - $yPos1) / 2 + $yPos1;
                $yPos = ($xPos2 - $xPos1) / 2 + $xPos1;
                if ($NameAngle == Constant::ZONE_NAME_ANGLE_AUTO) {
                    $txtPos = $this->getTextBox(
                        $xPos,
                        $yPos,
                        $this->fontName,
                        $this->fontSize,
                        0,
                        $areaName
                    );
                    $txtWidth = $txtPos[1]['x'] - $txtPos[0]['x'];
                    $NameAngle = 90;
                    if (abs($xPos2 - $xPos1) > $txtWidth) {
                        $NameAngle = 0;
                    }
                }
                $this->shadow = $restoreShadow;
                $this->drawText(
                    $yPos,
                    $xPos,
                    $areaName,
                    [
                        'r' => $NameR,
                        'g' => $NameG,
                        'b' => $NameB,
                        'alpha' => $Namealpha,
                        'Angle' => $NameAngle,
                        'align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE
                    ]
                );
                if ($disableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ['y1' => $xPos1, 'Y2' => $xPos2];
        }
    }
}
