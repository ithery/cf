<?php
use CImage_Chart_Constant as Constant;

trait CImage_Chart_Concern_Pie_Draw {
    public function draw2DPie($x, $y, $format = []) {
        $radius = isset($format['radius']) ? $format['radius'] : 60;
        $precision = isset($format['precision']) ? $format['precision'] : 0;
        $dataGapAngle = isset($format['dataGapAngle']) ? $format['dataGapAngle'] : 0;
        $dataGapRadius = isset($format['dataGapRadius']) ? $format['dataGapRadius'] : 0;
        $secondPass = isset($format['secondPass']) ? $format['secondPass'] : true;
        $border = isset($format['border']) ? $format['border'] : false;
        $borderR = isset($format['borderR']) ? $format['borderR'] : 255;
        $borderG = isset($format['borderG']) ? $format['borderG'] : 255;
        $borderB = isset($format['borderB']) ? $format['borderB'] : 255;
        $shadow = isset($format['shadow']) ? $format['shadow'] : false;
        $drawLabels = isset($format['drawLabels']) ? $format['drawLabels'] : false;
        $labelStacked = isset($format['labelStacked']) ? $format['labelStacked'] : false;
        $labelColor = isset($format['labelColor']) ? $format['labelColor'] : Constant::PIE_LABEL_COLOR_MANUAL;
        $labelR = isset($format['labelR']) ? $format['labelR'] : 0;
        $labelG = isset($format['labelG']) ? $format['labelG'] : 0;
        $labelB = isset($format['labelB']) ? $format['labelB'] : 0;
        $labelAlpha = isset($format['labelAlpha']) ? $format['labelAlpha'] : 100;
        $writeValues = isset($format['writeValues']) ? $format['writeValues'] : null;
        $valuePosition = isset($format['valuePosition']) ? $format['valuePosition'] : Constant::PIE_VALUE_OUTSIDE;
        $valuePadding = isset($format['valuePadding']) ? $format['valuePadding'] : 15;
        $valueSuffix = isset($format['valueSuffix']) ? $format['valueSuffix'] : '';
        $valueR = isset($format['valueR']) ? $format['valueR'] : 255;
        $valueG = isset($format['valueG']) ? $format['valueG'] : 255;
        $valueB = isset($format['valueB']) ? $format['valueB'] : 255;
        $valueAlpha = isset($format['valueAlpha']) ? $format['valueAlpha'] : 100;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;

        /* Data Processing */
        $data = $this->dataObject->getData();
        $palette = $this->dataObject->getPalette();
        /* Do we have an abscissa serie defined? */
        if ($data['abscissa'] == '') {
            return Constant::PIE_NO_ABSCISSA;
        }

        /* Try to find the data serie */
        $dataSerie = '';
        foreach ($data['series'] as $serieName => $serieData) {
            if ($serieName != $data['abscissa']) {
                $dataSerie = $serieName;
            }
        }

        /* Do we have data to compute? */
        if ($dataSerie == '') {
            return Constant::PIE_NO_DATASERIE;
        }

        /* Remove unused data */
        list($data, $palette) = $this->clean0Values($data, $palette, $dataSerie, $data['abscissa']);

        /* Compute the pie sum */
        $serieSum = $this->dataObject->getSum($dataSerie);

        /* Do we have data to draw? */
        if ($serieSum == 0) {
            return Constant::PIE_SUMISNULL;
        }

        /* Dump the real number of data to draw */
        $values = [];
        foreach ($data['series'][$dataSerie]['data'] as $Key => $value) {
            if ($value != 0) {
                $values[] = $value;
            }
        }

        /* Compute the wasted angular space between series */
        if (count($values) == 1) {
            $wastedAngular = 0;
        } else {
            $wastedAngular = count($values) * $dataGapAngle;
        }

        /* Compute the scale */
        $scaleFactor = (360 - $wastedAngular) / $serieSum;

        $restoreShadow = $this->chartObject->shadow;
        if ($this->chartObject->shadow) {
            $this->chartObject->shadow = false;

            $shadowFormat = $format;
            $shadowFormat['shadow'] = true;
            $this->draw2DPie($x + $this->chartObject->shadowX, $y + $this->chartObject->shadowY, $shadowFormat);
        }

        /* Draw the polygon pie elements */
        $step = 360 / (2 * Constant::PI * $radius);
        $offset = 0;
        $ID = 0;
        foreach ($values as $Key => $value) {
            if ($shadow) {
                $settings = ['r' => $this->chartObject->shadowR, 'g' => $this->chartObject->shadowG, 'b' => $this->chartObject->shadowB, 'alpha' => $this->chartObject->shadowa];
            } else {
                if (!isset($palette[$ID]['r'])) {
                    $Color = $this->chartObject->getRandomColor();
                    $palette[$ID] = $Color;
                    $this->dataObject->savePalette($ID, $Color);
                }
                $settings = ['r' => $palette[$ID]['r'], 'g' => $palette[$ID]['g'], 'b' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];
            }

            if (!$secondPass && !$shadow) {
                if (!$border) {
                    $settings['surrounding'] = 10;
                } else {
                    $settings['borderR'] = $borderR;
                    $settings['borderG'] = $borderG;
                    $settings['borderB'] = $borderB;
                }
            }

            $plots = [];
            $endAngle = $offset + ($value * $scaleFactor);
            if ($endAngle > 360) {
                $endAngle = 360;
            }

            $angle = ($endAngle - $offset) / 2 + $offset;
            if ($dataGapAngle == 0) {
                $x0 = $x;
                $y0 = $y;
            } else {
                $x0 = cos(($angle - 90) * Constant::PI / 180) * $dataGapRadius + $x;
                $y0 = sin(($angle - 90) * Constant::PI / 180) * $dataGapRadius + $y;
            }

            $plots[] = $x0;
            $plots[] = $y0;

            for ($i = $offset; $i <= $endAngle; $i = $i + $step) {
                $xc = cos(($i - 90) * Constant::PI / 180) * $radius + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * $radius + $y;

                if ($secondPass && ($i < 90)) {
                    $yc++;
                }
                if ($secondPass && ($i > 180 && $i < 270)) {
                    $xc++;
                }
                if ($secondPass && ($i >= 270)) {
                    $xc++;
                    $yc++;
                }

                $plots[] = $xc;
                $plots[] = $yc;
            }

            $this->chartObject->drawPolygon($plots, $settings);
            if ($recordImageMap && !$shadow) {
                $this->chartObject->addToImageMap('POLY', $this->arraySerialize($plots), $this->chartObject->toHTMLColor($palette[$ID]['r'], $palette[$ID]['g'], $palette[$ID]['b']), $data['series'][$data['abscissa']]['data'][$Key], $value);
            }

            if ($drawLabels && !$shadow && !$secondPass) {
                if ($labelColor == Constant::PIE_LABEL_COLOR_AUTO) {
                    $settings = ['fillR' => $palette[$ID]['r'], 'fillG' => $palette[$ID]['g'], 'fillB' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];
                } else {
                    $settings = ['fillR' => $labelR, 'fillG' => $labelG, 'fillB' => $labelB, 'alpha' => $labelAlpha];
                }

                $angle = ($endAngle - $offset) / 2 + $offset;
                $xc = cos(($angle - 90) * Constant::PI / 180) * $radius + $x;
                $yc = sin(($angle - 90) * Constant::PI / 180) * $radius + $y;

                $label = $data['series'][$data['abscissa']]['data'][$Key];

                if ($labelStacked) {
                    $this->writePieLabel($xc, $yc, $label, $angle, $settings, true, $x, $y, $radius);
                } else {
                    $this->writePieLabel($xc, $yc, $label, $angle, $settings, false);
                }
            }

            $offset = $i + $dataGapAngle;
            $ID++;
        }

        /* Second pass to smooth the angles */
        if ($secondPass) {
            $step = 360 / (2 * Constant::PI * $radius);
            $offset = 0;
            $ID = 0;
            foreach ($values as $Key => $value) {
                $FirstPoint = true;
                if ($shadow) {
                    $settings = ['r' => $this->chartObject->shadowR, 'g' => $this->chartObject->shadowG, 'b' => $this->chartObject->shadowB, 'alpha' => $this->chartObject->shadowa];
                } else {
                    if ($border) {
                        $settings = ['r' => $borderR, 'g' => $borderG, 'b' => $borderB];
                    } else {
                        $settings = ['r' => $palette[$ID]['r'], 'g' => $palette[$ID]['g'], 'b' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];
                    }
                }

                $endAngle = $offset + ($value * $scaleFactor);
                if ($endAngle > 360) {
                    $endAngle = 360;
                }

                if ($dataGapAngle == 0) {
                    $x0 = $x;
                    $y0 = $y;
                } else {
                    $angle = ($endAngle - $offset) / 2 + $offset;
                    $x0 = cos(($angle - 90) * Constant::PI / 180) * $dataGapRadius + $x;
                    $y0 = sin(($angle - 90) * Constant::PI / 180) * $dataGapRadius + $y;
                }
                $plots[] = $x0;
                $plots[] = $y0;

                for ($i = $offset; $i <= $endAngle; $i = $i + $step) {
                    $xc = cos(($i - 90) * Constant::PI / 180) * $radius + $x;
                    $yc = sin(($i - 90) * Constant::PI / 180) * $radius + $y;

                    if ($FirstPoint) {
                        $this->chartObject->drawLine($xc, $yc, $x0, $y0, $settings);
                    }
                    { $FirstPoint = false; }

                    $this->chartObject->drawAntialiasPixel($xc, $yc, $settings);
                }
                $this->chartObject->drawLine($xc, $yc, $x0, $y0, $settings);

                if ($drawLabels && !$shadow) {
                    if ($labelColor == Constant::PIE_LABEL_COLOR_AUTO) {
                        $settings = ['fillR' => $palette[$ID]['r'], 'fillG' => $palette[$ID]['g'], 'fillB' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];
                    } else {
                        $settings = ['fillR' => $labelR, 'fillG' => $labelG, 'fillB' => $labelB, 'alpha' => $labelAlpha];
                    }

                    $angle = ($endAngle - $offset) / 2 + $offset;
                    $xc = cos(($angle - 90) * Constant::PI / 180) * $radius + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * $radius + $y;

                    $label = $data['series'][$data['abscissa']]['data'][$Key];

                    if ($labelStacked) {
                        $this->writePieLabel($xc, $yc, $label, $angle, $settings, true, $x, $y, $radius);
                    } else {
                        $this->writePieLabel($xc, $yc, $label, $angle, $settings, false);
                    }
                }

                $offset = $i + $dataGapAngle;
                $ID++;
            }
        }

        if ($writeValues != null && !$shadow) {
            $step = 360 / (2 * Constant::PI * $radius);
            $offset = 0;
            $ID = count($values) - 1;
            $settings = ['align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE, 'r' => $valueR, 'g' => $valueG, 'b' => $valueB, 'alpha' => $valueAlpha];
            foreach ($values as $Key => $value) {
                $endAngle = ($value * $scaleFactor) + $offset;
                if ((int) $endAngle > 360) {
                    $endAngle = 0;
                }
                $angle = ($endAngle - $offset) / 2 + $offset;

                if ($valuePosition == Constant::PIE_VALUE_OUTSIDE) {
                    $xc = cos(($angle - 90) * Constant::PI / 180) * ($radius + $valuePadding) + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * ($radius + $valuePadding) + $y;
                } else {
                    $xc = cos(($angle - 90) * Constant::PI / 180) * ($radius) / 2 + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * ($radius) / 2 + $y;
                }

                if ($writeValues == Constant::PIE_VALUE_PERCENTAGE) {
                    $display = round((100 / $serieSum) * $value, $precision) . '%';
                } elseif ($writeValues == Constant::PIE_VALUE_NATURAL) {
                    $display = $value . $valueSuffix;
                }

                $this->chartObject->drawText($xc, $yc, $display, $settings);

                $offset = $endAngle + $dataGapAngle;
                $ID--;
            }
        }

        if ($drawLabels && $labelStacked) {
            $this->writeShiftedLabels();
        }

        $this->chartObject->shadow = $restoreShadow;

        return Constant::PIE_RENDERED;
    }

    /**
     * Draw a 3D pie chart.
     *
     * @param mixed $x
     * @param mixed $y
     * @param mixed $format
     */
    public function draw3DPie($x, $y, $format = []) {
        /* Rendering layout */
        $radius = isset($format['radius']) ? $format['radius'] : 80;
        $precision = isset($format['precision']) ? $format['precision'] : 0;
        $skewFactor = isset($format['skewFactor']) ? $format['skewFactor'] : .5;
        $sliceHeight = isset($format['sliceHeight']) ? $format['sliceHeight'] : 20;
        $dataGapAngle = isset($format['dataGapAngle']) ? $format['dataGapAngle'] : 0;
        $dataGapRadius = isset($format['dataGapRadius']) ? $format['dataGapRadius'] : 0;
        $secondPass = isset($format['secondPass']) ? $format['secondPass'] : true;
        $border = isset($format['border']) ? $format['border'] : false;
        $shadow = isset($format['shadow']) ? $format['shadow'] : false;
        $drawLabels = isset($format['drawLabels']) ? $format['drawLabels'] : false;
        $labelStacked = isset($format['labelStacked']) ? $format['labelStacked'] : false;
        $labelColor = isset($format['labelColor']) ? $format['labelColor'] : Constant::PIE_LABEL_COLOR_MANUAL;
        $labelR = isset($format['labelR']) ? $format['labelR'] : 0;
        $labelG = isset($format['labelG']) ? $format['labelG'] : 0;
        $labelB = isset($format['labelB']) ? $format['labelB'] : 0;
        $labelAlpha = isset($format['labelAlpha']) ? $format['labelAlpha'] : 100;
        $writeValues = isset($format['writeValues']) ? $format['writeValues'] : null; //PIE_VALUE_PERCENTAGE
        $valuePosition = isset($format['valuePosition']) ? $format['valuePosition'] : Constant::PIE_VALUE_INSIDE;
        $valuePadding = isset($format['valuePadding']) ? $format['valuePadding'] : 15;
        $valueSuffix = isset($format['valueSuffix']) ? $format['valueSuffix'] : '';
        $valueR = isset($format['valueR']) ? $format['valueR'] : 255;
        $valueG = isset($format['valueG']) ? $format['valueG'] : 255;
        $valueB = isset($format['valueB']) ? $format['valueB'] : 255;
        $valueAlpha = isset($format['valueAlpha']) ? $format['valueAlpha'] : 100;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;

        /* Error correction for overlaying rounded corners */
        if ($skewFactor < .5) {
            $skewFactor = .5;
        }

        /* Data Processing */
        $data = $this->dataObject->getData();
        $palette = $this->dataObject->getPalette();

        /* Do we have an abscissa serie defined? */
        if ($data['abscissa'] == '') {
            return Constant::PIE_NO_ABSCISSA;
        }

        /* Try to find the data serie */
        $dataSerie = '';
        foreach ($data['series'] as $serieName => $serieData) {
            if ($serieName != $data['abscissa']) {
                $dataSerie = $serieName;
            }
        }

        /* Do we have data to compute? */
        if ($dataSerie == '') {
            return Constant::PIE_NO_DATASERIE;
        }

        /* Remove unused data */
        list($data, $palette) = $this->clean0Values($data, $palette, $dataSerie, $data['abscissa']);

        /* Compute the pie sum */
        $serieSum = $this->dataObject->getSum($dataSerie);

        /* Do we have data to draw? */
        if ($serieSum == 0) {
            return Constant::PIE_SUMISNULL;
        }

        /* Dump the real number of data to draw */
        $values = [];
        foreach ($data['series'][$dataSerie]['data'] as $Key => $value) {
            if ($value != 0) {
                $values[] = $value;
            }
        }

        /* Compute the wasted angular space between series */
        if (count($values) == 1) {
            $wastedAngular = 0;
        } else {
            $wastedAngular = count($values) * $dataGapAngle;
        }

        /* Compute the scale */
        $scaleFactor = (360 - $wastedAngular) / $serieSum;

        $restoreShadow = $this->chartObject->shadow;
        if ($this->chartObject->shadow) {
            $this->chartObject->shadow = false;
        }

        /* Draw the polygon pie elements */
        $step = 360 / (2 * Constant::PI * $radius);
        $offset = 360;
        $ID = count($values) - 1;
        $values = array_reverse($values);
        $slice = 0;
        $slices = [];
        $sliceColors = [];
        $visible = [];
        $sliceAngle = [];
        foreach ($values as $Key => $value) {
            if (!isset($palette[$ID]['r'])) {
                $Color = $this->chartObject->getRandomColor();
                $palette[$ID] = $Color;
                $this->dataObject->savePalette($ID, $Color);
            }
            $settings = ['r' => $palette[$ID]['r'], 'g' => $palette[$ID]['g'], 'b' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];

            $sliceColors[$slice] = $settings;

            $startAngle = $offset;
            $endAngle = $offset - ($value * $scaleFactor);
            if ($endAngle < 0) {
                $endAngle = 0;
            }

            if ($startAngle > 180) {
                $visible[$slice]['start'] = true;
            } else {
                $visible[$slice]['start'] = true;
            }
            if ($endAngle < 180) {
                $visible[$slice]['End'] = false;
            } else {
                $visible[$slice]['End'] = true;
            }

            if ($dataGapAngle == 0) {
                $x0 = $x;
                $y0 = $y;
            } else {
                $angle = ($endAngle - $offset) / 2 + $offset;
                $x0 = cos(($angle - 90) * Constant::PI / 180) * $dataGapRadius + $x;
                $y0 = sin(($angle - 90) * Constant::PI / 180) * $dataGapRadius * $skewFactor + $y;
            }
            $slices[$slice][] = $x0;
            $slices[$slice][] = $y0;
            $sliceAngle[$slice][] = 0;

            for ($i = $offset; $i >= $endAngle; $i = $i - $step) {
                $xc = cos(($i - 90) * Constant::PI / 180) * $radius + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * $radius * $skewFactor + $y;

                if (($secondPass || $restoreShadow) && ($i < 90)) {
                    $yc++;
                }
                if (($secondPass || $restoreShadow) && ($i > 90 && $i < 180)) {
                    $xc++;
                }
                if (($secondPass || $restoreShadow) && ($i > 180 && $i < 270)) {
                    $xc++;
                }
                if (($secondPass || $restoreShadow) && ($i >= 270)) {
                    $xc++;
                    $yc++;
                }

                $slices[$slice][] = $xc;
                $slices[$slice][] = $yc;
                $sliceAngle[$slice][] = $i;
            }

            $offset = $i - $dataGapAngle;
            $ID--;
            $slice++;
        }

        /* Draw the bottom shadow if needed */
        if ($restoreShadow && ($this->chartObject->shadowX != 0 || $this->chartObject->shadowY != 0)) {
            foreach ($slices as $sliceID => $plots) {
                $shadowPie = [];
                for ($i = 0; $i < count($plots); $i = $i + 2) {
                    $shadowPie[] = $plots[$i] + $this->chartObject->shadowX;
                    $shadowPie[] = $plots[$i + 1] + $this->chartObject->shadowY;
                }

                $settings = ['r' => $this->chartObject->shadowR, 'g' => $this->chartObject->shadowG, 'b' => $this->chartObject->shadowB, 'alpha' => $this->chartObject->shadowa, 'NoBorder' => true];
                $this->chartObject->drawPolygon($shadowPie, $settings);
            }

            $step = 360 / (2 * Constant::PI * $radius);
            $offset = 360;
            foreach ($values as $Key => $value) {
                $endAngle = $offset - ($value * $scaleFactor);
                if ($endAngle < 0) {
                    $endAngle = 0;
                }

                for ($i = $offset; $i >= $endAngle; $i = $i - $step) {
                    $xc = cos(($i - 90) * Constant::PI / 180) * $radius + $x + $this->chartObject->shadowX;
                    $yc = sin(($i - 90) * Constant::PI / 180) * $radius * $skewFactor + $y + $this->chartObject->shadowY;

                    $this->chartObject->drawAntialiasPixel($xc, $yc, $settings);
                }

                $offset = $i - $dataGapAngle;
                $ID--;
            }
        }

        /* Draw the bottom pie splice */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['NoBorder'] = true;
            $this->chartObject->drawPolygon($plots, $settings);

            if ($secondPass) {
                $settings = $sliceColors[$sliceID];
                if ($border) {
                    $settings['r'] += 30;
                    $settings['g'] += 30;
                    $settings['b'] += 30;
                }

                if (isset($sliceAngle[$sliceID][1])) {
                    /* Empty error handling */
                    $angle = $sliceAngle[$sliceID][1];
                    $xc = cos(($angle - 90) * Constant::PI / 180) * $radius + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * $radius * $skewFactor + $y;
                    $this->chartObject->drawLine($plots[0], $plots[1], $xc, $yc, $settings);

                    $angle = $sliceAngle[$sliceID][count($sliceAngle[$sliceID]) - 1];
                    $xc = cos(($angle - 90) * Constant::PI / 180) * $radius + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * $radius * $skewFactor + $y;
                    $this->chartObject->drawLine($plots[0], $plots[1], $xc, $yc, $settings);
                }
            }
        }

        /* Draw the two vertical edges */
        $slices = array_reverse($slices);
        $sliceColors = array_reverse($sliceColors);
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['r'] += 10;
            $settings['g'] += 10;
            $settings['b'] += 10;
            $settings['NoBorder'] = true;

            if ($visible[$sliceID]['start'] && isset($plots[2])) {
                /* Empty error handling */
                $this->chartObject->drawLine($plots[2], $plots[3], $plots[2], $plots[3] - $sliceHeight, ['r' => $settings['r'], 'g' => $settings['g'], 'b' => $settings['b']]);
                $border = [];
                $border[] = $plots[0];
                $border[] = $plots[1];
                $border[] = $plots[0];
                $border[] = $plots[1] - $sliceHeight;
                $border[] = $plots[2];
                $border[] = $plots[3] - $sliceHeight;
                $border[] = $plots[2];
                $border[] = $plots[3];
                $this->chartObject->drawPolygon($border, $settings);
            }
        }

