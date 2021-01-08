<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 30, 2019, 2:57:31 AM
 */
use CImage_Chart_Constant as Constant;
use CImage_Chart_Helper as Helper;

class CImage_Chart_Draw extends CImage_Chart_BaseDraw {

    use CImage_Chart_Concern_RectangleDraw,
        CImage_Chart_Concern_LabelDraw,
        CImage_Chart_Concern_ChartDraw;
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

    /**
     * Draw a circle
     *
     * @param int       $xc
     * @param int       $yc
     * @param int|float $height
     * @param int|float $width
     * @param array     $format
     */
    public function drawCircle($xc, $yc, $height, $width, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $ticks = isset($format['ticks']) ? $format['ticks'] : null;
        $height = abs($height);
        $width = abs($width);
        if ($height == 0) {
            $height = 1;
        }
        if ($width == 0) {
            $width = 1;
        }
        $xc = floor($xc);
        $yc = floor($yc);
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawCircle(
                $xc + $this->shadowX,
                $yc + $this->shadowY,
                $height,
                $width,
                [
                    'r' => $this->shadowR,
                    'g' => $this->shadowG,
                    'b' => $this->shadowB,
                    'alpha' => $this->shadowA,
                    'ticks' => $ticks
                ]
            );
        }
        if ($width == 0) {
            $width = $height;
        }
        if ($r < 0) {
            $r = 0;
        }
        if ($r > 255) {
            $r = 255;
        }
        if ($g < 0) {
            $g = 0;
        }
        if ($g > 255) {
            $g = 255;
        }
        if ($b < 0) {
            $b = 0;
        }
        if ($b > 255) {
            $b = 255;
        }
        $step = 360 / (2 * Constant::PI * max($width, $height));
        $mode = 1;
        $Cpt = 1;
        for ($i = 0; $i <= 360; $i = $i + $step) {
            $x = cos($i * Constant::PI / 180) * $height + $xc;
            $y = sin($i * Constant::PI / 180) * $width + $yc;
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
                    $this->drawAntialiasPixel($x, $y, ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]);
                }
                $Cpt++;
            } else {
                $this->drawAntialiasPixel($x, $y, ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]);
            }
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a filled circle
     *
     * @param int       $x
     * @param int       $y
     * @param int|float $radius
     * @param array     $format
     */
    public function drawFilledCircle($x, $y, $radius, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $borderR = isset($format['borderR']) ? $format['borderR'] : -1;
        $borderG = isset($format['borderG']) ? $format['borderG'] : -1;
        $borderB = isset($format['borderB']) ? $format['borderB'] : -1;
        $borderalpha = isset($format['borderalpha']) ? $format['borderalpha'] : $alpha;
        $ticks = isset($format['ticks']) ? $format['ticks'] : null;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        if ($radius == 0) {
            $radius = 1;
        }
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        $x = floor($x);
        $y = floor($y);
        $radius = abs($radius);
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawFilledCircle(
                $x + $this->shadowX,
                $y + $this->shadowY,
                $radius,
                [
                    'r' => $this->shadowR,
                    'g' => $this->shadowG,
                    'b' => $this->shadowB,
                    'alpha' => $this->shadowA,
                    'ticks' => $ticks
                ]
            );
        }
        $this->Mask = [];
        $color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        for ($i = 0; $i <= $radius * 2; $i++) {
            $slice = sqrt($radius * $radius - ($radius - $i) * ($radius - $i));
            $xPos = floor($slice);
            $yPos = $y + $i - $radius;
            $aAlias = $slice - floor($slice);
            $this->Mask[$x - $xPos][$yPos] = true;
            $this->Mask[$x + $xPos][$yPos] = true;
            imageline($this->picture, $x - $xPos, $yPos, $x + $xPos, $yPos, $color);
        }
        if ($this->antialias) {
            $this->drawCircle(
                $x,
                $y,
                $radius,
                $radius,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks]
            );
        }
        $this->Mask = [];
        if ($borderR != -1) {
            $this->drawCircle(
                $x,
                $y,
                $radius,
                $radius,
                [
                    'r' => $borderR,
                    'g' => $borderG,
                    'b' => $borderB,
                    'alpha' => $borderalpha,
                    'ticks' => $ticks
                ]
            );
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Write text
     *
     * @param int|float $x
     * @param int|float $y
     * @param string    $text
     * @param array     $format
     *
     * @return array
     */
    public function drawText($x, $y, $text, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : $this->fontColorR;
        $g = isset($format['g']) ? $format['g'] : $this->fontColorG;
        $b = isset($format['b']) ? $format['b'] : $this->fontColorB;
        $angle = isset($format['angle']) ? $format['angle'] : 0;
        $align = isset($format['align']) ? $format['align'] : Constant::TEXT_ALIGN_BOTTOMLEFT;
        $alpha = isset($format['alpha']) ? $format['alpha'] : $this->fontColorA;
        $fontName = isset($format['fontName']) ? $this->loadFont($format['fontName'], 'fonts') : $this->fontName;
        $fontSize = isset($format['fontSize']) ? $format['fontSize'] : $this->fontSize;
        $showOrigine = isset($format['showOrigine']) ? $format['showOrigine'] : false;
        $tOffset = isset($format['TOffset']) ? $format['TOffset'] : 2;
        $drawBox = isset($format['drawBox']) ? $format['drawBox'] : false;
        $borderOffset = isset($format['borderOffset']) ? $format['borderOffset'] : 6;
        $boxRounded = isset($format['boxRounded']) ? $format['boxRounded'] : false;
        $roundedRadius = isset($format['roundedRadius']) ? $format['roundedRadius'] : 6;
        $boxR = isset($format['boxR']) ? $format['boxR'] : 255;
        $boxG = isset($format['boxG']) ? $format['boxG'] : 255;
        $boxB = isset($format['boxB']) ? $format['boxB'] : 255;
        $boxalpha = isset($format['boxalpha']) ? $format['boxalpha'] : 50;
        $boxSurrounding = isset($format['boxSurrounding']) ? $format['boxSurrounding'] : '';
        $boxborderR = isset($format['boxR']) ? $format['boxR'] : 0;
        $boxborderG = isset($format['boxG']) ? $format['boxG'] : 0;
        $boxborderB = isset($format['boxB']) ? $format['boxB'] : 0;
        $boxBorderalpha = isset($format['boxalpha']) ? $format['boxalpha'] : 50;
        $NoShadow = isset($format['noShadow']) ? $format['noShadow'] : false;
        $shadow = $this->shadow;
        if ($NoShadow) {
            $this->shadow = false;
        }
        if ($boxSurrounding != '') {
            $boxborderR = $boxR - $boxSurrounding;
            $boxborderG = $boxG - $boxSurrounding;
            $boxborderB = $boxB - $boxSurrounding;
            $boxBorderalpha = $boxalpha;
        }
        if ($showOrigine) {
            $myMarkerSettings = [
                'r' => 255,
                'g' => 0,
                'b' => 0,
                'borderR' => 255,
                'borderB' => 255,
                'borderG' => 255,
                'size' => 4
            ];
            $this->drawRectangleMarker($x, $y, $myMarkerSettings);
        }
        $txtPos = $this->getTextBox($x, $y, $fontName, $fontSize, $angle, $text);
        if ($drawBox && ($angle == 0 || $angle == 90 || $angle == 180 || $angle == 270)) {
            $t[0]['x'] = 0;
            $t[0]['y'] = 0;
            $t[1]['x'] = 0;
            $t[1]['y'] = 0;
            $t[2]['x'] = 0;
            $t[2]['y'] = 0;
            $t[3]['x'] = 0;
            $t[3]['y'] = 0;
            if ($angle == 0) {
                $t[0]['x'] = -$tOffset;
                $t[0]['y'] = $tOffset;
                $t[1]['x'] = $tOffset;
                $t[1]['y'] = $tOffset;
                $t[2]['x'] = $tOffset;
                $t[2]['y'] = -$tOffset;
                $t[3]['x'] = -$tOffset;
                $t[3]['y'] = -$tOffset;
            }
            $x1 = min($txtPos[0]['x'], $txtPos[1]['x'], $txtPos[2]['x'], $txtPos[3]['x']) - $borderOffset + 3;
            $y1 = min($txtPos[0]['y'], $txtPos[1]['y'], $txtPos[2]['y'], $txtPos[3]['y']) - $borderOffset;
            $x2 = max($txtPos[0]['x'], $txtPos[1]['x'], $txtPos[2]['x'], $txtPos[3]['x']) + $borderOffset + 3;
            $y2 = max($txtPos[0]['y'], $txtPos[1]['y'], $txtPos[2]['y'], $txtPos[3]['y']) + $borderOffset - 3;
            $x1 = $x1 - $txtPos[$align]['x'] + $x + $t[0]['x'];
            $y1 = $y1 - $txtPos[$align]['y'] + $y + $t[0]['y'];
            $x2 = $x2 - $txtPos[$align]['x'] + $x + $t[0]['x'];
            $y2 = $y2 - $txtPos[$align]['y'] + $y + $t[0]['y'];
            $settings = [
                'r' => $boxR,
                'g' => $boxG,
                'b' => $boxB,
                'alpha' => $boxalpha,
                'borderR' => $boxborderR,
                'borderG' => $boxborderG,
                'borderB' => $boxborderB,
                'borderalpha' => $boxBorderalpha
            ];
            if ($boxRounded) {
                $this->drawRoundedFilledRectangle($x1, $y1, $x2, $y2, $roundedRadius, $settings);
            } else {
                $this->drawFilledRectangle($x1, $y1, $x2, $y2, $settings);
            }
        }
        $x = $x - $txtPos[$align]['x'] + $x;
        $y = $y - $txtPos[$align]['y'] + $y;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $C_ShadowColor = $this->allocateColor(
                $this->picture,
                $this->shadowR,
                $this->shadowG,
                $this->shadowB,
                $this->shadowA
            );
            imagettftext(
                $this->picture,
                $fontSize,
                $angle,
                $x + $this->shadowX,
                $y + $this->shadowY,
                $C_ShadowColor,
                $fontName,
                $text
            );
        }
        $C_TextColor = $this->AllocateColor($this->picture, $r, $g, $b, $alpha);
        imagettftext($this->picture, $fontSize, $angle, $x, $y, $C_TextColor, $fontName, $text);
        $this->shadow = $shadow;
        return $txtPos;
    }

    /**
     * Draw a gradient within a defined area
     *
     * @param int   $x1
     * @param int   $y1
     * @param int   $x2
     * @param int   $y2
     * @param int   $direction
     * @param array $format
     *
     * @return null|integer
     */
    public function drawGradientArea($x1, $y1, $x2, $y2, $direction, array $format = []) {
        $startR = isset($format['startR']) ? $format['startR'] : 90;
        $startG = isset($format['startG']) ? $format['startG'] : 90;
        $startB = isset($format['startB']) ? $format['startB'] : 90;
        $endR = isset($format['endR']) ? $format['endR'] : 0;
        $endG = isset($format['endG']) ? $format['endG'] : 0;
        $endB = isset($format['endB']) ? $format['endB'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $levels = isset($format['levels']) ? $format['levels'] : null;
        $shadow = $this->shadow;
        $this->shadow = false;
        if ($startR == $endR && $startG == $endG && $startB == $endB) {
            $this->drawFilledRectangle(
                $x1,
                $y1,
                $x2,
                $y2,
                ['r' => $startR, 'g' => $startG, 'b' => $startB, 'alpha' => $alpha]
            );
            return 0;
        }
        if ($levels != null) {
            $endR = $startR + $levels;
            $endG = $startG + $levels;
            $endB = $startB + $levels;
        }
        if ($x1 > $x2) {
            list($x1, $x2) = [$x2, $x1];
        }
        if ($y1 > $y2) {
            list($y1, $y2) = [$y2, $y1];
        }
        if ($direction == Constant::DIRECTION_VERTICAL) {
            $width = abs($y2 - $y1);
        }
        if ($direction == Constant::DIRECTION_HORIZONTAL) {
            $width = abs($x2 - $x1);
        }
        $step = max(abs($endR - $startR), abs($endG - $startG), abs($endB - $startB));
        $stepSize = $width / $step;
        $rStep = ($endR - $startR) / $step;
        $gStep = ($endG - $startG) / $step;
        $bStep = ($endB - $startB) / $step;
        $r = $startR;
        $g = $startG;
        $b = $startB;
        switch ($direction) {
            case Constant::DIRECTION_VERTICAL:
                $startY = $y1;
                $endY = floor($y2) + 1;
                $lastY2 = $startY;
                for ($i = 0; $i <= $step; $i++) {
                    $y2 = floor($startY + ($i * $stepSize));
                    if ($y2 > $endY) {
                        $y2 = $endY;
                    }
                    if (($y1 != $y2 && $y1 < $y2) || $y2 == $endY) {
                        $color = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha];
                        $this->drawFilledRectangle($x1, $y1, $x2, $y2, $color);
                        $lastY2 = max($lastY2, $y2);
                        $y1 = $y2 + 1;
                    }
                    $r = $r + $rStep;
                    $g = $g + $gStep;
                    $b = $b + $bStep;
                }
                if ($lastY2 < $endY && isset($color)) {
                    for ($i = $lastY2 + 1; $i <= $endY; $i++) {
                        $this->drawLine($x1, $i, $x2, $i, $color);
                    }
                }
                break;
            case Constant::DIRECTION_HORIZONTAL:
                $startX = $x1;
                $endX = $x2;
                for ($i = 0; $i <= $step; $i++) {
                    $x2 = floor($startX + ($i * $stepSize));
                    if ($x2 > $endX) {
                        $x2 = $endX;
                    }
                    if (($x1 != $x2 && $x1 < $x2) || $x2 == $endX) {
                        $color = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha];
                        $this->drawFilledRectangle($x1, $y1, $x2, $y2, $color);
                        $x1 = $x2 + 1;
                    }
                    $r = $r + $rStep;
                    $g = $g + $gStep;
                    $b = $b + $bStep;
                }
                if ($x2 < $endX && isset($color)) {
                    $this->drawFilledRectangle($x2, $y1, $endX, $y2, $color);
                }
                break;
        }
        $this->shadow = $shadow;
    }

    /**
     * Draw an aliased pixel
     *
     * @param int   $x
     * @param int   $y
     * @param array $format
     *
     * @return int|null
     */
    public function drawAntialiasPixel($x, $y, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        if ($x < 0 || $y < 0 || $x >= $this->xSize || $y >= $this->ySize) {
            return -1;
        }
        if ($r < 0) {
            $r = 0;
        }
        if ($r > 255) {
            $r = 255;
        }
        if ($g < 0) {
            $g = 0;
        }
        if ($g > 255) {
            $g = 255;
        }
        if ($b < 0) {
            $b = 0;
        }
        if ($b > 255) {
            $b = 255;
        }
        if (!$this->antialias) {
            if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
                $shadowColor = $this->allocateColor(
                    $this->picture,
                    $this->shadowR,
                    $this->shadowG,
                    $this->shadowB,
                    $this->shadowA
                );
                imagesetpixel($this->picture, $x + $this->shadowX, $y + $this->shadowY, $shadowColor);
            }
            $plotColor = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            imagesetpixel($this->picture, $x, $y, $plotColor);
            return 0;
        }
        $xi = floor($x);
        $yi = floor($y);
        if ($xi == $x && $yi == $y) {
            if ($alpha == 100) {
                $this->drawalphaPixel($x, $y, 100, $r, $g, $b);
            } else {
                $this->drawalphaPixel($x, $y, $alpha, $r, $g, $b);
            }
        } else {
            $alpha1 = (((1 - ($x - floor($x))) * (1 - ($y - floor($y))) * 100) / 100) * $alpha;
            if ($alpha1 > $this->antialiasQuality) {
                $this->drawalphaPixel($xi, $yi, $alpha1, $r, $g, $b);
            }
            $alpha2 = ((($x - floor($x)) * (1 - ($y - floor($y))) * 100) / 100) * $alpha;
            if ($alpha2 > $this->antialiasQuality) {
                $this->drawalphaPixel($xi + 1, $yi, $alpha2, $r, $g, $b);
            }
            $alpha3 = (((1 - ($x - floor($x))) * ($y - floor($y)) * 100) / 100) * $alpha;
            if ($alpha3 > $this->antialiasQuality) {
                $this->drawalphaPixel($xi, $yi + 1, $alpha3, $r, $g, $b);
            }
            $alpha4 = ((($x - floor($x)) * ($y - floor($y)) * 100) / 100) * $alpha;
            if ($alpha4 > $this->antialiasQuality) {
                $this->drawalphaPixel($xi + 1, $yi + 1, $alpha4, $r, $g, $b);
            }
        }
    }

    /**
     * Draw a semi-transparent pixel
     *
     * @param int $x
     * @param int $y
     * @param int $alpha
     * @param int $r
     * @param int $g
     * @param int $b
     *
     * @return null|integer
     */
    public function drawalphaPixel($x, $y, $alpha, $r, $g, $b) {
        if (isset($this->Mask[$x]) && isset($this->Mask[$x][$y])) {
            return 0;
        }
        if ($x < 0 || $y < 0 || $x >= $this->xSize || $y >= $this->ySize) {
            return -1;
        }
        if ($r < 0) {
            $r = 0;
        }
        if ($r > 255) {
            $r = 255;
        }
        if ($g < 0) {
            $g = 0;
        }
        if ($g > 255) {
            $g = 255;
        }
        if ($b < 0) {
            $b = 0;
        }
        if ($b > 255) {
            $b = 255;
        }
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $alphaFactor = floor(($alpha / 100) * $this->shadowA);
            $shadowColor = $this->allocateColor(
                $this->picture,
                $this->shadowR,
                $this->shadowG,
                $this->shadowB,
                $alphaFactor
            );
            imagesetpixel($this->picture, $x + $this->shadowX, $y + $this->shadowY, $shadowColor);
        }
        $C_Aliased = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        imagesetpixel($this->picture, $x, $y, $C_Aliased);
    }

    /**
     * Load a PNG file and draw it over the chart
     *
     * @param int    $x
     * @param int    $y
     * @param string $fileName
     */
    public function drawFromPNG($x, $y, $fileName) {
        $this->drawFromPicture(1, $fileName, $x, $y);
    }

    /**
     * Load a GIF file and draw it over the chart
     *
     * @param int    $x
     * @param int    $y
     * @param string $fileName
     */
    public function drawFromGIF($x, $y, $fileName) {
        $this->drawFromPicture(2, $fileName, $x, $y);
    }

    /**
     * Load a JPEG file and draw it over the chart
     *
     * @param int    $x
     * @param int    $y
     * @param string $fileName
     */
    public function drawFromJPG($x, $y, $fileName) {
        $this->drawFromPicture(3, $fileName, $x, $y);
    }

    /**
     * Generic loader public function for external pictures
     *
     * @param int    $picType
     * @param string $fileName
     * @param int    $x
     * @param int    $y
     *
     * @return null|integer
     */
    public function drawFromPicture($picType, $fileName, $x, $y) {
        if (file_exists($fileName)) {
            list($width, $height) = $this->getPicInfo($fileName);
            if ($picType == 1) {
                $raster = imagecreatefrompng($fileName);
            } elseif ($picType == 2) {
                $raster = imagecreatefromgif($fileName);
            } elseif ($picType == 3) {
                $raster = imagecreatefromjpeg($fileName);
            } else {
                return 0;
            }
            $restoreShadow = $this->shadow;
            if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
                $this->shadow = false;
                if ($picType == 3) {
                    $this->drawFilledRectangle(
                        $x + $this->shadowX,
                        $y + $this->shadowY,
                        $x + $width + $this->shadowX,
                        $y + $height + $this->shadowY,
                        [
                            'r' => $this->shadowR,
                            'g' => $this->shadowG,
                            'b' => $this->shadowB,
                            'alpha' => $this->shadowA
                        ]
                    );
                } else {
                    $tranparentID = imagecolortransparent($raster);
                    for ($xc = 0; $xc <= $width - 1; $xc++) {
                        for ($yc = 0; $yc <= $height - 1; $yc++) {
                            $rGBa = imagecolorat($raster, $xc, $yc);
                            $values = imagecolorsforindex($raster, $rGBa);
                            if ($values['alpha'] < 120) {
                                $alphaFactor = floor(
                                    ($this->shadowA / 100) * ((100 / 127) * (127 - $values['alpha']))
                                );
                                $this->drawalphaPixel(
                                    $x + $xc + $this->shadowX,
                                    $y + $yc + $this->shadowY,
                                    $alphaFactor,
                                    $this->shadowR,
                                    $this->shadowG,
                                    $this->shadowB
                                );
                            }
                        }
                    }
                }
            }
            $this->shadow = $restoreShadow;
            imagecopy($this->picture, $raster, $x, $y, 0, 0, $width, $height);
            imagedestroy($raster);
        }
    }

    /**
     * Draw an arrow
     *
     * @param int   $x1
     * @param int   $y1
     * @param int   $x2
     * @param int   $y2
     * @param array $format
     */
    public function drawArrow($x1, $y1, $x2, $y2, array $format = []) {
        $fillR = isset($format['fillR']) ? $format['fillR'] : 0;
        $fillG = isset($format['fillG']) ? $format['fillG'] : 0;
        $fillB = isset($format['fillB']) ? $format['fillB'] : 0;
        $borderR = isset($format['borderR']) ? $format['borderR'] : $fillR;
        $borderG = isset($format['borderG']) ? $format['borderG'] : $fillG;
        $borderB = isset($format['borderB']) ? $format['borderB'] : $fillB;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $size = isset($format['size']) ? $format['size'] : 10;
        $ratio = isset($format['ratio']) ? $format['ratio'] : .5;
        $twoHeads = isset($format['TwoHeads']) ? $format['TwoHeads'] : false;
        $ticks = isset($format['ticks']) ? $format['ticks'] : false;
        /* Calculate the line angle */
        $angle = $this->getAngle($x1, $y1, $x2, $y2);
        /* Override Shadow support, this will be managed internally */
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawArrow(
                $x1 + $this->shadowX,
                $y1 + $this->shadowY,
                $x2 + $this->shadowX,
                $y2 + $this->shadowY,
                [
                    'fillR' => $this->shadowR,
                    'fillG' => $this->shadowG,
                    'fillB' => $this->shadowB,
                    'alpha' => $this->shadowA,
                    'size' => $size,
                    'ration' => $ratio,
                    'TwoHeads' => $twoHeads,
                    'ticks' => $ticks
                ]
            );
        }
        /* Draw the 1st Head */
        $tailX = cos(($angle - 180) * Constant::PI / 180) * $size + $x2;
        $tailY = sin(($angle - 180) * Constant::PI / 180) * $size + $y2;
        $points = [];
        $points[] = $x2;
        $points[] = $y2;
        $points[] = cos(($angle - 90) * Constant::PI / 180) * $size * $ratio + $tailX;
        $points[] = sin(($angle - 90) * Constant::PI / 180) * $size * $ratio + $tailY;
        $points[] = cos(($angle - 270) * Constant::PI / 180) * $size * $ratio + $tailX;
        $points[] = sin(($angle - 270) * Constant::PI / 180) * $size * $ratio + $tailY;
        $points[] = $x2;
        $points[] = $y2;
        /* Visual correction */
        if ($angle == 180 || $angle == 360) {
            $points[4] = $points[2];
        }
        if ($angle == 90 || $angle == 270) {
            $points[5] = $points[3];
        }
        $arrowColor = $this->allocateColor($this->picture, $fillR, $fillG, $fillB, $alpha);
        ImageFilledPolygon($this->picture, $points, 4, $arrowColor);
        $this->drawLine(
            $points[0],
            $points[1],
            $points[2],
            $points[3],
            ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha]
        );
        $this->drawLine(
            $points[2],
            $points[3],
            $points[4],
            $points[5],
            ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha]
        );
        $this->drawLine(
            $points[0],
            $points[1],
            $points[4],
            $points[5],
            ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha]
        );
        /* Draw the second head */
        if ($twoHeads) {
            $angle = $this->getAngle($x2, $y2, $x1, $y1);
            $tailX2 = cos(($angle - 180) * Constant::PI / 180) * $size + $x1;
            $tailY2 = sin(($angle - 180) * Constant::PI / 180) * $size + $y1;
            $points = [];
            $points[] = $x1;
            $points[] = $y1;
            $points[] = cos(($angle - 90) * Constant::PI / 180) * $size * $ratio + $tailX2;
            $points[] = sin(($angle - 90) * Constant::PI / 180) * $size * $ratio + $tailY2;
            $points[] = cos(($angle - 270) * Constant::PI / 180) * $size * $ratio + $tailX2;
            $points[] = sin(($angle - 270) * Constant::PI / 180) * $size * $ratio + $tailY2;
            $points[] = $x1;
            $points[] = $y1;
            /* Visual correction */
            if ($angle == 180 || $angle == 360) {
                $points[4] = $points[2];
            }
            if ($angle == 90 || $angle == 270) {
                $points[5] = $points[3];
            }
            $arrowColor = $this->allocateColor($this->picture, $fillR, $fillG, $fillB, $alpha);
            ImageFilledPolygon($this->picture, $points, 4, $arrowColor);
            $this->drawLine(
                $points[0],
                $points[1],
                $points[2],
                $points[3],
                ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha]
            );
            $this->drawLine(
                $points[2],
                $points[3],
                $points[4],
                $points[5],
                ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha]
            );
            $this->drawLine(
                $points[0],
                $points[1],
                $points[4],
                $points[5],
                ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha]
            );
            $this->drawLine(
                $tailX,
                $tailY,
                $tailX2,
                $tailY2,
                ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha, 'ticks' => $ticks]
            );
        } else {
            $this->drawLine(
                $x1,
                $y1,
                $tailX,
                $tailY,
                ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha, 'ticks' => $ticks]
            );
        }
        /* Re-enable shadows */
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a label with associated arrow
     *
     * @param int    $x1
     * @param int    $y1
     * @param string $text
     * @param array  $format
     */
    public function drawArrowLabel($x1, $y1, $text, array $format = []) {
        $fillR = isset($format['fillR']) ? $format['fillR'] : 0;
        $fillG = isset($format['fillG']) ? $format['fillG'] : 0;
        $fillB = isset($format['fillB']) ? $format['fillB'] : 0;
        $borderR = isset($format['borderR']) ? $format['borderR'] : $fillR;
        $borderG = isset($format['borderG']) ? $format['borderG'] : $fillG;
        $borderB = isset($format['borderB']) ? $format['borderB'] : $fillB;
        $fontName = isset($format['fontName']) ? $this->loadFont($format['fontName'], 'fonts') : $this->fontName;
        $fontSize = isset($format['fontSize']) ? $format['fontSize'] : $this->fontSize;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $length = isset($format['length']) ? $format['length'] : 50;
        $angle = isset($format['angle']) ? $format['angle'] : 315;
        $size = isset($format['size']) ? $format['size'] : 10;
        $position = isset($format['position']) ? $format['position'] : Constant::POSITION_TOP;
        $roundPos = isset($format['roundPos']) ? $format['roundPos'] : false;
        $ticks = isset($format['ticks']) ? $format['ticks'] : null;
        $angle = $angle % 360;
        $x2 = sin(($angle + 180) * Constant::PI / 180) * $length + $x1;
        $y2 = cos(($angle + 180) * Constant::PI / 180) * $length + $y1;
        if ($roundPos && $angle > 0 && $angle < 180) {
            $y2 = ceil($y2);
        }
        if ($roundPos && $angle > 180) {
            $y2 = floor($y2);
        }
        $this->drawArrow($x2, $y2, $x1, $y1, $format);
        $size = imagettfbbox($fontSize, 0, $fontName, $text);
        $txtWidth = max(abs($size[2] - $size[0]), abs($size[0] - $size[6]));
        if ($angle > 0 && $angle < 180) {
            $this->drawLine(
                $x2,
                $y2,
                $x2 - $txtWidth,
                $y2,
                ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha, 'ticks' => $ticks]
            );
            if ($position == Constant::POSITION_TOP) {
                $this->drawText(
                    $x2,
                    $y2 - 2,
                    $text,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $alpha,
                        'align' => Constant::TEXT_ALIGN_BOTTOMRIGHT
                    ]
                );
            } else {
                $this->drawText(
                    $x2,
                    $y2 + 4,
                    $text,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $alpha,
                        'align' => Constant::TEXT_ALIGN_TOPRIGHT
                    ]
                );
            }
        } else {
            $this->drawLine(
                $x2,
                $y2,
                $x2 + $txtWidth,
                $y2,
                ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha, 'ticks' => $ticks]
            );
            if ($position == Constant::POSITION_TOP) {
                $this->drawText(
                    $x2,
                    $y2 - 2,
                    $text,
                    ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha]
                );
            } else {
                $this->drawText(
                    $x2,
                    $y2 + 4,
                    $text,
                    [
                        'r' => $borderR,
                        'g' => $borderG,
                        'b' => $borderB,
                        'alpha' => $alpha,
                        'align' => Constant::TEXT_ALIGN_TOPLEFT
                    ]
                );
            }
        }
    }

    /**
     * Draw a progress bar filled with specified %
     *
     * @param int       $x
     * @param int       $y
     * @param int|float $percent
     * @param array     $format
     */
    public function drawProgress($x, $y, $percent, array $format = []) {
        if ($percent > 100) {
            $percent = 100;
        }
        if ($percent < 0) {
            $percent = 0;
        }
        $width = isset($format['width']) ? $format['width'] : 200;
        $height = isset($format['height']) ? $format['height'] : 20;
        $orientation = isset($format['orientation']) ? $format['orientation'] : Constant::ORIENTATION_HORIZONTAL;
        $showLabel = isset($format['showLabel']) ? $format['showLabel'] : false;
        $labelPos = isset($format['labelPos']) ? $format['labelPos'] : Constant::LABEL_POS_INSIDE;
        $margin = isset($format['margin']) ? $format['margin'] : 10;
        $r = isset($format['r']) ? $format['r'] : 130;
        $g = isset($format['g']) ? $format['g'] : 130;
        $b = isset($format['b']) ? $format['b'] : 130;
        $rFade = isset($format['rFade']) ? $format['rFade'] : -1;
        $gFade = isset($format['gFade']) ? $format['gFade'] : -1;
        $bFade = isset($format['bFade']) ? $format['bFade'] : -1;
        $borderR = isset($format['borderR']) ? $format['borderR'] : $r;
        $borderG = isset($format['borderG']) ? $format['borderG'] : $g;
        $borderB = isset($format['borderB']) ? $format['borderB'] : $b;
        $boxborderR = isset($format['boxborderR']) ? $format['boxborderR'] : 0;
        $boxborderG = isset($format['boxborderG']) ? $format['boxborderG'] : 0;
        $boxborderB = isset($format['boxborderB']) ? $format['boxborderB'] : 0;
        $boxBackR = isset($format['boxBackR']) ? $format['boxBackR'] : 255;
        $boxBackG = isset($format['boxBackG']) ? $format['boxBackG'] : 255;
        $boxBackB = isset($format['boxBackB']) ? $format['boxBackB'] : 255;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        $boxSurrounding = isset($format['boxSurrounding']) ? $format['boxSurrounding'] : null;
        $noAngle = isset($format['noAngle']) ? $format['noAngle'] : false;
        if ($rFade != -1 && $gFade != -1 && $bFade != -1) {
            $rFade = (($rFade - $r) / 100) * $percent + $r;
            $gFade = (($gFade - $g) / 100) * $percent + $g;
            $bFade = (($bFade - $b) / 100) * $percent + $b;
        }
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        if ($boxSurrounding != null) {
            $boxborderR = $boxBackR + $surrounding;
            $boxborderG = $boxBackG + $surrounding;
            $boxborderB = $boxBackB + $surrounding;
        }
        if ($orientation == Constant::ORIENTATION_VERTICAL) {
            $InnerHeight = (($height - 2) / 100) * $percent;
            $this->drawFilledRectangle(
                $x,
                $y,
                $x + $width,
                $y - $height,
                [
                    'r' => $boxBackR,
                    'g' => $boxBackG,
                    'b' => $boxBackB,
                    'borderR' => $boxborderR,
                    'borderG' => $boxborderG,
                    'borderB' => $boxborderB,
                    'noAngle' => $noAngle
                ]
            );
            $restoreShadow = $this->shadow;
            $this->shadow = false;
            if ($rFade != -1 && $gFade != -1 && $bFade != -1) {
                $gradientOptions = [
                    'StartR' => $rFade,
                    'StartG' => $gFade,
                    'StartB' => $bFade,
                    'endR' => $r,
                    'endG' => $g,
                    'endB' => $b
                ];
                $this->drawGradientArea(
                    $x + 1,
                    $y - 1,
                    $x + $width - 1,
                    $y - $InnerHeight,
                    Constant::DIRECTION_VERTICAL,
                    $gradientOptions
                );
                if ($surrounding) {
                    $this->drawRectangle(
                        $x + 1,
                        $y - 1,
                        $x + $width - 1,
                        $y - $InnerHeight,
                        ['r' => 255, 'g' => 255, 'b' => 255, 'alpha' => $surrounding]
                    );
                }
            } else {
                $this->drawFilledRectangle(
                    $x + 1,
                    $y - 1,
                    $x + $width - 1,
                    $y - $InnerHeight,
                    [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'borderR' => $borderR,
                        'borderG' => $borderG,
                        'borderB' => $borderB
                    ]
                );
            }
            $this->shadow = $restoreShadow;
            if ($showLabel && $labelPos == Constant::LABEL_POS_BOTTOM) {
                $this->drawText(
                    $x + ($width / 2),
                    $y + $margin,
                    $percent . '%',
                    ['align' => Constant::TEXT_ALIGN_TOPMIDDLE]
                );
            }
            if ($showLabel && $labelPos == Constant::LABEL_POS_TOP) {
                $this->drawText(
                    $x + ($width / 2),
                    $y - $height - $margin,
                    $percent . '%',
                    ['align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE]
                );
            }
            if ($showLabel && $labelPos == Constant::LABEL_POS_INSIDE) {
                $this->drawText(
                    $x + ($width / 2),
                    $y - $InnerHeight - $margin,
                    $percent . '%',
                    ['align' => Constant::TEXT_ALIGN_MIDDLELEFT, 'Angle' => 90]
                );
            }
            if ($showLabel && $labelPos == Constant::LABEL_POS_CENTER) {
                $this->drawText(
                    $x + ($width / 2),
                    $y - ($height / 2),
                    $percent . '%',
                    ['align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE, 'Angle' => 90]
                );
            }
        } else {
            if ($percent == 100) {
                $InnerWidth = $width - 1;
            } else {
                $InnerWidth = (($width - 2) / 100) * $percent;
            }
            $this->drawFilledRectangle(
                $x,
                $y,
                $x + $width,
                $y + $height,
                [
                    'r' => $boxBackR,
                    'g' => $boxBackG,
                    'b' => $boxBackB,
                    'borderR' => $boxborderR,
                    'borderG' => $boxborderG,
                    'borderB' => $boxborderB,
                    'noAngle' => $noAngle
                ]
            );
            $restoreShadow = $this->shadow;
            $this->shadow = false;
            if ($rFade != -1 && $gFade != -1 && $bFade != -1) {
                $gradientOptions = [
                    'StartR' => $r,
                    'StartG' => $g,
                    'StartB' => $b,
                    'endR' => $rFade,
                    'endG' => $gFade,
                    'endB' => $bFade
                ];
                $this->drawGradientArea(
                    $x + 1,
                    $y + 1,
                    $x + $InnerWidth,
                    $y + $height - 1,
                    Constant::DIRECTION_HORIZONTAL,
                    $gradientOptions
                );
                if ($surrounding) {
                    $this->drawRectangle(
                        $x + 1,
                        $y + 1,
                        $x + $InnerWidth,
                        $y + $height - 1,
                        ['r' => 255, 'g' => 255, 'b' => 255, 'alpha' => $surrounding]
                    );
                }
            } else {
                $this->drawFilledRectangle(
                    $x + 1,
                    $y + 1,
                    $x + $InnerWidth,
                    $y + $height - 1,
                    [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                        'borderR' => $borderR, 'borderG' => $borderG, 'borderB' => $borderB
                    ]
                );
            }
            $this->shadow = $restoreShadow;
            if ($showLabel && $labelPos == Constant::LABEL_POS_LEFT) {
                $this->drawText(
                    $x - $margin,
                    $y + ($height / 2),
                    $percent . '%',
                    ['align' => Constant::TEXT_ALIGN_MIDDLERIGHT]
                );
            }
            if ($showLabel && $labelPos == Constant::LABEL_POS_RIGHT) {
                $this->drawText(
                    $x + $width + $margin,
                    $y + ($height / 2),
                    $percent . '%',
                    ['align' => Constant::TEXT_ALIGN_MIDDLELEFT]
                );
            }
            if ($showLabel && $labelPos == Constant::LABEL_POS_CENTER) {
                $this->drawText(
                    $x + ($width / 2),
                    $y + ($height / 2),
                    $percent . '%',
                    ['align' => Constant::TEXT_ALIGN_MIDDLEMIDDLE]
                );
            }
            if ($showLabel && $labelPos == Constant::LABEL_POS_INSIDE) {
                $this->drawText(
                    $x + $InnerWidth + $margin,
                    $y + ($height / 2),
                    $percent . '%',
                    ['align' => Constant::TEXT_ALIGN_MIDDLELEFT]
                );
            }
        }
    }

    /**
     * Draw the legend of the active series
     *
     * @param int   $x
     * @param int   $y
     * @param array $format
     */
    public function drawLegend($x, $y, array $format = []) {
        $family = isset($format['family']) ? $format['family'] : Constant::LEGEND_FAMILY_BOX;
        $fontName = isset($format['fontName']) ? $this->loadFont($format['fontName'], 'fonts') : $this->fontName;
        $fontSize = isset($format['fontSize']) ? $format['fontSize'] : $this->fontSize;
        $fontR = isset($format['fontR']) ? $format['fontR'] : $this->fontColorR;
        $fontG = isset($format['fontG']) ? $format['fontG'] : $this->fontColorG;
        $fontB = isset($format['fontB']) ? $format['fontB'] : $this->fontColorB;
        $boxWidth = isset($format['boxWidth']) ? $format['boxWidth'] : 5;
        $boxHeight = isset($format['boxHeight']) ? $format['boxHeight'] : 5;
        $iconAreaWidth = isset($format['iconAreaWidth']) ? $format['iconAreaWidth'] : $boxWidth;
        $iconAreaHeight = isset($format['iconAreaHeight']) ? $format['iconAreaHeight'] : $boxHeight;
        $xSpacing = isset($format['xSpacing']) ? $format['xSpacing'] : 5;
        $margin = isset($format['margin']) ? $format['margin'] : 5;
        $r = isset($format['r']) ? $format['r'] : 200;
        $g = isset($format['g']) ? $format['g'] : 200;
        $b = isset($format['b']) ? $format['b'] : 200;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $borderR = isset($format['borderR']) ? $format['borderR'] : 255;
        $borderG = isset($format['borderG']) ? $format['borderG'] : 255;
        $borderB = isset($format['borderB']) ? $format['borderB'] : 255;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        $style = isset($format['style']) ? $format['style'] : Constant::LEGEND_ROUND;
        $mode = isset($format['mode']) ? $format['mode'] : Constant::LEGEND_VERTICAL;
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        $data = $this->dataSet->getData();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa'] && isset($serie['picture'])
            ) {
                list($picWidth, $picHeight) = $this->getPicInfo($serie['picture']);
                if ($iconAreaWidth < $picWidth) {
                    $iconAreaWidth = $picWidth;
                }
                if ($iconAreaHeight < $picHeight) {
                    $iconAreaHeight = $picHeight;
                }
            }
        }
        $yStep = max($this->fontSize, $iconAreaHeight) + 5;
        $xStep = $iconAreaWidth + 5;
        $xStep = $xSpacing;
        $boundaries = [];
        $boundaries['l'] = $x;
        $boundaries['T'] = $y;
        $boundaries['r'] = 0;
        $boundaries['b'] = 0;
        $vY = $y;
        $vX = $x;
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                if ($mode == Constant::LEGEND_VERTICAL) {
                    $boxArray = $this->getTextBox(
                        $vX + $iconAreaWidth + 4,
                        $vY + $iconAreaHeight / 2,
                        $fontName,
                        $fontSize,
                        0,
                        $serie['description']
                    );
                    if ($boundaries['T'] > $boxArray[2]['y'] + $iconAreaHeight / 2) {
                        $boundaries['T'] = $boxArray[2]['y'] + $iconAreaHeight / 2;
                    }
                    if ($boundaries['r'] < $boxArray[1]['x'] + 2) {
                        $boundaries['r'] = $boxArray[1]['x'] + 2;
                    }
                    if ($boundaries['b'] < $boxArray[1]['y'] + 2 + $iconAreaHeight / 2) {
                        $boundaries['b'] = $boxArray[1]['y'] + 2 + $iconAreaHeight / 2;
                    }
                    $lines = preg_split("/\n/", $serie['description']);
                    $vY = $vY + max($this->fontSize * count($lines), $iconAreaHeight) + 5;
                } elseif ($mode == Constant::LEGEND_HORIZONTAL) {
                    $lines = preg_split("/\n/", $serie['description']);
                    $width = [];
                    foreach ($lines as $key => $value) {
                        $boxArray = $this->getTextBox(
                            $vX + $iconAreaWidth + 6,
                            $y + $iconAreaHeight / 2 + (($this->fontSize + 3) * $key),
                            $fontName,
                            $fontSize,
                            0,
                            $value
                        );
                        if ($boundaries['T'] > $boxArray[2]['y'] + $iconAreaHeight / 2) {
                            $boundaries['T'] = $boxArray[2]['y'] + $iconAreaHeight / 2;
                        }
                        if ($boundaries['r'] < $boxArray[1]['x'] + 2) {
                            $boundaries['r'] = $boxArray[1]['x'] + 2;
                        }
                        if ($boundaries['b'] < $boxArray[1]['y'] + 2 + $iconAreaHeight / 2) {
                            $boundaries['b'] = $boxArray[1]['y'] + 2 + $iconAreaHeight / 2;
                        }
                        $width[] = $boxArray[1]['x'];
                    }
                    $vX = max($width) + $xStep;
                }
            }
        }
        $vY = $vY - $yStep;
        $vX = $vX - $xStep;
        $topOffset = $y - $boundaries['T'];
        if ($boundaries['b'] - ($vY + $iconAreaHeight) < $topOffset) {
            $boundaries['b'] = $vY + $iconAreaHeight + $topOffset;
        }
        if ($style == Constant::LEGEND_ROUND) {
            $this->drawRoundedFilledRectangle(
                $boundaries['l'] - $margin,
                $boundaries['T'] - $margin,
                $boundaries['r'] + $margin,
                $boundaries['b'] + $margin,
                $margin,
                [
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                    'alpha' => $alpha,
                    'borderR' => $borderR,
                    'borderG' => $borderG,
                    'borderB' => $borderB
                ]
            );
        } elseif ($style == Constant::LEGEND_BOX) {
            $this->drawFilledRectangle(
                $boundaries['l'] - $margin,
                $boundaries['T'] - $margin,
                $boundaries['r'] + $margin,
                $boundaries['b'] + $margin,
                [
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                    'alpha' => $alpha,
                    'borderR' => $borderR,
                    'borderG' => $borderG,
                    'borderB' => $borderB
                ]
            );
        }
        $restoreShadow = $this->shadow;
        $this->shadow = false;
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $ticks = $serie['ticks'];
                $weight = $serie['weight'];
                if (isset($serie['picture'])) {
                    $picture = $serie['picture'];
                    list($picWidth, $picHeight) = $this->getPicInfo($picture);
                    $picX = $x + $iconAreaWidth / 2;
                    $picY = $y + $iconAreaHeight / 2;
                    $this->drawFromPNG($picX - $picWidth / 2, $picY - $picHeight / 2, $picture);
                } else {
                    if ($family == Constant::LEGEND_FAMILY_BOX) {
                        $xOffset = 0;
                        if ($boxWidth != $iconAreaWidth) {
                            $xOffset = floor(($iconAreaWidth - $boxWidth) / 2);
                        }
                        $yOffset = 0;
                        if ($boxHeight != $iconAreaHeight) {
                            $yOffset = floor(($iconAreaHeight - $boxHeight) / 2);
                        }
                        $this->drawFilledRectangle(
                            $x + 1 + $xOffset,
                            $y + 1 + $yOffset,
                            $x + $boxWidth + $xOffset + 1,
                            $y + $boxHeight + 1 + $yOffset,
                            ['r' => 0, 'g' => 0, 'b' => 0, 'alpha' => 20]
                        );
                        $this->drawFilledRectangle(
                            $x + $xOffset,
                            $y + $yOffset,
                            $x + $boxWidth + $xOffset,
                            $y + $boxHeight + $yOffset,
                            ['r' => $r, 'g' => $g, 'b' => $b, 'surrounding' => 20]
                        );
                    } elseif ($family == Constant::LEGEND_FAMILY_CIRCLE) {
                        $this->drawFilledCircle(
                            $x + 1 + $iconAreaWidth / 2,
                            $y + 1 + $iconAreaHeight / 2,
                            min($iconAreaHeight / 2, $iconAreaWidth / 2),
                            ['r' => 0, 'g' => 0, 'b' => 0, 'alpha' => 20]
                        );
                        $this->drawFilledCircle(
                            $x + $iconAreaWidth / 2,
                            $y + $iconAreaHeight / 2,
                            min($iconAreaHeight / 2, $iconAreaWidth / 2),
                            ['r' => $r, 'g' => $g, 'b' => $b, 'surrounding' => 20]
                        );
                    } elseif ($family == Constant::LEGEND_FAMILY_LINE) {
                        $this->drawLine(
                            $x + 1,
                            $y + 1 + $iconAreaHeight / 2,
                            $x + 1 + $iconAreaWidth,
                            $y + 1 + $iconAreaHeight / 2,
                            ['r' => 0, 'g' => 0, 'b' => 0, 'alpha' => 20, 'ticks' => $ticks, 'weight' => $weight]
                        );
                        $this->drawLine(
                            $x,
                            $y + $iconAreaHeight / 2,
                            $x + $iconAreaWidth,
                            $y + $iconAreaHeight / 2,
                            ['r' => $r, 'g' => $g, 'b' => $b, 'ticks' => $ticks, 'weight' => $weight]
                        );
                    }
                }
                if ($mode == Constant::LEGEND_VERTICAL) {
                    $lines = preg_split("/\n/", $serie['description']);
                    foreach ($lines as $key => $value) {
                        $this->drawText(
                            $x + $iconAreaWidth + 4,
                            $y + $iconAreaHeight / 2 + (($this->fontSize + 3) * $key),
                            $value,
                            [
                                'r' => $fontR,
                                'g' => $fontG,
                                'b' => $fontB,
                                'align' => Constant::TEXT_ALIGN_MIDDLELEFT,
                                'fontSize' => $fontSize,
                                'fontName' => $fontName
                            ]
                        );
                    }
                    $y = $y + max($this->fontSize * count($lines), $iconAreaHeight) + 5;
                } elseif ($mode == Constant::LEGEND_HORIZONTAL) {
                    $lines = preg_split("/\n/", $serie['description']);
                    $width = [];
                    foreach ($lines as $key => $value) {
                        $boxArray = $this->drawText(
                            $x + $iconAreaWidth + 4,
                            $y + $iconAreaHeight / 2 + (($this->fontSize + 3) * $key),
                            $value,
                            [
                                'r' => $fontR,
                                'g' => $fontG,
                                'b' => $fontB,
                                'align' => Constant::TEXT_ALIGN_MIDDLELEFT,
                                'fontSize' => $fontSize,
                                'fontName' => $fontName
                            ]
                        );
                        $width[] = $boxArray[1]['x'];
                    }
                    $x = max($width) + 2 + $xStep;
                }
            }
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * @param array $format
     *
     * @throws Exception
     */
    public function drawScale(array $format = []) {
        $pos = isset($format['pos']) ? $format['pos'] : Constant::SCALE_POS_LEFTRIGHT;
        $floating = isset($format['floating']) ? $format['floating'] : false;
        $mode = isset($format['mode']) ? $format['mode'] : Constant::SCALE_MODE_FLOATING;
        $removeXAxis = isset($format['removeXAxis']) ? $format['removeXAxis'] : false;
        $removeYAxis = isset($format['removeYAxis']) ? $format['removeYAxis'] : false;
        $removeYAxiValues = isset($format['removeYAxisValues']) ? $format['removeYAxisValues'] : false;
        $minDivHeight = isset($format['minDivHeight']) ? $format['minDivHeight'] : 20;
        $factors = isset($format['factors']) ? $format['factors'] : [1, 2, 5];
        $manualScale = isset($format['manualScale']) ? $format['manualScale'] : ['0' => ['min' => -100, 'max' => 100]];
        $xMargin = isset($format['xMargin']) ? $format['xMargin'] : Constant::AUTO;
        $yMargin = isset($format['yMargin']) ? $format['yMargin'] : 0;
        $scaleSpacing = isset($format['scaleSpacing']) ? $format['scaleSpacing'] : 15;
        $InnerTickWidth = isset($format['innerTickWidth']) ? $format['innerTickWidth'] : 2;
        $outerTickWidth = isset($format['outerTickWidth']) ? $format['outerTickWidth'] : 2;
        $drawXLines = isset($format['drawXLines']) ? $format['drawXLines'] : true;
        $drawYLines = isset($format['drawYLines']) ? $format['drawYLines'] : Constant::ALL;
        $gridTicks = isset($format['gridTicks']) ? $format['gridTicks'] : 4;
        $gridR = isset($format['gridR']) ? $format['gridR'] : 255;
        $gridG = isset($format['gridG']) ? $format['gridG'] : 255;
        $gridB = isset($format['gridB']) ? $format['gridB'] : 255;
        $gridalpha = isset($format['gridalpha']) ? $format['gridalpha'] : 40;
        $axisRo = isset($format['axisR']) ? $format['axisR'] : 0;
        $axisGo = isset($format['axisG']) ? $format['axisG'] : 0;
        $axisBo = isset($format['axisB']) ? $format['axisB'] : 0;
        $axisalpha = isset($format['axisalpha']) ? $format['axisalpha'] : 100;
        $tickRo = isset($format['TickR']) ? $format['TickR'] : 0;
        $tickGo = isset($format['TickG']) ? $format['TickG'] : 0;
        $tickBo = isset($format['TickB']) ? $format['TickB'] : 0;
        $tickalpha = isset($format['Tickalpha']) ? $format['Tickalpha'] : 100;
        $drawSubTicks = isset($format['drawSubTicks']) ? $format['drawSubTicks'] : false;
        $InnerSubTickWidth = isset($format['innerSubTickWidth']) ? $format['innerSubTickWidth'] : 0;
        $outerSubTickWidth = isset($format['outerSubTickWidth']) ? $format['outerSubTickWidth'] : 2;
        $subTickR = isset($format['subTickR']) ? $format['subTickR'] : 255;
        $subTickG = isset($format['subTickG']) ? $format['subTickG'] : 0;
        $subTickB = isset($format['subTickB']) ? $format['subTickB'] : 0;
        $subTickalpha = isset($format['subTickalpha']) ? $format['subTickalpha'] : 100;
        $autoAxisLabels = isset($format['autoAxisLabels']) ? $format['autoAxisLabels'] : true;
        $xReleasePercent = isset($format['xReleasePercent']) ? $format['xReleasePercent'] : 1;
        $drawArrows = isset($format['drawArrows']) ? $format['drawArrows'] : false;
        $arrowSize = isset($format['arrowSize']) ? $format['arrowSize'] : 8;
        $CycleBackground = isset($format['CycleBackground']) ? $format['CycleBackground'] : false;
        $backgroundR1 = isset($format['backgroundR1']) ? $format['backgroundR1'] : 255;
        $backgroundG1 = isset($format['backgroundG1']) ? $format['backgroundG1'] : 255;
        $backgroundB1 = isset($format['backgroundB1']) ? $format['backgroundB1'] : 255;
        $backgroundalpha1 = isset($format['backgroundalpha1']) ? $format['backgroundalpha1'] : 20;
        $backgroundR2 = isset($format['backgroundR2']) ? $format['backgroundR2'] : 230;
        $backgroundG2 = isset($format['backgroundG2']) ? $format['backgroundG2'] : 230;
        $backgroundB2 = isset($format['backgroundB2']) ? $format['backgroundB2'] : 230;
        $backgroundalpha2 = isset($format['backgroundalpha2']) ? $format['backgroundalpha2'] : 20;
        $labelingMethod = isset($format['labelingMethod']) ? $format['labelingMethod'] : Constant::LABELING_ALL;
        $labelSkip = isset($format['labelSkip']) ? $format['labelSkip'] : 0;
        $labelRotation = isset($format['labelRotation']) ? $format['labelRotation'] : 0;
        $removeSkippedAxis = isset($format['removeSkippedAxis']) ? $format['removeSkippedAxis'] : false;
        $skippedAxisTicks = isset($format['skippedAxisTicks']) ? $format['skippedAxisTicks'] : $gridTicks + 2;
        $skippedAxisR = isset($format['skippedAxisR']) ? $format['skippedAxisR'] : $gridR;
        $skippedAxisG = isset($format['skippedAxisG']) ? $format['skippedAxisG'] : $gridG;
        $skippedAxisB = isset($format['skippedAxisB']) ? $format['skippedAxisB'] : $gridB;
        $skippedAxisalpha = isset($format['skippedAxisalpha']) ? $format['skippedAxisalpha'] : $gridalpha - 30;
        $skippedTickR = isset($format['skippedTickR']) ? $format['skippedTickR'] : $tickRo;
        $skippedTickG = isset($format['skippedTickG']) ? $format['skippedTickG'] : $tickGo;
        $skippedTickB = isset($format['skippedTicksB']) ? $format['skippedTickB'] : $tickBo;
        $skippedTickalpha = isset($format['skippedTickalpha']) ? $format['skippedTickalpha'] : $tickalpha - 80;
        $skippedInnerTickWidth = isset($format['skippedInnerTickWidth']) ? $format['skippedInnerTickWidth'] : 0;
        $skippedOuterTickWidth = isset($format['skippedOuterTickWidth']) ? $format['skippedOuterTickWidth'] : 2;
        /* Floating scale require X & Y margins to be set manually */
        if ($floating && ($xMargin == Constant::AUTO || $yMargin == 0)) {
            $floating = false;
        }
        /* Skip a NOTICE event in case of an empty array */
        if ($drawYLines == Constant::NONE || $drawYLines == false) {
            $drawYLines = ['zarma' => '31'];
        }
        /* Define the color for the skipped elements */
        $skippedAxisColor = [
            'r' => $skippedAxisR,
            'g' => $skippedAxisG,
            'b' => $skippedAxisB,
            'alpha' => $skippedAxisalpha,
            'ticks' => $skippedAxisTicks
        ];
        $skippedTickColor = [
            'r' => $skippedTickR,
            'g' => $skippedTickG,
            'b' => $skippedTickB,
            'alpha' => $skippedTickalpha
        ];
        $data = $this->dataSet->getData();
        $abscissa = null;
        if (isset($data['abscissa'])) {
            $abscissa = $data['abscissa'];
        }
        /* Unset the abscissa axis, needed if we display multiple charts on the same picture */
        if ($abscissa != null) {
            foreach ($data['axis'] as $axisId => $parameters) {
                if ($parameters['identity'] == Constant::AXIS_X) {
                    unset($data['axis'][$axisId]);
                }
            }
        }
        /* Build the scale settings */
        $gotXAxis = false;
        foreach ($data['axis'] as $axisId => $axisParameter) {
            if ($axisParameter['identity'] == Constant::AXIS_X) {
                $gotXAxis = true;
            }
            if ($pos == Constant::SCALE_POS_LEFTRIGHT && $axisParameter['identity'] == Constant::AXIS_Y) {
                $height = $this->graphAreaY2 - $this->graphAreaY1 - $yMargin * 2;
            } elseif ($pos == Constant::SCALE_POS_LEFTRIGHT && $axisParameter['identity'] == Constant::AXIS_X) {
                $height = $this->graphAreaX2 - $this->graphAreaX1;
            } elseif ($pos == Constant::SCALE_POS_TOPBOTTOM && $axisParameter['identity'] == Constant::AXIS_Y) {
                $height = $this->graphAreaX2 - $this->graphAreaX1 - $yMargin * 2;
                ;
            } else {
                $height = $this->graphAreaY2 - $this->graphAreaY1;
            }
            $axisMin = Constant::ABSOLUTE_MAX;
            $axisMax = Constant::OUT_OF_SIGHT;
            if ($mode == Constant::SCALE_MODE_FLOATING || $mode == Constant::SCALE_MODE_START0) {
                foreach ($data['series'] as $serieID => $serieParameter) {
                    if ($serieParameter['axis'] == $axisId && $data['series'][$serieID]['isDrawable'] && $data['abscissa'] != $serieID
                    ) {
                        $axisMax = max($axisMax, $data['series'][$serieID]['max']);
                        $axisMin = min($axisMin, $data['series'][$serieID]['min']);
                    }
                }
                $autoMargin = (($axisMax - $axisMin) / 100) * $xReleasePercent;
                $data['axis'][$axisId]['min'] = $axisMin - $autoMargin;
                $data['axis'][$axisId]['max'] = $axisMax + $autoMargin;
                if ($mode == Constant::SCALE_MODE_START0) {
                    $data['axis'][$axisId]['min'] = 0;
                }
            } elseif ($mode == Constant::SCALE_MODE_MANUAL) {
                if (isset($manualScale[$axisId]['min']) && isset($manualScale[$axisId]['max'])) {
                    $data['axis'][$axisId]['min'] = $manualScale[$axisId]['min'];
                    $data['axis'][$axisId]['max'] = $manualScale[$axisId]['max'];
                } else {
                    throw new Exception('Manual scale boundaries not set.');
                }
            } elseif ($mode == Constant::SCALE_MODE_ADDALL || $mode == Constant::SCALE_MODE_ADDALL_START0) {
                $series = [];
                foreach ($data['series'] as $serieID => $serieParameter) {
                    if ($serieParameter['axis'] == $axisId && $serieParameter['isDrawable'] && $data['abscissa'] != $serieID
                    ) {
                        $series[$serieID] = count($data['series'][$serieID]['data']);
                    }
                }
                for ($id = 0; $id <= max($series) - 1; $id++) {
                    $pointMin = 0;
                    $pointMax = 0;
                    foreach ($series as $serieID => $valuesCount) {
                        if (isset($data['series'][$serieID]['data'][$id]) && $data['series'][$serieID]['data'][$id] != null
                        ) {
                            $value = $data['series'][$serieID]['data'][$id];
                            if ($value > 0) {
                                $pointMax = $pointMax + $value;
                            } else {
                                $pointMin = $pointMin + $value;
                            }
                        }
                    }
                    $axisMax = max($axisMax, $pointMax);
                    $axisMin = min($axisMin, $pointMin);
                }
                $autoMargin = (($axisMax - $axisMin) / 100) * $xReleasePercent;
                $data['axis'][$axisId]['min'] = $axisMin - $autoMargin;
                $data['axis'][$axisId]['max'] = $axisMax + $autoMargin;
            }
            $maxDivs = floor($height / $minDivHeight);
            if ($mode == Constant::SCALE_MODE_ADDALL_START0) {
                $data['axis'][$axisId]['min'] = 0;
            }
            $scale = $this->computeScale(
                $data['axis'][$axisId]['min'],
                $data['axis'][$axisId]['max'],
                $maxDivs,
                $factors,
                $axisId
            );
            $data['axis'][$axisId]['margin'] = $axisParameter['identity'] == Constant::AXIS_X ? $xMargin : $yMargin;
            $data['axis'][$axisId]['scaleMin'] = $scale['xMin'];
            $data['axis'][$axisId]['scaleMax'] = $scale['xMax'];
            $data['axis'][$axisId]['rows'] = $scale['rows'];
            $data['axis'][$axisId]['rowHeight'] = $scale['rowHeight'];
            if (isset($scale['format'])) {
                $data['axis'][$axisId]['format'] = $scale['format'];
            }
            if (!isset($data['axis'][$axisId]['display'])) {
                $data['axis'][$axisId]['display'] = null;
            }
            if (!isset($data['axis'][$axisId]['format'])) {
                $data['axis'][$axisId]['format'] = null;
            }
            if (!isset($data['axis'][$axisId]['unit'])) {
                $data['axis'][$axisId]['unit'] = null;
            }
        }
        /* Still no X axis */
        if ($gotXAxis == false) {
            if ($abscissa != null) {
                $points = count($data['series'][$abscissa]['data']);
                $axisName = null;
                if ($autoAxisLabels) {
                    $axisName = isset($data['series'][$abscissa]['description']) ? $data['series'][$abscissa]['description'] : null;
                }
            } else {
                $points = 0;
                $axisName = isset($data['xAxisName']) ? $data['xAxisName'] : null;
                foreach ($data['series'] as $serieID => $serieParameter) {
                    if ($serieParameter['isDrawable']) {
                        $points = max($points, count($serieParameter['data']));
                    }
                }
            }
            $axisId = count($data['axis']);
            $data['axis'][$axisId]['identity'] = Constant::AXIS_X;
            if ($pos == Constant::SCALE_POS_LEFTRIGHT) {
                $data['axis'][$axisId]['position'] = Constant::AXIS_POSITION_BOTTOM;
            } else {
                $data['axis'][$axisId]['position'] = Constant::AXIS_POSITION_LEFT;
            }
            if (isset($data['abscissaName'])) {
                $data['axis'][$axisId]['name'] = $data['abscissaName'];
            }
            if ($xMargin == Constant::AUTO) {
                if ($pos == Constant::SCALE_POS_LEFTRIGHT) {
                    $height = $this->graphAreaX2 - $this->graphAreaX1;
                } else {
                    $height = $this->graphAreaY2 - $this->graphAreaY1;
                }
                if ($points == 0 || $points == 1) {
                    $data['axis'][$axisId]['margin'] = $height / 2;
                } else {
                    $data['axis'][$axisId]['margin'] = ($height / $points) / 2;
                }
            } else {
                $data['axis'][$axisId]['margin'] = $xMargin;
            }
            $data['axis'][$axisId]['rows'] = $points - 1;
            if (!isset($data['axis'][$axisId]['display'])) {
                $data['axis'][$axisId]['display'] = null;
            }
            if (!isset($data['axis'][$axisId]['format'])) {
                $data['axis'][$axisId]['format'] = null;
            }
            if (!isset($data['axis'][$axisId]['unit'])) {
                $data['axis'][$axisId]['unit'] = null;
            }
        }
        /* Do we need to reverse the abscissa position? */
        if ($pos != Constant::SCALE_POS_LEFTRIGHT) {
            $data['absicssaPosition'] = Constant::AXIS_POSITION_RIGHT;
            if ($data['absicssaPosition'] == Constant::AXIS_POSITION_BOTTOM) {
                $data['absicssaPosition'] = Constant::AXIS_POSITION_LEFT;
            }
        }
        $data['axis'][$axisId]['position'] = $data['absicssaPosition'];
        $this->dataSet->saveOrientation($pos);
        $this->dataSet->saveAxisConfig($data['axis']);
        $this->dataSet->saveYMargin($yMargin);
        $fontColorRo = $this->fontColorR;
        $fontColorGo = $this->fontColorG;
        $fontColorBo = $this->fontColorB;
        $axisPos['l'] = $this->graphAreaX1;
        $axisPos['r'] = $this->graphAreaX2;
        $axisPos['T'] = $this->graphAreaY1;
        $axisPos['b'] = $this->graphAreaY2;
        foreach ($data['axis'] as $axisId => $parameters) {
            if (isset($parameters['color'])) {
                $axisR = $parameters['color']['r'];
                $axisG = $parameters['color']['g'];
                $axisB = $parameters['color']['b'];
                $tickR = $parameters['color']['r'];
                $tickG = $parameters['color']['g'];
                $tickB = $parameters['color']['b'];
                $this->setFontProperties(
                    [
                        'r' => $parameters['color']['r'],
                        'g' => $parameters['color']['g'],
                        'b' => $parameters['color']['b']
                    ]
                );
            } else {
                $axisR = $axisRo;
                $axisG = $axisGo;
                $axisB = $axisBo;
                $tickR = $tickRo;
                $tickG = $tickGo;
                $tickB = $tickBo;
                $this->setFontProperties(['r' => $fontColorRo, 'g' => $fontColorGo, 'b' => $fontColorBo]);
            }
            $lastValue = 'w00t';
            $id = 1;
            if ($parameters['identity'] == Constant::AXIS_X) {
                if ($pos == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($parameters['position'] == Constant::AXIS_POSITION_BOTTOM) {
                        if ($labelRotation == 0) {
                            $labelAlign = Constant::TEXT_ALIGN_TOPMIDDLE;
                            $yLabelOffset = 2;
                        }
                        if ($labelRotation > 0 && $labelRotation < 190) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLERIGHT;
                            $yLabelOffset = 5;
                        }
                        if ($labelRotation == 180) {
                            $labelAlign = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                            $yLabelOffset = 5;
                        }
                        if ($labelRotation > 180 && $labelRotation < 360) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLELEFT;
                            $yLabelOffset = 2;
                        }
                        if (!$removeXAxis) {
                            if ($floating) {
                                $floatingOffset = $yMargin;
                                $this->drawLine(
                                    $this->graphAreaX1 + $parameters['margin'],
                                    $axisPos['b'],
                                    $this->graphAreaX2 - $parameters['margin'],
                                    $axisPos['b'],
                                    ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                                );
                            } else {
                                $floatingOffset = 0;
                                $this->drawLine(
                                    $this->graphAreaX1,
                                    $axisPos['b'],
                                    $this->graphAreaX2,
                                    $axisPos['b'],
                                    ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                    $this->graphAreaX2 - $parameters['margin'],
                                    $axisPos['b'],
                                    $this->graphAreaX2 + ($arrowSize * 2),
                                    $axisPos['b'],
                                    ['fillR' => $axisR, 'fillG' => $axisG, 'fillB' => $axisB, 'size' => $arrowSize]
                                );
                            }
                        }
                        $width = ($this->graphAreaX2 - $this->graphAreaX1) - $parameters['margin'] * 2;
                        if ($parameters['rows'] == 0) {
                            $step = $width;
                        } else {
                            $step = $width / ($parameters['rows']);
                        }
                        $maxBottom = $axisPos['b'];
                        for ($i = 0; $i <= $parameters['rows']; $i++) {
                            $xPos = $this->graphAreaX1 + $parameters['margin'] + $step * $i;
                            $yPos = $axisPos['b'];
                            if ($abscissa != null) {
                                $value = '';
                                if (isset($data['series'][$abscissa]['data'][$i])) {
                                    $value = $this->scaleFormat(
                                        $data['series'][$abscissa]['data'][$i],
                                        $data['xAxisDisplay'],
                                        $data['xAxisFormat'],
                                        $data['xAxisUnit']
                                    );
                                }
                            } else {
                                $value = $i;
                                if (isset($parameters['scaleMin']) && isset($parameters['rowHeight'])) {
                                    $value = $this->scaleFormat(
                                        $parameters['scaleMin'] + $parameters['rowHeight'] * $i,
                                        $data['xAxisDisplay'],
                                        $data['xAxisFormat'],
                                        $data['xAxisUnit']
                                    );
                                }
                            }
                            $id++;
                            $skipped = true;
                            if ($this->isValidLabel($value, $lastValue, $labelingMethod, $id, $labelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                    $xPos,
                                    $yPos + $outerTickWidth + $yLabelOffset,
                                    $value,
                                    ['angle' => $labelRotation, 'align' => $labelAlign]
                                );
                                $txtBottom = $yPos + $outerTickWidth + 2 + ($bounds[0]['y'] - $bounds[2]['y']);
                                $maxBottom = max($maxBottom, $txtBottom);
                                $lastValue = $value;
                                $skipped = false;
                            }
                            if ($removeXAxis) {
                                $skipped = false;
                            }
                            if ($skipped) {
                                if ($drawXLines && !$removeSkippedAxis) {
                                    $this->drawLine(
                                        $xPos,
                                        $this->graphAreaY1 + $floatingOffset,
                                        $xPos,
                                        $this->graphAreaY2 - $floatingOffset,
                                        $skippedAxisColor
                                    );
                                }
                                if (($skippedInnerTickWidth != 0 || $skippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                        $xPos,
                                        $yPos - $skippedInnerTickWidth,
                                        $xPos,
                                        $yPos + $skippedOuterTickWidth,
                                        $skippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines && ($xPos != $this->graphAreaX1 && $xPos != $this->graphAreaX2)
                                ) {
                                    $this->drawLine(
                                        $xPos,
                                        $this->graphAreaY1 + $floatingOffset,
                                        $xPos,
                                        $this->graphAreaY2 - $floatingOffset,
                                        [
                                            'r' => $gridR,
                                            'g' => $gridG,
                                            'b' => $gridB,
                                            'alpha' => $gridalpha,
                                            'ticks' => $gridTicks
                                        ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $outerTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                        $xPos,
                                        $yPos - $InnerTickWidth,
                                        $xPos,
                                        $yPos + $outerTickWidth,
                                        ['r' => $tickR, 'g' => $tickG, 'b' => $tickB, 'alpha' => $tickalpha]
                                    );
                                }
                            }
                        }
                        if (isset($parameters['name']) && !$removeXAxis) {
                            $yPos = $maxBottom + 2;
                            $xPos = $this->graphAreaX1 + ($this->graphAreaX2 - $this->graphAreaX1) / 2;
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos,
                                $parameters['name'],
                                ['align' => Constant::TEXT_ALIGN_TOPMIDDLE]
                            );
                            $maxBottom = $bounds[0]['y'];
                            $this->dataSet->data['graphArea']['y2'] = $maxBottom + $this->fontSize;
                        }
                        $axisPos['b'] = $maxBottom + $scaleSpacing;
                    } elseif ($parameters['position'] == Constant::AXIS_POSITION_TOP) {
                        if ($labelRotation == 0) {
                            $labelAlign = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                            $yLabelOffset = 2;
                        }
                        if ($labelRotation > 0 && $labelRotation < 190) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLELEFT;
                            $yLabelOffset = 2;
                        }
                        if ($labelRotation == 180) {
                            $labelAlign = Constant::TEXT_ALIGN_TOPMIDDLE;
                            $yLabelOffset = 5;
                        }
                        if ($labelRotation > 180 && $labelRotation < 360) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLERIGHT;
                            $yLabelOffset = 5;
                        }
                        if (!$removeXAxis) {
                            if ($floating) {
                                $floatingOffset = $yMargin;
                                $this->drawLine(
                                    $this->graphAreaX1 + $parameters['margin'],
                                    $axisPos['T'],
                                    $this->graphAreaX2 - $parameters['margin'],
                                    $axisPos['T'],
                                    ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                                );
                            } else {
                                $floatingOffset = 0;
                                $this->drawLine(
                                    $this->graphAreaX1,
                                    $axisPos['T'],
                                    $this->graphAreaX2,
                                    $axisPos['T'],
                                    ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                    $this->graphAreaX2 - $parameters['margin'],
                                    $axisPos['T'],
                                    $this->graphAreaX2 + ($arrowSize * 2),
                                    $axisPos['T'],
                                    ['fillR' => $axisR, 'fillG' => $axisG, 'fillB' => $axisB, 'size' => $arrowSize]
                                );
                            }
                        }
                        $width = ($this->graphAreaX2 - $this->graphAreaX1) - $parameters['margin'] * 2;
                        if ($parameters['rows'] == 0) {
                            $step = $width;
                        } else {
                            $step = $width / $parameters['rows'];
                        }
                        $minTop = $axisPos['T'];
                        for ($i = 0; $i <= $parameters['rows']; $i++) {
                            $xPos = $this->graphAreaX1 + $parameters['margin'] + $step * $i;
                            $yPos = $axisPos['T'];
                            if ($abscissa != null) {
                                $value = '';
                                if (isset($data['series'][$abscissa]['data'][$i])) {
                                    $value = $this->scaleFormat(
                                        $data['series'][$abscissa]['data'][$i],
                                        $data['xAxisDisplay'],
                                        $data['xAxisFormat'],
                                        $data['xAxisUnit']
                                    );
                                }
                            } else {
                                $value = $i;
                                if (isset($parameters['scaleMin']) && isset($parameters['rowHeight'])) {
                                    $value = $this->scaleFormat(
                                        $parameters['scaleMin'] + $parameters['rowHeight'] * $i,
                                        $data['xAxisDisplay'],
                                        $data['xAxisFormat'],
                                        $data['xAxisUnit']
                                    );
                                }
                            }
                            $id++;
                            $skipped = true;
                            if ($this->isValidLabel($value, $lastValue, $labelingMethod, $id, $labelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                    $xPos,
                                    $yPos - $outerTickWidth - $yLabelOffset,
                                    $value,
                                    ['angle' => $labelRotation, 'align' => $labelAlign]
                                );
                                $txtBox = $yPos - $outerTickWidth - 2 - ($bounds[0]['y'] - $bounds[2]['y']);
                                $minTop = min($minTop, $txtBox);
                                $lastValue = $value;
                                $skipped = false;
                            }
                            if ($removeXAxis) {
                                $skipped = false;
                            }
                            if ($skipped) {
                                if ($drawXLines && !$removeSkippedAxis) {
                                    $this->drawLine(
                                        $xPos,
                                        $this->graphAreaY1 + $floatingOffset,
                                        $xPos,
                                        $this->graphAreaY2 - $floatingOffset,
                                        $skippedAxisColor
                                    );
                                }
                                if (($skippedInnerTickWidth != 0 || $skippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                        $xPos,
                                        $yPos + $skippedInnerTickWidth,
                                        $xPos,
                                        $yPos - $skippedOuterTickWidth,
                                        $skippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines) {
                                    $this->drawLine(
                                        $xPos,
                                        $this->graphAreaY1 + $floatingOffset,
                                        $xPos,
                                        $this->graphAreaY2 - $floatingOffset,
                                        [
                                            'r' => $gridR,
                                            'g' => $gridG,
                                            'b' => $gridB,
                                            'alpha' => $gridalpha,
                                            'ticks' => $gridTicks
                                        ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $outerTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                        $xPos,
                                        $yPos + $InnerTickWidth,
                                        $xPos,
                                        $yPos - $outerTickWidth,
                                        [
                                            'r' => $tickR,
                                            'g' => $tickG,
                                            'b' => $tickB,
                                            'alpha' => $tickalpha
                                        ]
                                    );
                                }
                            }
                        }
                        if (isset($parameters['name']) && !$removeXAxis) {
                            $yPos = $minTop - 2;
                            $xPos = $this->graphAreaX1 + ($this->graphAreaX2 - $this->graphAreaX1) / 2;
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos,
                                $parameters['name'],
                                ['align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE]
                            );
                            $minTop = $bounds[2]['y'];
                            $this->dataSet->data['graphArea']['y1'] = $minTop;
                        }
                        $axisPos['T'] = $minTop - $scaleSpacing;
                    }
                } elseif ($pos == Constant::SCALE_POS_TOPBOTTOM) {
                    if ($parameters['position'] == Constant::AXIS_POSITION_LEFT) {
                        if ($labelRotation == 0) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLERIGHT;
                            $xLabelOffset = -2;
                        }
                        if ($labelRotation > 0 && $labelRotation < 190) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLERIGHT;
                            $xLabelOffset = -6;
                        }
                        if ($labelRotation == 180) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLELEFT;
                            $xLabelOffset = -2;
                        }
                        if ($labelRotation > 180 && $labelRotation < 360) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLELEFT;
                            $xLabelOffset = -5;
                        }
                        if (!$removeXAxis) {
                            if ($floating) {
                                $floatingOffset = $yMargin;
                                $this->drawLine(
                                    $axisPos['l'],
                                    $this->graphAreaY1 + $parameters['margin'],
                                    $axisPos['l'],
                                    $this->graphAreaY2 - $parameters['margin'],
                                    ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                                );
                            } else {
                                $floatingOffset = 0;
                                $this->drawLine(
                                    $axisPos['l'],
                                    $this->graphAreaY1,
                                    $axisPos['l'],
                                    $this->graphAreaY2,
                                    ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                    $axisPos['l'],
                                    $this->graphAreaY2 - $parameters['margin'],
                                    $axisPos['l'],
                                    $this->graphAreaY2 + ($arrowSize * 2),
                                    [
                                        'fillR' => $axisR,
                                        'fillG' => $axisG,
                                        'fillB' => $axisB,
                                        'size' => $arrowSize
                                    ]
                                );
                            }
                        }
                        $height = ($this->graphAreaY2 - $this->graphAreaY1) - $parameters['margin'] * 2;
                        if ($parameters['rows'] == 0) {
                            $step = $height;
                        } else {
                            $step = $height / $parameters['rows'];
                        }
                        $minLeft = $axisPos['l'];
                        for ($i = 0; $i <= $parameters['rows']; $i++) {
                            $yPos = $this->graphAreaY1 + $parameters['margin'] + $step * $i;
                            $xPos = $axisPos['l'];
                            if ($abscissa != null) {
                                $value = '';
                                if (isset($data['series'][$abscissa]['data'][$i])) {
                                    $value = $this->scaleFormat(
                                        $data['series'][$abscissa]['data'][$i],
                                        $data['xAxisDisplay'],
                                        $data['xAxisFormat'],
                                        $data['xAxisUnit']
                                    );
                                }
                            } else {
                                $value = $i;
                                if (isset($parameters['scaleMin']) && isset($parameters['rowHeight'])) {
                                    $value = $this->scaleFormat(
                                        $parameters['scaleMin'] + $parameters['rowHeight'] * $i,
                                        $data['xAxisDisplay'],
                                        $data['xAxisFormat'],
                                        $data['xAxisUnit']
                                    );
                                }
                            }
                            $id++;
                            $skipped = true;
                            if ($this->isValidLabel($value, $lastValue, $labelingMethod, $id, $labelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                    $xPos - $outerTickWidth + $xLabelOffset,
                                    $yPos,
                                    $value,
                                    ['angle' => $labelRotation, 'align' => $labelAlign]
                                );
                                $txtBox = $xPos - $outerTickWidth - 2 - ($bounds[1]['x'] - $bounds[0]['x']);
                                $minLeft = min($minLeft, $txtBox);
                                $lastValue = $value;
                                $skipped = false;
                            }
                            if ($removeXAxis) {
                                $skipped = false;
                            }
                            if ($skipped) {
                                if ($drawXLines && !$removeSkippedAxis) {
                                    $this->drawLine(
                                        $this->graphAreaX1 + $floatingOffset,
                                        $yPos,
                                        $this->graphAreaX2 - $floatingOffset,
                                        $yPos,
                                        $skippedAxisColor
                                    );
                                }
                                if (($skippedInnerTickWidth != 0 || $skippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                        $xPos - $skippedOuterTickWidth,
                                        $yPos,
                                        $xPos + $skippedInnerTickWidth,
                                        $yPos,
                                        $skippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines && ($yPos != $this->graphAreaY1 && $yPos != $this->graphAreaY2)) {
                                    $this->drawLine(
                                        $this->graphAreaX1 + $floatingOffset,
                                        $yPos,
                                        $this->graphAreaX2 - $floatingOffset,
                                        $yPos,
                                        [
                                            'r' => $gridR,
                                            'g' => $gridG,
                                            'b' => $gridB,
                                            'alpha' => $gridalpha,
                                            'ticks' => $gridTicks
                                        ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $outerTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                        $xPos - $outerTickWidth,
                                        $yPos,
                                        $xPos + $InnerTickWidth,
                                        $yPos,
                                        ['r' => $tickR, 'g' => $tickG, 'b' => $tickB, 'alpha' => $tickalpha]
                                    );
                                }
                            }
                        }
                        if (isset($parameters['name']) && !$removeXAxis) {
                            $xPos = $minLeft - 2;
                            $yPos = $this->graphAreaY1 + ($this->graphAreaY2 - $this->graphAreaY1) / 2;
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos,
                                $parameters['name'],
                                ['align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE, 'Angle' => 90]
                            );
                            $minLeft = $bounds[0]['x'];
                            $this->dataSet->data['graphArea']['x1'] = $minLeft;
                        }
                        $axisPos['l'] = $minLeft - $scaleSpacing;
                    } elseif ($parameters['position'] == Constant::AXIS_POSITION_RIGHT) {
                        if ($labelRotation == 0) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLELEFT;
                            $xLabelOffset = 2;
                        }
                        if ($labelRotation > 0 && $labelRotation < 190) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLELEFT;
                            $xLabelOffset = 6;
                        }
                        if ($labelRotation == 180) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLERIGHT;
                            $xLabelOffset = 5;
                        }
                        if ($labelRotation > 180 && $labelRotation < 360) {
                            $labelAlign = Constant::TEXT_ALIGN_MIDDLERIGHT;
                            $xLabelOffset = 7;
                        }
                        if (!$removeXAxis) {
                            if ($floating) {
                                $floatingOffset = $yMargin;
                                $this->drawLine(
                                    $axisPos['r'],
                                    $this->graphAreaY1 + $parameters['margin'],
                                    $axisPos['r'],
                                    $this->graphAreaY2 - $parameters['margin'],
                                    ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                                );
                            } else {
                                $floatingOffset = 0;
                                $this->drawLine(
                                    $axisPos['r'],
                                    $this->graphAreaY1,
                                    $axisPos['r'],
                                    $this->graphAreaY2,
                                    ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                    $axisPos['r'],
                                    $this->graphAreaY2 - $parameters['margin'],
                                    $axisPos['r'],
                                    $this->graphAreaY2 + ($arrowSize * 2),
                                    [
                                        'fillR' => $axisR,
                                        'fillG' => $axisG,
                                        'fillB' => $axisB,
                                        'size' => $arrowSize
                                    ]
                                );
                            }
                        }
                        $height = ($this->graphAreaY2 - $this->graphAreaY1) - $parameters['margin'] * 2;
                        if ($parameters['rows'] == 0) {
                            $step = $height;
                        } else {
                            $step = $height / $parameters['rows'];
                        }
                        $maxRight = $axisPos['r'];
                        for ($i = 0; $i <= $parameters['rows']; $i++) {
                            $yPos = $this->graphAreaY1 + $parameters['margin'] + $step * $i;
                            $xPos = $axisPos['r'];
                            if ($abscissa != null) {
                                $value = '';
                                if (isset($data['series'][$abscissa]['data'][$i])) {
                                    $value = $this->scaleFormat(
                                        $data['series'][$abscissa]['data'][$i],
                                        $data['xAxisDisplay'],
                                        $data['xAxisFormat'],
                                        $data['xAxisUnit']
                                    );
                                }
                            } else {
                                $value = $i;
                                if (isset($parameters['scaleMin']) && isset($parameters['rowHeight'])) {
                                    $value = $this->scaleFormat(
                                        $parameters['scaleMin'] + $parameters['rowHeight'] * $i,
                                        $data['xAxisDisplay'],
                                        $data['xAxisFormat'],
                                        $data['xAxisUnit']
                                    );
                                }
                            }
                            $id++;
                            $skipped = true;
                            if ($this->isValidLabel($value, $lastValue, $labelingMethod, $id, $labelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                    $xPos + $outerTickWidth + $xLabelOffset,
                                    $yPos,
                                    $value,
                                    ['angle' => $labelRotation, 'align' => $labelAlign]
                                );
                                $txtBox = $xPos + $outerTickWidth + 2 + ($bounds[1]['x'] - $bounds[0]['x']);
                                $maxRight = max($maxRight, $txtBox);
                                $lastValue = $value;
                                $skipped = false;
                            }
                            if ($removeXAxis) {
                                $skipped = false;
                            }
                            if ($skipped) {
                                if ($drawXLines && !$removeSkippedAxis) {
                                    $this->drawLine(
                                        $this->graphAreaX1 + $floatingOffset,
                                        $yPos,
                                        $this->graphAreaX2 - $floatingOffset,
                                        $yPos,
                                        $skippedAxisColor
                                    );
                                }
                                if (($skippedInnerTickWidth != 0 || $skippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                        $xPos + $skippedOuterTickWidth,
                                        $yPos,
                                        $xPos - $skippedInnerTickWidth,
                                        $yPos,
                                        $skippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines) {
                                    $this->drawLine(
                                        $this->graphAreaX1 + $floatingOffset,
                                        $yPos,
                                        $this->graphAreaX2 - $floatingOffset,
                                        $yPos,
                                        [
                                            'r' => $gridR,
                                            'g' => $gridG,
                                            'b' => $gridB,
                                            'alpha' => $gridalpha,
                                            'ticks' => $gridTicks
                                        ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $outerTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                        $xPos + $outerTickWidth,
                                        $yPos,
                                        $xPos - $InnerTickWidth,
                                        $yPos,
                                        [
                                            'r' => $tickR,
                                            'g' => $tickG,
                                            'b' => $tickB,
                                            'alpha' => $tickalpha
                                        ]
                                    );
                                }
                            }
                        }
                        if (isset($parameters['name']) && !$removeXAxis) {
                            $xPos = $maxRight + 4;
                            $yPos = $this->graphAreaY1 + ($this->graphAreaY2 - $this->graphAreaY1) / 2;
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos,
                                $parameters['name'],
                                ['align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE, 'Angle' => 270]
                            );
                            $maxRight = $bounds[1]['x'];
                            $this->dataSet->data['graphArea']['x2'] = $maxRight + $this->fontSize;
                        }
                        $axisPos['r'] = $maxRight + $scaleSpacing;
                    }
                }
            }
            if ($parameters['identity'] == Constant::AXIS_Y && !$removeYAxis) {
                if ($pos == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($parameters['position'] == Constant::AXIS_POSITION_LEFT) {
                        if ($floating) {
                            $floatingOffset = $xMargin;
                            $this->drawLine(
                                $axisPos['l'],
                                $this->graphAreaY1 + $parameters['margin'],
                                $axisPos['l'],
                                $this->graphAreaY2 - $parameters['margin'],
                                ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                            );
                        } else {
                            $floatingOffset = 0;
                            $this->drawLine(
                                $axisPos['l'],
                                $this->graphAreaY1,
                                $axisPos['l'],
                                $this->graphAreaY2,
                                ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                $axisPos['l'],
                                $this->graphAreaY1 + $parameters['margin'],
                                $axisPos['l'],
                                $this->graphAreaY1 - ($arrowSize * 2),
                                [
                                    'fillR' => $axisR,
                                    'fillG' => $axisG,
                                    'fillB' => $axisB,
                                    'size' => $arrowSize
                                ]
                            );
                        }
                        $height = ($this->graphAreaY2 - $this->graphAreaY1) - $parameters['margin'] * 2;
                        $step = $height / $parameters['rows'];
                        $subTicksSize = $step / 2;
                        $minLeft = $axisPos['l'];
                        $lastY = null;
                        for ($i = 0; $i <= $parameters['rows']; $i++) {
                            $yPos = $this->graphAreaY2 - $parameters['margin'] - $step * $i;
                            $xPos = $axisPos['l'];
                            $value = $this->scaleFormat(
                                $parameters['scaleMin'] + $parameters['rowHeight'] * $i,
                                $parameters['display'],
                                $parameters['format'],
                                $parameters['unit']
                            );
                            if ($i % 2 == 1) {
                                $bGColor = [
                                    'r' => $backgroundR1,
                                    'g' => $backgroundG1,
                                    'b' => $backgroundB1,
                                    'alpha' => $backgroundalpha1
                                ];
                            } else {
                                $bGColor = [
                                    'r' => $backgroundR2,
                                    'g' => $backgroundG2,
                                    'b' => $backgroundB2,
                                    'alpha' => $backgroundalpha2
                                ];
                            }
                            if ($lastY != null && $CycleBackground && ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                    $this->graphAreaX1 + $floatingOffset,
                                    $lastY,
                                    $this->graphAreaX2 - $floatingOffset,
                                    $yPos,
                                    $bGColor
                                );
                            }
                            if ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                    $this->graphAreaX1 + $floatingOffset,
                                    $yPos,
                                    $this->graphAreaX2 - $floatingOffset,
                                    $yPos,
                                    [
                                        'r' => $gridR,
                                        'g' => $gridG,
                                        'b' => $gridB,
                                        'alpha' => $gridalpha,
                                        'ticks' => $gridTicks
                                    ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters['rows']) {
                                $this->drawLine(
                                    $xPos - $outerSubTickWidth,
                                    $yPos - $subTicksSize,
                                    $xPos + $InnerSubTickWidth,
                                    $yPos - $subTicksSize,
                                    [
                                        'r' => $subTickR,
                                        'g' => $subTickG,
                                        'b' => $subTickB,
                                        'alpha' => $subTickalpha
                                    ]
                                );
                            }
                            if (!$removeYAxiValues) {
                                $this->drawLine(
                                    $xPos - $outerTickWidth,
                                    $yPos,
                                    $xPos + $InnerTickWidth,
                                    $yPos,
                                    ['r' => $tickR, 'g' => $tickG, 'b' => $tickB, 'alpha' => $tickalpha]
                                );
                                $bounds = $this->drawText(
                                    $xPos - $outerTickWidth - 2,
                                    $yPos,
                                    $value,
                                    ['align' => Constant::TEXT_ALIGN_MIDDLERIGHT]
                                );
                                $txtLeft = $xPos - $outerTickWidth - 2 - ($bounds[1]['x'] - $bounds[0]['x']);
                                $minLeft = min($minLeft, $txtLeft);
                            }
                            $lastY = $yPos;
                        }
                        if (isset($parameters['name'])) {
                            $xPos = $minLeft - 2;
                            $yPos = $this->graphAreaY1 + ($this->graphAreaY2 - $this->graphAreaY1) / 2;
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos,
                                $parameters['name'],
                                ['align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE, 'Angle' => 90]
                            );
                            $minLeft = $bounds[2]['x'];
                            $this->dataSet->data['graphArea']['x1'] = $minLeft;
                        }
                        $axisPos['l'] = $minLeft - $scaleSpacing;
                    } elseif ($parameters['position'] == Constant::AXIS_POSITION_RIGHT) {
                        if ($floating) {
                            $floatingOffset = $xMargin;
                            $this->drawLine(
                                $axisPos['r'],
                                $this->graphAreaY1 + $parameters['margin'],
                                $axisPos['r'],
                                $this->graphAreaY2 - $parameters['margin'],
                                ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                            );
                        } else {
                            $floatingOffset = 0;
                            $this->drawLine(
                                $axisPos['r'],
                                $this->graphAreaY1,
                                $axisPos['r'],
                                $this->graphAreaY2,
                                ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                $axisPos['r'],
                                $this->graphAreaY1 + $parameters['margin'],
                                $axisPos['r'],
                                $this->graphAreaY1 - ($arrowSize * 2),
                                [
                                    'fillR' => $axisR,
                                    'fillG' => $axisG,
                                    'fillB' => $axisB,
                                    'size' => $arrowSize
                                ]
                            );
                        }
                        $height = ($this->graphAreaY2 - $this->graphAreaY1) - $parameters['margin'] * 2;
                        $step = $height / $parameters['rows'];
                        $subTicksSize = $step / 2;
                        $maxLeft = $axisPos['r'];
                        $lastY = null;
                        for ($i = 0; $i <= $parameters['rows']; $i++) {
                            $yPos = $this->graphAreaY2 - $parameters['margin'] - $step * $i;
                            $xPos = $axisPos['r'];
                            $value = $this->scaleFormat(
                                $parameters['scaleMin'] + $parameters['rowHeight'] * $i,
                                $parameters['display'],
                                $parameters['format'],
                                $parameters['unit']
                            );
                            if ($i % 2 == 1) {
                                $bGColor = [
                                    'r' => $backgroundR1,
                                    'g' => $backgroundG1,
                                    'b' => $backgroundB1,
                                    'alpha' => $backgroundalpha1
                                ];
                            } else {
                                $bGColor = [
                                    'r' => $backgroundR2,
                                    'g' => $backgroundG2,
                                    'b' => $backgroundB2,
                                    'alpha' => $backgroundalpha2
                                ];
                            }
                            if ($lastY != null && $CycleBackground && ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                    $this->graphAreaX1 + $floatingOffset,
                                    $lastY,
                                    $this->graphAreaX2 - $floatingOffset,
                                    $yPos,
                                    $bGColor
                                );
                            }
                            if ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                    $this->graphAreaX1 + $floatingOffset,
                                    $yPos,
                                    $this->graphAreaX2 - $floatingOffset,
                                    $yPos,
                                    [
                                        'r' => $gridR,
                                        'g' => $gridG,
                                        'b' => $gridB,
                                        'alpha' => $gridalpha,
                                        'ticks' => $gridTicks
                                    ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters['rows']) {
                                $this->drawLine(
                                    $xPos - $outerSubTickWidth,
                                    $yPos - $subTicksSize,
                                    $xPos + $InnerSubTickWidth,
                                    $yPos - $subTicksSize,
                                    [
                                        'r' => $subTickR,
                                        'g' => $subTickG,
                                        'b' => $subTickB,
                                        'alpha' => $subTickalpha
                                    ]
                                );
                            }
                            $this->drawLine(
                                $xPos - $InnerTickWidth,
                                $yPos,
                                $xPos + $outerTickWidth,
                                $yPos,
                                ['r' => $tickR, 'g' => $tickG, 'b' => $tickB, 'alpha' => $tickalpha]
                            );
                            $bounds = $this->drawText(
                                $xPos + $outerTickWidth + 2,
                                $yPos,
                                $value,
                                ['align' => Constant::TEXT_ALIGN_MIDDLELEFT]
                            );
                            $txtLeft = $xPos + $outerTickWidth + 2 + ($bounds[1]['x'] - $bounds[0]['x']);
                            $maxLeft = max($maxLeft, $txtLeft);
                            $lastY = $yPos;
                        }
                        if (isset($parameters['name'])) {
                            $xPos = $maxLeft + 6;
                            $yPos = $this->graphAreaY1 + ($this->graphAreaY2 - $this->graphAreaY1) / 2;
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos,
                                $parameters['name'],
                                ['align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE, 'Angle' => 270]
                            );
                            $maxLeft = $bounds[2]['x'];
                            $this->dataSet->data['graphArea']['x2'] = $maxLeft + $this->fontSize;
                        }
                        $axisPos['r'] = $maxLeft + $scaleSpacing;
                    }
                } elseif ($pos == Constant::SCALE_POS_TOPBOTTOM) {
                    if ($parameters['position'] == Constant::AXIS_POSITION_TOP) {
                        if ($floating) {
                            $floatingOffset = $xMargin;
                            $this->drawLine(
                                $this->graphAreaX1 + $parameters['margin'],
                                $axisPos['T'],
                                $this->graphAreaX2 - $parameters['margin'],
                                $axisPos['T'],
                                ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                            );
                        } else {
                            $floatingOffset = 0;
                            $this->drawLine(
                                $this->graphAreaX1,
                                $axisPos['T'],
                                $this->graphAreaX2,
                                $axisPos['T'],
                                ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                $this->graphAreaX2 - $parameters['margin'],
                                $axisPos['T'],
                                $this->graphAreaX2 + ($arrowSize * 2),
                                $axisPos['T'],
                                [
                                    'fillR' => $axisR,
                                    'fillG' => $axisG,
                                    'fillB' => $axisB,
                                    'size' => $arrowSize
                                ]
                            );
                        }
                        $width = ($this->graphAreaX2 - $this->graphAreaX1) - $parameters['margin'] * 2;
                        $step = $width / $parameters['rows'];
                        $subTicksSize = $step / 2;
                        $minTop = $axisPos['T'];
                        $lastX = null;
                        for ($i = 0; $i <= $parameters['rows']; $i++) {
                            $xPos = $this->graphAreaX1 + $parameters['margin'] + $step * $i;
                            $yPos = $axisPos['T'];
                            $value = $this->scaleFormat(
                                $parameters['scaleMin'] + $parameters['rowHeight'] * $i,
                                $parameters['display'],
                                $parameters['format'],
                                $parameters['unit']
                            );
                            if ($i % 2 == 1) {
                                $bGColor = [
                                    'r' => $backgroundR1,
                                    'g' => $backgroundG1,
                                    'b' => $backgroundB1,
                                    'alpha' => $backgroundalpha1
                                ];
                            } else {
                                $bGColor = [
                                    'r' => $backgroundR2,
                                    'g' => $backgroundG2,
                                    'b' => $backgroundB2,
                                    'alpha' => $backgroundalpha2
                                ];
                            }
                            if ($lastX != null && $CycleBackground && ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                    $lastX,
                                    $this->graphAreaY1 + $floatingOffset,
                                    $xPos,
                                    $this->graphAreaY2 - $floatingOffset,
                                    $bGColor
                                );
                            }
                            if ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                    $xPos,
                                    $this->graphAreaY1 + $floatingOffset,
                                    $xPos,
                                    $this->graphAreaY2 - $floatingOffset,
                                    [
                                        'r' => $gridR,
                                        'g' => $gridG,
                                        'b' => $gridB,
                                        'alpha' => $gridalpha,
                                        'ticks' => $gridTicks
                                    ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters['rows']) {
                                $this->drawLine(
                                    $xPos + $subTicksSize,
                                    $yPos - $outerSubTickWidth,
                                    $xPos + $subTicksSize,
                                    $yPos + $InnerSubTickWidth,
                                    [
                                        'r' => $subTickR,
                                        'g' => $subTickG,
                                        'b' => $subTickB,
                                        'alpha' => $subTickalpha
                                    ]
                                );
                            }
                            $this->drawLine(
                                $xPos,
                                $yPos - $outerTickWidth,
                                $xPos,
                                $yPos + $InnerTickWidth,
                                ['r' => $tickR, 'g' => $tickG, 'b' => $tickB, 'alpha' => $tickalpha]
                            );
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos - $outerTickWidth - 2,
                                $value,
                                ['align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE]
                            );
                            $txtHeight = $yPos - $outerTickWidth - 2 - ($bounds[1]['y'] - $bounds[2]['y']);
                            $minTop = min($minTop, $txtHeight);
                            $lastX = $xPos;
                        }
                        if (isset($parameters['name'])) {
                            $yPos = $minTop - 2;
                            $xPos = $this->graphAreaX1 + ($this->graphAreaX2 - $this->graphAreaX1) / 2;
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos,
                                $parameters['name'],
                                ['align' => Constant::TEXT_ALIGN_BOTTOMMIDDLE]
                            );
                            $minTop = $bounds[2]['y'];
                            $this->dataSet->data['graphArea']['y1'] = $minTop;
                        }
                        $axisPos['T'] = $minTop - $scaleSpacing;
                    } elseif ($parameters['position'] == Constant::AXIS_POSITION_BOTTOM) {
                        if ($floating) {
                            $floatingOffset = $xMargin;
                            $this->drawLine(
                                $this->graphAreaX1 + $parameters['margin'],
                                $axisPos['b'],
                                $this->graphAreaX2 - $parameters['margin'],
                                $axisPos['b'],
                                ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                            );
                        } else {
                            $floatingOffset = 0;
                            $this->drawLine(
                                $this->graphAreaX1,
                                $axisPos['b'],
                                $this->graphAreaX2,
                                $axisPos['b'],
                                ['r' => $axisR, 'g' => $axisG, 'b' => $axisB, 'alpha' => $axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                $this->graphAreaX2 - $parameters['margin'],
                                $axisPos['b'],
                                $this->graphAreaX2 + ($arrowSize * 2),
                                $axisPos['b'],
                                [
                                    'fillR' => $axisR,
                                    'fillG' => $axisG,
                                    'fillB' => $axisB,
                                    'size' => $arrowSize
                                ]
                            );
                        }
                        $width = ($this->graphAreaX2 - $this->graphAreaX1) - $parameters['margin'] * 2;
                        $step = $width / $parameters['rows'];
                        $subTicksSize = $step / 2;
                        $maxBottom = $axisPos['b'];
                        $lastX = null;
                        for ($i = 0; $i <= $parameters['rows']; $i++) {
                            $xPos = $this->graphAreaX1 + $parameters['margin'] + $step * $i;
                            $yPos = $axisPos['b'];
                            $value = $this->scaleFormat(
                                $parameters['scaleMin'] + $parameters['rowHeight'] * $i,
                                $parameters['display'],
                                $parameters['format'],
                                $parameters['unit']
                            );
                            if ($i % 2 == 1) {
                                $bGColor = [
                                    'r' => $backgroundR1,
                                    'g' => $backgroundG1,
                                    'b' => $backgroundB1,
                                    'alpha' => $backgroundalpha1
                                ];
                            } else {
                                $bGColor = [
                                    'r' => $backgroundR2,
                                    'g' => $backgroundG2,
                                    'b' => $backgroundB2,
                                    'alpha' => $backgroundalpha2
                                ];
                            }
                            if ($lastX != null && $CycleBackground && ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                    $lastX,
                                    $this->graphAreaY1 + $floatingOffset,
                                    $xPos,
                                    $this->graphAreaY2 - $floatingOffset,
                                    $bGColor
                                );
                            }
                            if ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                    $xPos,
                                    $this->graphAreaY1 + $floatingOffset,
                                    $xPos,
                                    $this->graphAreaY2 - $floatingOffset,
                                    [
                                        'r' => $gridR,
                                        'g' => $gridG,
                                        'b' => $gridB,
                                        'alpha' => $gridalpha,
                                        'ticks' => $gridTicks
                                    ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters['rows']) {
                                $this->drawLine(
                                    $xPos + $subTicksSize,
                                    $yPos - $outerSubTickWidth,
                                    $xPos + $subTicksSize,
                                    $yPos + $InnerSubTickWidth,
                                    [
                                        'r' => $subTickR,
                                        'g' => $subTickG,
                                        'b' => $subTickB,
                                        'alpha' => $subTickalpha
                                    ]
                                );
                            }
                            $this->drawLine(
                                $xPos,
                                $yPos - $outerTickWidth,
                                $xPos,
                                $yPos + $InnerTickWidth,
                                ['r' => $tickR, 'g' => $tickG, 'b' => $tickB, 'alpha' => $tickalpha]
                            );
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos + $outerTickWidth + 2,
                                $value,
                                ['align' => Constant::TEXT_ALIGN_TOPMIDDLE]
                            );
                            $txtHeight = $yPos + $outerTickWidth + 2 + ($bounds[1]['y'] - $bounds[2]['y']);
                            $maxBottom = max($maxBottom, $txtHeight);
                            $lastX = $xPos;
                        }
                        if (isset($parameters['name'])) {
                            $yPos = $maxBottom + 2;
                            $xPos = $this->graphAreaX1 + ($this->graphAreaX2 - $this->graphAreaX1) / 2;
                            $bounds = $this->drawText(
                                $xPos,
                                $yPos,
                                $parameters['name'],
                                ['align' => Constant::TEXT_ALIGN_TOPMIDDLE]
                            );
                            $maxBottom = $bounds[0]['y'];
                            $this->dataSet->data['graphArea']['y2'] = $maxBottom + $this->fontSize;
                        }
                        $axisPos['b'] = $maxBottom + $scaleSpacing;
                    }
                }
            }
        }
    }

    /**
     * Draw an X threshold
     *
     * @param mixed   $value
     * @param boolean $format
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

    /**
     * Draw the derivative chart associated to the data series
     *
     * @param array $format
     */
    public function drawDerivative(array $format = []) {
        $offset = isset($format['offset']) ? $format['offset'] : 10;
        $serieSpacing = isset($format['serieSpacing']) ? $format['serieSpacing'] : 3;
        $derivativeHeight = isset($format['derivativeHeight']) ? $format['derivativeHeight'] : 4;
        $shadedSlopeBox = isset($format['shadedSlopeBox']) ? $format['shadedSlopeBox'] : false;
        $drawBackground = isset($format['drawBackground']) ? $format['drawBackground'] : true;
        $backgroundR = isset($format['backgroundR']) ? $format['backgroundR'] : 255;
        $backgroundG = isset($format['backgroundG']) ? $format['backgroundG'] : 255;
        $backgroundB = isset($format['backgroundB']) ? $format['backgroundB'] : 255;
        $backgroundalpha = isset($format['backgroundalpha']) ? $format['backgroundalpha'] : 20;
        $drawBorder = isset($format['drawBorder']) ? $format['drawBorder'] : true;
        $borderR = isset($format['borderR']) ? $format['borderR'] : 0;
        $borderG = isset($format['borderG']) ? $format['borderG'] : 0;
        $borderB = isset($format['borderB']) ? $format['borderB'] : 0;
        $borderalpha = isset($format['borderalpha']) ? $format['borderalpha'] : 100;
        $caption = isset($format['caption']) ? $format['caption'] : true;
        $captionHeight = isset($format['captionHeight']) ? $format['captionHeight'] : 10;
        $captionWidth = isset($format['captionWidth']) ? $format['captionWidth'] : 20;
        $captionMargin = isset($format['captionMargin']) ? $format['captionMargin'] : 4;
        $captionLine = isset($format['captionLine']) ? $format['captionLine'] : false;
        $captionBox = isset($format['captionBox']) ? $format['captionBox'] : false;
        $captionborderR = isset($format['captionborderR']) ? $format['captionborderR'] : 0;
        $captionborderG = isset($format['captionborderG']) ? $format['captionborderG'] : 0;
        $captionborderB = isset($format['captionborderB']) ? $format['captionborderB'] : 0;
        $captionFillR = isset($format['captionFillR']) ? $format['captionFillR'] : 255;
        $captionFillG = isset($format['captionFillG']) ? $format['captionFillG'] : 255;
        $captionFillB = isset($format['captionFillB']) ? $format['captionFillB'] : 255;
        $captionFillalpha = isset($format['captionFillalpha']) ? $format['captionFillalpha'] : 80;
        $positiveSlopeStartR = isset($format['positiveSlopeStartR']) ? $format['positiveSlopeStartR'] : 184;
        $positiveSlopeStartG = isset($format['positiveSlopeStartG']) ? $format['positiveSlopeStartG'] : 234;
        $positiveSlopeStartB = isset($format['positiveSlopeStartB']) ? $format['positiveSlopeStartB'] : 88;
        $positiveSlopeEndR = isset($format['positiveSlopeStartR']) ? $format['positiveSlopeStartR'] : 239;
        $positiveSlopeEndG = isset($format['positiveSlopeStartG']) ? $format['positiveSlopeStartG'] : 31;
        $positiveSlopeEndB = isset($format['positiveSlopeStartB']) ? $format['positiveSlopeStartB'] : 36;
        $NegativeSlopeStartR = isset($format['negativeSlopeStartR']) ? $format['negativeSlopeStartR'] : 184;
        $NegativeSlopeStartG = isset($format['negativeSlopeStartG']) ? $format['negativeSlopeStartG'] : 234;
        $NegativeSlopeStartB = isset($format['negativeSlopeStartB']) ? $format['negativeSlopeStartB'] : 88;
        $NegativeSlopeEndR = isset($format['negativeSlopeStartR']) ? $format['negativeSlopeStartR'] : 67;
        $NegativeSlopeEndG = isset($format['negativeSlopeStartG']) ? $format['negativeSlopeStartG'] : 124;
        $NegativeSlopeEndB = isset($format['negativeSlopeStartB']) ? $format['negativeSlopeStartB'] : 227;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
            $yPos = $this->dataSet->data['graphArea']['y2'] + $offset;
        } else {
            $xPos = $this->dataSet->data['graphArea']['x2'] + $offset;
        }
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $alpha = $serie['color']['alpha'];
                $ticks = $serie['ticks'];
                $weight = $serie['weight'];
                $axisId = $serie['axis'];
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisId' => $serie['axis']]
                );
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($caption) {
                        if ($captionLine) {
                            $startX = floor($this->graphAreaX1 - $captionWidth + $xMargin - $captionMargin);
                            $endX = floor($this->graphAreaX1 - $captionMargin + $xMargin);
                            $captionSettings = [
                                'r' => $r,
                                'g' => $g,
                                'b' => $b,
                                'alpha' => $alpha,
                                'ticks' => $ticks,
                                'weight' => $weight
                            ];
                            if ($captionBox) {
                                $this->drawFilledRectangle(
                                    $startX,
                                    $yPos,
                                    $endX,
                                    $yPos + $captionHeight,
                                    [
                                        'r' => $captionFillR,
                                        'g' => $captionFillG,
                                        'b' => $captionFillB,
                                        'borderR' => $captionborderR,
                                        'borderG' => $captionborderG,
                                        'borderB' => $captionborderB,
                                        'alpha' => $captionFillalpha
                                    ]
                                );
                            }
                            $this->drawLine(
                                $startX + 2,
                                $yPos + ($captionHeight / 2),
                                $endX - 2,
                                $yPos + ($captionHeight / 2),
                                $captionSettings
                            );
                        } else {
                            $this->drawFilledRectangle(
                                $this->graphAreaX1 - $captionWidth + $xMargin - $captionMargin,
                                $yPos,
                                $this->graphAreaX1 - $captionMargin + $xMargin,
                                $yPos + $captionHeight,
                                [
                                    'r' => $r,
                                    'g' => $g,
                                    'b' => $b,
                                    'borderR' => $captionborderR,
                                    'borderG' => $captionborderG,
                                    'borderB' => $captionborderB
                                ]
                            );
                        }
                    }
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                    } else {
                        $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    }
                    $x = $this->graphAreaX1 + $xMargin;
                    $topY = $yPos + ($captionHeight / 2) - ($derivativeHeight / 2);
                    $bottomY = $yPos + ($captionHeight / 2) + ($derivativeHeight / 2);
                    $startX = floor($this->graphAreaX1 + $xMargin);
                    $endX = floor($this->graphAreaX2 - $xMargin);
                    if ($drawBackground) {
                        $this->drawFilledRectangle(
                            $startX - 1,
                            $topY - 1,
                            $endX + 1,
                            $bottomY + 1,
                            [
                                'r' => $backgroundR,
                                'g' => $backgroundG,
                                'b' => $backgroundB,
                                'alpha' => $backgroundalpha
                            ]
                        );
                    }
                    if ($drawBorder) {
                        $this->drawRectangle(
                            $startX - 1,
                            $topY - 1,
                            $endX + 1,
                            $bottomY + 1,
                            [
                                'r' => $borderR,
                                'g' => $borderG,
                                'b' => $borderB,
                                'alpha' => $borderalpha
                            ]
                        );
                    }
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $restoreShadow = $this->shadow;
                    $this->shadow = false;
                    /* Determine the Max slope index */
                    $lastX = null;
                    $lastY = null;
                    $minSlope = 0;
                    $maxSlope = 1;
                    foreach ($posArray as $key => $y) {
                        if ($y != Constant::VOID && $lastX != null) {
                            $slope = ($lastY - $y);
                            if ($slope > $maxSlope) {
                                $maxSlope = $slope;
                            }
                            if ($slope < $minSlope) {
                                $minSlope = $slope;
                            }
                        }
                        if ($y == Constant::VOID) {
                            $lastX = null;
                            $lastY = null;
                        } else {
                            $lastX = $x;
                            $lastY = $y;
                        }
                    }
                    $lastX = null;
                    $lastY = null;
                    $lastColor = null;
                    foreach ($posArray as $key => $y) {
                        if ($y != Constant::VOID && $lastY != null) {
                            $slope = ($lastY - $y);
                            if ($slope >= 0) {
                                $slopeIndex = (100 / $maxSlope) * $slope;
                                $r = (($positiveSlopeEndR - $positiveSlopeStartR) / 100) * $slopeIndex + $positiveSlopeStartR;
                                $g = (($positiveSlopeEndG - $positiveSlopeStartG) / 100) * $slopeIndex + $positiveSlopeStartG;
                                $b = (($positiveSlopeEndB - $positiveSlopeStartB) / 100) * $slopeIndex + $positiveSlopeStartB;
                            } elseif ($slope < 0) {
                                $slopeIndex = (100 / abs($minSlope)) * abs($slope);
                                $r = (($NegativeSlopeEndR - $NegativeSlopeStartR) / 100) * $slopeIndex + $NegativeSlopeStartR;
                                $g = (($NegativeSlopeEndG - $NegativeSlopeStartG) / 100) * $slopeIndex + $NegativeSlopeStartG;
                                $b = (($NegativeSlopeEndB - $NegativeSlopeStartB) / 100) * $slopeIndex + $NegativeSlopeStartB;
                            }
                            $color = ['r' => $r, 'g' => $g, 'b' => $b];
                            if ($shadedSlopeBox && $lastColor != null) {// && $slope != 0
                                $gradientSettings = [
                                    'StartR' => $lastColor['r'],
                                    'StartG' => $lastColor['g'],
                                    'StartB' => $lastColor['b'],
                                    'endR' => $r,
                                    'endG' => $g,
                                    'endB' => $b
                                ];
                                $this->drawGradientArea(
                                    $lastX,
                                    $topY,
                                    $x,
                                    $bottomY,
                                    Constant::DIRECTION_HORIZONTAL,
                                    $gradientSettings
                                );
                            } elseif (!$shadedSlopeBox || $lastColor == null) { // || $slope == 0
                                $this->drawFilledRectangle(
                                    floor($lastX),
                                    $topY,
                                    floor($x),
                                    $bottomY,
                                    $color
                                );
                            }
                            $lastColor = $color;
                        }
                        if ($y == Constant::VOID) {
                            $lastY = null;
                        } else {
                            $lastX = $x;
                            $lastY = $y;
                        }
                        $x = $x + $xStep;
                    }
                    $yPos = $yPos + $captionHeight + $serieSpacing;
                } else {
                    if ($caption) {
                        $startY = floor($this->graphAreaY1 - $captionWidth + $xMargin - $captionMargin);
                        $endY = floor($this->graphAreaY1 - $captionMargin + $xMargin);
                        if ($captionLine) {
                            $captionSettings = [
                                'r' => $r,
                                'g' => $g,
                                'b' => $b,
                                'alpha' => $alpha,
                                'ticks' => $ticks,
                                'weight' => $weight
                            ];
                            if ($captionBox) {
                                $this->drawFilledRectangle(
                                    $xPos,
                                    $startY,
                                    $xPos + $captionHeight,
                                    $endY,
                                    [
                                        'r' => $captionFillR,
                                        'g' => $captionFillG,
                                        'b' => $captionFillB,
                                        'borderR' => $captionborderR,
                                        'borderG' => $captionborderG,
                                        'borderB' => $captionborderB,
                                        'alpha' => $captionFillalpha
                                    ]
                                );
                            }
                            $this->drawLine(
                                $xPos + ($captionHeight / 2),
                                $startY + 2,
                                $xPos + ($captionHeight / 2),
                                $endY - 2,
                                $captionSettings
                            );
                        } else {
                            $this->drawFilledRectangle(
                                $xPos,
                                $startY,
                                $xPos + $captionHeight,
                                $endY,
                                [
                                    'r' => $r,
                                    'g' => $g,
                                    'b' => $b,
                                    'borderR' => $captionborderR,
                                    'borderG' => $captionborderG,
                                    'borderB' => $captionborderB
                                ]
                            );
                        }
                    }
                    if ($xDivs == 0) {
                        $xStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                    } else {
                        $xStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    }
                    $y = $this->graphAreaY1 + $xMargin;
                    $topX = $xPos + ($captionHeight / 2) - ($derivativeHeight / 2);
                    $bottomX = $xPos + ($captionHeight / 2) + ($derivativeHeight / 2);
                    $startY = floor($this->graphAreaY1 + $xMargin);
                    $endY = floor($this->graphAreaY2 - $xMargin);
                    if ($drawBackground) {
                        $this->drawFilledRectangle(
                            $topX - 1,
                            $startY - 1,
                            $bottomX + 1,
                            $endY + 1,
                            [
                                'r' => $backgroundR,
                                'g' => $backgroundG,
                                'b' => $backgroundB,
                                'alpha' => $backgroundalpha
                            ]
                        );
                    }
                    if ($drawBorder) {
                        $this->drawRectangle(
                            $topX - 1,
                            $startY - 1,
                            $bottomX + 1,
                            $endY + 1,
                            [
                                'r' => $borderR,
                                'g' => $borderG,
                                'b' => $borderB,
                                'alpha' => $borderalpha
                            ]
                        );
                    }
                    if (!is_array($posArray)) {
                        $value = $posArray;
                        $posArray = [];
                        $posArray[0] = $value;
                    }
                    $restoreShadow = $this->shadow;
                    $this->shadow = false;
                    /* Determine the Max slope index */
                    $lastX = null;
                    $lastY = null;
                    $minSlope = 0;
                    $maxSlope = 1;
                    foreach ($posArray as $key => $x) {
                        if ($x != Constant::VOID && $lastX != null) {
                            $slope = ($x - $lastX);
                            if ($slope > $maxSlope) {
                                $maxSlope = $slope;
                            }
                            if ($slope < $minSlope) {
                                $minSlope = $slope;
                            }
                        }
                        if ($x == Constant::VOID) {
                            $lastX = null;
                        } else {
                            $lastX = $x;
                        }
                    }
                    $lastX = null;
                    $lastY = null;
                    $lastColor = null;
                    foreach ($posArray as $key => $x) {
                        if ($x != Constant::VOID && $lastX != null) {
                            $slope = ($x - $lastX);
                            if ($slope >= 0) {
                                $slopeIndex = (100 / $maxSlope) * $slope;
                                $r = (($positiveSlopeEndR - $positiveSlopeStartR) / 100) * $slopeIndex + $positiveSlopeStartR;
                                $g = (($positiveSlopeEndG - $positiveSlopeStartG) / 100) * $slopeIndex + $positiveSlopeStartG;
                                $b = (($positiveSlopeEndB - $positiveSlopeStartB) / 100) * $slopeIndex + $positiveSlopeStartB;
                            } elseif ($slope < 0) {
                                $slopeIndex = (100 / abs($minSlope)) * abs($slope);
                                $r = (($NegativeSlopeEndR - $NegativeSlopeStartR) / 100) * $slopeIndex + $NegativeSlopeStartR;
                                $g = (($NegativeSlopeEndG - $NegativeSlopeStartG) / 100) * $slopeIndex + $NegativeSlopeStartG;
                                $b = (($NegativeSlopeEndB - $NegativeSlopeStartB) / 100) * $slopeIndex + $NegativeSlopeStartB;
                            }
                            $color = ['r' => $r, 'g' => $g, 'b' => $b];
                            if ($shadedSlopeBox && $lastColor != null) {
                                $gradientSettings = [
                                    'StartR' => $lastColor['r'],
                                    'StartG' => $lastColor['g'],
                                    'StartB' => $lastColor['b'],
                                    'endR' => $r,
                                    'endG' => $g,
                                    'endB' => $b
                                ];
                                $this->drawGradientArea(
                                    $topX,
                                    $lastY,
                                    $bottomX,
                                    $y,
                                    Constant::DIRECTION_VERTICAL,
                                    $gradientSettings
                                );
                            } elseif (!$shadedSlopeBox || $lastColor == null) {
                                $this->drawFilledRectangle(
                                    $topX,
                                    floor($lastY),
                                    $bottomX,
                                    floor($y),
                                    $color
                                );
                            }
                            $lastColor = $color;
                        }
                        if ($x == Constant::VOID) {
                            $lastX = null;
                        } else {
                            $lastX = $x;
                            $lastY = $y;
                        }
                        $y = $y + $xStep;
                    }
                    $xPos = $xPos + $captionHeight + $serieSpacing;
                }
                $this->shadow = $restoreShadow;
            }
        }
    }

    /**
     * Draw the line of best fit
     *
     * @param array $format
     */
    public function drawBestFit(array $format = []) {
        $overrideTicks = isset($format['ticks']) ? $format['ticks'] : null;
        $overrideR = isset($format['r']) ? $format['r'] : Constant::VOID;
        $overrideG = isset($format['g']) ? $format['g'] : Constant::VOID;
        $overrideB = isset($format['b']) ? $format['b'] : Constant::VOID;
        $overridealpha = isset($format['alpha']) ? $format['alpha'] : Constant::VOID;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                if ($overrideR != Constant::VOID && $overrideG != Constant::VOID && $overrideB != Constant::VOID) {
                    $r = $overrideR;
                    $g = $overrideG;
                    $b = $overrideB;
                } else {
                    $r = $serie['color']['r'];
                    $g = $serie['color']['g'];
                    $b = $serie['color']['b'];
                }
                if ($overrideTicks == null) {
                    $ticks = $serie['ticks'];
                } else {
                    $ticks = $overrideTicks;
                }
                if ($overridealpha == Constant::VOID) {
                    $alpha = $serie['color']['alpha'];
                } else {
                    $alpha = $overridealpha;
                }
                $color = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks];
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisId' => $serie['axis']]
                );
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
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
                    $sxy = 0;
                    $sx = 0;
                    $sy = 0;
                    $sxx = 0;
                    foreach ($posArray as $key => $y) {
                        if ($y != Constant::VOID) {
                            $sxy = $sxy + $x * $y;
                            $sx = $sx + $x;
                            $sy = $sy + $y;
                            $sxx = $sxx + $x * $x;
                        }
                        $x = $x + $xStep;
                    }
                    $n = count($this->dataSet->stripVOID($posArray)); //$n = count($posArray);
                    $m = (($n * $sxy) - ($sx * $sy)) / (($n * $sxx) - ($sx * $sx));
                    $b = (($sy) - ($m * $sx)) / ($n);
                    $x1 = $this->graphAreaX1 + $xMargin;
                    $y1 = $m * $x1 + $b;
                    $x2 = $this->graphAreaX2 - $xMargin;
                    $y2 = $m * $x2 + $b;
                    if ($y1 < $this->graphAreaY1) {
                        $x1 = $x1 + ($this->graphAreaY1 - $y1);
                        $y1 = $this->graphAreaY1;
                    }
                    if ($y1 > $this->graphAreaY2) {
                        $x1 = $x1 + ($y1 - $this->graphAreaY2);
                        $y1 = $this->graphAreaY2;
                    }
                    if ($y2 < $this->graphAreaY1) {
                        $x2 = $x2 - ($this->graphAreaY1 - $y2);
                        $y2 = $this->graphAreaY1;
                    }
                    if ($y2 > $this->graphAreaY2) {
                        $x2 = $x2 - ($y2 - $this->graphAreaY2);
                        $y2 = $this->graphAreaY2;
                    }
                    $this->drawLine($x1, $y1, $x2, $y2, $color);
                } else {
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
                    $sxy = 0;
                    $sx = 0;
                    $sy = 0;
                    $sxx = 0;
                    foreach ($posArray as $key => $x) {
                        if ($x != Constant::VOID) {
                            $sxy = $sxy + $x * $y;
                            $sx = $sx + $y;
                            $sy = $sy + $x;
                            $sxx = $sxx + $y * $y;
                        }
                        $y = $y + $yStep;
                    }
                    $n = count($this->dataSet->stripVOID($posArray)); //$n = count($posArray);
                    $m = (($n * $sxy) - ($sx * $sy)) / (($n * $sxx) - ($sx * $sx));
                    $b = (($sy) - ($m * $sx)) / ($n);
                    $y1 = $this->graphAreaY1 + $xMargin;
                    $x1 = $m * $y1 + $b;
                    $y2 = $this->graphAreaY2 - $xMargin;
                    $x2 = $m * $y2 + $b;
                    if ($x1 < $this->graphAreaX1) {
                        $y1 = $y1 + ($this->graphAreaX1 - $x1);
                        $x1 = $this->graphAreaX1;
                    }
                    if ($x1 > $this->graphAreaX2) {
                        $y1 = $y1 + ($x1 - $this->graphAreaX2);
                        $x1 = $this->graphAreaX2;
                    }
                    if ($x2 < $this->graphAreaX1) {
                        $y2 = $y2 - ($this->graphAreaY1 - $x2);
                        $x2 = $this->graphAreaX1;
                    }
                    if ($x2 > $this->graphAreaX2) {
                        $y2 = $y2 - ($x2 - $this->graphAreaX2);
                        $x2 = $this->graphAreaX2;
                    }
                    $this->drawLine($x1, $y1, $x2, $y2, $color);
                }
            }
        }
    }


    /**
     * Draw a basic shape
     *
     * @param int       $x
     * @param int       $y
     * @param int       $shape
     * @param int       $plotSize
     * @param int       $plotBorder
     * @param int       $borderSize
     * @param int       $r
     * @param int       $g
     * @param int       $b
     * @param int|float $alpha
     * @param int       $borderR
     * @param int       $borderG
     * @param int       $borderB
     * @param int|float $borderalpha
     */
    public function drawShape(
        $x,
        $y,
        $shape,
        $plotSize,
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
    ) {
        if ($shape == Constant::SERIE_SHAPE_FILLEDCIRCLE) {
            if ($plotBorder) {
                $this->drawFilledCircle(
                    $x,
                    $y,
                    $plotSize + $borderSize,
                    ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $borderalpha]
                );
            }
            $this->drawFilledCircle(
                $x,
                $y,
                $plotSize,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
        } elseif ($shape == Constant::SERIE_SHAPE_FILLEDSQUARE) {
            if ($plotBorder) {
                $this->drawFilledRectangle(
                    $x - $plotSize - $borderSize,
                    $y - $plotSize - $borderSize,
                    $x + $plotSize + $borderSize,
                    $y + $plotSize + $borderSize,
                    ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $borderalpha]
                );
            }
            $this->drawFilledRectangle(
                $x - $plotSize,
                $y - $plotSize,
                $x + $plotSize,
                $y + $plotSize,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
        } elseif ($shape == Constant::SERIE_SHAPE_FILLEDTRIANGLE) {
            if ($plotBorder) {
                $pos = [];
                $pos[] = $x;
                $pos[] = $y - $plotSize - $borderSize;
                $pos[] = $x - $plotSize - $borderSize;
                $pos[] = $y + $plotSize + $borderSize;
                $pos[] = $x + $plotSize + $borderSize;
                $pos[] = $y + $plotSize + $borderSize;
                $this->drawPolygon(
                    $pos,
                    ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $borderalpha]
                );
            }
            $pos = [];
            $pos[] = $x;
            $pos[] = $y - $plotSize;
            $pos[] = $x - $plotSize;
            $pos[] = $y + $plotSize;
            $pos[] = $x + $plotSize;
            $pos[] = $y + $plotSize;
            $this->drawPolygon($pos, ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]);
        } elseif ($shape == Constant::SERIE_SHAPE_TRIANGLE) {
            $this->drawLine(
                $x,
                $y - $plotSize,
                $x - $plotSize,
                $y + $plotSize,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
            $this->drawLine(
                $x - $plotSize,
                $y + $plotSize,
                $x + $plotSize,
                $y + $plotSize,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
            $this->drawLine(
                $x + $plotSize,
                $y + $plotSize,
                $x,
                $y - $plotSize,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
        } elseif ($shape == Constant::SERIE_SHAPE_SQUARE) {
            $this->drawRectangle(
                $x - $plotSize,
                $y - $plotSize,
                $x + $plotSize,
                $y + $plotSize,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
        } elseif ($shape == Constant::SERIE_SHAPE_CIRCLE) {
            $this->drawCircle(
                $x,
                $y,
                $plotSize,
                $plotSize,
                ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]
            );
        } elseif ($shape == Constant::SERIE_SHAPE_DIAMOND) {
            $pos = [];
            $pos[] = $x - $plotSize;
            $pos[] = $y;
            $pos[] = $x;
            $pos[] = $y - $plotSize;
            $pos[] = $x + $plotSize;
            $pos[] = $y;
            $pos[] = $x;
            $pos[] = $y + $plotSize;
            $this->drawPolygon(
                $pos,
                [
                    'noFill' => true,
                    'borderR' => $r,
                    'borderG' => $g,
                    'borderB' => $b,
                    'borderalpha' => $alpha
                ]
            );
        } elseif ($shape == Constant::SERIE_SHAPE_FILLEDDIAMOND) {
            if ($plotBorder) {
                $pos = [];
                $pos[] = $x - $plotSize - $borderSize;
                $pos[] = $y;
                $pos[] = $x;
                $pos[] = $y - $plotSize - $borderSize;
                $pos[] = $x + $plotSize + $borderSize;
                $pos[] = $y;
                $pos[] = $x;
                $pos[] = $y + $plotSize + $borderSize;
                $this->drawPolygon(
                    $pos,
                    ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $borderalpha]
                );
            }
            $pos = [];
            $pos[] = $x - $plotSize;
            $pos[] = $y;
            $pos[] = $x;
            $pos[] = $y - $plotSize;
            $pos[] = $x + $plotSize;
            $pos[] = $y;
            $pos[] = $x;
            $pos[] = $y + $plotSize;
            $this->drawPolygon($pos, ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]);
        }
    }



    /**
     * Return the surrounding box of text area
     *
     * @param int    $x
     * @param int    $y
     * @param string $fontName
     * @param int    $fontSize
     * @param int    $angle
     * @param int    $Text
     *
     * @return array
     */
    public function getTextBox($x, $y, $fontName, $fontSize, $angle, $Text) {
        $coords = imagettfbbox($fontSize, 0, $this->loadFont($fontName, 'fonts'), $Text);
        $a = deg2rad($angle);
        $ca = cos($a);
        $sa = sin($a);
        $realPos = [];
        for ($i = 0; $i < 7; $i += 2) {
            $realPos[$i / 2]['x'] = $x + round($coords[$i] * $ca + $coords[$i + 1] * $sa);
            $realPos[$i / 2]['y'] = $y + round($coords[$i + 1] * $ca - $coords[$i] * $sa);
        }
        $realPos[Constant::TEXT_ALIGN_BOTTOMLEFT]['x'] = $realPos[0]['x'];
        $realPos[Constant::TEXT_ALIGN_BOTTOMLEFT]['y'] = $realPos[0]['y'];
        $realPos[Constant::TEXT_ALIGN_BOTTOMRIGHT]['x'] = $realPos[1]['x'];
        $realPos[Constant::TEXT_ALIGN_BOTTOMRIGHT]['y'] = $realPos[1]['y'];
        $realPos[Constant::TEXT_ALIGN_TOPLEFT]['x'] = $realPos[3]['x'];
        $realPos[Constant::TEXT_ALIGN_TOPLEFT]['y'] = $realPos[3]['y'];
        $realPos[Constant::TEXT_ALIGN_TOPRIGHT]['x'] = $realPos[2]['x'];
        $realPos[Constant::TEXT_ALIGN_TOPRIGHT]['y'] = $realPos[2]['y'];
        $realPos[Constant::TEXT_ALIGN_BOTTOMMIDDLE]['x'] = ($realPos[1]['x'] - $realPos[0]['x']) / 2 + $realPos[0]['x'];
        $realPos[Constant::TEXT_ALIGN_BOTTOMMIDDLE]['y'] = ($realPos[0]['y'] - $realPos[1]['y']) / 2 + $realPos[1]['y'];
        $realPos[Constant::TEXT_ALIGN_TOPMIDDLE]['x'] = ($realPos[2]['x'] - $realPos[3]['x']) / 2 + $realPos[3]['x'];
        $realPos[Constant::TEXT_ALIGN_TOPMIDDLE]['y'] = ($realPos[3]['y'] - $realPos[2]['y']) / 2 + $realPos[2]['y'];
        $realPos[Constant::TEXT_ALIGN_MIDDLELEFT]['x'] = ($realPos[0]['x'] - $realPos[3]['x']) / 2 + $realPos[3]['x'];
        $realPos[Constant::TEXT_ALIGN_MIDDLELEFT]['y'] = ($realPos[0]['y'] - $realPos[3]['y']) / 2 + $realPos[3]['y'];
        $realPos[Constant::TEXT_ALIGN_MIDDLERIGHT]['x'] = ($realPos[1]['x'] - $realPos[2]['x']) / 2 + $realPos[2]['x'];
        $realPos[Constant::TEXT_ALIGN_MIDDLERIGHT]['y'] = ($realPos[1]['y'] - $realPos[2]['y']) / 2 + $realPos[2]['y'];
        $realPos[Constant::TEXT_ALIGN_MIDDLEMIDDLE]['x'] = ($realPos[1]['x'] - $realPos[3]['x']) / 2 + $realPos[3]['x'];
        $realPos[Constant::TEXT_ALIGN_MIDDLEMIDDLE]['y'] = ($realPos[0]['y'] - $realPos[2]['y']) / 2 + $realPos[2]['y'];
        return $realPos;
    }

    /**
     * Write labels
     *
     * @param string $seriesName
     * @param array  $indexes
     * @param array  $format
     */
    public function writeLabel($seriesName, $indexes, array $format = []) {
        $overrideTitle = isset($format['overrideTitle']) ? $format['overrideTitle'] : null;
        $forceLabels = isset($format['forceLabels']) ? $format['forceLabels'] : null;
        $drawPoint = isset($format['drawPoint']) ? $format['drawPoint'] : Constant::LABEL_POINT_BOX;
        $drawVerticalLine = isset($format['drawVerticalLine']) ? $format['drawVerticalLine'] : false;
        $verticalLineR = isset($format['verticalLineR']) ? $format['verticalLineR'] : 0;
        $verticalLineG = isset($format['verticalLineG']) ? $format['verticalLineG'] : 0;
        $verticalLineB = isset($format['verticalLineB']) ? $format['verticalLineB'] : 0;
        $verticalLineAlpha = isset($format['verticalLineAlpha']) ? $format['verticalLineAlpha'] : 40;
        $verticalLineTicks = isset($format['verticalLineTicks']) ? $format['verticalLineTicks'] : 2;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        if (!is_array($indexes)) {
            $index = $indexes;
            $indexes = [];
            $indexes[] = $index;
        }
        if (!is_array($seriesName)) {
            $serieName = $seriesName;
            $seriesName = [];
            $seriesName[] = $serieName;
        }
        if ($forceLabels != null && !is_array($forceLabels)) {
            $forceLabel = $forceLabels;
            $forceLabels = [];
            $forceLabels[] = $forceLabel;
        }
        foreach ($indexes as $key => $index) {
            $series = [];
            if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                if ($xDivs == 0) {
                    $xStep = ($this->graphAreaX2 - $this->graphAreaX1) / 4;
                } else {
                    $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                }
                $x = $this->graphAreaX1 + $xMargin + $index * $xStep;
                if ($drawVerticalLine) {
                    $this->drawLine(
                        $x,
                        $this->graphAreaY1 + $data['yMargin'],
                        $x,
                        $this->graphAreaY2 - $data['yMargin'],
                        [
                            'r' => $verticalLineR,
                            'g' => $verticalLineG,
                            'b' => $verticalLineB,
                            'alpha' => $verticalLineAlpha,
                            'ticks' => $verticalLineTicks
                        ]
                    );
                }
                $minY = $this->graphAreaY2;
                foreach ($seriesName as $serieName) {
                    if (isset($data['series'][$serieName]['data'][$index])) {
                        $axisID = $data['series'][$serieName]['axis'];
                        $xAxisMode = $data['xAxisDisplay'];
                        $xAxisFormat = $data['xAxisFormat'];
                        $xAxisUnit = $data['xAxisUnit'];
                        $axisMode = $data['axis'][$axisID]['display'];
                        $axisFormat = $data['axis'][$axisID]['format'];
                        $axisUnit = $data['axis'][$axisID]['unit'];
                        $xLabel = '';
                        if (isset($data['abscissa']) && isset($data['series'][$data['abscissa']]['data'][$index])
                        ) {
                            $xLabel = $this->scaleFormat(
                                $data['series'][$data['abscissa']]['data'][$index],
                                $xAxisMode,
                                $xAxisFormat,
                                $xAxisUnit
                            );
                        }
                        if ($overrideTitle != null) {
                            $description = $overrideTitle;
                        } elseif (count($seriesName) == 1) {
                            $description = $data['series'][$serieName]['description'] . ' - ' . $xLabel;
                        } elseif (isset($data['abscissa']) && isset($data['series'][$data['abscissa']]['data'][$index])
                        ) {
                            $description = $xLabel;
                        }
                        $serie = [
                            'r' => $data['series'][$serieName]['color']['r'],
                            'g' => $data['series'][$serieName]['color']['g'],
                            'b' => $data['series'][$serieName]['color']['b'],
                            'alpha' => $data['series'][$serieName]['color']['alpha']
                        ];
                        if (count($seriesName) == 1 && isset($data['series'][$serieName]['xOffset'])
                        ) {
                            $serieOffset = $data['series'][$serieName]['xOffset'];
                        } else {
                            $serieOffset = 0;
                        }
                        $value = $data['series'][$serieName]['data'][$index];
                        if ($value == Constant::VOID) {
                            $value = 'NaN';
                        }
                        if ($forceLabels != null) {
                            $Caption = isset($forceLabels[$key]) ? $forceLabels[$key] : 'Not set';
                        } else {
                            $Caption = $this->scaleFormat($value, $axisMode, $axisFormat, $axisUnit);
                        }
                        if ($this->lastChartLayout == Constant::CHART_LAST_LAYOUT_STACKED) {
                            if ($value >= 0) {
                                $lookFor = '+';
                            } else {
                                $lookFor = '-';
                            }
                            $value = 0;
                            $done = false;
                            foreach ($data['series'] as $name => $serieLookup) {
                                if ($serieLookup['isDrawable'] == true && $name != $data['abscissa'] && !$done
                                ) {
                                    if (isset($data['series'][$name]['data'][$index]) && $data['series'][$name]['data'][$index] != Constant::VOID
                                    ) {
                                        if ($data['series'][$name]['data'][$index] >= 0 && $lookFor == '+') {
                                            $value = $value + $data['series'][$name]['data'][$index];
                                        }
                                        if ($data['series'][$name]['data'][$index] < 0 && $lookFor == '-') {
                                            $value = $value - $data['series'][$name]['data'][$index];
                                        }
                                        if ($name == $serieName) {
                                            $done = true;
                                        }
                                    }
                                }
                            }
                        }
                        $x = floor($this->graphAreaX1 + $xMargin + $index * $xStep + $serieOffset);
                        $y = floor($this->scaleComputeY($value, ['axisID' => $axisID]));
                        if ($y < $minY) {
                            $minY = $y;
                        }
                        if ($drawPoint == Constant::LABEL_POINT_CIRCLE) {
                            $this->drawFilledCircle(
                                $x,
                                $y,
                                3,
                                [
                                    'r' => 255,
                                    'g' => 255,
                                    'b' => 255,
                                    'BorderR' => 0,
                                    'BorderG' => 0,
                                    'BorderB' => 0
                                ]
                            );
                        } elseif ($drawPoint == Constant::LABEL_POINT_BOX) {
                            $this->drawFilledRectangle(
                                $x - 2,
                                $y - 2,
                                $x + 2,
                                $y + 2,
                                [
                                    'r' => 255,
                                    'g' => 255,
                                    'b' => 255,
                                    'BorderR' => 0,
                                    'BorderG' => 0,
                                    'BorderB' => 0
                                ]
                            );
                        }
                        $series[] = ['format' => $serie, 'Caption' => $Caption];
                    }
                }
                $this->drawLabelBox($x, $minY - 3, $description, $series, $format);
            } else {
                if ($xDivs == 0) {
                    $xStep = ($this->graphAreaY2 - $this->graphAreaY1) / 4;
                } else {
                    $xStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                }
                $y = $this->graphAreaY1 + $xMargin + $index * $xStep;
                if ($drawVerticalLine) {
                    $this->drawLine(
                        $this->graphAreaX1 + $data['yMargin'],
                        $y,
                        $this->graphAreaX2 - $data['yMargin'],
                        $y,
                        [
                            'r' => $verticalLineR,
                            'g' => $verticalLineG,
                            'b' => $verticalLineB,
                            'alpha' => $verticalLineAlpha,
                            'ticks' => $verticalLineTicks
                        ]
                    );
                }
                $minX = $this->graphAreaX2;
                foreach ($seriesName as $key => $serieName) {
                    if (isset($data['series'][$serieName]['data'][$index])) {
                        $axisID = $data['series'][$serieName]['axis'];
                        $xAxisMode = $data['xAxisDisplay'];
                        $xAxisFormat = $data['xAxisFormat'];
                        $xAxisUnit = $data['xAxisUnit'];
                        $axisMode = $data['axis'][$axisID]['display'];
                        $axisFormat = $data['axis'][$axisID]['format'];
                        $axisUnit = $data['axis'][$axisID]['unit'];
                        $xLabel = '';
                        if (isset($data['abscissa']) && isset($data['series'][$data['abscissa']]['data'][$index])
                        ) {
                            $xLabel = $this->scaleFormat(
                                $data['series'][$data['abscissa']]['data'][$index],
                                $xAxisMode,
                                $xAxisFormat,
                                $xAxisUnit
                            );
                        }
                        if ($overrideTitle != null) {
                            $description = $overrideTitle;
                        } elseif (count($seriesName) == 1) {
                            if (isset($data['abscissa']) && isset($data['series'][$data['abscissa']]['data'][$index])
                            ) {
                                $description = $data['series'][$serieName]['description'] . ' - ' . $xLabel;
                            }
                        } elseif (isset($data['abscissa']) && isset($data['series'][$data['abscissa']]['data'][$index])
                        ) {
                            $description = $xLabel;
                        }
                        $serie = [];
                        if (isset($data['extended']['palette'][$index])) {
                            $serie['r'] = $data['extended']['palette'][$index]['r'];
                            $serie['g'] = $data['extended']['palette'][$index]['g'];
                            $serie['b'] = $data['extended']['palette'][$index]['b'];
                            $serie['alpha'] = $data['extended']['palette'][$index]['alpha'];
                        } else {
                            $serie['r'] = $data['series'][$serieName]['color']['r'];
                            $serie['g'] = $data['series'][$serieName]['color']['g'];
                            $serie['b'] = $data['series'][$serieName]['color']['b'];
                            $serie['alpha'] = $data['series'][$serieName]['color']['alpha'];
                        }
                        if (count($seriesName) == 1 && isset($data['series'][$serieName]['xOffset'])) {
                            $serieOffset = $data['series'][$serieName]['xOffset'];
                        } else {
                            $serieOffset = 0;
                        }
                        $value = $data['series'][$serieName]['data'][$index];
                        if ($forceLabels != null) {
                            $Caption = isset($forceLabels[$key]) ? $forceLabels[$key] : 'Not set';
                        } else {
                            $Caption = $this->scaleFormat($value, $axisMode, $axisFormat, $axisUnit);
                        }
                        if ($value == Constant::VOID) {
                            $value = 'NaN';
                        }
                        if ($this->lastChartLayout == Constant::CHART_LAST_LAYOUT_STACKED) {
                            if ($value >= 0) {
                                $lookFor = '+';
                            } else {
                                $lookFor = '-';
                            }
                            $value = 0;
                            $done = false;
                            foreach ($data['series'] as $name => $serieLookup) {
                                if ($serieLookup['isDrawable'] == true && $name != $data['abscissa'] && !$done
                                ) {
                                    if (isset($data['series'][$name]['data'][$index]) && $data['series'][$name]['data'][$index] != Constant::VOID
                                    ) {
                                        if ($data['series'][$name]['data'][$index] >= 0 && $lookFor == '+') {
                                            $value = $value + $data['series'][$name]['data'][$index];
                                        }
                                        if ($data['series'][$name]['data'][$index] < 0 && $lookFor == '-') {
                                            $value = $value - $data['series'][$name]['data'][$index];
                                        }
                                        if ($name == $serieName) {
                                            $done = true;
                                        }
                                    }
                                }
                            }
                        }
                        $x = floor($this->scaleComputeY($value, ['axisID' => $axisID]));
                        $y = floor($this->graphAreaY1 + $xMargin + $index * $xStep + $serieOffset);
                        if ($x < $minX) {
                            $minX = $x;
                        }
                        if ($drawPoint == Constant::LABEL_POINT_CIRCLE) {
                            $this->drawFilledCircle(
                                $x,
                                $y,
                                3,
                                [
                                    'r' => 255,
                                    'g' => 255,
                                    'b' => 255,
                                    'BorderR' => 0,
                                    'BorderG' => 0,
                                    'BorderB' => 0
                                ]
                            );
                        } elseif ($drawPoint == Constant::LABEL_POINT_BOX) {
                            $this->drawFilledRectangle(
                                $x - 2,
                                $y - 2,
                                $x + 2,
                                $y + 2,
                                [
                                    'r' => 255,
                                    'g' => 255,
                                    'b' => 255,
                                    'BorderR' => 0,
                                    'BorderG' => 0,
                                    'BorderB' => 0
                                ]
                            );
                        }
                        $series[] = ['format' => $serie, 'Caption' => $Caption];
                    }
                }
                $this->drawLabelBox($minX, $y - 3, $description, $series, $format);
            }
        }
    }

    /**
     * Get the legend box size
     *
     * @param array $format
     *
     * @return array
     */
    public function getLegendSize(array $format = []) {
        $fontName = isset($format['fontName']) ? $this->loadFont($format['fontName'], 'fonts') : $this->FontName;
        $fontSize = isset($format['fontSize']) ? $format['fontSize'] : $this->FontSize;
        $margin = isset($format['margin']) ? $format['margin'] : 5;
        $mode = isset($format['mode']) ? $format['mode'] : Constant::LEGEND_VERTICAL;
        $boxWidth = isset($format['boxWidth']) ? $format['boxWidth'] : 5;
        $boxHeight = isset($format['boxHeight']) ? $format['boxHeight'] : 5;
        $iconAreaWidth = isset($format['iconAreaWidth']) ? $format['iconAreaWidth'] : $boxWidth;
        $iconAreaHeight = isset($format['iconAreaHeight']) ? $format['iconAreaHeight'] : $boxHeight;
        $xSpacing = isset($format['xSpacing']) ? $format['xSpacing'] : 5;
        $data = $this->dataSet->getData();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa'] && isset($serie['picture'])
            ) {
                list($picWidth, $picHeight) = $this->getPicInfo($serie['picture']);
                if ($iconAreaWidth < $picWidth) {
                    $iconAreaWidth = $picWidth;
                }
                if ($iconAreaHeight < $picHeight) {
                    $iconAreaHeight = $picHeight;
                }
            }
        }
        $yStep = max($this->FontSize, $iconAreaHeight) + 5;
        $xStep = $iconAreaWidth + 5;
        $xStep = $xSpacing;
        $x = 100;
        $y = 100;
        $boundaries = [];
        $boundaries['l'] = $x;
        $boundaries['t'] = $y;
        $boundaries['r'] = 0;
        $boundaries['b'] = 0;
        $vY = $y;
        $vX = $x;
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa']) {
                if ($mode == Constant::LEGEND_VERTICAL) {
                    $boxArray = $this->getTextBox(
                        $vX + $iconAreaWidth + 4,
                        $vY + $iconAreaHeight / 2,
                        $fontName,
                        $fontSize,
                        0,
                        $serie['description']
                    );
                    if ($boundaries['t'] > $boxArray[2]['y'] + $iconAreaHeight / 2) {
                        $boundaries['t'] = $boxArray[2]['y'] + $iconAreaHeight / 2;
                    }
                    if ($boundaries['r'] < $boxArray[1]['x'] + 2) {
                        $boundaries['r'] = $boxArray[1]['x'] + 2;
                    }
                    if ($boundaries['b'] < $boxArray[1]['y'] + 2 + $iconAreaHeight / 2) {
                        $boundaries['b'] = $boxArray[1]['y'] + 2 + $iconAreaHeight / 2;
                    }
                    $lines = preg_split("/\n/", $serie['description']);
                    $vY = $vY + max($this->FontSize * count($lines), $iconAreaHeight) + 5;
                } elseif ($mode == Constant::LEGEND_HORIZONTAL) {
                    $lines = preg_split("/\n/", $serie['description']);
                    $width = [];
                    foreach ($lines as $key => $value) {
                        $boxArray = $this->getTextBox(
                            $vX + $iconAreaWidth + 6,
                            $y + $iconAreaHeight / 2 + (($this->FontSize + 3) * $key),
                            $fontName,
                            $fontSize,
                            0,
                            $value
                        );
                        if ($boundaries['t'] > $boxArray[2]['y'] + $iconAreaHeight / 2) {
                            $boundaries['t'] = $boxArray[2]['y'] + $iconAreaHeight / 2;
                        }
                        if ($boundaries['r'] < $boxArray[1]['x'] + 2) {
                            $boundaries['r'] = $boxArray[1]['x'] + 2;
                        }
                        if ($boundaries['b'] < $boxArray[1]['y'] + 2 + $iconAreaHeight / 2) {
                            $boundaries['b'] = $boxArray[1]['y'] + 2 + $iconAreaHeight / 2;
                        }
                        $width[] = $boxArray[1]['x'];
                    }
                    $vX = max($width) + $xStep;
                }
            }
        }
        $vY = $vY - $yStep;
        $vX = $vX - $xStep;
        $topOffset = $y - $boundaries['t'];
        if ($boundaries['b'] - ($vY + $iconAreaHeight) < $topOffset) {
            $boundaries['b'] = $vY + $iconAreaHeight + $topOffset;
        }
        $width = ($boundaries['r'] + $margin) - ($boundaries['l'] - $margin);
        $height = ($boundaries['b'] + $margin) - ($boundaries['t'] - $margin);
        return ['Width' => $width, 'Height' => $height];
    }

    /**
     * Write Max value on a chart
     *
     * @param int   $type
     * @param array $format
     */
    public function writeBounds($type = Constant::BOUND_BOTH, $format = null) {
        $maxLabelTxt = isset($format['maxLabelTxt']) ? $format['maxLabelTxt'] : 'max=';
        $minLabelTxt = isset($format['minLabelTxt']) ? $format['minLabelTxt'] : 'min=';
        $decimals = isset($format['decimals']) ? $format['decimals'] : 1;
        $ExcludedSeries = isset($format['excludedSeries']) ? $format['excludedSeries'] : '';
        $displayOffset = isset($format['displayOffset']) ? $format['displayOffset'] : 4;
        $displayColor = isset($format['displayColor']) ? $format['displayColor'] : Constant::DISPLAY_MANUAL;
        $maxDisplayR = isset($format['maxDisplayR']) ? $format['maxDisplayR'] : 0;
        $maxDisplayG = isset($format['maxDisplayG']) ? $format['maxDisplayG'] : 0;
        $maxDisplayB = isset($format['maxDisplayB']) ? $format['maxDisplayB'] : 0;
        $minDisplayR = isset($format['minDisplayR']) ? $format['minDisplayR'] : 255;
        $minDisplayG = isset($format['minDisplayG']) ? $format['minDisplayG'] : 255;
        $minDisplayB = isset($format['minDisplayB']) ? $format['minDisplayB'] : 255;
        $minLabelPos = isset($format['minLabelPos']) ? $format['minLabelPos'] : Constant::BOUND_LABEL_POS_AUTO;
        $maxLabelPos = isset($format['maxLabelPos']) ? $format['maxLabelPos'] : Constant::BOUND_LABEL_POS_AUTO;
        $drawBox = isset($format['drawBox']) ? $format['drawBox'] : true;
        $drawBoxBorder = isset($format['drawBoxBorder']) ? $format['drawBoxBorder'] : false;
        $borderOffset = isset($format['borderOffset']) ? $format['borderOffset'] : 5;
        $boxRounded = isset($format['boxRounded']) ? $format['boxRounded'] : true;
        $roundedRadius = isset($format['roundedRadius']) ? $format['roundedRadius'] : 3;
        $boxR = isset($format['boxR']) ? $format['boxR'] : 0;
        $boxG = isset($format['boxG']) ? $format['boxG'] : 0;
        $boxB = isset($format['boxB']) ? $format['boxB'] : 0;
        $boxAlpha = isset($format['boxAlpha']) ? $format['boxAlpha'] : 20;
        $boxSurrounding = isset($format['boxSurrounding']) ? $format['boxSurrounding'] : '';
        $boxBorderR = isset($format['boxBorderR']) ? $format['boxBorderR'] : 255;
        $boxBorderG = isset($format['boxBorderG']) ? $format['boxBorderG'] : 255;
        $boxBorderB = isset($format['boxBorderB']) ? $format['boxBorderB'] : 255;
        $boxBorderAlpha = isset($format['boxBorderAlpha']) ? $format['boxBorderAlpha'] : 100;
        $CaptionSettings = [
            'DrawBox' => $drawBox,
            'DrawBoxBorder' => $drawBoxBorder,
            'BorderOffset' => $borderOffset,
            'BoxRounded' => $boxRounded,
            'RoundedRadius' => $roundedRadius,
            'BoxR' => $boxR,
            'BoxG' => $boxG,
            'BoxB' => $boxB,
            'BoxAlpha' => $boxAlpha,
            'BoxSurrounding' => $boxSurrounding,
            'BoxBorderR' => $boxBorderR,
            'BoxBorderG' => $boxBorderG,
            'BoxBorderB' => $boxBorderB,
            'BoxBorderAlpha' => $boxBorderAlpha
        ];
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        $data = $this->dataSet->getData();
        foreach ($data['series'] as $serieName => $serie) {
            if ($serie['isDrawable'] == true && $serieName != $data['abscissa'] && !isset($ExcludedSeries[$serieName])
            ) {
                $r = $serie['color']['r'];
                $g = $serie['color']['g'];
                $b = $serie['color']['b'];
                $minValue = $this->dataSet->getMin($serieName);
                $maxValue = $this->dataSet->getMax($serieName);
                $minPos = Constant::VOID;
                $maxPos = Constant::VOID;
                foreach ($serie['data'] as $key => $value) {
                    if ($value == $minValue && $minPos == Constant::VOID) {
                        $minPos = $key;
                    }
                    if ($value == $maxValue) {
                        $maxPos = $key;
                    }
                }
                $axisID = $serie['axis'];
                $mode = $data['axis'][$axisID]['display'];
                $format = $data['axis'][$axisID]['format'];
                $unit = $data['axis'][$axisID]['unit'];
                $posArray = $this->scaleComputeY(
                    $serie['data'],
                    ['axisID' => $serie['axis']]
                );
                if ($data['orientation'] == Constant::SCALE_POS_LEFTRIGHT) {
                    $xStep = ($this->graphAreaX2 - $this->graphAreaX1 - $xMargin * 2) / $xDivs;
                    $x = $this->graphAreaX1 + $xMargin;
                    $serieOffset = isset($serie['xOffset']) ? $serie['xOffset'] : 0;
                    if ($type == Constant::BOUND_MAX || $type == Constant::BOUND_BOTH) {
                        if ($maxLabelPos == Constant::BOUND_LABEL_POS_TOP || ($maxLabelPos == Constant::BOUND_LABEL_POS_AUTO && $maxValue >= 0)
                        ) {
                            $yPos = $posArray[$maxPos] - $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                        }
                        if ($maxLabelPos == Constant::BOUND_LABEL_POS_BOTTOM || ($maxLabelPos == Constant::BOUND_LABEL_POS_AUTO && $maxValue < 0)
                        ) {
                            $yPos = $posArray[$maxPos] + $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                        }
                        $xPos = $x + $maxPos * $xStep + $serieOffset;
                        $label = sprintf(
                            '%s%s',
                            $maxLabelTxt,
                            $this->scaleFormat(round($maxValue, $decimals), $mode, $format, $unit)
                        );
                        $txtPos = $this->getTextBox($xPos, $yPos, $this->FontName, $this->FontSize, 0, $label);
                        $xOffset = 0;
                        $yOffset = 0;
                        if ($txtPos[0]['x'] < $this->graphAreaX1) {
                            $xOffset = (($this->graphAreaX1 - $txtPos[0]['x']) / 2);
                        }
                        if ($txtPos[1]['x'] > $this->graphAreaX2) {
                            $xOffset = -(($txtPos[1]['x'] - $this->graphAreaX2) / 2);
                        }
                        if ($txtPos[2]['y'] < $this->graphAreaY1) {
                            $yOffset = $this->graphAreaY1 - $txtPos[2]['y'];
                        }
                        if ($txtPos[0]['y'] > $this->graphAreaY2) {
                            $yOffset = -($txtPos[0]['y'] - $this->graphAreaY2);
                        }
                        $CaptionSettings['r'] = $maxDisplayR;
                        $CaptionSettings['g'] = $maxDisplayG;
                        $CaptionSettings['b'] = $maxDisplayB;
                        $CaptionSettings['align'] = $align;
                        $this->drawText($xPos + $xOffset, $yPos + $yOffset, $label, $CaptionSettings);
                    }
                    if ($type == Constant::BOUND_MIN || $type == Constant::BOUND_BOTH) {
                        if ($minLabelPos == Constant::BOUND_LABEL_POS_TOP || ($minLabelPos == Constant::BOUND_LABEL_POS_AUTO && $minValue >= 0)
                        ) {
                            $yPos = $posArray[$minPos] - $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                        }
                        if ($minLabelPos == Constant::BOUND_LABEL_POS_BOTTOM || ($minLabelPos == Constant::BOUND_LABEL_POS_AUTO && $minValue < 0)
                        ) {
                            $yPos = $posArray[$minPos] + $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                        }
                        $xPos = $x + $minPos * $xStep + $serieOffset;
                        $label = sprintf(
                            '%s%s',
                            $minLabelTxt,
                            $this->scaleFormat(round($minValue, $decimals), $mode, $format, $unit)
                        );
                        $txtPos = $this->getTextBox($xPos, $yPos, $this->FontName, $this->FontSize, 0, $label);
                        $xOffset = 0;
                        $yOffset = 0;
                        if ($txtPos[0]['x'] < $this->graphAreaX1) {
                            $xOffset = (($this->graphAreaX1 - $txtPos[0]['x']) / 2);
                        }
                        if ($txtPos[1]['x'] > $this->graphAreaX2) {
                            $xOffset = -(($txtPos[1]['x'] - $this->graphAreaX2) / 2);
                        }
                        if ($txtPos[2]['y'] < $this->graphAreaY1) {
                            $yOffset = $this->graphAreaY1 - $txtPos[2]['y'];
                        }
                        if ($txtPos[0]['y'] > $this->graphAreaY2) {
                            $yOffset = -($txtPos[0]['y'] - $this->graphAreaY2);
                        }
                        $CaptionSettings['r'] = $minDisplayR;
                        $CaptionSettings['g'] = $minDisplayG;
                        $CaptionSettings['b'] = $minDisplayB;
                        $CaptionSettings['align'] = $align;
                        $this->drawText(
                            $xPos + $xOffset,
                            $yPos - $displayOffset + $yOffset,
                            $label,
                            $CaptionSettings
                        );
                    }
                } else {
                    $xStep = ($this->graphAreaY2 - $this->graphAreaY1 - $xMargin * 2) / $xDivs;
                    $x = $this->graphAreaY1 + $xMargin;
                    $serieOffset = isset($serie['xOffset']) ? $serie['xOffset'] : 0;
                    if ($type == Constant::BOUND_MAX || $type == Constant::BOUND_BOTH) {
                        if ($maxLabelPos == Constant::BOUND_LABEL_POS_TOP || ($maxLabelPos == Constant::BOUND_LABEL_POS_AUTO && $maxValue >= 0)
                        ) {
                            $yPos = $posArray[$maxPos] + $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_MIDDLELEFT;
                        }
                        if ($maxLabelPos == Constant::BOUND_LABEL_POS_BOTTOM || ($maxLabelPos == Constant::BOUND_LABEL_POS_AUTO && $maxValue < 0)
                        ) {
                            $yPos = $posArray[$maxPos] - $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_MIDDLERIGHT;
                        }
                        $xPos = $x + $maxPos * $xStep + $serieOffset;
                        $label = $maxLabelTxt . $this->scaleFormat($maxValue, $mode, $format, $unit);
                        $txtPos = $this->getTextBox($yPos, $xPos, $this->FontName, $this->FontSize, 0, $label);
                        $xOffset = 0;
                        $yOffset = 0;
                        if ($txtPos[0]['x'] < $this->graphAreaX1) {
                            $xOffset = $this->graphAreaX1 - $txtPos[0]['x'];
                        }
                        if ($txtPos[1]['x'] > $this->graphAreaX2) {
                            $xOffset = -($txtPos[1]['x'] - $this->graphAreaX2);
                        }
                        if ($txtPos[2]['y'] < $this->graphAreaY1) {
                            $yOffset = ($this->graphAreaY1 - $txtPos[2]['y']) / 2;
                        }
                        if ($txtPos[0]['y'] > $this->graphAreaY2) {
                            $yOffset = -(($txtPos[0]['y'] - $this->graphAreaY2) / 2);
                        }
                        $CaptionSettings['r'] = $maxDisplayR;
                        $CaptionSettings['g'] = $maxDisplayG;
                        $CaptionSettings['b'] = $maxDisplayB;
                        $CaptionSettings['align'] = $align;
                        $this->drawText($yPos + $xOffset, $xPos + $yOffset, $label, $CaptionSettings);
                    }
                    if ($type == Constant::BOUND_MIN || $type == Constant::BOUND_BOTH) {
                        if ($minLabelPos == Constant::BOUND_LABEL_POS_TOP || ($minLabelPos == Constant::BOUND_LABEL_POS_AUTO && $minValue >= 0)
                        ) {
                            $yPos = $posArray[$minPos] + $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_MIDDLELEFT;
                        }
                        if ($minLabelPos == Constant::BOUND_LABEL_POS_BOTTOM || ($minLabelPos == Constant::BOUND_LABEL_POS_AUTO && $minValue < 0)
                        ) {
                            $yPos = $posArray[$minPos] - $displayOffset + 2;
                            $align = Constant::TEXT_ALIGN_MIDDLERIGHT;
                        }
                        $xPos = $x + $minPos * $xStep + $serieOffset;
                        $label = $minLabelTxt . $this->scaleFormat($minValue, $mode, $format, $unit);
                        $txtPos = $this->getTextBox($yPos, $xPos, $this->FontName, $this->FontSize, 0, $label);
                        $xOffset = 0;
                        $yOffset = 0;
                        if ($txtPos[0]['x'] < $this->graphAreaX1) {
                            $xOffset = $this->graphAreaX1 - $txtPos[0]['x'];
                        }
                        if ($txtPos[1]['x'] > $this->graphAreaX2) {
                            $xOffset = -($txtPos[1]['x'] - $this->graphAreaX2);
                        }
                        if ($txtPos[2]['y'] < $this->graphAreaY1) {
                            $yOffset = ($this->graphAreaY1 - $txtPos[2]['y']) / 2;
                        }
                        if ($txtPos[0]['y'] > $this->graphAreaY2) {
                            $yOffset = -(($txtPos[0]['y'] - $this->graphAreaY2) / 2);
                        }
                        $CaptionSettings['r'] = $minDisplayR;
                        $CaptionSettings['g'] = $minDisplayG;
                        $CaptionSettings['b'] = $minDisplayB;
                        $CaptionSettings['align'] = $align;
                        $this->drawText($yPos + $xOffset, $xPos + $yOffset, $label, $CaptionSettings);
                    }
                }
            }
        }
    }
}
