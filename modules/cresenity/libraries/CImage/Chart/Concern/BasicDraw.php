<?php
use CImage_Chart_Constant as Constant;

trait CImage_Chart_Concern_BasicDraw {
    /**
     * Draw a polygon
     *
     * @param array $points
     * @param array $format
     */
    public function drawPolygon(array $points, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $noFill = isset($format['noFill']) ? $format['noFill'] : false;
        $noBorder = isset($format['noBorder']) ? $format['noBorder'] : false;
        $borderR = isset($format['borderR']) ? $format['borderR'] : $r;
        $borderG = isset($format['borderG']) ? $format['borderG'] : $g;
        $borderB = isset($format['borderB']) ? $format['borderB'] : $b;
        $borderalpha = isset($format['alpha']) ? $format['alpha'] : $alpha / 2;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        $skipX = isset($format['skipX']) ? $format['skipX'] : Constant::OUT_OF_SIGHT;
        $skipY = isset($format['skipY']) ? $format['skipY'] : Constant::OUT_OF_SIGHT;
        /* Calling the ImageFilledPolygon() public function over the $points array will round it */
        $backup = $points;
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        if ($skipX != Constant::OUT_OF_SIGHT) {
            $skipX = floor($skipX);
        }
        if ($skipY != Constant::OUT_OF_SIGHT) {
            $skipY = floor($skipY);
        }
        $restoreShadow = $this->shadow;
        if (!$noFill) {
            if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
                $this->shadow = false;
                for ($i = 0; $i <= count($points) - 1; $i = $i + 2) {
                    $shadow[] = $points[$i] + $this->shadowX;
                    $shadow[] = $points[$i + 1] + $this->shadowY;
                }
                $this->drawPolygon(
                    $shadow,
                    [
                        'r' => $this->shadowR,
                        'g' => $this->shadowG,
                        'b' => $this->shadowB,
                        'alpha' => $this->shadowA,
                        'noBorder' => true
                    ]
                );
            }
            $fillColor = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            if (count($points) >= 6) {
                ImageFilledPolygon($this->picture, $points, count($points) / 2, $fillColor);
            }
        }
        if (!$noBorder) {
            $points = $backup;
            if ($noFill) {
                $borderSettings = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha];
            } else {
                $borderSettings = [
                    'r' => $borderR,
                    'g' => $borderG,
                    'b' => $borderB,
                    'alpha' => $borderalpha
                ];
            }
            for ($i = 0; $i <= count($points) - 1; $i = $i + 2) {
                if (isset($points[$i + 2]) && !($points[$i] == $points[$i + 2] && $points[$i] == $skipX) && !($points[$i + 1] == $points[$i + 3] && $points[$i + 1] == $skipY)
                ) {
                    $this->drawLine(
                        $points[$i],
                        $points[$i + 1],
                        $points[$i + 2],
                        $points[$i + 3],
                        $borderSettings
                    );
                } elseif (!($points[$i] == $points[0] && $points[$i] == $skipX) && !($points[$i + 1] == $points[1] && $points[$i + 1] == $skipY)
                ) {
                    $this->drawLine($points[$i], $points[$i + 1], $points[0], $points[1], $borderSettings);
                }
            }
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Drawn a spline based on the bezier public function
     *
     * @param array $coordinates
     * @param array $format
     *
     * @return array
     */
    public function drawSpline(array $coordinates, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $force = isset($format['force']) ? $format['force'] : 30;
        $forces = isset($format['forces']) ? $format['forces'] : null;
        $showC = isset($format['showControl']) ? $format['showControl'] : false;
        $ticks = isset($format['ticks']) ? $format['ticks'] : null;
        $pathOnly = isset($format['pathOnly']) ? $format['pathOnly'] : false;
        $weight = isset($format['weight']) ? $format['weight'] : null;
        $Cpt = null;
        $mode = null;
        $result = [];
        $xLast = null;
        $yLast = null;
        for ($i = 1; $i <= count($coordinates) - 1; $i++) {
            $x1 = $coordinates[$i - 1][0];
            $y1 = $coordinates[$i - 1][1];
            $x2 = $coordinates[$i][0];
            $y2 = $coordinates[$i][1];
            if ($forces != null) {
                $force = $forces[$i];
            }
            /* First segment */
            if ($i == 1) {
                $xv1 = $x1;
                $yv1 = $y1;
            } else {
                $angle1 = $this->getAngle($xLast, $yLast, $x1, $y1);
                $angle2 = $this->getAngle($x1, $y1, $x2, $y2);
                $xOff = cos($angle2 * Constant::PI / 180) * $force + $x1;
                $yOff = sin($angle2 * Constant::PI / 180) * $force + $y1;
                $xv1 = cos($angle1 * Constant::PI / 180) * $force + $xOff;
                $yv1 = sin($angle1 * Constant::PI / 180) * $force + $yOff;
            }
            /* Last segment */
            if ($i == count($coordinates) - 1) {
                $xv2 = $x2;
                $yv2 = $y2;
            } else {
                $angle1 = $this->getAngle($x2, $y2, $coordinates[$i + 1][0], $coordinates[$i + 1][1]);
                $angle2 = $this->getAngle($x1, $y1, $x2, $y2);
                $xOff = cos(($angle2 + 180) * Constant::PI / 180) * $force + $x2;
                $yOff = sin(($angle2 + 180) * Constant::PI / 180) * $force + $y2;
                $xv2 = cos(($angle1 + 180) * Constant::PI / 180) * $force + $xOff;
                $yv2 = sin(($angle1 + 180) * Constant::PI / 180) * $force + $yOff;
            }
            $path = $this->drawBezier($x1, $y1, $x2, $y2, $xv1, $yv1, $xv2, $yv2, $format);
            if ($pathOnly) {
                $result[] = $path;
            }
            $xLast = $x1;
            $yLast = $y1;
        }
        return $result;
    }

