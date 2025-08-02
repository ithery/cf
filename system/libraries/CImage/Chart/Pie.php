<?php

use CImage_Chart_Constant as Constant;

class CImage_Chart_Pie {
    use CImage_Chart_Concern_Pie_Draw;

    protected $chartObject;

    protected $dataObject;

    protected $labelPos = [];

    protected $shadow;

    public function __construct(CImage_Chart_Image $image, CImage_Chart_Data $data) {
        $this->chartObject = $image;
        $this->dataObject = $data;
    }

    /**
     * Draw the legend of pie chart.
     *
     * @param int|float $x
     * @param int|float $y
     * @param string    $format
     */
    public function drawPieLegend($x, $y, $format = '') {
        $FontName = isset($format['fontName']) ? $format['fontName'] : $this->chartObject->fontName;
        $FontSize = isset($format['fontSize']) ? $format['fontSize'] : $this->chartObject->fontSize;
        $FontR = isset($format['fontR']) ? $format['fontR'] : $this->chartObject->fontColorR;
        $FontG = isset($format['fontG']) ? $format['fontG'] : $this->chartObject->fontColorG;
        $FontB = isset($format['fontB']) ? $format['fontB'] : $this->chartObject->fontColorB;
        $boxSize = isset($format['boxSize']) ? $format['boxSize'] : 5;
        $Margin = isset($format['Margin']) ? $format['Margin'] : 5;
        $r = isset($format['r']) ? $format['r'] : 200;
        $G = isset($format['g']) ? $format['g'] : 200;
        $b = isset($format['b']) ? $format['b'] : 200;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $borderR = isset($format['borderR']) ? $format['borderR'] : 255;
        $borderG = isset($format['borderG']) ? $format['borderG'] : 255;
        $borderB = isset($format['borderB']) ? $format['borderB'] : 255;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        $style = isset($format['style']) ? $format['style'] : Constant::LEGEND_ROUND;
        $Mode = isset($format['Mode']) ? $format['Mode'] : Constant::LEGEND_VERTICAL;

        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $G + $surrounding;
            $borderB = $b + $surrounding;
        }

        $yStep = max($this->chartObject->fontSize, $boxSize) + 5;
        $xStep = $boxSize + 5;

        /* Data Processing */
        $data = $this->dataObject->getData();
        $palette = $this->dataObject->getPalette();

        /* Do we have an abscissa serie defined? */
        if ($data['abscissa'] == '') {
            return Constant::PIE_NO_ABSCISSA;
        }

        $boundaries = '';
        $boundaries['l'] = $x;
        $boundaries['t'] = $y;
        $boundaries['r'] = 0;
        $boundaries['b'] = 0;
        $vY = $y;
        $vX = $x;
        foreach ($data['series'][$data['abscissa']]['data'] as $key => $value) {
            $boxArray = $this->chartObject->getTextBox($vX + $boxSize + 4, $vY + $boxSize / 2, $FontName, $FontSize, 0, $value);

            if ($Mode == Constant::LEGEND_VERTICAL) {
                if ($boundaries['t'] > $boxArray[2]['y'] + $boxSize / 2) {
                    $boundaries['t'] = $boxArray[2]['y'] + $boxSize / 2;
                }
                if ($boundaries['r'] < $boxArray[1]['x'] + 2) {
                    $boundaries['r'] = $boxArray[1]['x'] + 2;
                }
                if ($boundaries['b'] < $boxArray[1]['y'] + 2 + $boxSize / 2) {
                    $boundaries['b'] = $boxArray[1]['y'] + 2 + $boxSize / 2;
                }
                $vY = $vY + $yStep;
            } elseif ($Mode == Constant::LEGEND_HORIZONTAL) {
                if ($boundaries['t'] > $boxArray[2]['y'] + $boxSize / 2) {
                    $boundaries['t'] = $boxArray[2]['y'] + $boxSize / 2;
                }
                if ($boundaries['r'] < $boxArray[1]['x'] + 2) {
                    $boundaries['r'] = $boxArray[1]['x'] + 2;
                }
                if ($boundaries['b'] < $boxArray[1]['y'] + 2 + $boxSize / 2) {
                    $boundaries['b'] = $boxArray[1]['y'] + 2 + $boxSize / 2;
                }
                $vX = $boundaries['r'] + $xStep;
            }
        }
        $vY = $vY - $yStep;
        $vX = $vX - $xStep;

        $TopOffset = $y - $boundaries['t'];
        if ($boundaries['b'] - ($vY + $boxSize) < $TopOffset) {
            $boundaries['b'] = $vY + $boxSize + $TopOffset;
        }