        $slices = array_reverse($slices);
        $sliceColors = array_reverse($sliceColors);
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['r'] += 10;
            $settings['g'] += 10;
            $settings['b'] += 10;
            $settings['NoBorder'] = true;
            if ($visible[$sliceID]['End']) {
                $this->chartObject->drawLine($plots[count($plots) - 2], $plots[count($plots) - 1], $plots[count($plots) - 2], $plots[count($plots) - 1] - $sliceHeight, ['r' => $settings['r'], 'g' => $settings['g'], 'b' => $settings['b']]);

                $border = [];
                $border[] = $plots[0];
                $border[] = $plots[1];
                $border[] = $plots[0];
                $border[] = $plots[1] - $sliceHeight;
                $border[] = $plots[count($plots) - 2];
                $border[] = $plots[count($plots) - 1] - $sliceHeight;
                $border[] = $plots[count($plots) - 2];
                $border[] = $plots[count($plots) - 1];
                $this->chartObject->drawPolygon($border, $settings);
            }
        }

        /* Draw the rounded edges */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['r'] += 10;
            $settings['g'] += 10;
            $settings['b'] += 10;
            $settings['NoBorder'] = true;

            for ($j = 2; $j < count($plots) - 2; $j = $j + 2) {
                $angle = $sliceAngle[$sliceID][$j / 2];
                if ($angle < 270 && $angle > 90) {
                    $border = [];
                    $border[] = $plots[$j];
                    $border[] = $plots[$j + 1];
                    $border[] = $plots[$j + 2];
                    $border[] = $plots[$j + 3];
                    $border[] = $plots[$j + 2];
                    $border[] = $plots[$j + 3] - $sliceHeight;
                    $border[] = $plots[$j];
                    $border[] = $plots[$j + 1] - $sliceHeight;
                    $this->chartObject->drawPolygon($border, $settings);
                }
            }

            if ($secondPass) {
                $settings = $sliceColors[$sliceID];
                if ($border) {
                    $settings['r'] += 30;
                    $settings['g'] += 30;
                    $settings['b'] += 30;
                }

                if (isset($sliceAngle[$sliceID][1])) {
                    /* Empty error handling */
                    $angle = $sliceAngle[$sliceID][1];
                    if ($angle < 270 && $angle > 90) {
                        $xc = cos(($angle - 90) * Constant::PI / 180) * $radius + $x;
                        $yc = sin(($angle - 90) * Constant::PI / 180) * $radius * $skewFactor + $y;
                        $this->chartObject->drawLine($xc, $yc, $xc, $yc - $sliceHeight, $settings);
                    }
                }

                $angle = $sliceAngle[$sliceID][count($sliceAngle[$sliceID]) - 1];
                if ($angle < 270 && $angle > 90) {
                    $xc = cos(($angle - 90) * Constant::PI / 180) * $radius + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * $radius * $skewFactor + $y;
                    $this->chartObject->drawLine($xc, $yc, $xc, $yc - $sliceHeight, $settings);
                }

                if (isset($sliceAngle[$sliceID][1]) && $sliceAngle[$sliceID][1] > 270 && $sliceAngle[$sliceID][count($sliceAngle[$sliceID]) - 1] < 270) {
                    $xc = cos((270 - 90) * Constant::PI / 180) * $radius + $x;
                    $yc = sin((270 - 90) * Constant::PI / 180) * $radius * $skewFactor + $y;
                    $this->chartObject->drawLine($xc, $yc, $xc, $yc - $sliceHeight, $settings);
                }

                if (isset($sliceAngle[$sliceID][1]) && $sliceAngle[$sliceID][1] > 90 && $sliceAngle[$sliceID][count($sliceAngle[$sliceID]) - 1] < 90) {
                    $xc = cos((0) * Constant::PI / 180) * $radius + $x;
                    $yc = sin((0) * Constant::PI / 180) * $radius * $skewFactor + $y;
                    $this->chartObject->drawLine($xc, $yc, $xc, $yc - $sliceHeight, $settings);
                }
            }
        }

        /* Draw the top splice */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['r'] += 20;
            $settings['g'] += 20;
            $settings['b'] += 20;

            $Top = [];
            for ($j = 0; $j < count($plots); $j = $j + 2) {
                $Top[] = $plots[$j];
                $Top[] = $plots[$j + 1] - $sliceHeight;
            }
            $this->chartObject->drawPolygon($Top, $settings);

            if ($recordImageMap && !$shadow) {
                $this->chartObject->addToImageMap('POLY', $this->arraySerialize($Top), $this->chartObject->toHTMLColor($settings['r'], $settings['g'], $settings['b']), $data['series'][$data['abscissa']]['data'][count($slices) - $sliceID - 1], $values[$sliceID]);
            }
        }

        /* Second pass to smooth the angles */
        if ($secondPass) {
            $step = 360 / (2 * Constant::PI * $radius);
            $offset = 360;
            $ID = count($values) - 1;
            foreach ($values as $Key => $value) {
                $FirstPoint = true;
                if ($shadow) {
                    $settings = ['r' => $this->chartObject->shadowR, 'g' => $this->chartObject->shadowG, 'b' => $this->chartObject->shadowB, 'alpha' => $this->chartObject->shadowa];
                } else {
                    if ($border) {
                        $settings = ['r' => $palette[$ID]['r'] + 30, 'g' => $palette[$ID]['g'] + 30, 'b' => $palette[$ID]['b'] + 30, 'alpha' => $palette[$ID]['alpha']];
                    } else {
                        $settings = ['r' => $palette[$ID]['r'], 'g' => $palette[$ID]['g'], 'b' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];
                    }
                }

                $endAngle = $offset - ($value * $scaleFactor);
                if ($endAngle < 0) {
                    $endAngle = 0;
                }

                if ($dataGapAngle == 0) {
                    $x0 = $x;
                    $y0 = $y - $sliceHeight;
                } else {
                    $angle = ($endAngle - $offset) / 2 + $offset;
                    $x0 = cos(($angle - 90) * Constant::PI / 180) * $dataGapRadius + $x;
                    $y0 = sin(($angle - 90) * Constant::PI / 180) * $dataGapRadius * $skewFactor + $y - $sliceHeight;
                }
                $plots[] = $x0;
                $plots[] = $y0;

                for ($i = $offset; $i >= $endAngle; $i = $i - $step) {
                    $xc = cos(($i - 90) * Constant::PI / 180) * $radius + $x;
                    $yc = sin(($i - 90) * Constant::PI / 180) * $radius * $skewFactor + $y - $sliceHeight;

                    if ($FirstPoint) {
                        $this->chartObject->drawLine($xc, $yc, $x0, $y0, $settings);
                    }
                    { $FirstPoint = false; }

                    $this->chartObject->drawAntialiasPixel($xc, $yc, $settings);
                    if ($i < 270 && $i > 90) {
                        $this->chartObject->drawAntialiasPixel($xc, $yc + $sliceHeight, $settings);
                    }
                }
                $this->chartObject->drawLine($xc, $yc, $x0, $y0, $settings);

                $offset = $i - $dataGapAngle;
                $ID--;
            }
        }

        if ($writeValues != null) {
            $step = 360 / (2 * Constant::PI * $radius);
            $offset = 360;
            $ID = count($values) - 1;
            $settings = ['align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE, 'r' => $valueR, 'g' => $valueG, 'b' => $valueB, 'alpha' => $valueAlpha];
            foreach ($values as $Key => $value) {
                $endAngle = $offset - ($value * $scaleFactor);
                if ($endAngle < 0) {
                    $endAngle = 0;
                }

                $angle = ($endAngle - $offset) / 2 + $offset;

                if ($valuePosition == Constant::PIE_VALUE_OUTSIDE) {
                    $xc = cos(($angle - 90) * Constant::PI / 180) * ($radius + $valuePadding) + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * (($radius * $skewFactor) + $valuePadding) + $y - $sliceHeight;
                } else {
                    $xc = cos(($angle - 90) * Constant::PI / 180) * ($radius) / 2 + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * ($radius * $skewFactor) / 2 + $y - $sliceHeight;
                }

                if ($writeValues == Constant::PIE_VALUE_PERCENTAGE) {
                    $display = round((100 / $serieSum) * $value, $precision) . '%';
                } elseif ($writeValues == Constant::PIE_VALUE_NATURAL) {
                    $display = $value . $valueSuffix;
                }

                $this->chartObject->drawText($xc, $yc, $display, $settings);

                $offset = $endAngle - $dataGapAngle;
                $ID--;
            }
        }

        if ($drawLabels) {
            $step = 360 / (2 * Constant::PI * $radius);
            $offset = 360;
            $ID = count($values) - 1;
            foreach ($values as $Key => $value) {
                if ($labelColor == Constant::PIE_LABEL_COLOR_AUTO) {
                    $settings = ['fillR' => $palette[$ID]['r'], 'fillG' => $palette[$ID]['g'], 'fillB' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];
                } else {
                    $settings = ['fillR' => $labelR, 'fillG' => $labelG, 'fillB' => $labelB, 'alpha' => $labelAlpha];
                }

                $endAngle = $offset - ($value * $scaleFactor);
                if ($endAngle < 0) {
                    $endAngle = 0;
                }

                $angle = ($endAngle - $offset) / 2 + $offset;
                $xc = cos(($angle - 90) * Constant::PI / 180) * $radius + $x;
                $yc = sin(($angle - 90) * Constant::PI / 180) * $radius * $skewFactor + $y - $sliceHeight;

                if (isset($data['series'][$data['abscissa']]['data'][$ID])) {
                    $label = $data['series'][$data['abscissa']]['data'][$ID];

                    if ($labelStacked) {
                        $this->writePieLabel($xc, $yc, $label, $angle, $settings, true, $x, $y, $radius, true);
                    } else {
                        $this->writePieLabel($xc, $yc, $label, $angle, $settings, false);
                    }
                }

                $offset = $endAngle - $dataGapAngle;
                $ID--;
            }
        }

        if ($drawLabels && $labelStacked) {
            $this->writeShiftedLabels();
        }

        $this->chartObject->shadow = $restoreShadow;

        return Constant::PIE_RENDERED;
    }

    /**
     * Draw a ring chart.
     *
     * @param mixed $x
     * @param mixed $y
     * @param mixed $format
     */
    public function draw2DRing($x, $y, $format = '') {
        $OuterRadius = isset($format['radius']) ? $format['radius'] : 60;
        $precision = isset($format['precision']) ? $format['precision'] : 0;
        $InnerRadius = isset($format['radius']) ? $format['radius'] : 30;
        $border = isset($format['border']) ? $format['border'] : false;
        $borderR = isset($format['borderR']) ? $format['borderR'] : 255;
        $borderG = isset($format['borderG']) ? $format['borderG'] : 255;
        $borderB = isset($format['borderB']) ? $format['borderB'] : 255;
        $borderAlpha = isset($format['borderAlpha']) ? $format['borderAlpha'] : 100;
        $shadow = isset($format['shadow']) ? $format['shadow'] : false;
        $drawLabels = isset($format['drawLabels']) ? $format['drawLabels'] : false;
        $labelStacked = isset($format['labelStacked']) ? $format['labelStacked'] : false;
        $labelColor = isset($format['labelColor']) ? $format['labelColor'] : Constant::PIE_LABEL_COLOR_MANUAL;
        $labelR = isset($format['labelR']) ? $format['labelR'] : 0;
        $labelG = isset($format['labelG']) ? $format['labelG'] : 0;
        $labelB = isset($format['labelB']) ? $format['labelB'] : 0;
        $labelAlpha = isset($format['labelAlpha']) ? $format['labelAlpha'] : 100;
        $writeValues = isset($format['writeValues']) ? $format['writeValues'] : null; //PIE_VALUE_PERCENTAGE
        $valuePadding = isset($format['valuePadding']) ? $format['valuePadding'] : 5;
        $valuePosition = isset($format['valuePosition']) ? $format['valuePosition'] : Constant::PIE_VALUE_OUTSIDE;
        $valueSuffix = isset($format['valueSuffix']) ? $format['valueSuffix'] : '';
        $valueR = isset($format['valueR']) ? $format['valueR'] : 255;
        $valueG = isset($format['valueG']) ? $format['valueG'] : 255;
        $valueB = isset($format['valueB']) ? $format['valueB'] : 255;
        $valueAlpha = isset($format['valueAlpha']) ? $format['valueAlpha'] : 100;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;

        /* Data Processing */
        $data = $this->dataObject->getData();
        $palette = $this->dataObject->getPalette();

        /* Do we have an abscissa serie defined? */
        if ($data['abscissa'] == '') {
            return Constant::PIE_NO_ABSCISSA;
        }

        /* Try to find the data serie */
        $dataSerie = '';
        foreach ($data['series'] as $serieName => $serieData) {
            if ($serieName != $data['abscissa']) {
                $dataSerie = $serieName;
            }
        }

        /* Do we have data to compute? */
        if ($dataSerie == '') {
            return Constant::PIE_NO_DATASERIE;
        }

        /* Remove unused data */
        list($data, $palette) = $this->clean0Values($data, $palette, $dataSerie, $data['abscissa']);

        /* Compute the pie sum */
        $serieSum = $this->dataObject->getSum($dataSerie);

        /* Do we have data to draw? */
        if ($serieSum == 0) {
            return Constant::PIE_SUMISNULL;
        }

        /* Dump the real number of data to draw */
        $values = [];
        foreach ($data['series'][$dataSerie]['data'] as $Key => $value) {
            if ($value != 0) {
                $values[] = $value;
            }
        }

        /* Compute the wasted angular space between series */
        if (count($values) == 1) {
            $wastedAngular = 0;
        } else {
            $wastedAngular = 0;
        } // count($values)

        /* Compute the scale */
        $scaleFactor = (360 - $wastedAngular) / $serieSum;

        $restoreShadow = $this->chartObject->shadow;
        if ($this->chartObject->shadow) {
            $this->chartObject->shadow = false;

            $shadowFormat = $format;
            $shadowFormat['shadow'] = true;
            $this->draw2DRing($x + $this->chartObject->shadowX, $y + $this->chartObject->shadowY, $shadowFormat);
        }

        /* Draw the polygon pie elements */
        $step = 360 / (2 * Constant::PI * $OuterRadius);
        $offset = 0;
        $ID = 0;
        foreach ($values as $Key => $value) {
            if ($shadow) {
                $settings = ['r' => $this->chartObject->shadowR, 'g' => $this->chartObject->shadowG, 'b' => $this->chartObject->shadowB, 'alpha' => $this->chartObject->shadowa];
                $borderColor = $settings;
            } else {
                if (!isset($palette[$ID]['r'])) {
                    $Color = $this->chartObject->getRandomColor();
                    $palette[$ID] = $Color;
                    $this->dataObject->savePalette($ID, $Color);
                }
                $settings = ['r' => $palette[$ID]['r'], 'g' => $palette[$ID]['g'], 'b' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];

                if ($border) {
                    $borderColor = ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $borderAlpha];
                } else {
                    $borderColor = $settings;
                }
            }

            $plots = [];
            $boundaries = '';
            $aAPixels = [];
            $endAngle = $offset + ($value * $scaleFactor);
            if ($endAngle > 360) {
                $endAngle = 360;
            }
            for ($i = $offset; $i <= $endAngle; $i = $i + $step) {
                $xc = cos(($i - 90) * Constant::PI / 180) * $OuterRadius + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * $OuterRadius + $y;

                if (!isset($boundaries[0]['x1'])) {
                    $boundaries[0]['x1'] = $xc;
                    $boundaries[0]['y1'] = $yc;
                }
                $aAPixels[] = [$xc, $yc];

                if ($i < 90) {
                    $yc++;
                }
                if ($i > 180 && $i < 270) {
                    $xc++;
                }
                if ($i >= 270) {
                    $xc++;
                    $yc++;
                }

                $plots[] = $xc;
                $plots[] = $yc;
            }
            $boundaries[1]['x1'] = $xc;
            $boundaries[1]['y1'] = $yc;
            $lasti = $endAngle;

            for ($i = $endAngle; $i >= $offset; $i = $i - $step) {
                $xc = cos(($i - 90) * Constant::PI / 180) * ($InnerRadius - 1) + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * ($InnerRadius - 1) + $y;

                if (!isset($boundaries[1]['x2'])) {
                    $boundaries[1]['x2'] = $xc;
                    $boundaries[1]['y2'] = $yc;
                }
                $aAPixels[] = [$xc, $yc];

                $xc = cos(($i - 90) * Constant::PI / 180) * $InnerRadius + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * $InnerRadius + $y;

                if ($i < 90) {
                    $yc++;
                }
                if ($i > 180 && $i < 270) {
                    $xc++;
                }
                if ($i >= 270) {
                    $xc++;
                    $yc++;
                }

                $plots[] = $xc;
                $plots[] = $yc;
            }
            $boundaries[0]['x2'] = $xc;
            $boundaries[0]['y2'] = $yc;

            /* Draw the polygon */
            $this->chartObject->drawPolygon($plots, $settings);
            if ($recordImageMap && !$shadow) {
                $this->chartObject->addToImageMap('POLY', $this->arraySerialize($plots), $this->chartObject->toHTMLColor($palette[$ID]['r'], $palette[$ID]['g'], $palette[$ID]['b']), $data['series'][$data['abscissa']]['data'][$Key], $value);
            }

            /* Smooth the edges using AA */
            foreach ($aAPixels as $iKey => $pos) {
                $this->chartObject->drawAntialiasPixel($pos[0], $pos[1], $borderColor);
            }
            $this->chartObject->drawLine($boundaries[0]['x1'], $boundaries[0]['y1'], $boundaries[0]['x2'], $boundaries[0]['y2'], $borderColor);
            $this->chartObject->drawLine($boundaries[1]['x1'], $boundaries[1]['y1'], $boundaries[1]['x2'], $boundaries[1]['y2'], $borderColor);

            if ($drawLabels && !$shadow) {
                if ($labelColor == Constant::PIE_LABEL_COLOR_AUTO) {
                    $settings = ['fillR' => $palette[$ID]['r'], 'fillG' => $palette[$ID]['g'], 'fillB' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];
                } else {
                    $settings = ['fillR' => $labelR, 'fillG' => $labelG, 'fillB' => $labelB, 'alpha' => $labelAlpha];
                }

                $angle = ($endAngle - $offset) / 2 + $offset;
                $xc = cos(($angle - 90) * Constant::PI / 180) * $OuterRadius + $x;
                $yc = sin(($angle - 90) * Constant::PI / 180) * $OuterRadius + $y;

                $label = $data['series'][$data['abscissa']]['data'][$Key];

                if ($labelStacked) {
                    $this->writePieLabel($xc, $yc, $label, $angle, $settings, true, $x, $y, $OuterRadius);
                } else {
                    $this->writePieLabel($xc, $yc, $label, $angle, $settings, false);
                }
            }

            $offset = $lasti;
            $ID++;
        }

        if ($drawLabels && $labelStacked) {
            $this->writeShiftedLabels();
        }

        if ($writeValues && !$shadow) {
            $step = 360 / (2 * Constant::PI * $OuterRadius);
            $offset = 0;
            foreach ($values as $Key => $value) {
                $endAngle = $offset + ($value * $scaleFactor);
                if ($endAngle > 360) {
                    $endAngle = 360;
                }

                $angle = $offset + ($value * $scaleFactor) / 2;
                if ($valuePosition == Constant::PIE_VALUE_OUTSIDE) {
                    $xc = cos(($angle - 90) * Constant::PI / 180) * ($OuterRadius + $valuePadding) + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * ($OuterRadius + $valuePadding) + $y;
                    if ($angle >= 0 && $angle <= 90) {
                        $align = Constant::TEXT_ALIGN_BOTTOMLEFT;
                    }
                    if ($angle > 90 && $angle <= 180) {
                        $align = Constant::TEXT_ALIGN_TOPLEFT;
                    }
                    if ($angle > 180 && $angle <= 270) {
                        $align = Constant::TEXT_ALIGN_TOPRIGHT;
                    }
                    if ($angle > 270) {
                        $align = Constant::TEXT_ALIGN_BOTTOMRIGHT;
                    }
                } else {
                    $xc = cos(($angle - 90) * Constant::PI / 180) * (($OuterRadius - $InnerRadius) / 2 + $InnerRadius) + $x;
                    $yc = sin(($angle - 90) * Constant::PI / 180) * (($OuterRadius - $InnerRadius) / 2 + $InnerRadius) + $y;
                    $align = Constant::TEXT_ALIGN_MIDDLEMIDDLE;
                }

                if ($writeValues == Constant::PIE_VALUE_PERCENTAGE) {
                    $display = round((100 / $serieSum) * $value, $precision) . '%';
                } elseif ($writeValues == Constant::PIE_VALUE_NATURAL) {
                    $display = $value . $valueSuffix;
                } else {
                    $label = '';
                }

                $this->chartObject->drawText($xc, $yc, $display, ['align' => $align, 'r' => $valueR, 'g' => $valueG, 'b' => $valueB]);
                $offset = $endAngle;
            }
        }

        $this->chartObject->shadow = $restoreShadow;

        return Constant::PIE_RENDERED;
    }

    /**
     * Draw a 3D ring chart.
     *
     * @param mixed $x
     * @param mixed $y
     * @param mixed $format
     */
    public function draw3DRing($x, $y, $format = '') {
        $OuterRadius = isset($format['OuterRadius']) ? $format['OuterRadius'] : 100;
        $precision = isset($format['precision']) ? $format['precision'] : 0;
        $InnerRadius = isset($format['InnerRadius']) ? $format['InnerRadius'] : 30;
        $skewFactor = isset($format['skewFactor']) ? $format['skewFactor'] : .6;
        $sliceHeight = isset($format['sliceHeight']) ? $format['sliceHeight'] : 10;
        $dataGapAngle = isset($format['dataGapAngle']) ? $format['dataGapAngle'] : 10;
        $dataGapRadius = isset($format['dataGapRadius']) ? $format['dataGapRadius'] : 10;
        $border = isset($format['border']) ? $format['border'] : false;
        $shadow = isset($format['shadow']) ? $format['shadow'] : false;
        $drawLabels = isset($format['drawLabels']) ? $format['drawLabels'] : false;
        $labelStacked = isset($format['labelStacked']) ? $format['labelStacked'] : false;
        $labelColor = isset($format['labelColor']) ? $format['labelColor'] : Constant::PIE_LABEL_COLOR_MANUAL;
        $labelR = isset($format['labelR']) ? $format['labelR'] : 0;
        $labelG = isset($format['labelG']) ? $format['labelG'] : 0;
        $labelB = isset($format['labelB']) ? $format['labelB'] : 0;
        $labelAlpha = isset($format['labelAlpha']) ? $format['labelAlpha'] : 100;
        $Cf = isset($format['Cf']) ? $format['Cf'] : 20;
        $writeValues = isset($format['writeValues']) ? $format['writeValues'] : Constant::PIE_VALUE_NATURAL;
        $valuePadding = isset($format['valuePadding']) ? $format['valuePadding'] : $sliceHeight + 15;
        $valuePosition = isset($format['valuePosition']) ? $format['valuePosition'] : Constant::PIE_VALUE_OUTSIDE;
        $valueSuffix = isset($format['valueSuffix']) ? $format['valueSuffix'] : '';
        $valueR = isset($format['valueR']) ? $format['valueR'] : 255;
        $valueG = isset($format['valueG']) ? $format['valueG'] : 255;
        $valueB = isset($format['valueB']) ? $format['valueB'] : 255;
        $valueAlpha = isset($format['valueAlpha']) ? $format['valueAlpha'] : 100;
        $recordImageMap = isset($format['recordImageMap']) ? $format['recordImageMap'] : false;

        /* Error correction for overlaying rounded corners */
        if ($skewFactor < .5) {
            $skewFactor = .5;
        }

        /* Data Processing */
        $data = $this->dataObject->getData();
        $palette = $this->dataObject->getPalette();

        /* Do we have an abscissa serie defined? */
        if ($data['abscissa'] == '') {
            return Constant::PIE_NO_ABSCISSA;
        }

        /* Try to find the data serie */
        $dataSerie = '';
        foreach ($data['series'] as $serieName => $serieData) {
            if ($serieName != $data['abscissa']) {
                $dataSerie = $serieName;
            }
        }

        /* Do we have data to compute? */
        if ($dataSerie == '') {
            return Constant::PIE_NO_DATASERIE;
        }

        /* Remove unused data */
        list($data, $palette) = $this->clean0Values($data, $palette, $dataSerie, $data['abscissa']);

        /* Compute the pie sum */
        $serieSum = $this->dataObject->getSum($dataSerie);

        /* Do we have data to draw? */
        if ($serieSum == 0) {
            return Constant::PIE_SUMISNULL;
        }

        /* Dump the real number of data to draw */
        $values = [];
        foreach ($data['series'][$dataSerie]['data'] as $Key => $value) {
            if ($value != 0) {
                $values[] = $value;
            }
        }

        /* Compute the wasted angular space between series */
        if (count($values) == 1) {
            $wastedAngular = 0;
        } else {
            $wastedAngular = count($values) * $dataGapAngle;
        }

        /* Compute the scale */
        $scaleFactor = (360 - $wastedAngular) / $serieSum;

        $restoreShadow = $this->chartObject->shadow;
        if ($this->chartObject->shadow) {
            $this->chartObject->shadow = false;
        }

        /* Draw the polygon ring elements */
        $offset = 360;
        $ID = count($values) - 1;
        $values = array_reverse($values);
        $slice = 0;
        $slices = [];
        $sliceColors = [];
        $visible = '';
        $sliceAngle = '';
        foreach ($values as $Key => $value) {
            if (!isset($palette[$ID]['r'])) {
                $Color = $this->chartObject->getRandomColor();
                $palette[$ID] = $Color;
                $this->dataObject->savePalette($ID, $Color);
            }
            $settings = ['r' => $palette[$ID]['r'], 'g' => $palette[$ID]['g'], 'b' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];

            $sliceColors[$slice] = $settings;

            $startAngle = $offset;
            $endAngle = $offset - ($value * $scaleFactor);
            if ($endAngle < 0) {
                $endAngle = 0;
            }

            if ($startAngle > 180) {
                $visible[$slice]['start'] = true;
            } else {
                $visible[$slice]['start'] = true;
            }
            if ($endAngle < 180) {
                $visible[$slice]['End'] = false;
            } else {
                $visible[$slice]['End'] = true;
            }

            $step = (360 / (2 * Constant::PI * $OuterRadius)) / 2;
            $OutX1 = Constant::VOID;
            $OutY1 = Constant::VOID;
            for ($i = $offset; $i >= $endAngle; $i = $i - $step) {
                $xc = cos(($i - 90) * Constant::PI / 180) * ($OuterRadius + $dataGapRadius - 2) + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * ($OuterRadius + $dataGapRadius - 2) * $skewFactor + $y;
                $slices[$slice]['aA'][] = [$xc, $yc];

                $xc = cos(($i - 90) * Constant::PI / 180) * ($OuterRadius + $dataGapRadius - 1) + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * ($OuterRadius + $dataGapRadius - 1) * $skewFactor + $y;
                $slices[$slice]['aA'][] = [$xc, $yc];

                $xc = cos(($i - 90) * Constant::PI / 180) * ($OuterRadius + $dataGapRadius) + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * ($OuterRadius + $dataGapRadius) * $skewFactor + $y;
                $this->chartObject->drawAntialiasPixel($xc, $yc, $settings);

                if ($OutX1 == Constant::VOID) {
                    $OutX1 = $xc;
                    $OutY1 = $yc;
                }

                if ($i < 90) {
                    $yc++;
                }
                if ($i > 90 && $i < 180) {
                    $xc++;
                }
                if ($i > 180 && $i < 270) {
                    $xc++;
                }
                if ($i >= 270) {
                    $xc++;
                    $yc++;
                }

                $slices[$slice]['bottomPoly'][] = floor($xc);
                $slices[$slice]['bottomPoly'][] = floor($yc);
                $slices[$slice]['TopPoly'][] = floor($xc);
                $slices[$slice]['TopPoly'][] = floor($yc) - $sliceHeight;
                $slices[$slice]['angle'][] = $i;
            }
            $OutX2 = $xc;
            $OutY2 = $yc;

            $slices[$slice]['angle'][] = Constant::VOID;
            $lasti = $i;

            $step = (360 / (2 * Constant::PI * $InnerRadius)) / 2;
            $InX1 = Constant::VOID;
            $InY1 = Constant::VOID;
            for ($i = $endAngle; $i <= $offset; $i = $i + $step) {
                $xc = cos(($i - 90) * Constant::PI / 180) * ($InnerRadius + $dataGapRadius - 1) + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * ($InnerRadius + $dataGapRadius - 1) * $skewFactor + $y;
                $slices[$slice]['aA'][] = [$xc, $yc];

                $xc = cos(($i - 90) * Constant::PI / 180) * ($InnerRadius + $dataGapRadius) + $x;
                $yc = sin(($i - 90) * Constant::PI / 180) * ($InnerRadius + $dataGapRadius) * $skewFactor + $y;
                $slices[$slice]['aA'][] = [$xc, $yc];

                if ($InX1 == Constant::VOID) {
                    $InX1 = $xc;
                    $InY1 = $yc;
                }

                if ($i < 90) {
                    $yc++;
                }
                if ($i > 90 && $i < 180) {
                    $xc++;
                }
                if ($i > 180 && $i < 270) {
                    $xc++;
                }
                if ($i >= 270) {
                    $xc++;
                    $yc++;
                }

                $slices[$slice]['bottomPoly'][] = floor($xc);
                $slices[$slice]['bottomPoly'][] = floor($yc);
                $slices[$slice]['TopPoly'][] = floor($xc);
                $slices[$slice]['TopPoly'][] = floor($yc) - $sliceHeight;
                $slices[$slice]['angle'][] = $i;
            }
            $InX2 = $xc;
            $InY2 = $yc;

            $slices[$slice]['InX1'] = $InX1;
            $slices[$slice]['InY1'] = $InY1;
            $slices[$slice]['InX2'] = $InX2;
            $slices[$slice]['InY2'] = $InY2;
            $slices[$slice]['OutX1'] = $OutX1;
            $slices[$slice]['OutY1'] = $OutY1;
            $slices[$slice]['OutX2'] = $OutX2;
            $slices[$slice]['OutY2'] = $OutY2;

            $offset = $lasti - $dataGapAngle;
            $ID--;
            $slice++;
        }

        /* Draw the bottom pie splice */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['NoBorder'] = true;
            $this->chartObject->drawPolygon($plots['bottomPoly'], $settings);

            foreach ($plots['aA'] as $Key => $pos) {
                $this->chartObject->drawAntialiasPixel($pos[0], $pos[1], $settings);
            }

            $this->chartObject->drawLine($plots['InX1'], $plots['InY1'], $plots['OutX2'], $plots['OutY2'], $settings);
            $this->chartObject->drawLine($plots['InX2'], $plots['InY2'], $plots['OutX1'], $plots['OutY1'], $settings);
        }

        $slices = array_reverse($slices);
        $sliceColors = array_reverse($sliceColors);

        /* Draw the vertical edges (semi-visible) */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['NoBorder'] = true;
            $settings['r'] = $settings['r'] + $Cf;
            $settings['g'] = $settings['g'] + $Cf;
            $settings['b'] = $settings['b'] + $Cf;

            $startAngle = $plots['angle'][0];
            foreach ($plots['angle'] as $Key => $angle) {
                if ($angle == Constant::VOID) {
                    $endAngle = $plots['angle'][$Key - 1];
                }
            }

            if ($startAngle >= 270 || $startAngle <= 90) {
                $this->chartObject->drawLine($plots['OutX1'], $plots['OutY1'], $plots['OutX1'], $plots['OutY1'] - $sliceHeight, $settings);
            }
            if ($startAngle >= 270 || $startAngle <= 90) {
                $this->chartObject->drawLine($plots['OutX2'], $plots['OutY2'], $plots['OutX2'], $plots['OutY2'] - $sliceHeight, $settings);
            }

            $this->chartObject->drawLine($plots['InX1'], $plots['InY1'], $plots['InX1'], $plots['InY1'] - $sliceHeight, $settings);
            $this->chartObject->drawLine($plots['InX2'], $plots['InY2'], $plots['InX2'], $plots['InY2'] - $sliceHeight, $settings);
        }

        /* Draw the inner vertical slices */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['NoBorder'] = true;
            $settings['r'] = $settings['r'] + $Cf;
            $settings['g'] = $settings['g'] + $Cf;
            $settings['b'] = $settings['b'] + $Cf;

            $Outer = true;
            $Inner = false;
            $InnerPlotsA = '';
            $InnerPlotsB = '';
            foreach ($plots['angle'] as $ID => $angle) {
                if ($angle == Constant::VOID) {
                    $Outer = false;
                    $Inner = true;
                } elseif ($Inner) {
                    if (($angle < 90 || $angle > 270) && isset($plots['bottomPoly'][$ID * 2])) {
                        $xo = $plots['bottomPoly'][$ID * 2];
                        $yo = $plots['bottomPoly'][$ID * 2 + 1];

                        $InnerPlotsA[] = $xo;
                        $InnerPlotsA[] = $yo;
                        $InnerPlotsB[] = $xo;
                        $InnerPlotsB[] = $yo - $sliceHeight;
                    }
                }
            }

            if ($InnerPlotsA != '') {
                $InnerPlots = array_merge($InnerPlotsA, $this->arrayReverse($InnerPlotsB));
                $this->chartObject->drawPolygon($InnerPlots, $settings);
            }
        }

        /* Draw the splice top and left poly */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['NoBorder'] = true;
            $settings['r'] = $settings['r'] + $Cf * 1.5;
            $settings['g'] = $settings['g'] + $Cf * 1.5;
            $settings['b'] = $settings['b'] + $Cf * 1.5;

            $startAngle = $plots['angle'][0];
            foreach ($plots['angle'] as $Key => $angle) {
                if ($angle == Constant::VOID) {
                    $endAngle = $plots['angle'][$Key - 1];
                }
            }

            if ($startAngle < 180) {
                $points = [];
                $points[] = $plots['InX2'];
                $points[] = $plots['InY2'];
                $points[] = $plots['InX2'];
                $points[] = $plots['InY2'] - $sliceHeight;
                $points[] = $plots['OutX1'];
                $points[] = $plots['OutY1'] - $sliceHeight;
                $points[] = $plots['OutX1'];
                $points[] = $plots['OutY1'];

                $this->chartObject->drawPolygon($points, $settings);
            }

            if ($endAngle > 180) {
                $points = [];
                $points[] = $plots['InX1'];
                $points[] = $plots['InY1'];
                $points[] = $plots['InX1'];
                $points[] = $plots['InY1'] - $sliceHeight;
                $points[] = $plots['OutX2'];
                $points[] = $plots['OutY2'] - $sliceHeight;
                $points[] = $plots['OutX2'];
                $points[] = $plots['OutY2'];

                $this->chartObject->drawPolygon($points, $settings);
            }
        }

        /* Draw the vertical edges (visible) */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['NoBorder'] = true;
            $settings['r'] = $settings['r'] + $Cf;
            $settings['g'] = $settings['g'] + $Cf;
            $settings['b'] = $settings['b'] + $Cf;

            $startAngle = $plots['angle'][0];
            foreach ($plots['angle'] as $Key => $angle) {
                if ($angle == Constant::VOID) {
                    $endAngle = $plots['angle'][$Key - 1];
                }
            }

            if ($startAngle <= 270 && $startAngle >= 90) {
                $this->chartObject->drawLine($plots['OutX1'], $plots['OutY1'], $plots['OutX1'], $plots['OutY1'] - $sliceHeight, $settings);
            }
            if ($endAngle <= 270 && $endAngle >= 90) {
                $this->chartObject->drawLine($plots['OutX2'], $plots['OutY2'], $plots['OutX2'], $plots['OutY2'] - $sliceHeight, $settings);
            }
        }

        /* Draw the outer vertical slices */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['NoBorder'] = true;
            $settings['r'] = $settings['r'] + $Cf;
            $settings['g'] = $settings['g'] + $Cf;
            $settings['b'] = $settings['b'] + $Cf;

            $Outer = true;
            $Inner = false;
            $OuterPlotsA = '';
            $OuterPlotsB = '';
            $InnerPlotsA = '';
            $InnerPlotsB = '';
            foreach ($plots['angle'] as $ID => $angle) {
                if ($angle == Constant::VOID) {
                    $Outer = false;
                    $Inner = true;
                } elseif ($Outer) {
                    if (($angle > 90 && $angle < 270) && isset($plots['bottomPoly'][$ID * 2])) {
                        $xo = $plots['bottomPoly'][$ID * 2];
                        $yo = $plots['bottomPoly'][$ID * 2 + 1];

                        $OuterPlotsA[] = $xo;
                        $OuterPlotsA[] = $yo;
                        $OuterPlotsB[] = $xo;
                        $OuterPlotsB[] = $yo - $sliceHeight;
                    }
                }
            }
            if ($OuterPlotsA != '') {
                $OuterPlots = array_merge($OuterPlotsA, $this->arrayReverse($OuterPlotsB));
                $this->chartObject->drawPolygon($OuterPlots, $settings);
            }
        }

        $slices = array_reverse($slices);
        $sliceColors = array_reverse($sliceColors);

        /* Draw the top pie splice */
        foreach ($slices as $sliceID => $plots) {
            $settings = $sliceColors[$sliceID];
            $settings['NoBorder'] = true;
            $settings['r'] = $settings['r'] + $Cf * 2;
            $settings['g'] = $settings['g'] + $Cf * 2;
            $settings['b'] = $settings['b'] + $Cf * 2;

            $this->chartObject->drawPolygon($plots['TopPoly'], $settings);

            if ($recordImageMap) {
                $this->chartObject->addToImageMap('POLY', $this->arraySerialize($plots['TopPoly']), $this->chartObject->toHTMLColor($settings['r'], $settings['g'], $settings['b']), $data['series'][$data['abscissa']]['data'][$sliceID], $data['series'][$dataSerie]['data'][count($slices) - $sliceID - 1]);
            }

            foreach ($plots['aA'] as $Key => $pos) {
                $this->chartObject->drawAntialiasPixel($pos[0], $pos[1] - $sliceHeight, $settings);
            }

            $this->chartObject->drawLine($plots['InX1'], $plots['InY1'] - $sliceHeight, $plots['OutX2'], $plots['OutY2'] - $sliceHeight, $settings);
            $this->chartObject->drawLine($plots['InX2'], $plots['InY2'] - $sliceHeight, $plots['OutX1'], $plots['OutY1'] - $sliceHeight, $settings);
        }

        if ($drawLabels) {
            $offset = 360;
            foreach ($values as $Key => $value) {
                $startAngle = $offset;
                $endAngle = $offset - ($value * $scaleFactor);
                if ($endAngle < 0) {
                    $endAngle = 0;
                }

                if ($labelColor == Constant::PIE_LABEL_COLOR_AUTO) {
                    $settings = ['fillR' => $palette[$ID]['r'], 'fillG' => $palette[$ID]['g'], 'fillB' => $palette[$ID]['b'], 'alpha' => $palette[$ID]['alpha']];
                } else {
                    $settings = ['fillR' => $labelR, 'fillG' => $labelG, 'fillB' => $labelB, 'alpha' => $labelAlpha];
                }

                $angle = ($endAngle - $offset) / 2 + $offset;
                $xc = cos(($angle - 90) * Constant::PI / 180) * ($OuterRadius + $dataGapRadius) + $x;
                $yc = sin(($angle - 90) * Constant::PI / 180) * ($OuterRadius + $dataGapRadius) * $skewFactor + $y;

                if ($writeValues == Constant::PIE_VALUE_PERCENTAGE) {
                    $label = $display = round((100 / $serieSum) * $value, $precision) . '%';
                } elseif ($writeValues == Constant::PIE_VALUE_NATURAL) {
                    $label = $data['series'][$data['abscissa']]['data'][$Key];
                } else {
                    $label = '';
                }

                if ($labelStacked) {
                    $this->writePieLabel($xc, $yc - $sliceHeight, $label, $angle, $settings, true, $x, $y, $OuterRadius);
                } else {
                    $this->writePieLabel($xc, $yc - $sliceHeight, $label, $angle, $settings, false);
                }

                $offset = $endAngle - $dataGapAngle;
                $ID--;
                $slice++;
            }
        }
        if ($drawLabels && $labelStacked) {
            $this->writeShiftedLabels();
        }

        $this->chartObject->shadow = $restoreShadow;

        return Constant::PIE_RENDERED;
    }
}