    /**
     * Draw a bezier curve with two controls points
     *
     * @param int   $x1
     * @param int   $y1
     * @param int   $x2
     * @param int   $y2
     * @param int   $xv1
     * @param int   $yv1
     * @param int   $xv2
     * @param int   $yv2
     * @param array $format
     *
     * @return array
     */
    public function drawBezier($x1, $y1, $x2, $y2, $xv1, $yv1, $xv2, $yv2, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $showC = isset($format['showControl']) ? $format['showControl'] : false;
        $segments = isset($format['segments']) ? $format['segments'] : null;
        $ticks = isset($format['ticks']) ? $format['ticks'] : null;
        $NoDraw = isset($format['noDraw']) ? $format['noDraw'] : false;
        $pathOnly = isset($format['pathOnly']) ? $format['pathOnly'] : false;
        $weight = isset($format['weight']) ? $format['weight'] : null;
        $drawArrow = isset($format['drawArrow']) ? $format['drawArrow'] : false;
        $arrowSize = isset($format['arrowSize']) ? $format['arrowSize'] : 10;
        $arrowRatio = isset($format['arrowRatio']) ? $format['arrowRatio'] : .5;
        $arrowTwoHeads = isset($format['arrowTwoHeads']) ? $format['arrowTwoHeads'] : false;
        if ($segments == null) {
            $length = $this->getLength($x1, $y1, $x2, $y2);
            $precision = ($length * 125) / 1000;
        } else {
            $precision = $segments;
        }
        $p[0]['x'] = $x1;
        $p[0]['y'] = $y1;
        $p[1]['x'] = $xv1;
        $p[1]['y'] = $yv1;
        $p[2]['x'] = $xv2;
        $p[2]['y'] = $yv2;
        $p[3]['x'] = $x2;
        $p[3]['y'] = $y2;
        /* Compute the bezier points */
        $Q = [];
        $id = 0;
        for ($i = 0; $i <= $precision; $i = $i + 1) {
            $u = $i / $precision;
            $C = [];
            $C[0] = (1 - $u) * (1 - $u) * (1 - $u);
            $C[1] = ($u * 3) * (1 - $u) * (1 - $u);
            $C[2] = 3 * $u * $u * (1 - $u);
            $C[3] = $u * $u * $u;
            for ($j = 0; $j <= 3; $j++) {
                if (!isset($Q[$id])) {
                    $Q[$id] = [];
                }
                if (!isset($Q[$id]['x'])) {
                    $Q[$id]['x'] = 0;
                }
                if (!isset($Q[$id]['y'])) {
                    $Q[$id]['y'] = 0;
                }
                $Q[$id]['x'] = $Q[$id]['x'] + $p[$j]['x'] * $C[$j];
                $Q[$id]['y'] = $Q[$id]['y'] + $p[$j]['y'] * $C[$j];
            }
            $id++;
        }
        $Q[$id]['x'] = $x2;
        $Q[$id]['y'] = $y2;
        if (!$NoDraw) {
            /* Display the control points */
            if ($showC && !$pathOnly) {
                $xv1 = floor($xv1);
                $yv1 = floor($yv1);
                $xv2 = floor($xv2);
                $yv2 = floor($yv2);
                $this->drawLine($x1, $y1, $x2, $y2, ['r' => 0, 'g' => 0, 'b' => 0, 'alpha' => 30]);
                $myMarkerSettings = [
                    'r' => 255,
                    'g' => 0,
                    'b' => 0,
                    'borderR' => 255,
                    'borderB' => 255,
                    'borderG' => 255,
                    'size' => 4
                ];
                $this->drawRectangleMarker($xv1, $yv1, $myMarkerSettings);
                $this->drawText($xv1 + 4, $yv1, 'v1');
                $myMarkerSettings = [
                    'r' => 0,
                    'g' => 0,
                    'b' => 255,
                    'borderR' => 255,
                    'borderB' => 255,
                    'borderG' => 255,
                    'size' => 4
                ];
                $this->drawRectangleMarker($xv2, $yv2, $myMarkerSettings);
                $this->drawText($xv2 + 4, $yv2, 'v2');
            }
            /* Draw the bezier */
            $lastX = null;
            $lastY = null;
            $Cpt = null;
            $mode = null;
            $arrowS = [];
            foreach ($Q as $point) {
                $x = $point['x'];
                $y = $point['y'];
                /* Get the first segment */
                if (!count($arrowS) && $lastX != null && $lastY != null) {
                    $arrowS['x2'] = $lastX;
                    $arrowS['y2'] = $lastY;
                    $arrowS['x1'] = $x;
                    $arrowS['y1'] = $y;
                }
                if ($lastX != null && $lastY != null && !$pathOnly) {
                    list($Cpt, $mode) = $this->drawLine(
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
                            'Cpt' => $Cpt,
                            'mode' => $mode,
                            'weight' => $weight
                        ]
                    );
                }
                /* Get the last segment */
                $arrowE['x1'] = $lastX;
                $arrowE['y1'] = $lastY;
                $arrowE['x2'] = $x;
                $arrowE['y2'] = $y;
                $lastX = $x;
                $lastY = $y;
            }
            if ($drawArrow && !$pathOnly) {
                $arrowSettings = [
                    'fillR' => $r,
                    'fillG' => $g,
                    'fillB' => $b,
                    'alpha' => $alpha,
                    'size' => $arrowSize,
                    'ration' => $arrowRatio
                ];
                if ($arrowTwoHeads) {
                    $this->drawArrow($arrowS['x1'], $arrowS['y1'], $arrowS['x2'], $arrowS['y2'], $arrowSettings);
                }
                $this->drawArrow($arrowE['x1'], $arrowE['y1'], $arrowE['x2'], $arrowE['y2'], $arrowSettings);
            }
        }
        return $Q;
    }

    /**
     * Draw a line between two points
     *
     * @param int|float $x1
     * @param int|float $y1
     * @param int|float $x2
     * @param int|float $y2
     * @param array     $format
     *
     * @return array|int
     */
    public function drawLine($x1, $y1, $x2, $y2, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $ticks = isset($format['ticks']) ? $format['ticks'] : null;
        $Cpt = isset($format['Cpt']) ? $format['Cpt'] : 1;
        $mode = isset($format['mode']) ? $format['mode'] : 1;
        $weight = isset($format['weight']) ? $format['weight'] : null;
        $threshold = isset($format['Threshold']) ? $format['Threshold'] : null;
        if ($this->antialias == false && $ticks == null) {
            if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
                $shadowColor = $this->allocateColor(
                    $this->picture,
                    $this->shadowR,
                    $this->shadowG,
                    $this->shadowB,
                    $this->shadowA
                );
                imageline(
                    $this->picture,
                    $x1 + $this->shadowX,
                    $y1 + $this->shadowY,
                    $x2 + $this->shadowX,
                    $y2 + $this->shadowY,
                    $shadowColor
                );
            }
            $color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            imageline($this->picture, $x1, $y1, $x2, $y2, $color);
            return 0;
        }
        $distance = sqrt(($x2 - $x1) * ($x2 - $x1) + ($y2 - $y1) * ($y2 - $y1));
        if ($distance == 0) {
            return -1;
        }
        /* Derivative algorithm for overweighted lines, re-route to polygons primitives */
        if ($weight != null) {
            $angle = $this->getAngle($x1, $y1, $x2, $y2);
            $polySettings = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'borderalpha' => $alpha];
            if ($ticks == null) {
                $points = [];
                $points[] = cos(deg2rad($angle - 90)) * $weight + $x1;
                $points[] = sin(deg2rad($angle - 90)) * $weight + $y1;
                $points[] = cos(deg2rad($angle + 90)) * $weight + $x1;
                $points[] = sin(deg2rad($angle + 90)) * $weight + $y1;
                $points[] = cos(deg2rad($angle + 90)) * $weight + $x2;
                $points[] = sin(deg2rad($angle + 90)) * $weight + $y2;
                $points[] = cos(deg2rad($angle - 90)) * $weight + $x2;
                $points[] = sin(deg2rad($angle - 90)) * $weight + $y2;
                $this->drawPolygon($points, $polySettings);
            } else {
                for ($i = 0; $i <= $distance; $i = $i + $ticks * 2) {
                    $xa = (($x2 - $x1) / $distance) * $i + $x1;
                    $ya = (($y2 - $y1) / $distance) * $i + $y1;
                    $xb = (($x2 - $x1) / $distance) * ($i + $ticks) + $x1;
                    $yb = (($y2 - $y1) / $distance) * ($i + $ticks) + $y1;
                    $points = [];
                    $points[] = cos(deg2rad($angle - 90)) * $weight + $xa;
                    $points[] = sin(deg2rad($angle - 90)) * $weight + $ya;
                    $points[] = cos(deg2rad($angle + 90)) * $weight + $xa;
                    $points[] = sin(deg2rad($angle + 90)) * $weight + $ya;
                    $points[] = cos(deg2rad($angle + 90)) * $weight + $xb;
                    $points[] = sin(deg2rad($angle + 90)) * $weight + $yb;
                    $points[] = cos(deg2rad($angle - 90)) * $weight + $xb;
                    $points[] = sin(deg2rad($angle - 90)) * $weight + $yb;
                    $this->drawPolygon($points, $polySettings);
                }
            }
            return 1;
        }
        $xStep = ($x2 - $x1) / $distance;
        $yStep = ($y2 - $y1) / $distance;
        for ($i = 0; $i <= $distance; $i++) {
            $x = $i * $xStep + $x1;
            $y = $i * $yStep + $y1;
            $color = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha];
            if ($threshold != null) {
                foreach ($threshold as $key => $parameters) {
                    if ($y <= $parameters['minX'] && $y >= $parameters['maxX']) {
                        if (isset($parameters['r'])) {
                            $rT = $parameters['r'];
                        } else {
                            $rT = 0;
                        }
                        if (isset($parameters['g'])) {
                            $gT = $parameters['g'];
                        } else {
                            $gT = 0;
                        }
                        if (isset($parameters['b'])) {
                            $bT = $parameters['b'];
                        } else {
                            $bT = 0;
                        }
                        if (isset($parameters['alpha'])) {
                            $alphaT = $parameters['alpha'];
                        } else {
                            $alphaT = 0;
                        }
                        $color = ['r' => $rT, 'g' => $gT, 'b' => $bT, 'alpha' => $alphaT];
                    }
                }
            }
            if ($ticks != null) {
                if ($Cpt % $ticks == 0) {
                    $Cpt = 0;
                    if ($mode == 1) {
                        $mode = 0;
                    } else {
                        $mode = 1;
                    }
                }
                if ($mode == 1) {
                    $this->drawAntialiasPixel($x, $y, $color);
                }
                $Cpt++;
            } else {
                $this->drawAntialiasPixel($x, $y, $color);
            }
        }
        return [$Cpt, $mode];
    }
}