        if ($style == Constant::LEGEND_ROUND) {
            $this->chartObject->drawRoundedFilledRectangle($boundaries['l'] - $Margin, $boundaries['t'] - $Margin, $boundaries['r'] + $Margin, $boundaries['b'] + $Margin, $Margin, ['r' => $r, 'g' => $G, 'b' => $b, 'alpha' => $alpha, 'BorderR' => $borderR, 'BorderG' => $borderG, 'BorderB' => $borderB]);
        } elseif ($style == Constant::LEGEND_BOX) {
            $this->chartObject->drawFilledRectangle($boundaries['l'] - $Margin, $boundaries['t'] - $Margin, $boundaries['r'] + $Margin, $boundaries['b'] + $Margin, ['r' => $r, 'g' => $G, 'b' => $b, 'alpha' => $alpha, 'BorderR' => $borderR, 'BorderG' => $borderG, 'BorderB' => $borderB]);
        }

        $restoreShadow = $this->chartObject->shadow;
        $this->chartObject->shadow = false;
        foreach ($data['series'][$data['abscissa']]['data'] as $key => $value) {
            $r = $palette[$key]['r'];
            $G = $palette[$key]['g'];
            $b = $palette[$key]['b'];

            $this->chartObject->drawFilledRectangle($x + 1, $y + 1, $x + $boxSize + 1, $y + $boxSize + 1, ['r' => 0, 'g' => 0, 'b' => 0, 'alpha' => 20]);
            $this->chartObject->drawFilledRectangle($x, $y, $x + $boxSize, $y + $boxSize, ['r' => $r, 'g' => $G, 'b' => $b, 'Surrounding' => 20]);
            if ($Mode == Constant::LEGEND_VERTICAL) {
                $this->chartObject->drawText($x + $boxSize + 4, $y + $boxSize / 2, $value, ['r' => $FontR, 'g' => $FontG, 'b' => $FontB, 'align' => Constant::TEXT_ALIGN_MIDDLELEFT, 'fontName' => $FontName, 'fontSize' => $FontSize]);
                $y = $y + $yStep;
            } elseif ($Mode == Constant::LEGEND_HORIZONTAL) {
                $boxArray = $this->chartObject->drawText($x + $boxSize + 4, $y + $boxSize / 2, $value, ['r' => $FontR, 'g' => $FontG, 'b' => $FontB, 'align' => Constant::TEXT_ALIGN_MIDDLELEFT, 'fontName' => $FontName, 'fontSize' => $FontSize]);
                $x = $boxArray[1]['x'] + 2 + $xStep;
            }
        }

        $this->shadow = $restoreShadow;
    }

    /**
     * Set the color of the specified slice.
     *
     * @param mixed $sliceID
     * @param mixed $format
     */
    public function setSliceColor($sliceID, $format = '') {
        $r = isset($format['r']) ? $format['r'] : 0;
        $G = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;

        $this->dataObject->palette[$sliceID]['r'] = $r;
        $this->dataObject->palette[$sliceID]['g'] = $G;
        $this->dataObject->palette[$sliceID]['b'] = $b;
        $this->dataObject->palette[$sliceID]['alpha'] = $alpha;
    }

    /**
     * Internally used compute the label positions.
     *
     * @param mixed $x
     * @param mixed $y
     * @param mixed $label
     * @param mixed $angle
     * @param mixed $settings
     * @param mixed $stacked
     * @param mixed $xc
     * @param mixed $yc
     * @param mixed $radius
     * @param mixed $reversed
     */
    public function writePieLabel($x, $y, $label, $angle, $settings, $stacked, $xc = 0, $yc = 0, $radius = 0, $reversed = false) {
        $labelOffset = 30;
        $FontName = $this->chartObject->fontName;
        $FontSize = $this->chartObject->fontSize;

        if (!$stacked) {
            $settings['angle'] = 360 - $angle;
            $settings['length'] = 25;
            $settings['size'] = 8;

            $this->chartObject->drawArrowLabel($x, $y, ' ' . $label . ' ', $settings);
        } else {
            $x2 = cos(deg2rad($angle - 90)) * 20 + $x;
            $y2 = sin(deg2rad($angle - 90)) * 20 + $y;

            $TxtPos = $this->chartObject->getTextBox($x, $y, $FontName, $FontSize, 0, $label);
            $Height = $TxtPos[0]['y'] - $TxtPos[2]['y'];
            $yTop = $y2 - $Height / 2 - 2;
            $yBottom = $y2 + $Height / 2 + 2;

            if ($this->labelPos != '') {
                $done = false;
                foreach ($this->labelPos as $key => $settings) {
                    if (!$done) {
                        if ($angle <= 90 && (($yTop >= $settings['yTop'] && $yTop <= $settings['yBottom']) || ($yBottom >= $settings['yTop'] && $yBottom <= $settings['yBottom']))) {
                            $this->shift(0, 180, -($Height + 2), $reversed);
                            $done = true;
                        }
                        if ($angle > 90 && $angle <= 180 && (($yTop >= $settings['yTop'] && $yTop <= $settings['yBottom']) || ($yBottom >= $settings['yTop'] && $yBottom <= $settings['yBottom']))) {
                            $this->shift(0, 180, -($Height + 2), $reversed);
                            $done = true;
                        }
                        if ($angle > 180 && $angle <= 270 && (($yTop >= $settings['yTop'] && $yTop <= $settings['yBottom']) || ($yBottom >= $settings['yTop'] && $yBottom <= $settings['yBottom']))) {
                            $this->shift(180, 360, ($Height + 2), $reversed);
                            $done = true;
                        }
                        if ($angle > 270 && $angle <= 360 && (($yTop >= $settings['yTop'] && $yTop <= $settings['yBottom']) || ($yBottom >= $settings['yTop'] && $yBottom <= $settings['yBottom']))) {
                            $this->shift(180, 360, ($Height + 2), $reversed);
                            $done = true;
                        }
                    }
                }
            }

            $labelSettings = ['yTop' => $yTop, 'yBottom' => $yBottom, 'label' => $label, 'angle' => $angle, 'x1' => $x, 'y1' => $y, 'x2' => $x2, 'y2' => $y2];
            if ($angle <= 180) {
                $labelSettings['x3'] = $xc + $radius + $labelOffset;
            }
            if ($angle > 180) {
                $labelSettings['x3'] = $xc - $radius - $labelOffset;
            }
            $this->labelPos[] = $labelSettings;
        }
    }

    /**
     * Internally used to shift label positions.
     *
     * @param mixed $startAngle
     * @param mixed $EndAngle
     * @param mixed $Offset
     * @param mixed $reversed
     */
    public function shift($startAngle, $EndAngle, $Offset, $reversed) {
        if ($reversed) {
            $Offset = -$Offset;
        }
        foreach ($this->labelPos as $key => $settings) {
            if ($settings['angle'] > $startAngle && $settings['angle'] <= $EndAngle) {
                $this->labelPos[$key]['yTop'] = $settings['yTop'] + $Offset;
                $this->labelPos[$key]['yBottom'] = $settings['yBottom'] + $Offset;
                $this->labelPos[$key]['y2'] = $settings['y2'] + $Offset;
            }
        }
    }

    /**
     * Internally used to write the re-computed labels.
     */
    public function writeShiftedLabels() {
        if ($this->labelPos == '') {
            return 0;
        }
        foreach ($this->labelPos as $key => $settings) {
            $x1 = $settings['x1'];
            $y1 = $settings['y1'];
            $x2 = $settings['x2'];
            $y2 = $settings['y2'];
            $x3 = $settings['x3'];
            $angle = $settings['angle'];
            $label = $settings['label'];

            $this->chartObject->drawArrow($x2, $y2, $x1, $y1, ['Size' => 8]);
            if ($angle <= 180) {
                $this->chartObject->drawLine($x2, $y2, $x3, $y2);
                $this->chartObject->drawText($x3 + 2, $y2, $label, ['align' => Constant::TEXT_ALIGN_MIDDLELEFT]);
            } else {
                $this->chartObject->drawLine($x2, $y2, $x3, $y2);
                $this->chartObject->drawText($x3 - 2, $y2, $label, ['align' => Constant::TEXT_ALIGN_MIDDLERIGHT]);
            }
        }
    }

    /**
     * Serialize an array.
     *
     * @param mixed $data
     */
    public function arraySerialize($data) {
        $result = '';
        foreach ($data as $key => $value) {
            if ($result == '') {
                $result = floor($value);
            } else {
                $result = $result . ',' . floor($value);
            }
        }

        return $result;
    }

    /**
     * Reverse an array.
     *
     * @param mixed $plots
     */
    public function arrayReverse($plots) {
        $result = '';

        for ($i = count($plots) - 1; $i >= 0; $i = $i - 2) {
            $result[] = $plots[$i - 1];
            $result[] = $plots[$i];
        }

        return $result;
    }

    /**
     * Remove unused series & values.
     *
     * @param mixed $data
     * @param mixed $palette
     * @param mixed $dataSerie
     * @param mixed $abscissaSerie
     */
    public function clean0Values($data, $palette, $dataSerie, $abscissaSerie) {
        $newPalette = [];
        $newData = [];
        $newAbscissa = [];

        /* Remove unused series */
        foreach ($data['series'] as $serieName => $serieSettings) {
            if ($serieName != $dataSerie && $serieName != $abscissaSerie) {
                unset($data['series'][$serieName]);
            }
        }

        /* Remove NULL values */
        foreach ($data['series'][$dataSerie]['data'] as $key => $value) {
            if ($value != 0) {
                $newData[] = $value;
                $newAbscissa[] = $data['series'][$abscissaSerie]['data'][$key];
                if (isset($palette[$key])) {
                    $newPalette[] = $palette[$key];
                }
            }
        }
        $data['series'][$dataSerie]['data'] = $newData;
        $data['series'][$abscissaSerie]['data'] = $newAbscissa;

        return [$data, $newPalette];
    }
}
