<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 30, 2019, 2:57:31 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CImage_Chart_Constant as Constant;

class CImage_Chart_Draw extends CImage_Chart_BaseDraw {

    /**
     * Draw a polygon
     * @param array $points
     * @param array $format
     */
    public function drawPolygon(array $points, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $noFill = isset($format["noFill"]) ? $format["noFill"] : false;
        $noBorder = isset($format["noBorder"]) ? $format["noBorder"] : false;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $r;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $g;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $b;
        $borderalpha = isset($format["alpha"]) ? $format["alpha"] : $alpha / 2;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $skipX = isset($format["skipX"]) ? $format["skipX"] : Constant::OUT_OF_SIGHT;
        $skipY = isset($format["skipY"]) ? $format["skipY"] : Constant::OUT_OF_SIGHT;
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
                        $shadow, [
                    "r" => $this->shadowR,
                    "g" => $this->shadowG,
                    "b" => $this->shadowB,
                    "alpha" => $this->shadowA,
                    "noBorder" => true
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
                $borderSettings = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha];
            } else {
                $borderSettings = [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha
                ];
            }
            for ($i = 0; $i <= count($points) - 1; $i = $i + 2) {
                if (isset($points[$i + 2]) && !($points[$i] == $points[$i + 2] && $points[$i] == $skipX) && !($points[$i + 1] == $points[$i + 3] && $points[$i + 1] == $skipY)
                ) {
                    $this->drawLine(
                            $points[$i], $points[$i + 1], $points[$i + 2], $points[$i + 3], $borderSettings
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
     * Draw a rectangle with rounded corners
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int|float $radius
     * @param array $format
     * @return null|integer
     */
    public function drawRoundedRectangle($x1, $y1, $x2, $y2, $radius, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        list($x1, $y1, $x2, $y2) = $this->fixBoxCoordinates($x1, $y1, $x2, $y2);
        if ($x2 - $x1 < $radius) {
            $radius = floor((($x2 - $x1)) / 2);
        }
        if ($y2 - $y1 < $radius) {
            $radius = floor((($y2 - $y1)) / 2);
        }
        $color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "noBorder" => true];
        if ($radius <= 0) {
            $this->drawRectangle($x1, $y1, $x2, $y2, $color);
            return 0;
        }
        if ($this->antialias) {
            $this->drawLine($x1 + $radius, $y1, $x2 - $radius, $y1, $color);
            $this->drawLine($x2, $y1 + $radius, $x2, $y2 - $radius, $color);
            $this->drawLine($x2 - $radius, $y2, $x1 + $radius, $y2, $color);
            $this->drawLine($x1, $y1 + $radius, $x1, $y2 - $radius, $color);
        } else {
            $color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            imageline($this->picture, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
            imageline($this->picture, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);
            imageline($this->picture, $x2 - $radius, $y2, $x1 + $radius, $y2, $color);
            imageline($this->picture, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
        }
        $step = 360 / (2 * PI * $radius);
        for ($i = 0; $i <= 90; $i = $i + $step) {
            $x = cos(($i + 180) * PI / 180) * $radius + $x1 + $radius;
            $y = sin(($i + 180) * PI / 180) * $radius + $y1 + $radius;
            $this->drawAntialiasPixel($x, $y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
            $x = cos(($i + 90) * PI / 180) * $radius + $x1 + $radius;
            $y = sin(($i + 90) * PI / 180) * $radius + $y2 - $radius;
            $this->drawAntialiasPixel($x, $y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
            $x = cos($i * PI / 180) * $radius + $x2 - $radius;
            $y = sin($i * PI / 180) * $radius + $y2 - $radius;
            $this->drawAntialiasPixel($x, $y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
            $x = cos(($i + 270) * PI / 180) * $radius + $x2 - $radius;
            $y = sin(($i + 270) * PI / 180) * $radius + $y1 + $radius;
            $this->drawAntialiasPixel($x, $y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
        }
    }

    /**
     * Draw a rectangle with rounded corners
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int|float $radius
     * @param array $format
     * @return null|integer
     */
    public function drawRoundedFilledRectangle($x1, $y1, $x2, $y2, $radius, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : -1;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : -1;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : -1;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        /* Temporary fix for AA issue */
        $y1 = floor($y1);
        $y2 = floor($y2);
        $x1 = floor($x1);
        $x2 = floor($x2);
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        if ($borderR == -1) {
            $borderR = $r;
            $borderG = $g;
            $borderB = $b;
        }
        list($x1, $y1, $x2, $y2) = $this->fixBoxCoordinates($x1, $y1, $x2, $y2);
        if ($x2 - $x1 < $radius * 2) {
            $radius = floor((($x2 - $x1)) / 4);
        }
        if ($y2 - $y1 < $radius * 2) {
            $radius = floor((($y2 - $y1)) / 4);
        }
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawRoundedFilledRectangle(
                    $x1 + $this->shadowX, $y1 + $this->shadowY, $x2 + $this->shadowX, $y2 + $this->shadowY, $radius, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowA
                    ]
            );
        }
        $color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "noBorder" => true];
        if ($radius <= 0) {
            $this->drawFilledRectangle($x1, $y1, $x2, $y2, $color);
            return 0;
        }
        $yTop = $y1 + $radius;
        $yBottom = $y2 - $radius;
        $step = 360 / (2 * PI * $radius);
        $positions = [];
        $radius--;
        $minY = null;
        $maxY = null;
        for ($i = 0; $i <= 90; $i = $i + $step) {
            $xp1 = cos(($i + 180) * PI / 180) * $radius + $x1 + $radius;
            $xp2 = cos(((90 - $i) + 270) * PI / 180) * $radius + $x2 - $radius;
            $yp = floor(sin(($i + 180) * PI / 180) * $radius + $yTop);
            if (null === $minY || $yp > $minY) {
                $minY = $yp;
            }
            if ($xp1 <= floor($x1)) {
                $xp1++;
            }
            if ($xp2 >= floor($x2)) {
                $xp2--;
            }
            $xp1++;
            if (!isset($positions[$yp])) {
                $positions[$yp]["x1"] = $xp1;
                $positions[$yp]["x2"] = $xp2;
            } else {
                $positions[$yp]["x1"] = ($positions[$yp]["x1"] + $xp1) / 2;
                $positions[$yp]["x2"] = ($positions[$yp]["x2"] + $xp2) / 2;
            }
            $xp1 = cos(($i + 90) * PI / 180) * $radius + $x1 + $radius;
            $xp2 = cos((90 - $i) * PI / 180) * $radius + $x2 - $radius;
            $yp = floor(sin(($i + 90) * PI / 180) * $radius + $yBottom);
            if (null === $maxY || $yp < $maxY) {
                $maxY = $yp;
            }
            if ($xp1 <= floor($x1)) {
                $xp1++;
            }
            if ($xp2 >= floor($x2)) {
                $xp2--;
            }
            $xp1++;
            if (!isset($positions[$yp])) {
                $positions[$yp]["x1"] = $xp1;
                $positions[$yp]["x2"] = $xp2;
            } else {
                $positions[$yp]["x1"] = ($positions[$yp]["x1"] + $xp1) / 2;
                $positions[$yp]["x2"] = ($positions[$yp]["x2"] + $xp2) / 2;
            }
        }
        $manualColor = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        foreach ($positions as $yp => $bounds) {
            $x1 = $bounds["x1"];
            $x1Dec = $this->getFirstDecimal($x1);
            if ($x1Dec != 0) {
                $x1 = floor($x1) + 1;
            }
            $x2 = $bounds["x2"];
            $x2Dec = $this->getFirstDecimal($x2);
            if ($x2Dec != 0) {
                $x2 = floor($x2) - 1;
            }
            imageline($this->picture, $x1, $yp, $x2, $yp, $manualColor);
        }
        $this->drawFilledRectangle($x1, $minY + 1, floor($x2), $maxY - 1, $color);
        $radius++;
        $this->drawRoundedRectangle(
                $x1, $y1, $x2 + 1, $y2 - 1, $radius, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
        );
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a rectangle
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param array $format
     */
    public function drawRectangle($x1, $y1, $x2, $y2, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $noAngle = isset($format["noAngle"]) ? $format["noAngle"] : false;
        if ($x1 > $x2) {
            list($x1, $x2) = [$x2, $x1];
        }
        if ($y1 > $y2) {
            list($y1, $y2) = [$y2, $y1];
        }
        if ($this->antialias) {
            if ($noAngle) {
                $this->drawLine(
                        $x1 + 1, $y1, $x2 - 1, $y1, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $x2, $y1 + 1, $x2, $y2 - 1, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $x2 - 1, $y2, $x1 + 1, $y2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $x1, $y1 + 1, $x1, $y2 - 1, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
            } else {
                $this->drawLine(
                        $x1 + 1, $y1, $x2 - 1, $y1, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $x2, $y1, $x2, $y2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $x2 - 1, $y2, $x1 + 1, $y2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $x1, $y1, $x1, $y2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
            }
        } else {
            $color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            imagerectangle($this->picture, $x1, $y1, $x2, $y2, $color);
        }
    }

    /**
     * Draw a filled rectangle
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param array $format
     */
    public function drawFilledRectangle($x1, $y1, $x2, $y2, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : -1;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : -1;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : -1;
        $borderalpha = isset($format["borderalpha"]) ? $format["borderalpha"] : $alpha;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $noAngle = isset($format["noAngle"]) ? $format["noAngle"] : null;
        $dash = isset($format["dash"]) ? $format["dash"] : false;
        $dashStep = isset($format["dashStep"]) ? $format["dashStep"] : 4;
        $dashR = isset($format["dashR"]) ? $format["dashR"] : 0;
        $dashG = isset($format["dashG"]) ? $format["dashG"] : 0;
        $dashB = isset($format["dashB"]) ? $format["dashB"] : 0;
        $noBorder = isset($format["noBorder"]) ? $format["noBorder"] : false;
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        if ($x1 > $x2) {
            list($x1, $x2) = [$x2, $x1];
        }
        if ($y1 > $y2) {
            list($y1, $y2) = [$y2, $y1];
        }
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawFilledRectangle(
                    $x1 + $this->shadowX, $y1 + $this->shadowY, $x2 + $this->shadowX, $y2 + $this->shadowY, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowA,
                "ticks" => $ticks,
                "noAngle" => $noAngle
                    ]
            );
        }
        $color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        if ($noAngle) {
            imagefilledrectangle($this->picture, ceil($x1) + 1, ceil($y1), floor($x2) - 1, floor($y2), $color);
            imageline($this->picture, ceil($x1), ceil($y1) + 1, ceil($x1), floor($y2) - 1, $color);
            imageline($this->picture, floor($x2), ceil($y1) + 1, floor($x2), floor($y2) - 1, $color);
        } else {
            imagefilledrectangle($this->picture, ceil($x1), ceil($y1), floor($x2), floor($y2), $color);
        }
        if ($dash) {
            if ($borderR != -1) {
                $iX1 = $x1 + 1;
                $iY1 = $y1 + 1;
                $iX2 = $x2 - 1;
                $iY2 = $y2 - 1;
            } else {
                $iX1 = $x1;
                $iY1 = $y1;
                $iX2 = $x2;
                $iY2 = $y2;
            }
            $color = $this->allocateColor($this->picture, $dashR, $dashG, $dashB, $alpha);
            $y = $iY1 - $dashStep;
            for ($x = $iX1; $x <= $iX2 + ($iY2 - $iY1); $x = $x + $dashStep) {
                $y = $y + $dashStep;
                if ($x > $iX2) {
                    $xa = $x - ($x - $iX2);
                    $ya = $iY1 + ($x - $iX2);
                } else {
                    $xa = $x;
                    $ya = $iY1;
                }
                if ($y > $iY2) {
                    $xb = $iX1 + ($y - $iY2);
                    $yb = $y - ($y - $iY2);
                } else {
                    $xb = $iX1;
                    $yb = $y;
                }
                imageline($this->picture, $xa, $ya, $xb, $yb, $color);
            }
        }
        if ($this->antialias && !$noBorder) {
            if ($x1 < ceil($x1)) {
                $alphaA = $alpha * (ceil($x1) - $x1);
                $color = $this->allocateColor($this->picture, $r, $g, $b, $alphaA);
                imageline($this->picture, ceil($x1) - 1, ceil($y1), ceil($x1) - 1, floor($y2), $color);
            }
            if ($y1 < ceil($y1)) {
                $alphaA = $alpha * (ceil($y1) - $y1);
                $color = $this->allocateColor($this->picture, $r, $g, $b, $alphaA);
                imageline($this->picture, ceil($x1), ceil($y1) - 1, floor($x2), ceil($y1) - 1, $color);
            }
            if ($x2 > floor($x2)) {
                $alphaA = $alpha * (.5 - ($x2 - floor($x2)));
                $color = $this->allocateColor($this->picture, $r, $g, $b, $alphaA);
                imageline($this->picture, floor($x2) + 1, ceil($y1), floor($x2) + 1, floor($y2), $color);
            }
            if ($y2 > floor($y2)) {
                $alphaA = $alpha * (.5 - ($y2 - floor($y2)));
                $color = $this->allocateColor($this->picture, $r, $g, $b, $alphaA);
                imageline($this->picture, ceil($x1), floor($y2) + 1, floor($x2), floor($y2) + 1, $color);
            }
        }
        if ($borderR != -1) {
            $this->drawRectangle(
                    $x1, $y1, $x2, $y2, [
                "r" => $borderR,
                "g" => $borderG,
                "b" => $borderB,
                "alpha" => $borderalpha,
                "ticks" => $ticks,
                "noAngle" => $noAngle
                    ]
            );
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a rectangular marker of the specified size
     * @param int $x
     * @param int $y
     * @param array $format
     */
    public function drawRectangleMarker($x, $y, array $format = []) {
        $size = isset($format["size"]) ? $format["size"] : 4;
        $halfSize = floor($size / 2);
        $this->drawFilledRectangle($x - $halfSize, $y - $halfSize, $x + $halfSize, $y + $halfSize, $format);
    }

    /**
     * Drawn a spline based on the bezier public function
     * @param array $coordinates
     * @param array $format
     * @return array
     */
    public function drawSpline(array $coordinates, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $force = isset($format["force"]) ? $format["force"] : 30;
        $forces = isset($format["forces"]) ? $format["forces"] : null;
        $showC = isset($format["showControl"]) ? $format["showControl"] : false;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $pathOnly = isset($format["pathOnly"]) ? $format["pathOnly"] : false;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
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
                $xOff = cos($angle2 * PI / 180) * $force + $x1;
                $yOff = sin($angle2 * PI / 180) * $force + $y1;
                $xv1 = cos($angle1 * PI / 180) * $force + $xOff;
                $yv1 = sin($angle1 * PI / 180) * $force + $yOff;
            }
            /* Last segment */
            if ($i == count($coordinates) - 1) {
                $xv2 = $x2;
                $yv2 = $y2;
            } else {
                $angle1 = $this->getAngle($x2, $y2, $coordinates[$i + 1][0], $coordinates[$i + 1][1]);
                $angle2 = $this->getAngle($x1, $y1, $x2, $y2);
                $xOff = cos(($angle2 + 180) * PI / 180) * $force + $x2;
                $yOff = sin(($angle2 + 180) * PI / 180) * $force + $y2;
                $xv2 = cos(($angle1 + 180) * PI / 180) * $force + $xOff;
                $yv2 = sin(($angle1 + 180) * PI / 180) * $force + $yOff;
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
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int $xv1
     * @param int $yv1
     * @param int $xv2
     * @param int $yv2
     * @param array $format
     * @return array
     */
    public function drawBezier($x1, $y1, $x2, $y2, $xv1, $yv1, $xv2, $yv2, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $showC = isset($format["showControl"]) ? $format["showControl"] : false;
        $segments = isset($format["segments"]) ? $format["segments"] : null;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $NoDraw = isset($format["noDraw"]) ? $format["noDraw"] : false;
        $pathOnly = isset($format["pathOnly"]) ? $format["pathOnly"] : false;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
        $drawArrow = isset($format["drawArrow"]) ? $format["drawArrow"] : false;
        $arrowSize = isset($format["arrowSize"]) ? $format["arrowSize"] : 10;
        $arrowRatio = isset($format["arrowRatio"]) ? $format["arrowRatio"] : .5;
        $arrowTwoHeads = isset($format["arrowTwoHeads"]) ? $format["arrowTwoHeads"] : false;
        if ($segments == null) {
            $length = $this->getLength($x1, $y1, $x2, $y2);
            $precision = ($length * 125) / 1000;
        } else {
            $precision = $segments;
        }
        $p[0]["x"] = $x1;
        $p[0]["y"] = $y1;
        $p[1]["x"] = $xv1;
        $p[1]["y"] = $yv1;
        $p[2]["x"] = $xv2;
        $p[2]["y"] = $yv2;
        $p[3]["x"] = $x2;
        $p[3]["y"] = $y2;
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
                if (!isset($Q[$id]["x"])) {
                    $Q[$id]["x"] = 0;
                }
                if (!isset($Q[$id]["y"])) {
                    $Q[$id]["y"] = 0;
                }
                $Q[$id]["x"] = $Q[$id]["x"] + $p[$j]["x"] * $C[$j];
                $Q[$id]["y"] = $Q[$id]["y"] + $p[$j]["y"] * $C[$j];
            }
            $id++;
        }
        $Q[$id]["x"] = $x2;
        $Q[$id]["y"] = $y2;
        if (!$NoDraw) {
            /* Display the control points */
            if ($showC && !$pathOnly) {
                $xv1 = floor($xv1);
                $yv1 = floor($yv1);
                $xv2 = floor($xv2);
                $yv2 = floor($yv2);
                $this->drawLine($x1, $y1, $x2, $y2, ["r" => 0, "g" => 0, "b" => 0, "alpha" => 30]);
                $myMarkerSettings = [
                    "r" => 255,
                    "g" => 0,
                    "b" => 0,
                    "borderR" => 255,
                    "borderB" => 255,
                    "borderG" => 255,
                    "size" => 4
                ];
                $this->drawRectangleMarker($xv1, $yv1, $myMarkerSettings);
                $this->drawText($xv1 + 4, $yv1, "v1");
                $myMarkerSettings = [
                    "r" => 0,
                    "g" => 0,
                    "b" => 255,
                    "borderR" => 255,
                    "borderB" => 255,
                    "borderG" => 255,
                    "size" => 4
                ];
                $this->drawRectangleMarker($xv2, $yv2, $myMarkerSettings);
                $this->drawText($xv2 + 4, $yv2, "v2");
            }
            /* Draw the bezier */
            $lastX = null;
            $lastY = null;
            $Cpt = null;
            $mode = null;
            $arrowS = [];
            foreach ($Q as $point) {
                $x = $point["x"];
                $y = $point["y"];
                /* Get the first segment */
                if (!count($arrowS) && $lastX != null && $lastY != null) {
                    $arrowS["x2"] = $lastX;
                    $arrowS["y2"] = $lastY;
                    $arrowS["x1"] = $x;
                    $arrowS["y1"] = $y;
                }
                if ($lastX != null && $lastY != null && !$pathOnly) {
                    list($Cpt, $mode) = $this->drawLine(
                            $lastX, $lastY, $x, $y, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha,
                        "ticks" => $ticks,
                        "Cpt" => $Cpt,
                        "mode" => $mode,
                        "weight" => $weight
                            ]
                    );
                }
                /* Get the last segment */
                $arrowE["x1"] = $lastX;
                $arrowE["y1"] = $lastY;
                $arrowE["x2"] = $x;
                $arrowE["y2"] = $y;
                $lastX = $x;
                $lastY = $y;
            }
            if ($drawArrow && !$pathOnly) {
                $arrowSettings = [
                    "fillR" => $r,
                    "fillG" => $g,
                    "fillB" => $b,
                    "alpha" => $alpha,
                    "size" => $arrowSize,
                    "ration" => $arrowRatio
                ];
                if ($arrowTwoHeads) {
                    $this->drawArrow($arrowS["x1"], $arrowS["y1"], $arrowS["x2"], $arrowS["y2"], $arrowSettings);
                }
                $this->drawArrow($arrowE["x1"], $arrowE["y1"], $arrowE["x2"], $arrowE["y2"], $arrowSettings);
            }
        }
        return $Q;
    }

    /**
     * Draw a line between two points
     * @param int|float $x1
     * @param int|float $y1
     * @param int|float $x2
     * @param int|float $y2
     * @param array $format
     * @return array|int
     */
    public function drawLine($x1, $y1, $x2, $y2, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $Cpt = isset($format["Cpt"]) ? $format["Cpt"] : 1;
        $mode = isset($format["mode"]) ? $format["mode"] : 1;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
        $threshold = isset($format["Threshold"]) ? $format["Threshold"] : null;
        if ($this->antialias == false && $ticks == null) {
            if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
                $shadowColor = $this->allocateColor(
                        $this->picture, $this->shadowR, $this->shadowG, $this->shadowB, $this->shadowA
                );
                imageline(
                        $this->picture, $x1 + $this->shadowX, $y1 + $this->shadowY, $x2 + $this->shadowX, $y2 + $this->shadowY, $shadowColor
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
            $polySettings = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "borderalpha" => $alpha];
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
            $color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha];
            if ($threshold != null) {
                foreach ($threshold as $key => $parameters) {
                    if ($y <= $parameters["minX"] && $y >= $parameters["maxX"]) {
                        if (isset($parameters["r"])) {
                            $rT = $parameters["r"];
                        } else {
                            $rT = 0;
                        }
                        if (isset($parameters["g"])) {
                            $gT = $parameters["g"];
                        } else {
                            $gT = 0;
                        }
                        if (isset($parameters["b"])) {
                            $bT = $parameters["b"];
                        } else {
                            $bT = 0;
                        }
                        if (isset($parameters["alpha"])) {
                            $alphaT = $parameters["alpha"];
                        } else {
                            $alphaT = 0;
                        }
                        $color = ["r" => $rT, "g" => $gT, "b" => $bT, "alpha" => $alphaT];
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
     * @param int $xc
     * @param int $yc
     * @param int|float $height
     * @param int|float $width
     * @param array $format
     */
    public function drawCircle($xc, $yc, $height, $width, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
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
                    $xc + $this->shadowX, $yc + $this->shadowY, $height, $width, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowA,
                "ticks" => $ticks
                    ]
            );
        }
        if ($width == 0) {
            $width = $height;
        }
        if ($r < 0) {
            $r = 0;
        } if ($r > 255) {
            $r = 255;
        }
        if ($g < 0) {
            $g = 0;
        } if ($g > 255) {
            $g = 255;
        }
        if ($b < 0) {
            $b = 0;
        } if ($b > 255) {
            $b = 255;
        }
        $step = 360 / (2 * PI * max($width, $height));
        $mode = 1;
        $Cpt = 1;
        for ($i = 0; $i <= 360; $i = $i + $step) {
            $x = cos($i * PI / 180) * $height + $xc;
            $y = sin($i * PI / 180) * $width + $yc;
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
                    $this->drawAntialiasPixel($x, $y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
                }
                $Cpt++;
            } else {
                $this->drawAntialiasPixel($x, $y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
            }
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a filled circle
     * @param int $x
     * @param int $y
     * @param int|float $radius
     * @param array $format
     */
    public function drawFilledCircle($x, $y, $radius, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : -1;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : -1;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : -1;
        $borderalpha = isset($format["borderalpha"]) ? $format["borderalpha"] : $alpha;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
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
                    $x + $this->shadowX, $y + $this->shadowY, $radius, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowA,
                "ticks" => $ticks
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
                    $x, $y, $radius, $radius, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
            );
        }
        $this->Mask = [];
        if ($borderR != -1) {
            $this->drawCircle(
                    $x, $y, $radius, $radius, [
                "r" => $borderR,
                "g" => $borderG,
                "b" => $borderB,
                "alpha" => $borderalpha,
                "ticks" => $ticks
                    ]
            );
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Write text
     * @param int|float $x
     * @param int|float $y
     * @param string $text
     * @param array $format
     * @return array
     */
    public function drawText($x, $y, $text, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : $this->fontColorR;
        $g = isset($format["g"]) ? $format["g"] : $this->fontColorG;
        $b = isset($format["b"]) ? $format["b"] : $this->fontColorB;
        $angle = isset($format["angle"]) ? $format["angle"] : 0;
        $align = isset($format["align"]) ? $format["align"] : Constant::TEXT_ALIGN_BOTTOMLEFT;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : $this->fontColorA;
        $fontName = isset($format["fontName"]) ? $this->loadFont($format["fontName"], 'fonts') : $this->fontName;
        $fontSize = isset($format["fontSize"]) ? $format["fontSize"] : $this->fontSize;
        $showOrigine = isset($format["showOrigine"]) ? $format["showOrigine"] : false;
        $tOffset = isset($format["TOffset"]) ? $format["TOffset"] : 2;
        $drawBox = isset($format["drawBox"]) ? $format["drawBox"] : false;
        $borderOffset = isset($format["borderOffset"]) ? $format["borderOffset"] : 6;
        $boxRounded = isset($format["boxRounded"]) ? $format["boxRounded"] : false;
        $roundedRadius = isset($format["roundedRadius"]) ? $format["roundedRadius"] : 6;
        $boxR = isset($format["boxR"]) ? $format["boxR"] : 255;
        $boxG = isset($format["boxG"]) ? $format["boxG"] : 255;
        $boxB = isset($format["boxB"]) ? $format["boxB"] : 255;
        $boxalpha = isset($format["boxalpha"]) ? $format["boxalpha"] : 50;
        $boxSurrounding = isset($format["boxSurrounding"]) ? $format["boxSurrounding"] : "";
        $boxborderR = isset($format["boxR"]) ? $format["boxR"] : 0;
        $boxborderG = isset($format["boxG"]) ? $format["boxG"] : 0;
        $boxborderB = isset($format["boxB"]) ? $format["boxB"] : 0;
        $boxBorderalpha = isset($format["boxalpha"]) ? $format["boxalpha"] : 50;
        $NoShadow = isset($format["noShadow"]) ? $format["noShadow"] : false;
        $shadow = $this->shadow;
        if ($NoShadow) {
            $this->shadow = false;
        }
        if ($boxSurrounding != "") {
            $boxborderR = $boxR - $boxSurrounding;
            $boxborderG = $boxG - $boxSurrounding;
            $boxborderB = $boxB - $boxSurrounding;
            $boxBorderalpha = $boxalpha;
        }
        if ($showOrigine) {
            $myMarkerSettings = [
                "r" => 255,
                "g" => 0,
                "b" => 0,
                "borderR" => 255,
                "borderB" => 255,
                "borderG" => 255,
                "size" => 4
            ];
            $this->drawRectangleMarker($x, $y, $myMarkerSettings);
        }
        $txtPos = $this->getTextBox($x, $y, $fontName, $fontSize, $angle, $text);
        if ($drawBox && ($angle == 0 || $angle == 90 || $angle == 180 || $angle == 270)) {
            $t[0]["x"] = 0;
            $t[0]["y"] = 0;
            $t[1]["x"] = 0;
            $t[1]["y"] = 0;
            $t[2]["x"] = 0;
            $t[2]["y"] = 0;
            $t[3]["x"] = 0;
            $t[3]["y"] = 0;
            if ($angle == 0) {
                $t[0]["x"] = -$tOffset;
                $t[0]["y"] = $tOffset;
                $t[1]["x"] = $tOffset;
                $t[1]["y"] = $tOffset;
                $t[2]["x"] = $tOffset;
                $t[2]["y"] = -$tOffset;
                $t[3]["x"] = -$tOffset;
                $t[3]["y"] = -$tOffset;
            }
            $x1 = min($txtPos[0]["x"], $txtPos[1]["x"], $txtPos[2]["x"], $txtPos[3]["x"]) - $borderOffset + 3;
            $y1 = min($txtPos[0]["y"], $txtPos[1]["y"], $txtPos[2]["y"], $txtPos[3]["y"]) - $borderOffset;
            $x2 = max($txtPos[0]["x"], $txtPos[1]["x"], $txtPos[2]["x"], $txtPos[3]["x"]) + $borderOffset + 3;
            $y2 = max($txtPos[0]["y"], $txtPos[1]["y"], $txtPos[2]["y"], $txtPos[3]["y"]) + $borderOffset - 3;
            $x1 = $x1 - $txtPos[$align]["x"] + $x + $t[0]["x"];
            $y1 = $y1 - $txtPos[$align]["y"] + $y + $t[0]["y"];
            $x2 = $x2 - $txtPos[$align]["x"] + $x + $t[0]["x"];
            $y2 = $y2 - $txtPos[$align]["y"] + $y + $t[0]["y"];
            $settings = [
                "r" => $boxR,
                "g" => $boxG,
                "b" => $boxB,
                "alpha" => $boxalpha,
                "borderR" => $boxborderR,
                "borderG" => $boxborderG,
                "borderB" => $boxborderB,
                "borderalpha" => $boxBorderalpha
            ];
            if ($boxRounded) {
                $this->drawRoundedFilledRectangle($x1, $y1, $x2, $y2, $roundedRadius, $settings);
            } else {
                $this->drawFilledRectangle($x1, $y1, $x2, $y2, $settings);
            }
        }
        $x = $x - $txtPos[$align]["x"] + $x;
        $y = $y - $txtPos[$align]["y"] + $y;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $C_ShadowColor = $this->allocateColor(
                    $this->picture, $this->shadowR, $this->shadowG, $this->shadowB, $this->shadowA
            );
            imagettftext(
                    $this->picture, $fontSize, $angle, $x + $this->shadowX, $y + $this->shadowY, $C_ShadowColor, $fontName, $text
            );
        }
        $C_TextColor = $this->AllocateColor($this->picture, $r, $g, $b, $alpha);
        imagettftext($this->picture, $fontSize, $angle, $x, $y, $C_TextColor, $fontName, $text);
        $this->shadow = $shadow;
        return $txtPos;
    }

    /**
     * Draw a gradient within a defined area
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int $direction
     * @param array $format
     * @return null|integer
     */
    public function drawGradientArea($x1, $y1, $x2, $y2, $direction, array $format = []) {
        $startR = isset($format["startR"]) ? $format["startR"] : 90;
        $startG = isset($format["startG"]) ? $format["startG"] : 90;
        $startB = isset($format["startB"]) ? $format["startB"] : 90;
        $endR = isset($format["endR"]) ? $format["endR"] : 0;
        $endG = isset($format["endG"]) ? $format["endG"] : 0;
        $endB = isset($format["endB"]) ? $format["endB"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $levels = isset($format["levels"]) ? $format["levels"] : null;
        $shadow = $this->shadow;
        $this->shadow = false;
        if ($startR == $endR && $startG == $endG && $startB == $endB) {
            $this->drawFilledRectangle(
                    $x1, $y1, $x2, $y2, ["r" => $startR, "g" => $startG, "b" => $startB, "alpha" => $alpha]
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
                        $color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha];
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
                        $color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha];
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
     * @param int $x
     * @param int $y
     * @param array $format
     * @return int|null
     */
    public function drawAntialiasPixel($x, $y, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        if ($x < 0 || $y < 0 || $x >= $this->xSize || $y >= $this->ySize) {
            return -1;
        }
        if ($r < 0) {
            $r = 0;
        } if ($r > 255) {
            $r = 255;
        }
        if ($g < 0) {
            $g = 0;
        } if ($g > 255) {
            $g = 255;
        }
        if ($b < 0) {
            $b = 0;
        } if ($b > 255) {
            $b = 255;
        }
        if (!$this->antialias) {
            if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
                $shadowColor = $this->allocateColor(
                        $this->picture, $this->shadowR, $this->shadowG, $this->shadowB, $this->shadowA
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
     * @param int $x
     * @param int $y
     * @param int $alpha
     * @param int $r
     * @param int $g
     * @param int $b
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
        } if ($r > 255) {
            $r = 255;
        }
        if ($g < 0) {
            $g = 0;
        } if ($g > 255) {
            $g = 255;
        }
        if ($b < 0) {
            $b = 0;
        } if ($b > 255) {
            $b = 255;
        }
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $alphaFactor = floor(($alpha / 100) * $this->shadowA);
            $shadowColor = $this->allocateColor(
                    $this->picture, $this->shadowR, $this->shadowG, $this->shadowB, $alphaFactor
            );
            imagesetpixel($this->picture, $x + $this->shadowX, $y + $this->shadowY, $shadowColor);
        }
        $C_Aliased = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        imagesetpixel($this->picture, $x, $y, $C_Aliased);
    }

    /**
     * Load a PNG file and draw it over the chart
     * @param int $x
     * @param int $y
     * @param string $fileName
     */
    public function drawFromPNG($x, $y, $fileName) {
        $this->drawFromPicture(1, $fileName, $x, $y);
    }

    /**
     * Load a GIF file and draw it over the chart
     * @param int $x
     * @param int $y
     * @param string $fileName
     */
    public function drawFromGIF($x, $y, $fileName) {
        $this->drawFromPicture(2, $fileName, $x, $y);
    }

    /**
     * Load a JPEG file and draw it over the chart
     * @param int $x
     * @param int $y
     * @param string $fileName
     */
    public function drawFromJPG($x, $y, $fileName) {
        $this->drawFromPicture(3, $fileName, $x, $y);
    }

    /**
     * Generic loader public function for external pictures
     * @param int $picType
     * @param string $fileName
     * @param int $x
     * @param int $y
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
                            $x + $this->shadowX, $y + $this->shadowY, $x + $width + $this->shadowX, $y + $height + $this->shadowY, [
                        "r" => $this->shadowR,
                        "g" => $this->shadowG,
                        "b" => $this->shadowB,
                        "alpha" => $this->shadowA
                            ]
                    );
                } else {
                    $tranparentID = imagecolortransparent($raster);
                    for ($xc = 0; $xc <= $width - 1; $xc++) {
                        for ($yc = 0; $yc <= $height - 1; $yc++) {
                            $rGBa = imagecolorat($raster, $xc, $yc);
                            $values = imagecolorsforindex($raster, $rGBa);
                            if ($values["alpha"] < 120) {
                                $alphaFactor = floor(
                                        ($this->shadowA / 100) * ((100 / 127) * (127 - $values["alpha"]))
                                );
                                $this->drawalphaPixel(
                                        $x + $xc + $this->shadowX, $y + $yc + $this->shadowY, $alphaFactor, $this->shadowR, $this->shadowG, $this->shadowB
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
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param array $format
     */
    public function drawArrow($x1, $y1, $x2, $y2, array $format = []) {
        $fillR = isset($format["fillR"]) ? $format["fillR"] : 0;
        $fillG = isset($format["fillG"]) ? $format["fillG"] : 0;
        $fillB = isset($format["fillB"]) ? $format["fillB"] : 0;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $fillR;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $fillG;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $fillB;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $size = isset($format["size"]) ? $format["size"] : 10;
        $ratio = isset($format["ratio"]) ? $format["ratio"] : .5;
        $twoHeads = isset($format["TwoHeads"]) ? $format["TwoHeads"] : false;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : false;
        /* Calculate the line angle */
        $angle = $this->getAngle($x1, $y1, $x2, $y2);
        /* Override Shadow support, this will be managed internally */
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawArrow(
                    $x1 + $this->shadowX, $y1 + $this->shadowY, $x2 + $this->shadowX, $y2 + $this->shadowY, [
                "fillR" => $this->shadowR,
                "fillG" => $this->shadowG,
                "fillB" => $this->shadowB,
                "alpha" => $this->shadowA,
                "size" => $size,
                "ration" => $ratio,
                "TwoHeads" => $twoHeads,
                "ticks" => $ticks
                    ]
            );
        }
        /* Draw the 1st Head */
        $tailX = cos(($angle - 180) * PI / 180) * $size + $x2;
        $tailY = sin(($angle - 180) * PI / 180) * $size + $y2;
        $points = [];
        $points[] = $x2;
        $points[] = $y2;
        $points[] = cos(($angle - 90) * PI / 180) * $size * $ratio + $tailX;
        $points[] = sin(($angle - 90) * PI / 180) * $size * $ratio + $tailY;
        $points[] = cos(($angle - 270) * PI / 180) * $size * $ratio + $tailX;
        $points[] = sin(($angle - 270) * PI / 180) * $size * $ratio + $tailY;
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
                $points[0], $points[1], $points[2], $points[3], ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
        );
        $this->drawLine(
                $points[2], $points[3], $points[4], $points[5], ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
        );
        $this->drawLine(
                $points[0], $points[1], $points[4], $points[5], ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
        );
        /* Draw the second head */
        if ($twoHeads) {
            $angle = $this->getAngle($x2, $y2, $x1, $y1);
            $tailX2 = cos(($angle - 180) * PI / 180) * $size + $x1;
            $tailY2 = sin(($angle - 180) * PI / 180) * $size + $y1;
            $points = [];
            $points[] = $x1;
            $points[] = $y1;
            $points[] = cos(($angle - 90) * PI / 180) * $size * $ratio + $tailX2;
            $points[] = sin(($angle - 90) * PI / 180) * $size * $ratio + $tailY2;
            $points[] = cos(($angle - 270) * PI / 180) * $size * $ratio + $tailX2;
            $points[] = sin(($angle - 270) * PI / 180) * $size * $ratio + $tailY2;
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
                    $points[0], $points[1], $points[2], $points[3], ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
            );
            $this->drawLine(
                    $points[2], $points[3], $points[4], $points[5], ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
            );
            $this->drawLine(
                    $points[0], $points[1], $points[4], $points[5], ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
            );
            $this->drawLine(
                    $tailX, $tailY, $tailX2, $tailY2, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha, "ticks" => $ticks]
            );
        } else {
            $this->drawLine(
                    $x1, $y1, $tailX, $tailY, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha, "ticks" => $ticks]
            );
        }
        /* Re-enable shadows */
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a label with associated arrow
     * @param int $x1
     * @param int $y1
     * @param string $text
     * @param array $format
     */
    public function drawArrowLabel($x1, $y1, $text, array $format = []) {
        $fillR = isset($format["fillR"]) ? $format["fillR"] : 0;
        $fillG = isset($format["fillG"]) ? $format["fillG"] : 0;
        $fillB = isset($format["fillB"]) ? $format["fillB"] : 0;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $fillR;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $fillG;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $fillB;
        $fontName = isset($format["fontName"]) ? $this->loadFont($format["fontName"], 'fonts') : $this->fontName;
        $fontSize = isset($format["fontSize"]) ? $format["fontSize"] : $this->fontSize;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $length = isset($format["length"]) ? $format["length"] : 50;
        $angle = isset($format["angle"]) ? $format["angle"] : 315;
        $size = isset($format["size"]) ? $format["size"] : 10;
        $position = isset($format["position"]) ? $format["position"] : POSITION_TOP;
        $roundPos = isset($format["roundPos"]) ? $format["roundPos"] : false;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $angle = $angle % 360;
        $x2 = sin(($angle + 180) * PI / 180) * $length + $x1;
        $y2 = cos(($angle + 180) * PI / 180) * $length + $y1;
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
                    $x2, $y2, $x2 - $txtWidth, $y2, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha, "ticks" => $ticks]
            );
            if ($position == Constant::POSITION_TOP) {
                $this->drawText(
                        $x2, $y2 - 2, $text, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $alpha,
                    "align" => Constant::TEXT_ALIGN_BOTTOMRIGHT
                        ]
                );
            } else {
                $this->drawText(
                        $x2, $y2 + 4, $text, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $alpha,
                    "align" => TEXT_ALIGN_TOPRIGHT
                        ]
                );
            }
        } else {
            $this->drawLine(
                    $x2, $y2, $x2 + $txtWidth, $y2, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha, "ticks" => $ticks]
            );
            if ($position == POSITION_TOP) {
                $this->drawText(
                        $x2, $y2 - 2, $text, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
                );
            } else {
                $this->drawText(
                        $x2, $y2 + 4, $text, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $alpha,
                    "align" => TEXT_ALIGN_TOPLEFT
                        ]
                );
            }
        }
    }

    /**
     * Draw a progress bar filled with specified %
     * @param int $x
     * @param int $y
     * @param int|float $percent
     * @param array $format
     */
    public function drawProgress($x, $y, $percent, array $format = []) {
        if ($percent > 100) {
            $percent = 100;
        }
        if ($percent < 0) {
            $percent = 0;
        }
        $width = isset($format["width"]) ? $format["width"] : 200;
        $height = isset($format["height"]) ? $format["height"] : 20;
        $orientation = isset($format["orientation"]) ? $format["orientation"] : Constant::ORIENTATION_HORIZONTAL;
        $showLabel = isset($format["showLabel"]) ? $format["showLabel"] : false;
        $labelPos = isset($format["labelPos"]) ? $format["labelPos"] : Constant::LABEL_POS_INSIDE;
        $margin = isset($format["margin"]) ? $format["margin"] : 10;
        $r = isset($format["r"]) ? $format["r"] : 130;
        $g = isset($format["g"]) ? $format["g"] : 130;
        $b = isset($format["b"]) ? $format["b"] : 130;
        $rFade = isset($format["rFade"]) ? $format["rFade"] : -1;
        $gFade = isset($format["gFade"]) ? $format["gFade"] : -1;
        $bFade = isset($format["bFade"]) ? $format["bFade"] : -1;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $r;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $g;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $b;
        $boxborderR = isset($format["boxborderR"]) ? $format["boxborderR"] : 0;
        $boxborderG = isset($format["boxborderG"]) ? $format["boxborderG"] : 0;
        $boxborderB = isset($format["boxborderB"]) ? $format["boxborderB"] : 0;
        $boxBackR = isset($format["boxBackR"]) ? $format["boxBackR"] : 255;
        $boxBackG = isset($format["boxBackG"]) ? $format["boxBackG"] : 255;
        $boxBackB = isset($format["boxBackB"]) ? $format["boxBackB"] : 255;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $boxSurrounding = isset($format["boxSurrounding"]) ? $format["boxSurrounding"] : null;
        $noAngle = isset($format["noAngle"]) ? $format["noAngle"] : false;
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
        if ($orientation == ORIENTATION_VERTICAL) {
            $InnerHeight = (($height - 2) / 100) * $percent;
            $this->drawFilledRectangle(
                    $x, $y, $x + $width, $y - $height, [
                "r" => $boxBackR,
                "g" => $boxBackG,
                "b" => $boxBackB,
                "borderR" => $boxborderR,
                "borderG" => $boxborderG,
                "borderB" => $boxborderB,
                "noAngle" => $noAngle
                    ]
            );
            $restoreShadow = $this->shadow;
            $this->shadow = false;
            if ($rFade != -1 && $gFade != -1 && $bFade != -1) {
                $gradientOptions = [
                    "StartR" => $rFade,
                    "StartG" => $gFade,
                    "StartB" => $bFade,
                    "endR" => $r,
                    "endG" => $g,
                    "endB" => $b
                ];
                $this->drawGradientArea(
                        $x + 1, $y - 1, $x + $width - 1, $y - $InnerHeight, DIRECTION_VERTICAL, $gradientOptions
                );
                if ($surrounding) {
                    $this->drawRectangle(
                            $x + 1, $y - 1, $x + $width - 1, $y - $InnerHeight, ["r" => 255, "g" => 255, "b" => 255, "alpha" => $surrounding]
                    );
                }
            } else {
                $this->drawFilledRectangle(
                        $x + 1, $y - 1, $x + $width - 1, $y - $InnerHeight, [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "borderR" => $borderR,
                    "borderG" => $borderG,
                    "borderB" => $borderB
                        ]
                );
            }
            $this->shadow = $restoreShadow;
            if ($showLabel && $labelPos == LABEL_POS_BOTTOM) {
                $this->drawText(
                        $x + ($width / 2), $y + $margin, $percent . "%", ["align" => TEXT_ALIGN_TOPMIDDLE]
                );
            }
            if ($showLabel && $labelPos == LABEL_POS_TOP) {
                $this->drawText(
                        $x + ($width / 2), $y - $height - $margin, $percent . "%", ["align" => TEXT_ALIGN_BOTTOMMIDDLE]
                );
            }
            if ($showLabel && $labelPos == LABEL_POS_INSIDE) {
                $this->drawText(
                        $x + ($width / 2), $y - $InnerHeight - $margin, $percent . "%", ["align" => TEXT_ALIGN_MIDDLELEFT, "Angle" => 90]
                );
            }
            if ($showLabel && $labelPos == LABEL_POS_CENTER) {
                $this->drawText(
                        $x + ($width / 2), $y - ($height / 2), $percent . "%", ["align" => TEXT_ALIGN_MIDDLEMIDDLE, "Angle" => 90]
                );
            }
        } else {
            if ($percent == 100) {
                $InnerWidth = $width - 1;
            } else {
                $InnerWidth = (($width - 2) / 100) * $percent;
            }
            $this->drawFilledRectangle(
                    $x, $y, $x + $width, $y + $height, [
                "r" => $boxBackR,
                "g" => $boxBackG,
                "b" => $boxBackB,
                "borderR" => $boxborderR,
                "borderG" => $boxborderG,
                "borderB" => $boxborderB,
                "noAngle" => $noAngle
                    ]
            );
            $restoreShadow = $this->shadow;
            $this->shadow = false;
            if ($rFade != -1 && $gFade != -1 && $bFade != -1) {
                $gradientOptions = [
                    "StartR" => $r,
                    "StartG" => $g,
                    "StartB" => $b,
                    "endR" => $rFade,
                    "endG" => $gFade,
                    "endB" => $bFade
                ];
                $this->drawGradientArea(
                        $x + 1, $y + 1, $x + $InnerWidth, $y + $height - 1, DIRECTION_HORIZONTAL, $gradientOptions
                );
                if ($surrounding) {
                    $this->drawRectangle(
                            $x + 1, $y + 1, $x + $InnerWidth, $y + $height - 1, ["r" => 255, "g" => 255, "b" => 255, "alpha" => $surrounding]
                    );
                }
            } else {
                $this->drawFilledRectangle(
                        $x + 1, $y + 1, $x + $InnerWidth, $y + $height - 1, [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "borderR" => $borderR, "borderG" => $borderG, "borderB" => $borderB
                        ]
                );
            }
            $this->shadow = $restoreShadow;
            if ($showLabel && $labelPos == LABEL_POS_LEFT) {
                $this->drawText(
                        $x - $margin, $y + ($height / 2), $percent . "%", ["align" => TEXT_ALIGN_MIDDLERIGHT]
                );
            }
            if ($showLabel && $labelPos == LABEL_POS_RIGHT) {
                $this->drawText(
                        $x + $width + $margin, $y + ($height / 2), $percent . "%", ["align" => TEXT_ALIGN_MIDDLELEFT]
                );
            }
            if ($showLabel && $labelPos == LABEL_POS_CENTER) {
                $this->drawText(
                        $x + ($width / 2), $y + ($height / 2), $percent . "%", ["align" => TEXT_ALIGN_MIDDLEMIDDLE]
                );
            }
            if ($showLabel && $labelPos == LABEL_POS_INSIDE) {
                $this->drawText(
                        $x + $InnerWidth + $margin, $y + ($height / 2), $percent . "%", ["align" => TEXT_ALIGN_MIDDLELEFT]
                );
            }
        }
    }

    /**
     * Draw the legend of the active series
     * @param int $x
     * @param int $y
     * @param array $format
     */
    public function drawLegend($x, $y, array $format = []) {
        $family = isset($format["family"]) ? $format["family"] : Constant::LEGEND_FAMILY_BOX;
        $fontName = isset($format["fontName"]) ? $this->loadFont($format["fontName"], 'fonts') : $this->fontName;
        $fontSize = isset($format["fontSize"]) ? $format["fontSize"] : $this->fontSize;
        $fontR = isset($format["fontR"]) ? $format["fontR"] : $this->fontColorR;
        $fontG = isset($format["fontG"]) ? $format["fontG"] : $this->fontColorG;
        $fontB = isset($format["fontB"]) ? $format["fontB"] : $this->fontColorB;
        $boxWidth = isset($format["boxWidth"]) ? $format["boxWidth"] : 5;
        $boxHeight = isset($format["boxHeight"]) ? $format["boxHeight"] : 5;
        $iconAreaWidth = isset($format["iconAreaWidth"]) ? $format["iconAreaWidth"] : $boxWidth;
        $iconAreaHeight = isset($format["iconAreaHeight"]) ? $format["iconAreaHeight"] : $boxHeight;
        $xSpacing = isset($format["xSpacing"]) ? $format["xSpacing"] : 5;
        $margin = isset($format["margin"]) ? $format["margin"] : 5;
        $r = isset($format["r"]) ? $format["r"] : 200;
        $g = isset($format["g"]) ? $format["g"] : 200;
        $b = isset($format["b"]) ? $format["b"] : 200;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : 255;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : 255;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : 255;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $style = isset($format["style"]) ? $format["style"] : Constant::LEGEND_ROUND;
        $mode = isset($format["mode"]) ? $format["mode"] : Constant::LEGEND_VERTICAL;
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        $data = $this->dataSet->getData();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"] && isset($serie["picture"])
            ) {
                list($picWidth, $picHeight) = $this->getPicInfo($serie["picture"]);
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
        $boundaries["l"] = $x;
        $boundaries["T"] = $y;
        $boundaries["r"] = 0;
        $boundaries["b"] = 0;
        $vY = $y;
        $vX = $x;
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                if ($mode == Constant::LEGEND_VERTICAL) {
                    $boxArray = $this->getTextBox(
                            $vX + $iconAreaWidth + 4, $vY + $iconAreaHeight / 2, $fontName, $fontSize, 0, $serie["description"]
                    );
                    if ($boundaries["T"] > $boxArray[2]["y"] + $iconAreaHeight / 2) {
                        $boundaries["T"] = $boxArray[2]["y"] + $iconAreaHeight / 2;
                    }
                    if ($boundaries["r"] < $boxArray[1]["x"] + 2) {
                        $boundaries["r"] = $boxArray[1]["x"] + 2;
                    }
                    if ($boundaries["b"] < $boxArray[1]["y"] + 2 + $iconAreaHeight / 2) {
                        $boundaries["b"] = $boxArray[1]["y"] + 2 + $iconAreaHeight / 2;
                    }
                    $lines = preg_split("/\n/", $serie["description"]);
                    $vY = $vY + max($this->fontSize * count($lines), $iconAreaHeight) + 5;
                } elseif ($mode == Constant::LEGEND_HORIZONTAL) {
                    $lines = preg_split("/\n/", $serie["description"]);
                    $width = [];
                    foreach ($lines as $key => $value) {
                        $boxArray = $this->getTextBox(
                                $vX + $iconAreaWidth + 6, $y + $iconAreaHeight / 2 + (($this->fontSize + 3) * $key), $fontName, $fontSize, 0, $value
                        );
                        if ($boundaries["T"] > $boxArray[2]["y"] + $iconAreaHeight / 2) {
                            $boundaries["T"] = $boxArray[2]["y"] + $iconAreaHeight / 2;
                        }
                        if ($boundaries["r"] < $boxArray[1]["x"] + 2) {
                            $boundaries["r"] = $boxArray[1]["x"] + 2;
                        }
                        if ($boundaries["b"] < $boxArray[1]["y"] + 2 + $iconAreaHeight / 2) {
                            $boundaries["b"] = $boxArray[1]["y"] + 2 + $iconAreaHeight / 2;
                        }
                        $width[] = $boxArray[1]["x"];
                    }
                    $vX = max($width) + $xStep;
                }
            }
        }
        $vY = $vY - $yStep;
        $vX = $vX - $xStep;
        $topOffset = $y - $boundaries["T"];
        if ($boundaries["b"] - ($vY + $iconAreaHeight) < $topOffset) {
            $boundaries["b"] = $vY + $iconAreaHeight + $topOffset;
        }
        if ($style == Constant::LEGEND_ROUND) {
            $this->drawRoundedFilledRectangle(
                    $boundaries["l"] - $margin, $boundaries["T"] - $margin, $boundaries["r"] + $margin, $boundaries["b"] + $margin, $margin, [
                "r" => $r,
                "g" => $g,
                "b" => $b,
                "alpha" => $alpha,
                "borderR" => $borderR,
                "borderG" => $borderG,
                "borderB" => $borderB
                    ]
            );
        } elseif ($style == Constant::LEGEND_BOX) {
            $this->drawFilledRectangle(
                    $boundaries["l"] - $margin, $boundaries["T"] - $margin, $boundaries["r"] + $margin, $boundaries["b"] + $margin, [
                "r" => $r,
                "g" => $g,
                "b" => $b,
                "alpha" => $alpha,
                "borderR" => $borderR,
                "borderG" => $borderG,
                "borderB" => $borderB
                    ]
            );
        }
        $restoreShadow = $this->shadow;
        $this->shadow = false;
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $ticks = $serie["ticks"];
                $weight = $serie["weight"];
                if (isset($serie["picture"])) {
                    $picture = $serie["picture"];
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
                                $x + 1 + $xOffset, $y + 1 + $yOffset, $x + $boxWidth + $xOffset + 1, $y + $boxHeight + 1 + $yOffset, ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20]
                        );
                        $this->drawFilledRectangle(
                                $x + $xOffset, $y + $yOffset, $x + $boxWidth + $xOffset, $y + $boxHeight + $yOffset, ["r" => $r, "g" => $g, "b" => $b, "surrounding" => 20]
                        );
                    } elseif ($family == LEGEND_FAMILY_CIRCLE) {
                        $this->drawFilledCircle(
                                $x + 1 + $iconAreaWidth / 2, $y + 1 + $iconAreaHeight / 2, min($iconAreaHeight / 2, $iconAreaWidth / 2), ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20]
                        );
                        $this->drawFilledCircle(
                                $x + $iconAreaWidth / 2, $y + $iconAreaHeight / 2, min($iconAreaHeight / 2, $iconAreaWidth / 2), ["r" => $r, "g" => $g, "b" => $b, "surrounding" => 20]
                        );
                    } elseif ($family == Constant::LEGEND_FAMILY_LINE) {
                        $this->drawLine(
                                $x + 1, $y + 1 + $iconAreaHeight / 2, $x + 1 + $iconAreaWidth, $y + 1 + $iconAreaHeight / 2, ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20, "ticks" => $ticks, "weight" => $weight]
                        );
                        $this->drawLine(
                                $x, $y + $iconAreaHeight / 2, $x + $iconAreaWidth, $y + $iconAreaHeight / 2, ["r" => $r, "g" => $g, "b" => $b, "ticks" => $ticks, "weight" => $weight]
                        );
                    }
                }
                if ($mode == Constant::LEGEND_VERTICAL) {
                    $lines = preg_split("/\n/", $serie["description"]);
                    foreach ($lines as $key => $value) {
                        $this->drawText(
                                $x + $iconAreaWidth + 4, $y + $iconAreaHeight / 2 + (($this->fontSize + 3) * $key), $value, [
                            "r" => $fontR,
                            "g" => $fontG,
                            "b" => $fontB,
                            "align" => Constant::TEXT_ALIGN_MIDDLELEFT,
                            "fontSize" => $fontSize,
                            "fontName" => $fontName
                                ]
                        );
                    }
                    $y = $y + max($this->fontSize * count($lines), $iconAreaHeight) + 5;
                } elseif ($mode == Constant::LEGEND_HORIZONTAL) {
                    $lines = preg_split("/\n/", $serie["description"]);
                    $width = [];
                    foreach ($lines as $key => $value) {
                        $boxArray = $this->drawText(
                                $x + $iconAreaWidth + 4, $y + $iconAreaHeight / 2 + (($this->fontSize + 3) * $key), $value, [
                            "r" => $fontR,
                            "g" => $fontG,
                            "b" => $fontB,
                            "align" => Constant::TEXT_ALIGN_MIDDLELEFT,
                            "fontSize" => $fontSize,
                            "fontName" => $fontName
                                ]
                        );
                        $width[] = $boxArray[1]["x"];
                    }
                    $x = max($width) + 2 + $xStep;
                }
            }
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * @param array $format
     * @throws Exception
     */
    public function drawScale(array $format = []) {
        $pos = isset($format["pos"]) ? $format["pos"] : Constant::SCALE_POS_LEFTRIGHT;
        $floating = isset($format["floating"]) ? $format["floating"] : false;
        $mode = isset($format["mode"]) ? $format["mode"] : Constant::SCALE_MODE_FLOATING;
        $removeXAxis = isset($format["removeXAxis"]) ? $format["removeXAxis"] : false;
        $removeYAxis = isset($format["removeYAxis"]) ? $format["removeYAxis"] : false;
        $removeYAxiValues = isset($format["removeYAxisValues"]) ? $format["removeYAxisValues"] : false;
        $minDivHeight = isset($format["minDivHeight"]) ? $format["minDivHeight"] : 20;
        $factors = isset($format["factors"]) ? $format["factors"] : [1, 2, 5];
        $manualScale = isset($format["manualScale"]) ? $format["manualScale"] : ["0" => ["min" => -100, "max" => 100]]
        ;
        $xMargin = isset($format["xMargin"]) ? $format["xMargin"] : Constant::AUTO;
        $yMargin = isset($format["yMargin"]) ? $format["yMargin"] : 0;
        $scaleSpacing = isset($format["scaleSpacing"]) ? $format["scaleSpacing"] : 15;
        $InnerTickWidth = isset($format["innerTickWidth"]) ? $format["innerTickWidth"] : 2;
        $outerTickWidth = isset($format["outerTickWidth"]) ? $format["outerTickWidth"] : 2;
        $drawXLines = isset($format["drawXLines"]) ? $format["drawXLines"] : true;
        $drawYLines = isset($format["drawYLines"]) ? $format["drawYLines"] : Constant::ALL;
        $gridTicks = isset($format["gridTicks"]) ? $format["gridTicks"] : 4;
        $gridR = isset($format["gridR"]) ? $format["gridR"] : 255;
        $gridG = isset($format["gridG"]) ? $format["gridG"] : 255;
        $gridB = isset($format["gridB"]) ? $format["gridB"] : 255;
        $gridalpha = isset($format["gridalpha"]) ? $format["gridalpha"] : 40;
        $axisRo = isset($format["axisR"]) ? $format["axisR"] : 0;
        $axisGo = isset($format["axisG"]) ? $format["axisG"] : 0;
        $axisBo = isset($format["axisB"]) ? $format["axisB"] : 0;
        $axisalpha = isset($format["axisalpha"]) ? $format["axisalpha"] : 100;
        $tickRo = isset($format["TickR"]) ? $format["TickR"] : 0;
        $tickGo = isset($format["TickG"]) ? $format["TickG"] : 0;
        $tickBo = isset($format["TickB"]) ? $format["TickB"] : 0;
        $tickalpha = isset($format["Tickalpha"]) ? $format["Tickalpha"] : 100;
        $drawSubTicks = isset($format["drawSubTicks"]) ? $format["drawSubTicks"] : false;
        $InnerSubTickWidth = isset($format["innerSubTickWidth"]) ? $format["innerSubTickWidth"] : 0;
        $outerSubTickWidth = isset($format["outerSubTickWidth"]) ? $format["outerSubTickWidth"] : 2;
        $subTickR = isset($format["subTickR"]) ? $format["subTickR"] : 255;
        $subTickG = isset($format["subTickG"]) ? $format["subTickG"] : 0;
        $subTickB = isset($format["subTickB"]) ? $format["subTickB"] : 0;
        $subTickalpha = isset($format["subTickalpha"]) ? $format["subTickalpha"] : 100;
        $autoAxisLabels = isset($format["autoAxisLabels"]) ? $format["autoAxisLabels"] : true;
        $xReleasePercent = isset($format["xReleasePercent"]) ? $format["xReleasePercent"] : 1;
        $drawArrows = isset($format["drawArrows"]) ? $format["drawArrows"] : false;
        $arrowSize = isset($format["arrowSize"]) ? $format["arrowSize"] : 8;
        $CycleBackground = isset($format["CycleBackground"]) ? $format["CycleBackground"] : false;
        $backgroundR1 = isset($format["backgroundR1"]) ? $format["backgroundR1"] : 255;
        $backgroundG1 = isset($format["backgroundG1"]) ? $format["backgroundG1"] : 255;
        $backgroundB1 = isset($format["backgroundB1"]) ? $format["backgroundB1"] : 255;
        $backgroundalpha1 = isset($format["backgroundalpha1"]) ? $format["backgroundalpha1"] : 20;
        $backgroundR2 = isset($format["backgroundR2"]) ? $format["backgroundR2"] : 230;
        $backgroundG2 = isset($format["backgroundG2"]) ? $format["backgroundG2"] : 230;
        $backgroundB2 = isset($format["backgroundB2"]) ? $format["backgroundB2"] : 230;
        $backgroundalpha2 = isset($format["backgroundalpha2"]) ? $format["backgroundalpha2"] : 20;
        $labelingMethod = isset($format["labelingMethod"]) ? $format["labelingMethod"] : Constant::LABELING_ALL;
        $labelSkip = isset($format["labelSkip"]) ? $format["labelSkip"] : 0;
        $labelRotation = isset($format["labelRotation"]) ? $format["labelRotation"] : 0;
        $removeSkippedAxis = isset($format["removeSkippedAxis"]) ? $format["removeSkippedAxis"] : false;
        $skippedAxisTicks = isset($format["skippedAxisTicks"]) ? $format["skippedAxisTicks"] : $gridTicks + 2;
        $skippedAxisR = isset($format["skippedAxisR"]) ? $format["skippedAxisR"] : $gridR;
        $skippedAxisG = isset($format["skippedAxisG"]) ? $format["skippedAxisG"] : $gridG;
        $skippedAxisB = isset($format["skippedAxisB"]) ? $format["skippedAxisB"] : $gridB;
        $skippedAxisalpha = isset($format["skippedAxisalpha"]) ? $format["skippedAxisalpha"] : $gridalpha - 30;
        $skippedTickR = isset($format["skippedTickR"]) ? $format["skippedTickR"] : $tickRo;
        $skippedTickG = isset($format["skippedTickG"]) ? $format["skippedTickG"] : $tickGo;
        $skippedTickB = isset($format["skippedTicksB"]) ? $format["skippedTickB"] : $tickBo;
        $skippedTickalpha = isset($format["skippedTickalpha"]) ? $format["skippedTickalpha"] : $tickalpha - 80;
        $skippedInnerTickWidth = isset($format["skippedInnerTickWidth"]) ? $format["skippedInnerTickWidth"] : 0;
        $skippedOuterTickWidth = isset($format["skippedOuterTickWidth"]) ? $format["skippedOuterTickWidth"] : 2;
        /* Floating scale require X & Y margins to be set manually */
        if ($floating && ($xMargin == Constant::AUTO || $yMargin == 0)) {
            $floating = false;
        }
        /* Skip a NOTICE event in case of an empty array */
        if ($drawYLines == Constant::NONE || $drawYLines == false) {
            $drawYLines = ["zarma" => "31"];
        }
        /* Define the color for the skipped elements */
        $skippedAxisColor = [
            "r" => $skippedAxisR,
            "g" => $skippedAxisG,
            "b" => $skippedAxisB,
            "alpha" => $skippedAxisalpha,
            "ticks" => $skippedAxisTicks
        ];
        $skippedTickColor = [
            "r" => $skippedTickR,
            "g" => $skippedTickG,
            "b" => $skippedTickB,
            "alpha" => $skippedTickalpha
        ];
        $data = $this->dataSet->getData();
        $abscissa = null;
        if (isset($data["abscissa"])) {
            $abscissa = $data["abscissa"];
        }
        /* Unset the abscissa axis, needed if we display multiple charts on the same picture */
        if ($abscissa != null) {
            foreach ($data["axis"] as $axisId => $parameters) {
                if ($parameters["identity"] == Constant::AXIS_X) {
                    unset($data["axis"][$axisId]);
                }
            }
        }
        /* Build the scale settings */
        $gotXAxis = false;
        foreach ($data["axis"] as $axisId => $axisParameter) {
            if ($axisParameter["identity"] == Constant::AXIS_X) {
                $gotXAxis = true;
            }
            if ($pos == Constant::SCALE_POS_LEFTRIGHT && $axisParameter["identity"] == Constant::AXIS_Y) {
                $height = $this->graphAreaY2 - $this->graphAreaY1 - $yMargin * 2;
            } elseif ($pos == Constant::SCALE_POS_LEFTRIGHT && $axisParameter["identity"] == Constant::AXIS_X) {
                $height = $this->graphAreaX2 - $this->graphAreaX1;
            } elseif ($pos == Constant::SCALE_POS_TOPBOTTOM && $axisParameter["identity"] == Constant::AXIS_Y) {
                $height = $this->graphAreaX2 - $this->graphAreaX1 - $yMargin * 2;
                ;
            } else {
                $height = $this->graphAreaY2 - $this->graphAreaY1;
            }
            $axisMin = Constant::ABSOLUTE_MAX;
            $axisMax = Constant::OUT_OF_SIGHT;
            if ($mode == Constant::SCALE_MODE_FLOATING || $mode == Constant::SCALE_MODE_START0) {
                foreach ($data["series"] as $serieID => $serieParameter) {
                    if ($serieParameter["axis"] == $axisId && $data["series"][$serieID]["isDrawable"] && $data["abscissa"] != $serieID
                    ) {
                        $axisMax = max($axisMax, $data["series"][$serieID]["max"]);
                        $axisMin = min($axisMin, $data["series"][$serieID]["min"]);
                    }
                }
                $autoMargin = (($axisMax - $axisMin) / 100) * $xReleasePercent;
                $data["axis"][$axisId]["min"] = $axisMin - $autoMargin;
                $data["axis"][$axisId]["max"] = $axisMax + $autoMargin;
                if ($mode == Constant::SCALE_MODE_START0) {
                    $data["axis"][$axisId]["min"] = 0;
                }
            } elseif ($mode == Constant::SCALE_MODE_MANUAL) {
                if (isset($manualScale[$axisId]["min"]) && isset($manualScale[$axisId]["max"])) {
                    $data["axis"][$axisId]["min"] = $manualScale[$axisId]["min"];
                    $data["axis"][$axisId]["max"] = $manualScale[$axisId]["max"];
                } else {
                    throw new Exception("Manual scale boundaries not set.");
                }
            } elseif ($mode == Constant::SCALE_MODE_ADDALL || $mode == Constant::SCALE_MODE_ADDALL_START0) {
                $series = [];
                foreach ($data["series"] as $serieID => $serieParameter) {
                    if ($serieParameter["axis"] == $axisId && $serieParameter["isDrawable"] && $data["abscissa"] != $serieID
                    ) {
                        $series[$serieID] = count($data["series"][$serieID]["data"]);
                    }
                }
                for ($id = 0; $id <= max($series) - 1; $id++) {
                    $pointMin = 0;
                    $pointMax = 0;
                    foreach ($series as $serieID => $valuesCount) {
                        if (isset($data["series"][$serieID]["data"][$id]) && $data["series"][$serieID]["data"][$id] != null
                        ) {
                            $value = $data["series"][$serieID]["data"][$id];
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
                $data["axis"][$axisId]["min"] = $axisMin - $autoMargin;
                $data["axis"][$axisId]["max"] = $axisMax + $autoMargin;
            }
            $maxDivs = floor($height / $minDivHeight);
            if ($mode == Constant::SCALE_MODE_ADDALL_START0) {
                $data["axis"][$axisId]["min"] = 0;
            }
            $scale = $this->computeScale(
                    $data["axis"][$axisId]["min"], $data["axis"][$axisId]["max"], $maxDivs, $factors, $axisId
            );
            $data["axis"][$axisId]["margin"] = $axisParameter["identity"] == Constant::AXIS_X ? $xMargin : $yMargin;
            $data["axis"][$axisId]["scaleMin"] = $scale["xMin"];
            $data["axis"][$axisId]["scaleMax"] = $scale["xMax"];
            $data["axis"][$axisId]["rows"] = $scale["rows"];
            $data["axis"][$axisId]["rowHeight"] = $scale["rowHeight"];
            if (isset($scale["format"])) {
                $data["axis"][$axisId]["format"] = $scale["format"];
            }
            if (!isset($data["axis"][$axisId]["display"])) {
                $data["axis"][$axisId]["display"] = null;
            }
            if (!isset($data["axis"][$axisId]["format"])) {
                $data["axis"][$axisId]["format"] = null;
            }
            if (!isset($data["axis"][$axisId]["unit"])) {
                $data["axis"][$axisId]["unit"] = null;
            }
        }
        /* Still no X axis */
        if ($gotXAxis == false) {
            if ($abscissa != null) {
                $points = count($data["series"][$abscissa]["data"]);
                $axisName = null;
                if ($autoAxisLabels) {
                    $axisName = isset($data["series"][$abscissa]["description"]) ? $data["series"][$abscissa]["description"] : null
                    ;
                }
            } else {
                $points = 0;
                $axisName = isset($data["xAxisName"]) ? $data["xAxisName"] : null;
                foreach ($data["series"] as $serieID => $serieParameter) {
                    if ($serieParameter["isDrawable"]) {
                        $points = max($points, count($serieParameter["data"]));
                    }
                }
            }
            $axisId = count($data["axis"]);
            $data["axis"][$axisId]["identity"] = Constant::AXIS_X;
            if ($pos == Constant::SCALE_POS_LEFTRIGHT) {
                $data["axis"][$axisId]["position"] = Constant::AXIS_POSITION_BOTTOM;
            } else {
                $data["axis"][$axisId]["position"] = Constant::AXIS_POSITION_LEFT;
            }
            if (isset($data["abscissaName"])) {
                $data["axis"][$axisId]["name"] = $data["abscissaName"];
            }
            if ($xMargin == Constant::AUTO) {
                if ($pos == Constant::SCALE_POS_LEFTRIGHT) {
                    $height = $this->graphAreaX2 - $this->graphAreaX1;
                } else {
                    $height = $this->graphAreaY2 - $this->graphAreaY1;
                }
                if ($points == 0 || $points == 1) {
                    $data["axis"][$axisId]["margin"] = $height / 2;
                } else {
                    $data["axis"][$axisId]["margin"] = ($height / $points) / 2;
                }
            } else {
                $data["axis"][$axisId]["margin"] = $xMargin;
            }
            $data["axis"][$axisId]["rows"] = $points - 1;
            if (!isset($data["axis"][$axisId]["display"])) {
                $data["axis"][$axisId]["display"] = null;
            }
            if (!isset($data["axis"][$axisId]["format"])) {
                $data["axis"][$axisId]["format"] = null;
            }
            if (!isset($data["axis"][$axisId]["unit"])) {
                $data["axis"][$axisId]["unit"] = null;
            }
        }
        /* Do we need to reverse the abscissa position? */
        if ($pos != Constant::SCALE_POS_LEFTRIGHT) {
            $data["absicssaPosition"] = Constant::AXIS_POSITION_RIGHT;
            if ($data["absicssaPosition"] == Constant::AXIS_POSITION_BOTTOM) {
                $data["absicssaPosition"] = Constant::AXIS_POSITION_LEFT;
            }
        }
        $data["axis"][$axisId]["position"] = $data["absicssaPosition"];
        $this->dataSet->saveOrientation($pos);
        $this->dataSet->saveAxisConfig($data["axis"]);
        $this->dataSet->saveYMargin($yMargin);
        $fontColorRo = $this->fontColorR;
        $fontColorGo = $this->fontColorG;
        $fontColorBo = $this->fontColorB;
        $axisPos["l"] = $this->graphAreaX1;
        $axisPos["r"] = $this->graphAreaX2;
        $axisPos["T"] = $this->graphAreaY1;
        $axisPos["b"] = $this->graphAreaY2;
        foreach ($data["axis"] as $axisId => $parameters) {
            if (isset($parameters["color"])) {
                $axisR = $parameters["color"]["r"];
                $axisG = $parameters["color"]["g"];
                $axisB = $parameters["color"]["b"];
                $tickR = $parameters["color"]["r"];
                $tickG = $parameters["color"]["g"];
                $tickB = $parameters["color"]["b"];
                $this->setFontProperties(
                        [
                            "r" => $parameters["color"]["r"],
                            "g" => $parameters["color"]["g"],
                            "b" => $parameters["color"]["b"]
                        ]
                );
            } else {
                $axisR = $axisRo;
                $axisG = $axisGo;
                $axisB = $axisBo;
                $tickR = $tickRo;
                $tickG = $tickGo;
                $tickB = $tickBo;
                $this->setFontProperties(["r" => $fontColorRo, "g" => $fontColorGo, "b" => $fontColorBo]);
            }
            $lastValue = "w00t";
            $id = 1;
            if ($parameters["identity"] == Constant::AXIS_X) {
                if ($pos == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($parameters["position"] == Constant::AXIS_POSITION_BOTTOM) {
                        if ($labelRotation == 0) {
                            $labelAlign = Constant::TEXT_ALIGN_TOPMIDDLE;
                            $yLabelOffset = 2;
                        }
                        if ($labelRotation > 0 && $labelRotation < 190) {
                            $labelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $yLabelOffset = 5;
                        }
                        if ($labelRotation == 180) {
                            $labelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
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
                                        $this->graphAreaX1 + $parameters["margin"], $axisPos["b"], $this->graphAreaX2 - $parameters["margin"], $axisPos["b"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                                );
                            } else {
                                $floatingOffset = 0;
                                $this->drawLine(
                                        $this->graphAreaX1, $axisPos["b"], $this->graphAreaX2, $axisPos["b"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                        $this->graphAreaX2 - $parameters["margin"], $axisPos["b"], $this->graphAreaX2 + ($arrowSize * 2), $axisPos["b"], ["fillR" => $axisR, "fillG" => $axisG, "fillB" => $axisB, "size" => $arrowSize]
                                );
                            }
                        }
                        $width = ($this->graphAreaX2 - $this->graphAreaX1) - $parameters["margin"] * 2;
                        if ($parameters["rows"] == 0) {
                            $step = $width;
                        } else {
                            $step = $width / ($parameters["rows"]);
                        }
                        $maxBottom = $axisPos["b"];
                        for ($i = 0; $i <= $parameters["rows"]; $i++) {
                            $xPos = $this->graphAreaX1 + $parameters["margin"] + $step * $i;
                            $yPos = $axisPos["b"];
                            if ($abscissa != null) {
                                $value = "";
                                if (isset($data["series"][$abscissa]["data"][$i])) {
                                    $value = $this->scaleFormat(
                                            $data["series"][$abscissa]["data"][$i], $data["xAxisDisplay"], $data["xAxisFormat"], $data["xAxisUnit"]
                                    );
                                }
                            } else {
                                $value = $i;
                                if (isset($parameters["scaleMin"]) && isset($parameters["rowHeight"])) {
                                    $value = $this->scaleFormat(
                                            $parameters["scaleMin"] + $parameters["rowHeight"] * $i, $data["xAxisDisplay"], $data["xAxisFormat"], $data["xAxisUnit"]
                                    );
                                }
                            }
                            $id++;
                            $skipped = true;
                            if ($this->isValidLabel($value, $lastValue, $labelingMethod, $id, $labelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                        $xPos, $yPos + $outerTickWidth + $yLabelOffset, $value, ["angle" => $labelRotation, "align" => $labelAlign]
                                );
                                $txtBottom = $yPos + $outerTickWidth + 2 + ($bounds[0]["y"] - $bounds[2]["y"]);
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
                                            $xPos, $this->graphAreaY1 + $floatingOffset, $xPos, $this->graphAreaY2 - $floatingOffset, $skippedAxisColor
                                    );
                                }
                                if (($skippedInnerTickWidth != 0 || $skippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                            $xPos, $yPos - $skippedInnerTickWidth, $xPos, $yPos + $skippedOuterTickWidth, $skippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines && ($xPos != $this->graphAreaX1 && $xPos != $this->graphAreaX2)
                                ) {
                                    $this->drawLine(
                                            $xPos, $this->graphAreaY1 + $floatingOffset, $xPos, $this->graphAreaY2 - $floatingOffset, [
                                        "r" => $gridR,
                                        "g" => $gridG,
                                        "b" => $gridB,
                                        "alpha" => $gridalpha,
                                        "ticks" => $gridTicks
                                            ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $outerTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                            $xPos, $yPos - $InnerTickWidth, $xPos, $yPos + $outerTickWidth, ["r" => $tickR, "g" => $tickG, "b" => $tickB, "alpha" => $tickalpha]
                                    );
                                }
                            }
                        }
                        if (isset($parameters["name"]) && !$removeXAxis) {
                            $yPos = $maxBottom + 2;
                            $xPos = $this->graphAreaX1 + ($this->graphAreaX2 - $this->graphAreaX1) / 2;
                            $bounds = $this->drawText(
                                    $xPos, $yPos, $parameters["name"], ["align" => TEXT_ALIGN_TOPMIDDLE]
                            );
                            $maxBottom = $bounds[0]["y"];
                            $this->dataSet->data["graphArea"]["y2"] = $maxBottom + $this->fontSize;
                        }
                        $axisPos["b"] = $maxBottom + $scaleSpacing;
                    } elseif ($parameters["position"] == Constant::AXIS_POSITION_TOP) {
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
                                        $this->graphAreaX1 + $parameters["margin"], $axisPos["T"], $this->graphAreaX2 - $parameters["margin"], $axisPos["T"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                                );
                            } else {
                                $floatingOffset = 0;
                                $this->drawLine(
                                        $this->graphAreaX1, $axisPos["T"], $this->graphAreaX2, $axisPos["T"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                        $this->graphAreaX2 - $parameters["margin"], $axisPos["T"], $this->graphAreaX2 + ($arrowSize * 2), $axisPos["T"], ["fillR" => $axisR, "fillG" => $axisG, "fillB" => $axisB, "size" => $arrowSize]
                                );
                            }
                        }
                        $width = ($this->graphAreaX2 - $this->graphAreaX1) - $parameters["margin"] * 2;
                        if ($parameters["rows"] == 0) {
                            $step = $width;
                        } else {
                            $step = $width / $parameters["rows"];
                        }
                        $minTop = $axisPos["T"];
                        for ($i = 0; $i <= $parameters["rows"]; $i++) {
                            $xPos = $this->graphAreaX1 + $parameters["margin"] + $step * $i;
                            $yPos = $axisPos["T"];
                            if ($abscissa != null) {
                                $value = "";
                                if (isset($data["series"][$abscissa]["data"][$i])) {
                                    $value = $this->scaleFormat(
                                            $data["series"][$abscissa]["data"][$i], $data["xAxisDisplay"], $data["xAxisFormat"], $data["xAxisUnit"]
                                    );
                                }
                            } else {
                                $value = $i;
                                if (isset($parameters["scaleMin"]) && isset($parameters["rowHeight"])) {
                                    $value = $this->scaleFormat(
                                            $parameters["scaleMin"] + $parameters["rowHeight"] * $i, $data["xAxisDisplay"], $data["xAxisFormat"], $data["xAxisUnit"]
                                    );
                                }
                            }
                            $id++;
                            $skipped = true;
                            if ($this->isValidLabel($value, $lastValue, $labelingMethod, $id, $labelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                        $xPos, $yPos - $outerTickWidth - $yLabelOffset, $value, ["angle" => $labelRotation, "align" => $labelAlign]
                                );
                                $txtBox = $yPos - $outerTickWidth - 2 - ($bounds[0]["y"] - $bounds[2]["y"]);
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
                                            $xPos, $this->graphAreaY1 + $floatingOffset, $xPos, $this->graphAreaY2 - $floatingOffset, $skippedAxisColor
                                    );
                                }
                                if (($skippedInnerTickWidth != 0 || $skippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                            $xPos, $yPos + $skippedInnerTickWidth, $xPos, $yPos - $skippedOuterTickWidth, $skippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines) {
                                    $this->drawLine(
                                            $xPos, $this->graphAreaY1 + $floatingOffset, $xPos, $this->graphAreaY2 - $floatingOffset, [
                                        "r" => $gridR,
                                        "g" => $gridG,
                                        "b" => $gridB,
                                        "alpha" => $gridalpha,
                                        "ticks" => $gridTicks
                                            ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $outerTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                            $xPos, $yPos + $InnerTickWidth, $xPos, $yPos - $outerTickWidth, [
                                        "r" => $tickR,
                                        "g" => $tickG,
                                        "b" => $tickB,
                                        "alpha" => $tickalpha
                                            ]
                                    );
                                }
                            }
                        }
                        if (isset($parameters["name"]) && !$removeXAxis) {
                            $yPos = $minTop - 2;
                            $xPos = $this->graphAreaX1 + ($this->graphAreaX2 - $this->graphAreaX1) / 2;
                            $bounds = $this->drawText(
                                    $xPos, $yPos, $parameters["name"], ["align" => TEXT_ALIGN_BOTTOMMIDDLE]
                            );
                            $minTop = $bounds[2]["y"];
                            $this->dataSet->data["graphArea"]["y1"] = $minTop;
                        }
                        $axisPos["T"] = $minTop - $scaleSpacing;
                    }
                } elseif ($pos == Constant::SCALE_POS_TOPBOTTOM) {
                    if ($parameters["position"] == Constant::AXIS_POSITION_LEFT) {
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
                                        $axisPos["l"], $this->graphAreaY1 + $parameters["margin"], $axisPos["l"], $this->graphAreaY2 - $parameters["margin"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                                );
                            } else {
                                $floatingOffset = 0;
                                $this->drawLine(
                                        $axisPos["l"], $this->graphAreaY1, $axisPos["l"], $this->graphAreaY2, ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                        $axisPos["l"], $this->graphAreaY2 - $parameters["margin"], $axisPos["l"], $this->graphAreaY2 + ($arrowSize * 2), [
                                    "fillR" => $axisR,
                                    "fillG" => $axisG,
                                    "fillB" => $axisB,
                                    "size" => $arrowSize
                                        ]
                                );
                            }
                        }
                        $height = ($this->graphAreaY2 - $this->graphAreaY1) - $parameters["margin"] * 2;
                        if ($parameters["rows"] == 0) {
                            $step = $height;
                        } else {
                            $step = $height / $parameters["rows"];
                        }
                        $minLeft = $axisPos["l"];
                        for ($i = 0; $i <= $parameters["rows"]; $i++) {
                            $yPos = $this->graphAreaY1 + $parameters["margin"] + $step * $i;
                            $xPos = $axisPos["l"];
                            if ($abscissa != null) {
                                $value = "";
                                if (isset($data["series"][$abscissa]["data"][$i])) {
                                    $value = $this->scaleFormat(
                                            $data["series"][$abscissa]["data"][$i], $data["xAxisDisplay"], $data["xAxisFormat"], $data["xAxisUnit"]
                                    );
                                }
                            } else {
                                $value = $i;
                                if (isset($parameters["scaleMin"]) && isset($parameters["rowHeight"])) {
                                    $value = $this->scaleFormat(
                                            $parameters["scaleMin"] + $parameters["rowHeight"] * $i, $data["xAxisDisplay"], $data["xAxisFormat"], $data["xAxisUnit"]
                                    );
                                }
                            }
                            $id++;
                            $skipped = true;
                            if ($this->isValidLabel($value, $lastValue, $labelingMethod, $id, $labelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                        $xPos - $outerTickWidth + $xLabelOffset, $yPos, $value, ["angle" => $labelRotation, "align" => $labelAlign]
                                );
                                $txtBox = $xPos - $outerTickWidth - 2 - ($bounds[1]["x"] - $bounds[0]["x"]);
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
                                            $this->graphAreaX1 + $floatingOffset, $yPos, $this->graphAreaX2 - $floatingOffset, $yPos, $skippedAxisColor
                                    );
                                }
                                if (($skippedInnerTickWidth != 0 || $skippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                            $xPos - $skippedOuterTickWidth, $yPos, $xPos + $skippedInnerTickWidth, $yPos, $skippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines &&
                                        ($yPos != $this->graphAreaY1 && $yPos != $this->graphAreaY2)
                                ) {
                                    $this->drawLine(
                                            $this->graphAreaX1 + $floatingOffset, $yPos, $this->graphAreaX2 - $floatingOffset, $yPos, [
                                        "r" => $gridR,
                                        "g" => $gridG,
                                        "b" => $gridB,
                                        "alpha" => $gridalpha,
                                        "ticks" => $gridTicks
                                            ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $outerTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                            $xPos - $outerTickWidth, $yPos, $xPos + $InnerTickWidth, $yPos, ["r" => $tickR, "g" => $tickG, "b" => $tickB, "alpha" => $tickalpha]
                                    );
                                }
                            }
                        }
                        if (isset($parameters["name"]) && !$removeXAxis) {
                            $xPos = $minLeft - 2;
                            $yPos = $this->graphAreaY1 + ($this->graphAreaY2 - $this->graphAreaY1) / 2;
                            $bounds = $this->drawText(
                                    $xPos, $yPos, $parameters["name"], ["align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 90]
                            );
                            $minLeft = $bounds[0]["x"];
                            $this->dataSet->data["graphArea"]["x1"] = $minLeft;
                        }
                        $axisPos["l"] = $minLeft - $scaleSpacing;
                    } elseif ($parameters["position"] == AXIS_POSITION_RIGHT) {
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
                                        $axisPos["r"], $this->graphAreaY1 + $parameters["margin"], $axisPos["r"], $this->graphAreaY2 - $parameters["margin"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                                );
                            } else {
                                $floatingOffset = 0;
                                $this->drawLine(
                                        $axisPos["r"], $this->graphAreaY1, $axisPos["r"], $this->graphAreaY2, ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                        $axisPos["r"], $this->graphAreaY2 - $parameters["margin"], $axisPos["r"], $this->graphAreaY2 + ($arrowSize * 2), [
                                    "fillR" => $axisR,
                                    "fillG" => $axisG,
                                    "fillB" => $axisB,
                                    "size" => $arrowSize
                                        ]
                                );
                            }
                        }
                        $height = ($this->graphAreaY2 - $this->graphAreaY1) - $parameters["margin"] * 2;
                        if ($parameters["rows"] == 0) {
                            $step = $height;
                        } else {
                            $step = $height / $parameters["rows"];
                        }
                        $maxRight = $axisPos["r"];
                        for ($i = 0; $i <= $parameters["rows"]; $i++) {
                            $yPos = $this->graphAreaY1 + $parameters["margin"] + $step * $i;
                            $xPos = $axisPos["r"];
                            if ($abscissa != null) {
                                $value = "";
                                if (isset($data["series"][$abscissa]["data"][$i])) {
                                    $value = $this->scaleFormat(
                                            $data["series"][$abscissa]["data"][$i], $data["xAxisDisplay"], $data["xAxisFormat"], $data["xAxisUnit"]
                                    );
                                }
                            } else {
                                $value = $i;
                                if (isset($parameters["scaleMin"]) && isset($parameters["rowHeight"])) {
                                    $value = $this->scaleFormat(
                                            $parameters["scaleMin"] + $parameters["rowHeight"] * $i, $data["xAxisDisplay"], $data["xAxisFormat"], $data["xAxisUnit"]
                                    );
                                }
                            }
                            $id++;
                            $skipped = true;
                            if ($this->isValidLabel($value, $lastValue, $labelingMethod, $id, $labelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                        $xPos + $outerTickWidth + $xLabelOffset, $yPos, $value, ["angle" => $labelRotation, "align" => $labelAlign]
                                );
                                $txtBox = $xPos + $outerTickWidth + 2 + ($bounds[1]["x"] - $bounds[0]["x"]);
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
                                            $this->graphAreaX1 + $floatingOffset, $yPos, $this->graphAreaX2 - $floatingOffset, $yPos, $skippedAxisColor
                                    );
                                }
                                if (($skippedInnerTickWidth != 0 || $skippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                            $xPos + $skippedOuterTickWidth, $yPos, $xPos - $skippedInnerTickWidth, $yPos, $skippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines) {
                                    $this->drawLine(
                                            $this->graphAreaX1 + $floatingOffset, $yPos, $this->graphAreaX2 - $floatingOffset, $yPos, [
                                        "r" => $gridR,
                                        "g" => $gridG,
                                        "b" => $gridB,
                                        "alpha" => $gridalpha,
                                        "ticks" => $gridTicks
                                            ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $outerTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                            $xPos + $outerTickWidth, $yPos, $xPos - $InnerTickWidth, $yPos, [
                                        "r" => $tickR,
                                        "g" => $tickG,
                                        "b" => $tickB,
                                        "alpha" => $tickalpha
                                            ]
                                    );
                                }
                            }
                        }
                        if (isset($parameters["name"]) && !$removeXAxis) {
                            $xPos = $maxRight + 4;
                            $yPos = $this->graphAreaY1 + ($this->graphAreaY2 - $this->graphAreaY1) / 2;
                            $bounds = $this->drawText(
                                    $xPos, $yPos, $parameters["name"], ["align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 270]
                            );
                            $maxRight = $bounds[1]["x"];
                            $this->dataSet->data["graphArea"]["x2"] = $maxRight + $this->fontSize;
                        }
                        $axisPos["r"] = $maxRight + $scaleSpacing;
                    }
                }
            }
            if ($parameters["identity"] == Constant::AXIS_Y && !$removeYAxis) {
                if ($pos == Constant::SCALE_POS_LEFTRIGHT) {
                    if ($parameters["position"] == Constant::AXIS_POSITION_LEFT) {
                        if ($floating) {
                            $floatingOffset = $xMargin;
                            $this->drawLine(
                                    $axisPos["l"], $this->graphAreaY1 + $parameters["margin"], $axisPos["l"], $this->graphAreaY2 - $parameters["margin"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                            );
                        } else {
                            $floatingOffset = 0;
                            $this->drawLine(
                                    $axisPos["l"], $this->graphAreaY1, $axisPos["l"], $this->graphAreaY2, ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                    $axisPos["l"], $this->graphAreaY1 + $parameters["margin"], $axisPos["l"], $this->graphAreaY1 - ($arrowSize * 2), [
                                "fillR" => $axisR,
                                "fillG" => $axisG,
                                "fillB" => $axisB,
                                "size" => $arrowSize
                                    ]
                            );
                        }
                        $height = ($this->graphAreaY2 - $this->graphAreaY1) - $parameters["margin"] * 2;
                        $step = $height / $parameters["rows"];
                        $subTicksSize = $step / 2;
                        $minLeft = $axisPos["l"];
                        $lastY = null;
                        for ($i = 0; $i <= $parameters["rows"]; $i++) {
                            $yPos = $this->graphAreaY2 - $parameters["margin"] - $step * $i;
                            $xPos = $axisPos["l"];
                            $value = $this->scaleFormat(
                                    $parameters["scaleMin"] + $parameters["rowHeight"] * $i, $parameters["display"], $parameters["format"], $parameters["unit"]
                            );
                            if ($i % 2 == 1) {
                                $bGColor = [
                                    "r" => $backgroundR1,
                                    "g" => $backgroundG1,
                                    "b" => $backgroundB1,
                                    "alpha" => $backgroundalpha1
                                ];
                            } else {
                                $bGColor = [
                                    "r" => $backgroundR2,
                                    "g" => $backgroundG2,
                                    "b" => $backgroundB2,
                                    "alpha" => $backgroundalpha2
                                ];
                            }
                            if ($lastY != null && $CycleBackground && ($drawYLines == ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                        $this->graphAreaX1 + $floatingOffset, $lastY, $this->graphAreaX2 - $floatingOffset, $yPos, $bGColor
                                );
                            }
                            if ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                        $this->graphAreaX1 + $floatingOffset, $yPos, $this->graphAreaX2 - $floatingOffset, $yPos, [
                                    "r" => $gridR,
                                    "g" => $gridG,
                                    "b" => $gridB,
                                    "alpha" => $gridalpha,
                                    "ticks" => $gridTicks
                                        ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters["rows"]) {
                                $this->drawLine(
                                        $xPos - $outerSubTickWidth, $yPos - $subTicksSize, $xPos + $InnerSubTickWidth, $yPos - $subTicksSize, [
                                    "r" => $subTickR,
                                    "g" => $subTickG,
                                    "b" => $subTickB,
                                    "alpha" => $subTickalpha
                                        ]
                                );
                            }
                            if (!$removeYAxiValues) {
                                $this->drawLine(
                                        $xPos - $outerTickWidth, $yPos, $xPos + $InnerTickWidth, $yPos, ["r" => $tickR, "g" => $tickG, "b" => $tickB, "alpha" => $tickalpha]
                                );
                                $bounds = $this->drawText(
                                        $xPos - $outerTickWidth - 2, $yPos, $value, ["align" => Constant::TEXT_ALIGN_MIDDLERIGHT]
                                );
                                $txtLeft = $xPos - $outerTickWidth - 2 - ($bounds[1]["x"] - $bounds[0]["x"]);
                                $minLeft = min($minLeft, $txtLeft);
                            }
                            $lastY = $yPos;
                        }
                        if (isset($parameters["name"])) {
                            $xPos = $minLeft - 2;
                            $yPos = $this->graphAreaY1 + ($this->graphAreaY2 - $this->graphAreaY1) / 2;
                            $bounds = $this->drawText(
                                    $xPos, $yPos, $parameters["name"], ["align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 90]
                            );
                            $minLeft = $bounds[2]["x"];
                            $this->dataSet->data["graphArea"]["x1"] = $minLeft;
                        }
                        $axisPos["l"] = $minLeft - $scaleSpacing;
                    } elseif ($parameters["position"] == Constant::AXIS_POSITION_RIGHT) {
                        if ($floating) {
                            $floatingOffset = $xMargin;
                            $this->drawLine(
                                    $axisPos["r"], $this->graphAreaY1 + $parameters["margin"], $axisPos["r"], $this->graphAreaY2 - $parameters["margin"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                            );
                        } else {
                            $floatingOffset = 0;
                            $this->drawLine(
                                    $axisPos["r"], $this->graphAreaY1, $axisPos["r"], $this->graphAreaY2, ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                    $axisPos["r"], $this->graphAreaY1 + $parameters["margin"], $axisPos["r"], $this->graphAreaY1 - ($arrowSize * 2), [
                                "fillR" => $axisR,
                                "fillG" => $axisG,
                                "fillB" => $axisB,
                                "size" => $arrowSize
                                    ]
                            );
                        }
                        $height = ($this->graphAreaY2 - $this->graphAreaY1) - $parameters["margin"] * 2;
                        $step = $height / $parameters["rows"];
                        $subTicksSize = $step / 2;
                        $maxLeft = $axisPos["r"];
                        $lastY = null;
                        for ($i = 0; $i <= $parameters["rows"]; $i++) {
                            $yPos = $this->graphAreaY2 - $parameters["margin"] - $step * $i;
                            $xPos = $axisPos["r"];
                            $value = $this->scaleFormat(
                                    $parameters["scaleMin"] + $parameters["rowHeight"] * $i, $parameters["display"], $parameters["format"], $parameters["unit"]
                            );
                            if ($i % 2 == 1) {
                                $bGColor = [
                                    "r" => $backgroundR1,
                                    "g" => $backgroundG1,
                                    "b" => $backgroundB1,
                                    "alpha" => $backgroundalpha1
                                ];
                            } else {
                                $bGColor = [
                                    "r" => $backgroundR2,
                                    "g" => $backgroundG2,
                                    "b" => $backgroundB2,
                                    "alpha" => $backgroundalpha2
                                ];
                            }
                            if ($lastY != null && $CycleBackground && ($drawYLines == ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                        $this->graphAreaX1 + $floatingOffset, $lastY, $this->graphAreaX2 - $floatingOffset, $yPos, $bGColor
                                );
                            }
                            if ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                        $this->graphAreaX1 + $floatingOffset, $yPos, $this->graphAreaX2 - $floatingOffset, $yPos, [
                                    "r" => $gridR,
                                    "g" => $gridG,
                                    "b" => $gridB,
                                    "alpha" => $gridalpha,
                                    "ticks" => $gridTicks
                                        ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters["rows"]) {
                                $this->drawLine(
                                        $xPos - $outerSubTickWidth, $yPos - $subTicksSize, $xPos + $InnerSubTickWidth, $yPos - $subTicksSize, [
                                    "r" => $subTickR,
                                    "g" => $subTickG,
                                    "b" => $subTickB,
                                    "alpha" => $subTickalpha
                                        ]
                                );
                            }
                            $this->drawLine(
                                    $xPos - $InnerTickWidth, $yPos, $xPos + $outerTickWidth, $yPos, ["r" => $tickR, "g" => $tickG, "b" => $tickB, "alpha" => $tickalpha]
                            );
                            $bounds = $this->drawText(
                                    $xPos + $outerTickWidth + 2, $yPos, $value, ["align" => TEXT_ALIGN_MIDDLELEFT]
                            );
                            $txtLeft = $xPos + $outerTickWidth + 2 + ($bounds[1]["x"] - $bounds[0]["x"]);
                            $maxLeft = max($maxLeft, $txtLeft);
                            $lastY = $yPos;
                        }
                        if (isset($parameters["name"])) {
                            $xPos = $maxLeft + 6;
                            $yPos = $this->graphAreaY1 + ($this->graphAreaY2 - $this->graphAreaY1) / 2;
                            $bounds = $this->drawText(
                                    $xPos, $yPos, $parameters["name"], ["align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 270]
                            );
                            $maxLeft = $bounds[2]["x"];
                            $this->dataSet->data["graphArea"]["x2"] = $maxLeft + $this->fontSize;
                        }
                        $axisPos["r"] = $maxLeft + $scaleSpacing;
                    }
                } elseif ($pos == Constant::SCALE_POS_TOPBOTTOM) {
                    if ($parameters["position"] == Constant::AXIS_POSITION_TOP) {
                        if ($floating) {
                            $floatingOffset = $xMargin;
                            $this->drawLine(
                                    $this->graphAreaX1 + $parameters["margin"], $axisPos["T"], $this->graphAreaX2 - $parameters["margin"], $axisPos["T"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                            );
                        } else {
                            $floatingOffset = 0;
                            $this->drawLine(
                                    $this->graphAreaX1, $axisPos["T"], $this->graphAreaX2, $axisPos["T"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                    $this->graphAreaX2 - $parameters["margin"], $axisPos["T"], $this->graphAreaX2 + ($arrowSize * 2), $axisPos["T"], [
                                "fillR" => $axisR,
                                "fillG" => $axisG,
                                "fillB" => $axisB,
                                "size" => $arrowSize
                                    ]
                            );
                        }
                        $width = ($this->graphAreaX2 - $this->graphAreaX1) - $parameters["margin"] * 2;
                        $step = $width / $parameters["rows"];
                        $subTicksSize = $step / 2;
                        $minTop = $axisPos["T"];
                        $lastX = null;
                        for ($i = 0; $i <= $parameters["rows"]; $i++) {
                            $xPos = $this->graphAreaX1 + $parameters["margin"] + $step * $i;
                            $yPos = $axisPos["T"];
                            $value = $this->scaleFormat(
                                    $parameters["scaleMin"] + $parameters["rowHeight"] * $i, $parameters["display"], $parameters["format"], $parameters["unit"]
                            );
                            if ($i % 2 == 1) {
                                $bGColor = [
                                    "r" => $backgroundR1,
                                    "g" => $backgroundG1,
                                    "b" => $backgroundB1,
                                    "alpha" => $backgroundalpha1
                                ];
                            } else {
                                $bGColor = [
                                    "r" => $backgroundR2,
                                    "g" => $backgroundG2,
                                    "b" => $backgroundB2,
                                    "alpha" => $backgroundalpha2
                                ];
                            }
                            if ($lastX != null && $CycleBackground && ($drawYLines == ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                        $lastX, $this->graphAreaY1 + $floatingOffset, $xPos, $this->graphAreaY2 - $floatingOffset, $bGColor
                                );
                            }
                            if ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                        $xPos, $this->graphAreaY1 + $floatingOffset, $xPos, $this->graphAreaY2 - $floatingOffset, [
                                    "r" => $gridR,
                                    "g" => $gridG,
                                    "b" => $gridB,
                                    "alpha" => $gridalpha,
                                    "ticks" => $gridTicks
                                        ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters["rows"]) {
                                $this->drawLine(
                                        $xPos + $subTicksSize, $yPos - $outerSubTickWidth, $xPos + $subTicksSize, $yPos + $InnerSubTickWidth, [
                                    "r" => $subTickR,
                                    "g" => $subTickG,
                                    "b" => $subTickB,
                                    "alpha" => $subTickalpha
                                        ]
                                );
                            }
                            $this->drawLine(
                                    $xPos, $yPos - $outerTickWidth, $xPos, $yPos + $InnerTickWidth, ["r" => $tickR, "g" => $tickG, "b" => $tickB, "alpha" => $tickalpha]
                            );
                            $bounds = $this->drawText(
                                    $xPos, $yPos - $outerTickWidth - 2, $value, ["align" => Constant::TEXT_ALIGN_BOTTOMMIDDLE]
                            );
                            $txtHeight = $yPos - $outerTickWidth - 2 - ($bounds[1]["y"] - $bounds[2]["y"]);
                            $minTop = min($minTop, $txtHeight);
                            $lastX = $xPos;
                        }
                        if (isset($parameters["name"])) {
                            $yPos = $minTop - 2;
                            $xPos = $this->graphAreaX1 + ($this->graphAreaX2 - $this->graphAreaX1) / 2;
                            $bounds = $this->drawText(
                                    $xPos, $yPos, $parameters["name"], ["align" => Constant::TEXT_ALIGN_BOTTOMMIDDLE]
                            );
                            $minTop = $bounds[2]["y"];
                            $this->dataSet->data["graphArea"]["y1"] = $minTop;
                        }
                        $axisPos["T"] = $minTop - $scaleSpacing;
                    } elseif ($parameters["position"] == Constant::AXIS_POSITION_BOTTOM) {
                        if ($floating) {
                            $floatingOffset = $xMargin;
                            $this->drawLine(
                                    $this->graphAreaX1 + $parameters["margin"], $axisPos["b"], $this->graphAreaX2 - $parameters["margin"], $axisPos["b"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                            );
                        } else {
                            $floatingOffset = 0;
                            $this->drawLine(
                                    $this->graphAreaX1, $axisPos["b"], $this->graphAreaX2, $axisPos["b"], ["r" => $axisR, "g" => $axisG, "b" => $axisB, "alpha" => $axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                    $this->graphAreaX2 - $parameters["margin"], $axisPos["b"], $this->graphAreaX2 + ($arrowSize * 2), $axisPos["b"], [
                                "fillR" => $axisR,
                                "fillG" => $axisG,
                                "fillB" => $axisB,
                                "size" => $arrowSize
                                    ]
                            );
                        }
                        $width = ($this->graphAreaX2 - $this->graphAreaX1) - $parameters["margin"] * 2;
                        $step = $width / $parameters["rows"];
                        $subTicksSize = $step / 2;
                        $maxBottom = $axisPos["b"];
                        $lastX = null;
                        for ($i = 0; $i <= $parameters["rows"]; $i++) {
                            $xPos = $this->graphAreaX1 + $parameters["margin"] + $step * $i;
                            $yPos = $axisPos["b"];
                            $value = $this->scaleFormat(
                                    $parameters["scaleMin"] + $parameters["rowHeight"] * $i, $parameters["display"], $parameters["format"], $parameters["unit"]
                            );
                            if ($i % 2 == 1) {
                                $bGColor = [
                                    "r" => $backgroundR1,
                                    "g" => $backgroundG1,
                                    "b" => $backgroundB1,
                                    "alpha" => $backgroundalpha1
                                ];
                            } else {
                                $bGColor = [
                                    "r" => $backgroundR2,
                                    "g" => $backgroundG2,
                                    "b" => $backgroundB2,
                                    "alpha" => $backgroundalpha2
                                ];
                            }
                            if ($lastX != null && $CycleBackground && ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                        $lastX, $this->graphAreaY1 + $floatingOffset, $xPos, $this->graphAreaY2 - $floatingOffset, $bGColor
                                );
                            }
                            if ($drawYLines == Constant::ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                        $xPos, $this->graphAreaY1 + $floatingOffset, $xPos, $this->graphAreaY2 - $floatingOffset, [
                                    "r" => $gridR,
                                    "g" => $gridG,
                                    "b" => $gridB,
                                    "alpha" => $gridalpha,
                                    "ticks" => $gridTicks
                                        ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters["rows"]) {
                                $this->drawLine(
                                        $xPos + $subTicksSize, $yPos - $outerSubTickWidth, $xPos + $subTicksSize, $yPos + $InnerSubTickWidth, [
                                    "r" => $subTickR,
                                    "g" => $subTickG,
                                    "b" => $subTickB,
                                    "alpha" => $subTickalpha
                                        ]
                                );
                            }
                            $this->drawLine(
                                    $xPos, $yPos - $outerTickWidth, $xPos, $yPos + $InnerTickWidth, ["r" => $tickR, "g" => $tickG, "b" => $tickB, "alpha" => $tickalpha]
                            );
                            $bounds = $this->drawText(
                                    $xPos, $yPos + $outerTickWidth + 2, $value, ["align" => Constant::TEXT_ALIGN_TOPMIDDLE]
                            );
                            $txtHeight = $yPos + $outerTickWidth + 2 + ($bounds[1]["y"] - $bounds[2]["y"]);
                            $maxBottom = max($maxBottom, $txtHeight);
                            $lastX = $xPos;
                        }
                        if (isset($parameters["name"])) {
                            $yPos = $maxBottom + 2;
                            $xPos = $this->graphAreaX1 + ($this->graphAreaX2 - $this->graphAreaX1) / 2;
                            $bounds = $this->drawText(
                                    $xPos, $yPos, $parameters["name"], ["align" => Constant::TEXT_ALIGN_TOPMIDDLE]
                            );
                            $maxBottom = $bounds[0]["y"];
                            $this->dataSet->data["graphArea"]["y2"] = $maxBottom + $this->fontSize;
                        }
                        $axisPos["b"] = $maxBottom + $scaleSpacing;
                    }
                }
            }
        }
    }

    /**
     * Draw an X threshold
     * @param mixed $value
     * @param boolean $format
     * @return array|null|integer
     */
    public function drawXThreshold($value, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 255;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 50;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : 6;
        $wide = isset($format["wide"]) ? $format["wide"] : false;
        $wideFactor = isset($format["wideFactor"]) ? $format["wideFactor"] : 5;
        $writeCaption = isset($format["writeCaption"]) ? $format["writeCaption"] : false;
        $caption = isset($format["caption"]) ? $format["caption"] : null;
        $captionAlign = isset($format["captionAlign"]) ? $format["captionAlign"] : CAPTION_LEFT_TOP;
        $captionOffset = isset($format["captionOffset"]) ? $format["captionOffset"] : 5;
        $captionR = isset($format["captionR"]) ? $format["captionR"] : 255;
        $captionG = isset($format["captionG"]) ? $format["captionG"] : 255;
        $captionB = isset($format["captionB"]) ? $format["captionB"] : 255;
        $captionalpha = isset($format["captionalpha"]) ? $format["captionalpha"] : 100;
        $drawBox = isset($format["drawBox"]) ? $format["drawBox"] : true;
        $drawBoxBorder = isset($format["drawBoxBorder"]) ? $format["drawBoxBorder"] : false;
        $borderOffset = isset($format["borderOffset"]) ? $format["borderOffset"] : 3;
        $boxRounded = isset($format["boxRounded"]) ? $format["boxRounded"] : true;
        $roundedRadius = isset($format["roundedRadius"]) ? $format["roundedRadius"] : 3;
        $boxR = isset($format["boxR"]) ? $format["boxR"] : 0;
        $boxG = isset($format["boxG"]) ? $format["boxG"] : 0;
        $boxB = isset($format["boxB"]) ? $format["boxB"] : 0;
        $boxalpha = isset($format["boxalpha"]) ? $format["boxalpha"] : 30;
        $boxSurrounding = isset($format["boxSurrounding"]) ? $format["boxSurrounding"] : "";
        $boxborderR = isset($format["boxborderR"]) ? $format["boxborderR"] : 255;
        $boxborderG = isset($format["boxborderG"]) ? $format["boxborderG"] : 255;
        $boxborderB = isset($format["boxborderB"]) ? $format["boxborderB"] : 255;
        $boxBorderalpha = isset($format["boxBorderalpha"]) ? $format["boxBorderalpha"] : 100;
        $valueIsLabel = isset($format["valueIsLabel"]) ? $format["valueIsLabel"] : false;
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
            $format["valueIsLabel"] = false;
            foreach ($data["series"][$data["abscissa"]]["data"] as $key => $serieValue) {
                if ($serieValue == $value) {
                    $this->drawXThreshold($key, $format);
                }
            }
            return 0;
        }
        $captionSettings = [
            "DrawBox" => $drawBox,
            "DrawBoxBorder" => $drawBoxBorder,
            "borderOffset" => $borderOffset,
            "BoxRounded" => $boxRounded,
            "RoundedRadius" => $roundedRadius,
            "BoxR" => $boxR,
            "BoxG" => $boxG,
            "BoxB" => $boxB,
            "Boxalpha" => $boxalpha,
            "BoxSurrounding" => $boxSurrounding,
            "BoxborderR" => $boxborderR,
            "BoxborderG" => $boxborderG,
            "BoxborderB" => $boxborderB,
            "BoxBorderalpha" => $boxBorderalpha,
            "r" => $captionR,
            "g" => $captionG,
            "b" => $captionB,
            "alpha" => $captionalpha
        ];
        if ($caption == null) {
            $caption = $value;
            if (isset($data["abscissa"]) && isset($data["series"][$data["abscissa"]]["data"][$value])
            ) {
                $caption = $data["series"][$data["abscissa"]]["data"][$value];
            }
        }
        if ($data["orientation"] == Constant::SCALE_POS_LEFTRIGHT) {
            $xStep = (($this->graphAreaX2 - $this->graphAreaX1) - $xScale[0] * 2) / $xScale[1];
            $xPos = $this->graphAreaX1 + $xScale[0] + $xStep * $value;
            $yPos1 = $this->graphAreaY1 + $data["yMargin"];
            $yPos2 = $this->graphAreaY2 - $data["yMargin"];
            if ($xPos >= $this->graphAreaX1 + $abscissaMargin && $xPos <= $this->graphAreaX2 - $abscissaMargin
            ) {
                $this->drawLine(
                        $xPos, $yPos1, $xPos, $yPos2, [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "ticks" => $ticks,
                    "weight" => $weight
                        ]
                );
                if ($wide) {
                    $this->drawLine(
                            $xPos - 1, $yPos1, $xPos - 1, $yPos2, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                    $this->drawLine(
                            $xPos + 1, $yPos1, $xPos + 1, $yPos2, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                }
                if ($writeCaption) {
                    if ($captionAlign == CAPTION_LEFT_TOP) {
                        $y = $yPos1 + $captionOffset;
                        $captionSettings["align"] = TEXT_ALIGN_TOPMIDDLE;
                    } else {
                        $y = $yPos2 - $captionOffset;
                        $captionSettings["align"] = TEXT_ALIGN_BOTTOMMIDDLE;
                    }
                    $this->drawText($xPos, $y, $caption, $captionSettings);
                }
                return ["x" => $xPos];
            }
        } elseif ($data["orientation"] == SCALE_POS_TOPBOTTOM) {
            $xStep = (($this->graphAreaY2 - $this->graphAreaY1) - $xScale[0] * 2) / $xScale[1];
            $xPos = $this->graphAreaY1 + $xScale[0] + $xStep * $value;
            $yPos1 = $this->graphAreaX1 + $data["yMargin"];
            $yPos2 = $this->graphAreaX2 - $data["yMargin"];
            if ($xPos >= $this->graphAreaY1 + $abscissaMargin && $xPos <= $this->graphAreaY2 - $abscissaMargin
            ) {
                $this->drawLine(
                        $yPos1, $xPos, $yPos2, $xPos, [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "ticks" => $ticks,
                    "weight" => $weight
                        ]
                );
                if ($wide) {
                    $this->drawLine(
                            $yPos1, $xPos - 1, $yPos2, $xPos - 1, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                    $this->drawLine(
                            $yPos1, $xPos + 1, $yPos2, $xPos + 1, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                }
                if ($writeCaption) {
                    if ($captionAlign == CAPTION_LEFT_TOP) {
                        $y = $yPos1 + $captionOffset;
                        $captionSettings["align"] = TEXT_ALIGN_MIDDLELEFT;
                    } else {
                        $y = $yPos2 - $captionOffset;
                        $captionSettings["align"] = TEXT_ALIGN_MIDDLERIGHT;
                    }
                    $this->drawText($y, $xPos, $caption, $captionSettings);
                }
                return ["x" => $xPos];
            }
        }
    }

    /**
     * Draw an X threshold area
     * @param mixed $value1
     * @param mixed $value2
     * @param array $format
     * @return array|null
     */
    public function drawXThresholdArea($value1, $value2, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 255;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 20;
        $border = isset($format["border"]) ? $format["border"] : true;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $r;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $g;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $b;
        $borderalpha = isset($format["borderalpha"]) ? $format["borderalpha"] : $alpha + 20;
        $borderTicks = isset($format["borderTicks"]) ? $format["borderTicks"] : 2;
        $areaName = isset($format["areaName"]) ? $format["areaName"] : null;
        $NameAngle = isset($format["nameAngle"]) ? $format["nameAngle"] : ZONE_NAME_ANGLE_AUTO;
        $NameR = isset($format["nameR"]) ? $format["nameR"] : 255;
        $NameG = isset($format["nameG"]) ? $format["nameG"] : 255;
        $NameB = isset($format["nameB"]) ? $format["nameB"] : 255;
        $Namealpha = isset($format["namealpha"]) ? $format["namealpha"] : 100;
        $disableShadowOnArea = isset($format["disableShadowOnArea"]) ? $format["disableShadowOnArea"] : true;
        $restoreShadow = $this->shadow;
        if ($disableShadowOnArea && $this->shadow) {
            $this->shadow = false;
        }
        if ($borderalpha > 100) {
            $borderalpha = 100;
        }
        $data = $this->dataSet->getData();
        $xScale = $this->scaleGetXSettings();
        if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
            $xStep = (($this->graphAreaX2 - $this->graphAreaX1) - $xScale[0] * 2) / $xScale[1];
            $xPos1 = $this->graphAreaX1 + $xScale[0] + $xStep * $value1;
            $xPos2 = $this->graphAreaX1 + $xScale[0] + $xStep * $value2;
            $yPos1 = $this->graphAreaY1 + $data["yMargin"];
            $yPos2 = $this->graphAreaY2 - $data["yMargin"];
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
                    $xPos1, $yPos1, $xPos2, $yPos2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            if ($border) {
                $this->drawLine(
                        $xPos1, $yPos1, $xPos1, $yPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
                $this->drawLine(
                        $xPos2, $yPos1, $xPos2, $yPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
            }
            if ($areaName != null) {
                $xPos = ($xPos2 - $xPos1) / 2 + $xPos1;
                $yPos = ($yPos2 - $yPos1) / 2 + $yPos1;
                if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
                    $txtPos = $this->getTextBox(
                            $xPos, $yPos, $this->fontName, $this->fontSize, 0, $areaName
                    );
                    $txtWidth = $txtPos[1]["x"] - $txtPos[0]["x"];
                    $NameAngle = 90;
                    if (abs($xPos2 - $xPos1) > $txtWidth) {
                        $NameAngle = 0;
                    }
                }
                $this->shadow = $restoreShadow;
                $this->drawText(
                        $xPos, $yPos, $areaName, [
                    "r" => $NameR,
                    "g" => $NameG,
                    "b" => $NameB,
                    "alpha" => $Namealpha,
                    "Angle" => $NameAngle,
                    "align" => TEXT_ALIGN_MIDDLEMIDDLE
                        ]
                );
                if ($disableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ["x1" => $xPos1, "X2" => $xPos2];
        } elseif ($data["orientation"] == SCALE_POS_TOPBOTTOM) {
            $xStep = (($this->graphAreaY2 - $this->graphAreaY1) - $xScale[0] * 2) / $xScale[1];
            $xPos1 = $this->graphAreaY1 + $xScale[0] + $xStep * $value1;
            $xPos2 = $this->graphAreaY1 + $xScale[0] + $xStep * $value2;
            $yPos1 = $this->graphAreaX1 + $data["yMargin"];
            $yPos2 = $this->graphAreaX2 - $data["yMargin"];
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
                    $yPos1, $xPos1, $yPos2, $xPos2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            if ($border) {
                $this->drawLine(
                        $yPos1, $xPos1, $yPos2, $xPos1, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
                $this->drawLine(
                        $yPos1, $xPos2, $yPos2, $xPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
            }
            if ($areaName != null) {
                $xPos = ($xPos2 - $xPos1) / 2 + $xPos1;
                $yPos = ($yPos2 - $yPos1) / 2 + $yPos1;
                $this->shadow = $restoreShadow;
                $this->drawText(
                        $yPos, $xPos, $areaName, [
                    "r" => $NameR,
                    "g" => $NameG,
                    "b" => $NameB,
                    "alpha" => $Namealpha,
                    "Angle" => 0,
                    "align" => TEXT_ALIGN_MIDDLEMIDDLE
                        ]
                );
                if ($disableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ["x1" => $xPos1, "X2" => $xPos2];
        }
    }

    /**
     * Draw an Y threshold with the computed scale
     * @param mixed $value
     * @param array $format
     * @return array|int
     */
    public function drawThreshold($value, array $format = []) {
        $axisId = isset($format["axisId"]) ? $format["axisId"] : 0;
        $r = isset($format["r"]) ? $format["r"] : 255;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 50;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : 6;
        $wide = isset($format["wide"]) ? $format["wide"] : false;
        $wideFactor = isset($format["wideFactor"]) ? $format["wideFactor"] : 5;
        $writeCaption = isset($format["writeCaption"]) ? $format["writeCaption"] : false;
        $caption = isset($format["caption"]) ? $format["caption"] : null;
        $captionAlign = isset($format["captionAlign"]) ? $format["captionAlign"] : CAPTION_LEFT_TOP;
        $captionOffset = isset($format["captionOffset"]) ? $format["captionOffset"] : 10;
        $captionR = isset($format["captionR"]) ? $format["captionR"] : 255;
        $captionG = isset($format["captionG"]) ? $format["captionG"] : 255;
        $captionB = isset($format["captionB"]) ? $format["captionB"] : 255;
        $captionalpha = isset($format["captionalpha"]) ? $format["captionalpha"] : 100;
        $drawBox = isset($format["drawBox"]) ? $format["drawBox"] : true;
        $drawBoxBorder = isset($format["drawBoxBorder"]) ? $format["drawBoxBorder"] : false;
        $borderOffset = isset($format["borderOffset"]) ? $format["borderOffset"] : 5;
        $boxRounded = isset($format["boxRounded"]) ? $format["boxRounded"] : true;
        $roundedRadius = isset($format["roundedRadius"]) ? $format["roundedRadius"] : 3;
        $boxR = isset($format["boxR"]) ? $format["boxR"] : 0;
        $boxG = isset($format["boxG"]) ? $format["boxG"] : 0;
        $boxB = isset($format["boxB"]) ? $format["boxB"] : 0;
        $boxalpha = isset($format["boxalpha"]) ? $format["boxalpha"] : 20;
        $boxSurrounding = isset($format["boxSurrounding"]) ? $format["boxSurrounding"] : "";
        $boxborderR = isset($format["boxborderR"]) ? $format["boxborderR"] : 255;
        $boxborderG = isset($format["boxborderG"]) ? $format["boxborderG"] : 255;
        $boxborderB = isset($format["boxborderB"]) ? $format["boxborderB"] : 255;
        $boxBorderalpha = isset($format["boxBorderalpha"]) ? $format["boxBorderalpha"] : 100;
        $NoMargin = isset($format["noMargin"]) ? $format["noMargin"] : false;
        if (is_array($value)) {
            foreach ($value as $key => $id) {
                $this->drawThreshold($id, $format);
            }
            return 0;
        }
        $captionSettings = [
            "DrawBox" => $drawBox,
            "DrawBoxBorder" => $drawBoxBorder,
            "borderOffset" => $borderOffset,
            "BoxRounded" => $boxRounded,
            "RoundedRadius" => $roundedRadius,
            "BoxR" => $boxR,
            "BoxG" => $boxG,
            "BoxB" => $boxB,
            "Boxalpha" => $boxalpha,
            "BoxSurrounding" => $boxSurrounding,
            "BoxborderR" => $boxborderR,
            "BoxborderG" => $boxborderG,
            "BoxborderB" => $boxborderB,
            "BoxBorderalpha" => $boxBorderalpha,
            "r" => $captionR,
            "g" => $captionG,
            "b" => $captionB,
            "alpha" => $captionalpha
        ];
        $data = $this->dataSet->getData();
        $abscissaMargin = $this->getAbscissaMargin($data);
        if ($NoMargin) {
            $abscissaMargin = 0;
        }
        if (!isset($data["axis"][$axisId])) {
            return -1;
        }
        if ($caption == null) {
            $caption = $value;
        }
        if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
            $yPos = $this->scaleComputeY($value, ["axisId" => $axisId]);
            if ($yPos >= $this->graphAreaY1 + $data["axis"][$axisId]["margin"] && $yPos <= $this->graphAreaY2 - $data["axis"][$axisId]["margin"]
            ) {
                $x1 = $this->graphAreaX1 + $abscissaMargin;
                $x2 = $this->graphAreaX2 - $abscissaMargin;
                $this->drawLine(
                        $x1, $yPos, $x2, $yPos, [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "ticks" => $ticks,
                    "weight" => $weight
                        ]
                );
                if ($wide) {
                    $this->drawLine(
                            $x1, $yPos - 1, $x2, $yPos - 1, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                    $this->drawLine(
                            $x1, $yPos + 1, $x2, $yPos + 1, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                }
                if ($writeCaption) {
                    if ($captionAlign == CAPTION_LEFT_TOP) {
                        $x = $x1 + $captionOffset;
                        $captionSettings["align"] = TEXT_ALIGN_MIDDLELEFT;
                    } else {
                        $x = $x2 - $captionOffset;
                        $captionSettings["align"] = TEXT_ALIGN_MIDDLERIGHT;
                    }
                    $this->drawText($x, $yPos, $caption, $captionSettings);
                }
            }
            return ["y" => $yPos];
        }
        if ($data["orientation"] == SCALE_POS_TOPBOTTOM) {
            $xPos = $this->scaleComputeY($value, ["axisId" => $axisId]);
            if ($xPos >= $this->graphAreaX1 + $data["axis"][$axisId]["margin"] && $xPos <= $this->graphAreaX2 - $data["axis"][$axisId]["margin"]
            ) {
                $y1 = $this->graphAreaY1 + $abscissaMargin;
                $y2 = $this->graphAreaY2 - $abscissaMargin;
                $this->drawLine(
                        $xPos, $y1, $xPos, $y2, [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "ticks" => $ticks,
                    "weight" => $weight
                        ]
                );
                if ($wide) {
                    $this->drawLine(
                            $xPos - 1, $y1, $xPos - 1, $y2, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                    $this->drawLine(
                            $xPos + 1, $y1, $xPos + 1, $y2, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                }
                if ($writeCaption) {
                    if ($captionAlign == CAPTION_LEFT_TOP) {
                        $y = $y1 + $captionOffset;
                        $captionSettings["align"] = TEXT_ALIGN_TOPMIDDLE;
                    } else {
                        $y = $y2 - $captionOffset;
                        $captionSettings["align"] = TEXT_ALIGN_BOTTOMMIDDLE;
                    }
                    $captionSettings["align"] = TEXT_ALIGN_TOPMIDDLE;
                    $this->drawText($xPos, $y, $caption, $captionSettings);
                }
            }
            return ["y" => $xPos];
        }
    }

    /**
     * Draw a threshold with the computed scale
     * @param mixed $value1
     * @param mixed $value2
     * @param array $format
     * @return array|int|null
     */
    public function drawThresholdArea($value1, $value2, array $format = []) {
        $axisId = isset($format["axisId"]) ? $format["axisId"] : 0;
        $r = isset($format["r"]) ? $format["r"] : 255;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 20;
        $border = isset($format["border"]) ? $format["border"] : true;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $r;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $g;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $b;
        $borderalpha = isset($format["borderalpha"]) ? $format["borderalpha"] : $alpha + 20;
        $borderTicks = isset($format["borderTicks"]) ? $format["borderTicks"] : 2;
        $areaName = isset($format["areaName"]) ? $format["areaName"] : null;
        $NameAngle = isset($format["nameAngle"]) ? $format["nameAngle"] : ZONE_NAME_ANGLE_AUTO;
        $NameR = isset($format["nameR"]) ? $format["nameR"] : 255;
        $NameG = isset($format["nameG"]) ? $format["nameG"] : 255;
        $NameB = isset($format["nameB"]) ? $format["nameB"] : 255;
        $Namealpha = isset($format["namealpha"]) ? $format["namealpha"] : 100;
        $disableShadowOnArea = isset($format["disableShadowOnArea"]) ? $format["disableShadowOnArea"] : true;
        $NoMargin = isset($format["noMargin"]) ? $format["noMargin"] : false;
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
        if (!isset($data["axis"][$axisId])) {
            return -1;
        }
        if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
            $xPos1 = $this->graphAreaX1 + $abscissaMargin;
            $xPos2 = $this->graphAreaX2 - $abscissaMargin;
            $yPos1 = $this->scaleComputeY($value1, ["axisId" => $axisId]);
            $yPos2 = $this->scaleComputeY($value2, ["axisId" => $axisId]);
            if ($yPos1 < $this->graphAreaY1 + $data["axis"][$axisId]["margin"]) {
                $yPos1 = $this->graphAreaY1 + $data["axis"][$axisId]["margin"];
            }
            if ($yPos1 > $this->graphAreaY2 - $data["axis"][$axisId]["margin"]) {
                $yPos1 = $this->graphAreaY2 - $data["axis"][$axisId]["margin"];
            }
            if ($yPos2 < $this->graphAreaY1 + $data["axis"][$axisId]["margin"]) {
                $yPos2 = $this->graphAreaY1 + $data["axis"][$axisId]["margin"];
            }
            if ($yPos2 > $this->graphAreaY2 - $data["axis"][$axisId]["margin"]) {
                $yPos2 = $this->graphAreaY2 - $data["axis"][$axisId]["margin"];
            }
            $this->drawFilledRectangle(
                    $xPos1, $yPos1, $xPos2, $yPos2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            if ($border) {
                $this->drawLine(
                        $xPos1, $yPos1, $xPos2, $yPos1, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
                $this->drawLine(
                        $xPos1, $yPos2, $xPos2, $yPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
            }
            if ($areaName != null) {
                $xPos = ($xPos2 - $xPos1) / 2 + $xPos1;
                $yPos = ($yPos2 - $yPos1) / 2 + $yPos1;
                $this->shadow = $restoreShadow;
                $this->drawText(
                        $xPos, $yPos, $areaName, [
                    "r" => $NameR,
                    "g" => $NameG,
                    "b" => $NameB,
                    "alpha" => $Namealpha,
                    "Angle" => 0,
                    "align" => TEXT_ALIGN_MIDDLEMIDDLE
                        ]
                );
                if ($disableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ["y1" => $yPos1, "Y2" => $yPos2];
        } elseif ($data["orientation"] == SCALE_POS_TOPBOTTOM) {
            $yPos1 = $this->graphAreaY1 + $abscissaMargin;
            $yPos2 = $this->graphAreaY2 - $abscissaMargin;
            $xPos1 = $this->scaleComputeY($value1, ["axisId" => $axisId]);
            $xPos2 = $this->scaleComputeY($value2, ["axisId" => $axisId]);
            if ($xPos1 < $this->graphAreaX1 + $data["axis"][$axisId]["margin"]) {
                $xPos1 = $this->graphAreaX1 + $data["axis"][$axisId]["margin"];
            }
            if ($xPos1 > $this->graphAreaX2 - $data["axis"][$axisId]["margin"]) {
                $xPos1 = $this->graphAreaX2 - $data["axis"][$axisId]["margin"];
            }
            if ($xPos2 < $this->graphAreaX1 + $data["axis"][$axisId]["margin"]) {
                $xPos2 = $this->graphAreaX1 + $data["axis"][$axisId]["margin"];
            }
            if ($xPos2 > $this->graphAreaX2 - $data["axis"][$axisId]["margin"]) {
                $xPos2 = $this->graphAreaX2 - $data["axis"][$axisId]["margin"];
            }
            $this->drawFilledRectangle(
                    $xPos1, $yPos1, $xPos2, $yPos2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            if ($border) {
                $this->drawLine(
                        $xPos1, $yPos1, $xPos1, $yPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
                $this->drawLine(
                        $xPos2, $yPos1, $xPos2, $yPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
            }
            if ($areaName != null) {
                $xPos = ($yPos2 - $yPos1) / 2 + $yPos1;
                $yPos = ($xPos2 - $xPos1) / 2 + $xPos1;
                if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
                    $txtPos = $this->getTextBox(
                            $xPos, $yPos, $this->fontName, $this->fontSize, 0, $areaName
                    );
                    $txtWidth = $txtPos[1]["x"] - $txtPos[0]["x"];
                    $NameAngle = 90;
                    if (abs($xPos2 - $xPos1) > $txtWidth) {
                        $NameAngle = 0;
                    }
                }
                $this->shadow = $restoreShadow;
                $this->drawText(
                        $yPos, $xPos, $areaName, [
                    "r" => $NameR,
                    "g" => $NameG,
                    "b" => $NameB,
                    "alpha" => $Namealpha,
                    "Angle" => $NameAngle,
                    "align" => TEXT_ALIGN_MIDDLEMIDDLE
                        ]
                );
                if ($disableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ["y1" => $xPos1, "Y2" => $xPos2];
        }
    }

    /**
     * Draw a plot chart
     * @param array $format
     */
    public function drawPlotChart(array $format = []) {
        $plotSize = isset($format["plotSize"]) ? $format["plotSize"] : null;
        $plotBorder = isset($format["plotBorder"]) ? $format["plotBorder"] : false;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : 50;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : 50;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : 50;
        $borderalpha = isset($format["borderalpha"]) ? $format["borderalpha"] : 30;
        $borderSize = isset($format["borderSize"]) ? $format["borderSize"] : 2;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $displayValues = isset($format["displayValues"]) ? $format["displayValues"] : false;
        $displayOffset = isset($format["displayOffset"]) ? $format["displayOffset"] : 4;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["displayR"]) ? $format["displayR"] : 0;
        $displayG = isset($format["displayG"]) ? $format["displayG"] : 0;
        $displayB = isset($format["displayB"]) ? $format["displayB"] : 0;
        $recordImageMap = isset($format["recordImageMap"]) ? $format["recordImageMap"] : false;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                if (isset($serie["weight"])) {
                    $serieWeight = $serie["weight"] + 2;
                } else {
                    $serieWeight = 2;
                }
                if ($plotSize != null) {
                    $serieWeight = $plotSize;
                }
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = (int) $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                if ($surrounding != null) {
                    $borderR = $r + $surrounding;
                    $borderG = $g + $surrounding;
                    $borderB = $b + $surrounding;
                }
                if (isset($serie["picture"])) {
                    $picture = $serie["picture"];
                    list($picWidth, $picHeight, $picType) = $this->getPicInfo($picture);
                } else {
                    $picture = null;
                    $picOffset = 0;
                }
                if ($displayColor == DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $shape = $serie["shape"];
                $mode = $data["axis"][$axisId]["display"];
                $format = $data["axis"][$axisId]["format"];
                $unit = $data["axis"][$axisId]["unit"];
                if (isset($serie["description"])) {
                    $serieDescription = $serie["description"];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY($serie["data"], ["axisId" => $serie["axis"]]);
                $this->dataSet->data["series"][$serieName]["xOffset"] = 0;
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                                    $x, $y - $displayOffset - $serieWeight - $borderSize - $picOffset, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($y != VOID) {
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "CIRCLE", floor($x) . "," . floor($y) . "," . $serieWeight, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat(
                                                $serie["data"][$key], $mode, $format, $unit
                                        )
                                );
                            }
                            if ($picture != null) {
                                $this->drawFromPicture(
                                        $picType, $picture, $x - $picWidth / 2, $y - $picHeight / 2
                                );
                            } else {
                                $this->drawShape(
                                        $x, $y, $shape, $serieWeight, $plotBorder, $borderSize, $r, $g, $b, $alpha, $borderR, $borderG, $borderB, $borderalpha
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
                                    $x + $displayOffset + $serieWeight + $borderSize + $picOffset, $y, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($x != VOID) {
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "CIRCLE", floor($x) . "," . floor($y) . "," . $serieWeight, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                );
                            }
                            if ($picture != null) {
                                $this->drawFromPicture(
                                        $picType, $picture, $x - $picWidth / 2, $y - $picHeight / 2
                                );
                            } else {
                                $this->drawShape(
                                        $x, $y, $shape, $serieWeight, $plotBorder, $borderSize, $r, $g, $b, $alpha, $borderR, $borderG, $borderB, $borderalpha
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
     * @param array $format
     */
    public function drawSplineChart(array $format = []) {
        $breakVoid = isset($format["breakVoid"]) ? $format["breakVoid"] : true;
        $voidTicks = isset($format["voidTicks"]) ? $format["voidTicks"] : 4;
        $breakR = isset($format["breakR"]) ? $format["breakR"] : null; // 234
        $breakG = isset($format["breakG"]) ? $format["breakG"] : null; // 55
        $breakB = isset($format["breakB"]) ? $format["breakB"] : null; // 26
        $displayValues = isset($format["displayValues"]) ? $format["displayValues"] : false;
        $displayOffset = isset($format["displayOffset"]) ? $format["displayOffset"] : 2;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["displayR"]) ? $format["displayR"] : 0;
        $displayG = isset($format["displayG"]) ? $format["displayG"] : 0;
        $displayB = isset($format["displayB"]) ? $format["displayB"] : 0;
        $recordImageMap = isset($format["recordImageMap"]) ? $format["recordImageMap"] : false;
        $ImageMapPlotSize = isset($format["imageMapPlotSize"]) ? $format["imageMapPlotSize"] : 5;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                $weight = $serie["weight"];
                if ($breakR == null) {
                    $breakSettings = [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha,
                        "ticks" => $voidTicks
                    ];
                } else {
                    $breakSettings = [
                        "r" => $breakR,
                        "g" => $breakG,
                        "b" => $breakB,
                        "alpha" => $alpha,
                        "ticks" => $voidTicks,
                        "weight" => $weight
                    ];
                }
                if ($displayColor == DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["display"];
                $format = $data["axis"][$axisId]["format"];
                $unit = $data["axis"][$axisId]["unit"];
                if (isset($serie["description"])) {
                    $serieDescription = $serie["description"];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisId" => $serie["axis"]]
                );
                $this->dataSet->data["series"][$serieName]["xOffset"] = 0;
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                                    $x, $y - $displayOffset, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($recordImageMap && $y != VOID) {
                            $this->addToImageMap(
                                    "CIRCLE", floor($x) . "," . floor($y) . "," . $ImageMapPlotSize, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                            );
                        }
                        if ($y == VOID && $lastY != null) {
                            $this->drawSpline(
                                    $wayPoints, [
                                "Force" => $force,
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                                    ]
                            );
                            $wayPoints = [];
                        }
                        if ($y != VOID && $lastY == null && $lastGoodY != null && !$breakVoid) {
                            $this->drawLine($lastGoodX, $lastGoodY, $x, $y, $breakSettings);
                        }
                        if ($y != VOID) {
                            $wayPoints[] = [$x, $y];
                        }
                        if ($y != VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($y == VOID) {
                            $y = null;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        $x = $x + $xStep;
                    }
                    $this->drawSpline(
                            $wayPoints, [
                        "Force" => $force,
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha,
                        "ticks" => $ticks,
                        "weight" => $weight
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
                                    $x + $displayOffset, $y, $this->scaleFormat(
                                            $serie["data"][$key], $mode, $format, $unit
                                    ), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($recordImageMap && $x != VOID) {
                            $this->addToImageMap(
                                    "CIRCLE", floor($x) . "," . floor($y) . "," . $ImageMapPlotSize, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                            );
                        }
                        if ($x == VOID && $lastX != null) {
                            $this->drawSpline(
                                    $wayPoints, [
                                "Force" => $force,
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                                    ]
                            );
                            $wayPoints = [];
                        }
                        if ($x != VOID && $lastX == null && $lastGoodX != null && !$breakVoid) {
                            $this->drawLine($lastGoodX, $lastGoodY, $x, $y, $breakSettings);
                        }
                        if ($x != VOID) {
                            $wayPoints[] = [$x, $y];
                        }
                        if ($x != VOID) {
                            $lastGoodX = $x;
                            $lastGoodY = $y;
                        }
                        if ($x == VOID) {
                            $x = null;
                        }
                        $lastX = $x;
                        $lastY = $y;
                        $y = $y + $yStep;
                    }
                    $this->drawSpline(
                            $wayPoints, [
                        "Force" => $force,
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha,
                        "ticks" => $ticks,
                        "weight" => $weight
                            ]
                    );
                }
            }
        }
    }

    /**
     * Draw a filled spline chart
     * @param array $format
     */
    public function drawFilledSplineChart(array $format = []) {
        $displayValues = isset($format["displayValues"]) ? $format["displayValues"] : false;
        $displayOffset = isset($format["displayOffset"]) ? $format["displayOffset"] : 2;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["displayR"]) ? $format["displayR"] : 0;
        $displayG = isset($format["displayG"]) ? $format["displayG"] : 0;
        $displayB = isset($format["displayB"]) ? $format["displayB"] : 0;
        $aroundZero = isset($format["aroundZero"]) ? $format["aroundZero"] : true;
        $threshold = isset($format["Threshold"]) ? $format["Threshold"] : null;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                if ($displayColor == DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["display"];
                $format = $data["axis"][$axisId]["format"];
                $unit = $data["axis"][$axisId]["unit"];
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisId" => $serie["axis"]]
                );
                if ($aroundZero) {
                    $yZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                }
                if ($threshold != null) {
                    foreach ($threshold as $key => $params) {
                        $threshold[$key]["minX"] = $this->scaleComputeY(
                                $params["min"], ["axisId" => $serie["axis"]]
                        );
                        $threshold[$key]["maxX"] = $this->scaleComputeY(
                                $params["max"], ["axisId" => $serie["axis"]]
                        );
                    }
                }
                $this->dataSet->data["series"][$serieName]["xOffset"] = 0;
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                                    $x, $y - $displayOffset, $this->scaleFormat(
                                            $serie["data"][$key], $mode, $format, $unit
                                    ), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($y == VOID) {
                            $area = $this->drawSpline(
                                    $wayPoints, ["force" => $force, "PathOnly" => true]
                            );
                            if (count($area)) {
                                foreach ($area as $key => $points) {
                                    $Corners = [];
                                    $Corners[] = $area[$key][0]["x"];
                                    $Corners[] = $yZero;
                                    foreach ($points as $subKey => $point) {
                                        if ($subKey == count($points) - 1) {
                                            $Corners[] = $point["x"] - 1;
                                        } else {
                                            $Corners[] = $point["x"];
                                        }
                                        $Corners[] = $point["y"] + 1;
                                    }
                                    $Corners[] = $points[$subKey]["x"] - 1;
                                    $Corners[] = $yZero;
                                    $this->drawPolygonChart(
                                            $Corners, [
                                        "r" => $r,
                                        "g" => $g,
                                        "b" => $b,
                                        "alpha" => $alpha / 2,
                                        "noBorder" => true,
                                        "Threshold" => $threshold
                                            ]
                                    );
                                }
                                $this->drawSpline(
                                        $wayPoints, [
                                    "Force" => $force,
                                    "r" => $r,
                                    "g" => $g,
                                    "b" => $b,
                                    "alpha" => $alpha,
                                    "ticks" => $ticks
                                        ]
                                );
                            }
                            $wayPoints = [];
                        } else {
                            $wayPoints[] = [$x, $y - .5]; /* -.5 for AA visual fix */
                        }
                        $x = $x + $xStep;
                    }
                    $area = $this->drawSpline($wayPoints, ["force" => $force, "PathOnly" => true]);
                    if (count($area)) {
                        foreach ($area as $key => $points) {
                            $Corners = [];
                            $Corners[] = $area[$key][0]["x"];
                            $Corners[] = $yZero;
                            foreach ($points as $subKey => $point) {
                                if ($subKey == count($points) - 1) {
                                    $Corners[] = $point["x"] - 1;
                                } else {
                                    $Corners[] = $point["x"];
                                }
                                $Corners[] = $point["y"] + 1;
                            }
                            $Corners[] = $points[$subKey]["x"] - 1;
                            $Corners[] = $yZero;
                            $this->drawPolygonChart(
                                    $Corners, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha / 2,
                                "noBorder" => true,
                                "Threshold" => $threshold
                                    ]
                            );
                        }
                        $this->drawSpline(
                                $wayPoints, [
                            "Force" => $force,
                            "r" => $r,
                            "g" => $g,
                            "b" => $b,
                            "alpha" => $alpha,
                            "ticks" => $ticks
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
                                    $x + $displayOffset, $y, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($x == VOID) {
                            $area = $this->drawSpline(
                                    $wayPoints, ["force" => $force, "PathOnly" => true]
                            );
                            if (count($area)) {
                                foreach ($area as $key => $points) {
                                    $Corners = [];
                                    $Corners[] = $yZero;
                                    $Corners[] = $area[$key][0]["y"];
                                    foreach ($points as $subKey => $point) {
                                        if ($subKey == count($points) - 1) {
                                            $Corners[] = $point["x"] - 1;
                                        } else {
                                            $Corners[] = $point["x"];
                                        }
                                        $Corners[] = $point["y"];
                                    }
                                    $Corners[] = $yZero;
                                    $Corners[] = $points[$subKey]["y"] - 1;
                                    $this->drawPolygonChart(
                                            $Corners, [
                                        "r" => $r,
                                        "g" => $g,
                                        "b" => $b,
                                        "alpha" => $alpha / 2,
                                        "noBorder" => true,
                                        "Threshold" => $threshold
                                            ]
                                    );
                                }
                                $this->drawSpline(
                                        $wayPoints, [
                                    "Force" => $force,
                                    "r" => $r,
                                    "g" => $g,
                                    "b" => $b,
                                    "alpha" => $alpha,
                                    "ticks" => $ticks
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
                            $wayPoints, ["force" => $force, "PathOnly" => true]
                    );
                    if (count($area)) {
                        foreach ($area as $key => $points) {
                            $Corners = [];
                            $Corners[] = $yZero;
                            $Corners[] = $area[$key][0]["y"];
                            foreach ($points as $subKey => $point) {
                                if ($subKey == count($points) - 1) {
                                    $Corners[] = $point["x"] - 1;
                                } else {
                                    $Corners[] = $point["x"];
                                }
                                $Corners[] = $point["y"];
                            }
                            $Corners[] = $yZero;
                            $Corners[] = $points[$subKey]["y"] - 1;
                            $this->drawPolygonChart(
                                    $Corners, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha / 2,
                                "noBorder" => true,
                                "Threshold" => $threshold
                                    ]
                            );
                        }
                        $this->drawSpline(
                                $wayPoints, [
                            "Force" => $force,
                            "r" => $r,
                            "g" => $g,
                            "b" => $b,
                            "alpha" => $alpha,
                            "ticks" => $ticks
                                ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Draw a line chart
     * @param array $format
     */
    public function drawLineChart(array $format = []) {
        $breakVoid = isset($format["breakVoid"]) ? $format["breakVoid"] : true;
        $voidTicks = isset($format["voidTicks"]) ? $format["voidTicks"] : 4;
        $breakR = isset($format["breakR"]) ? $format["breakR"] : null;
        $breakG = isset($format["breakG"]) ? $format["breakG"] : null;
        $breakB = isset($format["breakB"]) ? $format["breakB"] : null;
        $displayValues = isset($format["displayValues"]) ? $format["displayValues"] : false;
        $displayOffset = isset($format["displayOffset"]) ? $format["displayOffset"] : 2;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : Constant::DISPLAY_MANUAL;
        $displayR = isset($format["displayR"]) ? $format["displayR"] : 0;
        $displayG = isset($format["displayG"]) ? $format["displayG"] : 0;
        $displayB = isset($format["displayB"]) ? $format["displayB"] : 0;
        $recordImageMap = isset($format["recordImageMap"]) ? $format["recordImageMap"] : false;
        $ImageMapPlotSize = isset($format["imageMapPlotSize"]) ? $format["imageMapPlotSize"] : 5;
        $forceColor = isset($format["forceColor"]) ? $format["forceColor"] : false;
        $forceR = isset($format["forceR"]) ? $format["forceR"] : 0;
        $forceG = isset($format["forceG"]) ? $format["forceG"] : 0;
        $forceB = isset($format["forceB"]) ? $format["forceB"] : 0;
        $forcealpha = isset($format["forcealpha"]) ? $format["forcealpha"] : 100;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                $weight = $serie["weight"];
                if ($forceColor) {
                    $r = $forceR;
                    $g = $forceG;
                    $b = $forceB;
                    $alpha = $forcealpha;
                }
                if ($breakR == null) {
                    $breakSettings = [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha,
                        "ticks" => $voidTicks,
                        "weight" => $weight
                    ];
                } else {
                    $breakSettings = [
                        "r" => $breakR,
                        "g" => $breakG,
                        "b" => $breakB,
                        "alpha" => $alpha,
                        "ticks" => $voidTicks,
                        "weight" => $weight
                    ];
                }
                if ($displayColor == Constant::DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["display"];
                $format = $data["axis"][$axisId]["format"];
                $unit = $data["axis"][$axisId]["unit"];
                if (isset($serie["description"])) {
                    $serieDescription = $serie["description"];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisId" => $serie["axis"]]
                );
                $this->dataSet->data["series"][$serieName]["xOffset"] = 0;
                if ($data["orientation"] == Constant::SCALE_POS_LEFTRIGHT) {
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
                        if ($displayValues && $serie["data"][$key] != Constant::VOID) {
                            if ($serie["data"][$key] > 0) {
                                $align = Constant::TEXT_ALIGN_BOTTOMMIDDLE;
                                $offset = $displayOffset;
                            } else {
                                $align = Constant::TEXT_ALIGN_TOPMIDDLE;
                                $offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $x, $y - $offset - $weight, $this->scaleFormat(
                                            $serie["data"][$key], $mode, $format, $unit
                                    ), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => $align
                                    ]
                            );
                        }
                        if ($recordImageMap && $y != Constant::VOID) {
                            $this->addToImageMap(
                                    "CIRCLE", floor($x) . "," . floor($y) . "," . $ImageMapPlotSize, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                            );
                        }
                        if ($y != Constant::VOID && $lastX != null && $lastY != null) {
                            $this->drawLine(
                                    $lastX, $lastY, $x, $y, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                                    ]
                            );
                        }
                        if ($y != Constant::VOID && $lastY == null && $lastGoodY != null && !$breakVoid) {
                            $this->drawLine(
                                    $lastGoodX, $lastGoodY, $x, $y, $breakSettings
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
                        if ($displayValues && $serie["data"][$key] != Constant::VOID) {
                            $this->drawText(
                                    $x + $displayOffset + $weight, $y, $this->scaleFormat(
                                            $serie["data"][$key], $mode, $format, $unit
                                    ), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => Constant::TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($recordImageMap && $x != Constant::VOID) {
                            $this->addToImageMap(
                                    "CIRCLE", floor($x) . "," . floor($y) . "," . $ImageMapPlotSize, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                            );
                        }
                        if ($x != Constant::VOID && $lastX != null && $lastY != null) {
                            $this->drawLine(
                                    $lastX, $lastY, $x, $y, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                                    ]
                            );
                        }
                        if ($x != Constant::VOID && $lastX == null && $lastGoodY != null && !$breakVoid) {
                            $this->drawLine(
                                    $lastGoodX, $lastGoodY, $x, $y, $breakSettings
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
     * @param array $format
     * @return null|integer
     */
    public function drawZoneChart($serieA, $serieB, array $format = []) {
        $axisId = isset($format["axisId"]) ? $format["axisId"] : 0;
        $lineR = isset($format["lineR"]) ? $format["lineR"] : 150;
        $lineG = isset($format["lineG"]) ? $format["lineG"] : 150;
        $lineB = isset($format["lineB"]) ? $format["lineB"] : 150;
        $linealpha = isset($format["linealpha"]) ? $format["linealpha"] : 50;
        $lineTicks = isset($format["lineTicks"]) ? $format["lineTicks"] : 1;
        $areaR = isset($format["areaR"]) ? $format["areaR"] : 150;
        $areaG = isset($format["areaG"]) ? $format["areaG"] : 150;
        $areaB = isset($format["areaB"]) ? $format["areaB"] : 150;
        $areaalpha = isset($format["areaalpha"]) ? $format["areaalpha"] : 5;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        if (!isset($data["series"][$serieA]["data"]) || !isset($data["series"][$serieB]["data"])
        ) {
            return 0;
        }
        $serieAData = $data["series"][$serieA]["data"];
        $serieBData = $data["series"][$serieB]["data"];
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        $mode = $data["axis"][$axisId]["display"];
        $format = $data["axis"][$axisId]["format"];
        $posArrayA = $this->scaleComputeY($serieAData, ["axisId" => $axisId]);
        $posArrayB = $this->scaleComputeY($serieBData, ["axisId" => $axisId]);
        if (count($posArrayA) != count($posArrayB)) {
            return 0;
        }
        if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                    $bounds, [
                "r" => $areaR,
                "g" => $areaG,
                "b" => $areaB,
                "alpha" => $areaalpha
                    ]
            );
            for ($i = 0; $i <= count($boundsA) - 4; $i = $i + 2) {
                $this->drawLine(
                        $boundsA[$i], $boundsA[$i + 1], $boundsA[$i + 2], $boundsA[$i + 3], [
                    "r" => $lineR,
                    "g" => $lineG,
                    "b" => $lineB,
                    "alpha" => $linealpha,
                    "ticks" => $lineTicks
                        ]
                );
                $this->drawLine(
                        $boundsB[$i], $boundsB[$i + 1], $boundsB[$i + 2], $boundsB[$i + 3], [
                    "r" => $lineR,
                    "g" => $lineG,
                    "b" => $lineB,
                    "alpha" => $linealpha,
                    "ticks" => $lineTicks
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
                    $bounds, ["r" => $areaR, "g" => $areaG, "b" => $areaB, "alpha" => $areaalpha]
            );
            for ($i = 0; $i <= count($boundsA) - 4; $i = $i + 2) {
                $this->drawLine(
                        $boundsA[$i], $boundsA[$i + 1], $boundsA[$i + 2], $boundsA[$i + 3], [
                    "r" => $lineR,
                    "g" => $lineG,
                    "b" => $lineB,
                    "alpha" => $linealpha,
                    "ticks" => $lineTicks
                        ]
                );
                $this->drawLine(
                        $boundsB[$i], $boundsB[$i + 1], $boundsB[$i + 2], $boundsB[$i + 3], [
                    "r" => $lineR,
                    "g" => $lineG,
                    "b" => $lineB,
                    "alpha" => $linealpha,
                    "ticks" => $lineTicks
                        ]
                );
            }
        }
    }

    /**
     * Draw a step chart
     * @param array $format
     */
    public function drawStepChart(array $format = []) {
        $breakVoid = isset($format["breakVoid"]) ? $format["breakVoid"] : false;
        $reCenter = isset($format["reCenter"]) ? $format["reCenter"] : true;
        $voidTicks = isset($format["voidTicks"]) ? $format["voidTicks"] : 4;
        $breakR = isset($format["breakR"]) ? $format["breakR"] : null;
        $breakG = isset($format["breakG"]) ? $format["breakG"] : null;
        $breakB = isset($format["breakB"]) ? $format["breakB"] : null;
        $displayValues = isset($format["displayValues"]) ? $format["displayValues"] : false;
        $displayOffset = isset($format["displayOffset"]) ? $format["displayOffset"] : 2;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["displayR"]) ? $format["displayR"] : 0;
        $displayG = isset($format["displayG"]) ? $format["displayG"] : 0;
        $displayB = isset($format["displayB"]) ? $format["displayB"] : 0;
        $recordImageMap = isset($format["recordImageMap"]) ? $format["recordImageMap"] : false;
        $ImageMapPlotSize = isset($format["imageMapPlotSize"]) ? $format["imageMapPlotSize"] : 5;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                $weight = $serie["weight"];
                if (isset($serie["description"])) {
                    $serieDescription = $serie["description"];
                } else {
                    $serieDescription = $serieName;
                }
                if ($breakR == null) {
                    $breakSettings = [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha,
                        "ticks" => $voidTicks,
                        "weight" => $weight
                    ];
                } else {
                    $breakSettings = [
                        "r" => $breakR,
                        "g" => $breakG,
                        "b" => $breakB,
                        "alpha" => $alpha,
                        "ticks" => $voidTicks,
                        "weight" => $weight
                    ];
                }
                if ($displayColor == DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["display"];
                $format = $data["axis"][$axisId]["format"];
                $unit = $data["axis"][$axisId]["unit"];
                $color = [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "ticks" => $ticks,
                    "weight" => $weight
                ];
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisId" => $serie["axis"]]
                );
                $this->dataSet->data["series"][$serieName]["xOffset"] = 0;
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                        if ($displayValues && $serie["data"][$key] != VOID) {
                            if ($y <= $lastY) {
                                $align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $offset = $displayOffset;
                            } else {
                                $align = TEXT_ALIGN_TOPMIDDLE;
                                $offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $x, $y - $offset - $weight, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit), ["r" => $displayR, "g" => $displayG, "b" => $displayB, "align" => $align]
                            );
                        }
                        if ($y != VOID && $lastX != null && $lastY != null) {
                            $this->drawLine($lastX, $lastY, $x, $lastY, $color);
                            $this->drawLine($x, $lastY, $x, $y, $color);
                            if ($reCenter && $x + $xStep < $this->graphAreaX2 - $xMargin) {
                                $this->drawLine($x, $y, $x + $xStep, $y, $color);
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    '%s,%s,%s,%s', floor($x - $ImageMapPlotSize), floor($y - $ImageMapPlotSize), floor($x + $xStep + $ImageMapPlotSize), floor($y + $ImageMapPlotSize)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                    );
                                }
                            } else {
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    '%s,%s,%s,%s', floor($lastX - $ImageMapPlotSize), floor($lastY - $ImageMapPlotSize), floor($x + $ImageMapPlotSize), floor($lastY + $ImageMapPlotSize)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                    );
                                }
                            }
                        }
                        if ($y != VOID && $lastY == null && $lastGoodY != null && !$breakVoid) {
                            if ($reCenter) {
                                $this->drawLine($lastGoodX + $xStep, $lastGoodY, $x, $lastGoodY, $breakSettings);
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    '%s,%s,%s,%s', floor($lastGoodX + $xStep - $ImageMapPlotSize), floor($lastGoodY - $ImageMapPlotSize), floor($x + $ImageMapPlotSize), floor($lastGoodY + $ImageMapPlotSize)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                    );
                                }
                            } else {
                                $this->drawLine($lastGoodX, $lastGoodY, $x, $lastGoodY, $breakSettings);
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    '%s,%s,%s,%s', floor($lastGoodX - $ImageMapPlotSize), floor($lastGoodY - $ImageMapPlotSize), floor($x + $ImageMapPlotSize), floor($lastGoodY + $ImageMapPlotSize)
                                            ), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                    );
                                }
                            }
                            $this->drawLine($x, $lastGoodY, $x, $y, $breakSettings);
                            $lastGoodY = null;
                        } elseif (!$breakVoid && $lastGoodY == null && $y != VOID) {
                            $this->drawLine($this->graphAreaX1 + $xMargin, $y, $x, $y, $breakSettings);
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($this->graphAreaX1 + $xMargin - $ImageMapPlotSize), floor($y - $ImageMapPlotSize), floor($x + $ImageMapPlotSize), floor($y + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                );
                            }
                        }
                        if ($y != VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($y == VOID) {
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
                                    "RECT", sprintf(
                                            '%s,%s,%s,%s', floor($lastX - $ImageMapPlotSize), floor($lastY - $ImageMapPlotSize), floor($this->graphAreaX2 - $xMargin + $ImageMapPlotSize), floor($lastY + $ImageMapPlotSize)
                                    ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
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
                        if ($displayValues && $serie["data"][$key] != VOID) {
                            if ($x >= $lastX) {
                                $align = TEXT_ALIGN_MIDDLELEFT;
                                $offset = $displayOffset;
                            } else {
                                $align = TEXT_ALIGN_MIDDLERIGHT;
                                $offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $x + $offset + $weight, $y, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => $align
                                    ]
                            );
                        }
                        if ($x != VOID && $lastX != null && $lastY != null) {
                            $this->drawLine($lastX, $lastY, $lastX, $y, $color);
                            $this->drawLine($lastX, $y, $x, $y, $color);
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($lastX - $ImageMapPlotSize), floor($lastY - $ImageMapPlotSize), floor($lastX + $xStep + $ImageMapPlotSize), floor($y + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                );
                            }
                        }
                        if ($x != VOID && $lastX == null && $lastGoodY != null && !$breakVoid) {
                            $this->drawLine(
                                    $lastGoodX, $lastGoodY, $lastGoodX, $lastGoodY + $yStep, $color
                            );
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($lastGoodX - $ImageMapPlotSize), floor($lastGoodY - $ImageMapPlotSize), floor($lastGoodX + $ImageMapPlotSize), floor($lastGoodY + $yStep + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                );
                            }
                            $this->drawLine(
                                    $lastGoodX, $lastGoodY + $yStep, $lastGoodX, $y, $breakSettings
                            );
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($lastGoodX - $ImageMapPlotSize), floor($lastGoodY + $yStep - $ImageMapPlotSize), floor($lastGoodX + $ImageMapPlotSize), floor($yStep + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                );
                            }
                            $this->drawLine($lastGoodX, $y, $x, $y, $breakSettings);
                            $lastGoodY = null;
                        } elseif ($x != VOID && $lastGoodY == null && !$breakVoid) {
                            $this->drawLine($x, $this->graphAreaY1 + $xMargin, $x, $y, $breakSettings);
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($x - $ImageMapPlotSize), floor($this->graphAreaY1 + $xMargin - $ImageMapPlotSize), floor($x + $ImageMapPlotSize), floor($y + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                );
                            }
                        }
                        if ($x != VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($x == VOID) {
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
                                    "RECT", sprintf(
                                            '%s,%s,%s,%s', floor($lastX - $ImageMapPlotSize), floor($lastY - $ImageMapPlotSize), floor($lastX + $ImageMapPlotSize), floor($this->graphAreaY2 - $xMargin + $ImageMapPlotSize)
                                    ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Draw a step chart
     * @param array $format
     */
    public function drawFilledStepChart(array $format = []) {
        $reCenter = isset($format["reCenter"]) ? $format["reCenter"] : true;
        $displayValues = isset($format["displayValues"]) ? $format["displayValues"] : false;
        $displayOffset = isset($format["displayOffset"]) ? $format["displayOffset"] : 2;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : DISPLAY_MANUAL;
        $forceTransparency = isset($format["forceTransparency"]) ? $format["forceTransparency"] : null;
        $displayR = isset($format["displayR"]) ? $format["displayR"] : 0;
        $displayG = isset($format["displayG"]) ? $format["displayG"] : 0;
        $displayB = isset($format["displayB"]) ? $format["displayB"] : 0;
        $aroundZero = isset($format["aroundZero"]) ? $format["aroundZero"] : true;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                if ($displayColor == DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $format = $data["axis"][$axisId]["format"];
                $color = ["r" => $r, "g" => $g, "b" => $b];
                if ($forceTransparency != null) {
                    $color["alpha"] = $forceTransparency;
                } else {
                    $color["alpha"] = $alpha;
                }
                $posArray = $this->scaleComputeY($serie["data"], ["axisId" => $serie["axis"]]);
                $yZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                $this->dataSet->data["series"][$serieName]["xOffset"] = 0;
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                        if ($y == VOID && $lastX != null && $lastY != null && count($points)) {
                            $points[] = $lastX;
                            $points[] = $lastY;
                            $points[] = $x;
                            $points[] = $lastY;
                            $points[] = $x;
                            $points[] = $yZero;
                            $this->drawPolygon($points, $color);
                            $points = [];
                        }
                        if ($y != VOID && $lastX != null && $lastY != null) {
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
                        if ($y != VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($y == VOID) {
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
                        if ($x == VOID && $lastX != null && $lastY != null && count($points)) {
                            $points[] = $lastX;
                            $points[] = $lastY;
                            $points[] = $lastX;
                            $points[] = $y;
                            $points[] = $yZero;
                            $points[] = $y;
                            $this->drawPolygon($points, $color);
                            $points = [];
                        }
                        if ($x != VOID && $lastX != null && $lastY != null) {
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
                        if ($x != VOID) {
                            $lastGoodY = $y;
                            $lastGoodX = $x;
                        }
                        if ($x == VOID) {
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
     * @param array $format
     */
    public function drawAreaChart(array $format = []) {
        $displayValues = isset($format["displayValues"]) ? $format["displayValues"] : false;
        $displayOffset = isset($format["displayOffset"]) ? $format["displayOffset"] : 2;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["displayR"]) ? $format["displayR"] : 0;
        $displayG = isset($format["displayG"]) ? $format["displayG"] : 0;
        $displayB = isset($format["displayB"]) ? $format["displayB"] : 0;
        $forceTransparency = isset($format["forceTransparency"]) ? $format["forceTransparency"] : 25;
        $aroundZero = isset($format["aroundZero"]) ? $format["aroundZero"] : true;
        $threshold = isset($format["Threshold"]) ? $format["Threshold"] : null;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                if ($displayColor == DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["display"];
                $format = $data["axis"][$axisId]["format"];
                $unit = $data["axis"][$axisId]["unit"];
                $posArray = $this->scaleComputeY($serie["data"], ["axisId" => $serie["axis"]]);
                $yZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                if ($threshold != null) {
                    foreach ($threshold as $key => $params) {
                        $threshold[$key]["minX"] = $this->scaleComputeY(
                                $params["min"], ["axisId" => $serie["axis"]]
                        );
                        $threshold[$key]["maxX"] = $this->scaleComputeY(
                                $params["max"], ["axisId" => $serie["axis"]]
                        );
                    }
                }
                $this->dataSet->data["series"][$serieName]["xOffset"] = 0;
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                        if ($displayValues && $serie["data"][$key] != VOID) {
                            if ($serie["data"][$key] > 0) {
                                $align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $offset = $displayOffset;
                            } else {
                                $align = TEXT_ALIGN_TOPMIDDLE;
                                $offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $x, $y - $offset, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit), ["r" => $displayR, "g" => $displayG, "b" => $displayB, "align" => $align]
                            );
                        }
                        if ($y == VOID && isset($areas[$areaID])) {
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
                        } elseif ($y != VOID) {
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
                                    $points, [
                                "r" => $this->shadowR,
                                "g" => $this->shadowG,
                                "b" => $this->shadowB,
                                "alpha" => $this->shadowA
                                    ]
                            );
                        }
                    }
                    $alpha = $forceTransparency != null ? $forceTransparency : $alpha;
                    $color = [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha,
                        "Threshold" => $threshold
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
                        if ($displayValues && $serie["data"][$key] != VOID) {
                            if ($serie["data"][$key] > 0) {
                                $align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $offset = $displayOffset;
                            } else {
                                $align = TEXT_ALIGN_TOPMIDDLE;
                                $offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $x + $offset, $y, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "align" => $align
                                    ]
                            );
                        }
                        if ($x == VOID && isset($areas[$areaID])) {
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
                        } elseif ($x != VOID) {
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
                                    $points, [
                                "r" => $this->shadowR,
                                "g" => $this->shadowG,
                                "b" => $this->shadowB,
                                "alpha" => $this->shadowA
                                    ]
                            );
                        }
                    }
                    $alpha = $forceTransparency != null ? $forceTransparency : $alpha;
                    $color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "Threshold" => $threshold];
                    foreach ($areas as $key => $points) {
                        $this->drawPolygonChart($points, $color);
                    }
                }
            }
        }
    }

    /**
     * Draw a bar chart
     * @param array $format
     */
    public function drawBarChart(array $format = []) {
        $floating0Serie = isset($format["floating0Serie"]) ? $format["floating0Serie"] : null;
        $floating0Value = isset($format["floating0Value"]) ? $format["floating0Value"] : null;
        $draw0Line = isset($format["draw0Line"]) ? $format["draw0Line"] : false;
        $displayValues = isset($format["displayValues"]) ? $format["displayValues"] : false;
        $displayOffset = isset($format["displayOffset"]) ? $format["displayOffset"] : 2;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : DISPLAY_MANUAL;
        $displayFont = isset($format["displayFont"]) ? $format["displayFont"] : $this->fontName;
        $displaySize = isset($format["displaySize"]) ? $format["displaySize"] : $this->fontSize;
        $displayPos = isset($format["displayPos"]) ? $format["displayPos"] : LABEL_POS_OUTSIDE;
        $displayShadow = isset($format["displayShadow"]) ? $format["displayShadow"] : true;
        $displayR = isset($format["displayR"]) ? $format["displayR"] : 0;
        $displayG = isset($format["displayG"]) ? $format["displayG"] : 0;
        $displayB = isset($format["displayB"]) ? $format["displayB"] : 0;
        $aroundZero = isset($format["aroundZero"]) ? $format["aroundZero"] : true;
        $Interleave = isset($format["interleave"]) ? $format["interleave"] : .5;
        $rounded = isset($format["rounded"]) ? $format["rounded"] : false;
        $roundRadius = isset($format["roundRadius"]) ? $format["roundRadius"] : 4;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : -1;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : -1;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : -1;
        $gradient = isset($format["gradient"]) ? $format["gradient"] : false;
        $gradientMode = isset($format["gradientMode"]) ? $format["gradientMode"] : GRADIENT_SIMPLE;
        $gradientalpha = isset($format["gradientalpha"]) ? $format["gradientalpha"] : 20;
        $gradientStartR = isset($format["gradientStartR"]) ? $format["gradientStartR"] : 255;
        $gradientStartG = isset($format["gradientStartG"]) ? $format["gradientStartG"] : 255;
        $gradientStartB = isset($format["gradientStartB"]) ? $format["gradientStartB"] : 255;
        $gradientEndR = isset($format["gradientEndR"]) ? $format["gradientEndR"] : 0;
        $gradientEndG = isset($format["gradientEndG"]) ? $format["gradientEndG"] : 0;
        $gradientEndB = isset($format["gradientEndB"]) ? $format["gradientEndB"] : 0;
        $txtMargin = isset($format["TxtMargin"]) ? $format["TxtMargin"] : 6;
        $overrideColors = isset($format["overrideColors"]) ? $format["overrideColors"] : null;
        $overrideSurrounding = isset($format["overrideSurrounding"]) ? $format["overrideSurrounding"] : 30;
        $InnerSurrounding = isset($format["innerSurrounding"]) ? $format["innerSurrounding"] : null;
        $InnerborderR = isset($format["innerborderR"]) ? $format["innerborderR"] : -1;
        $InnerborderG = isset($format["innerborderG"]) ? $format["innerborderG"] : -1;
        $InnerborderB = isset($format["innerborderB"]) ? $format["innerborderB"] : -1;
        $recordImageMap = isset($format["recordImageMap"]) ? $format["recordImageMap"] : false;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        if ($overrideColors != null) {
            $overrideColors = $this->validatePalette($overrideColors, $overrideSurrounding);
            $this->dataSet->saveExtendedData("Palette", $overrideColors);
        }
        $restoreShadow = $this->shadow;
        $seriesCount = $this->countDrawableSeries();
        $CurrentSerie = 0;
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                if ($displayColor == DISPLAY_AUTO) {
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
                        "r" => $InnerborderR,
                        "g" => $InnerborderG,
                        "b" => $InnerborderB
                    ];
                }
                $color = [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "borderR" => $borderR,
                    "borderG" => $borderG,
                    "borderB" => $borderB
                ];
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["display"];
                $format = $data["axis"][$axisId]["format"];
                $unit = $data["axis"][$axisId]["unit"];
                if (isset($serie["description"])) {
                    $serieDescription = $serie["description"];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisId" => $serie["axis"]]
                );
                if ($floating0Value != null) {
                    $yZero = $this->scaleComputeY(
                            $floating0Value, ["axisId" => $serie["axis"]]
                    );
                } else {
                    $yZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                }
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                    $this->dataSet->data["series"][$serieName]["xOffset"] = $xOffset + $xSize / 2;
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
                            if (isset($data["series"][$floating0Serie]["data"][$key])) {
                                $value = $data["series"][$floating0Serie]["data"][$key];
                            } else {
                                $value = 0;
                            }
                            $yZero = $this->scaleComputeY($value, ["axisId" => $serie["axis"]]);
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
                                    "r" => $overrideColors[$id]["r"],
                                    "g" => $overrideColors[$id]["g"],
                                    "b" => $overrideColors[$id]["b"],
                                    "alpha" => $overrideColors[$id]["alpha"],
                                    "borderR" => $overrideColors[$id]["borderR"],
                                    "borderG" => $overrideColors[$id]["borderG"],
                                    "borderB" => $overrideColors[$id]["borderB"]
                                ];
                            } else {
                                $color = $this->getRandomColor();
                            }
                        }
                        if ($y2 != VOID) {
                            $barHeight = $y1 - $y2;
                            if ($serie["data"][$key] == 0) {
                                $this->drawLine(
                                        $x + $xOffset + $xSpace, $y1, $x + $xOffset + $xSize - $xSpace, $y1, $color
                                );
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    "%s,%s,%s,%s", floor($x + $xOffset + $xSpace), floor($y1 - 1), floor($x + $xOffset + $xSize - $xSpace), floor($y1 + 1)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                    );
                                }
                            } else {
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    "%s,%s,%s,%s", floor($x + $xOffset + $xSpace), floor($y1), floor($x + $xOffset + $xSize - $xSpace), floor($y2)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                    );
                                }
                                if ($rounded) {
                                    $this->drawRoundedFilledRectangle(
                                            $x + $xOffset + $xSpace, $y1, $x + $xOffset + $xSize - $xSpace, $y2, $roundRadius, $color
                                    );
                                } else {
                                    $this->drawFilledRectangle(
                                            $x + $xOffset + $xSpace, $y1, $x + $xOffset + $xSize - $xSpace, $y2, $color
                                    );
                                    if ($InnerColor != null) {
                                        $this->drawRectangle(
                                                $x + $xOffset + $xSpace + 1, min($y1, $y2) + 1, $x + $xOffset + $xSize - $xSpace - 1, max($y1, $y2) - 1, $InnerColor
                                        );
                                    }
                                    if ($gradient) {
                                        $this->shadow = false;
                                        if ($gradientMode == GRADIENT_SIMPLE) {
                                            if ($serie["data"][$key] >= 0) {
                                                $gradienColor = [
                                                    "StartR" => $gradientStartR,
                                                    "StartG" => $gradientStartG,
                                                    "StartB" => $gradientStartB,
                                                    "endR" => $gradientEndR,
                                                    "endG" => $gradientEndG,
                                                    "endB" => $gradientEndB,
                                                    "alpha" => $gradientalpha
                                                ];
                                            } else {
                                                $gradienColor = [
                                                    "StartR" => $gradientEndR,
                                                    "StartG" => $gradientEndG,
                                                    "StartB" => $gradientEndB,
                                                    "endR" => $gradientStartR,
                                                    "endG" => $gradientStartG,
                                                    "endB" => $gradientStartB,
                                                    "alpha" => $gradientalpha
                                                ];
                                            }
                                            $this->drawGradientArea(
                                                    $x + $xOffset + $xSpace, $y1, $x + $xOffset + $xSize - $xSpace, $y2, DIRECTION_VERTICAL, $gradienColor
                                            );
                                        } elseif ($gradientMode == GRADIENT_EFFECT_CAN) {
                                            $gradienColor1 = [
                                                "StartR" => $gradientEndR,
                                                "StartG" => $gradientEndG,
                                                "StartB" => $gradientEndB,
                                                "endR" => $gradientStartR,
                                                "endG" => $gradientStartG,
                                                "endB" => $gradientStartB,
                                                "alpha" => $gradientalpha
                                            ];
                                            $gradienColor2 = [
                                                "StartR" => $gradientStartR,
                                                "StartG" => $gradientStartG,
                                                "StartB" => $gradientStartB,
                                                "endR" => $gradientEndR,
                                                "endG" => $gradientEndG,
                                                "endB" => $gradientEndB,
                                                "alpha" => $gradientalpha
                                            ];
                                            $xSpan = floor($xSize / 3);
                                            $this->drawGradientArea(
                                                    $x + $xOffset + $xSpace, $y1, $x + $xOffset + $xSpan - $xSpace, $y2, DIRECTION_HORIZONTAL, $gradienColor1
                                            );
                                            $this->drawGradientArea(
                                                    $x + $xOffset + $xSpan + $xSpace, $y1, $x + $xOffset + $xSize - $xSpace, $y2, DIRECTION_HORIZONTAL, $gradienColor2
                                            );
                                        }
                                        $this->shadow = $restoreShadow;
                                    }
                                }
                                if ($draw0Line) {
                                    $line0Color = ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20];
                                    if (abs($y1 - $y2) > 3) {
                                        $line0Width = 3;
                                    } else {
                                        $line0Width = 1;
                                    }
                                    if ($y1 - $y2 < 0) {
                                        $line0Width = -$line0Width;
                                    }
                                    $this->drawFilledRectangle(
                                            $x + $xOffset + $xSpace, floor($y1), $x + $xOffset + $xSize - $xSpace, floor($y1) - $line0Width, $line0Color
                                    );
                                    $this->drawLine(
                                            $x + $xOffset + $xSpace, floor($y1), $x + $xOffset + $xSize - $xSpace, floor($y1), $line0Color
                                    );
                                }
                            }
                            if ($displayValues && $serie["data"][$key] != VOID) {
                                if ($displayShadow) {
                                    $this->shadow = true;
                                }
                                $caption = $this->scaleFormat($serie["data"][$key], $mode, $format, $unit);
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 90, $caption);
                                $txtHeight = $txtPos[0]["y"] - $txtPos[1]["y"] + $txtMargin;
                                if ($displayPos == LABEL_POS_INSIDE && abs($txtHeight) < abs($barHeight)) {
                                    $CenterX = (($x + $xOffset + $xSize - $xSpace) - ($x + $xOffset + $xSpace)) / 2 + $x + $xOffset + $xSpace
                                    ;
                                    $CenterY = ($y2 - $y1) / 2 + $y1;
                                    $this->drawText(
                                            $CenterX, $CenterY, $caption, [
                                        "r" => $displayR,
                                        "g" => $displayG,
                                        "b" => $displayB,
                                        "align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                        "fontSize" => $displaySize,
                                        "Angle" => 90
                                            ]
                                    );
                                } else {
                                    if ($serie["data"][$key] >= 0) {
                                        $align = TEXT_ALIGN_BOTTOMMIDDLE;
                                        $offset = $displayOffset;
                                    } else {
                                        $align = TEXT_ALIGN_TOPMIDDLE;
                                        $offset = -$displayOffset;
                                    }
                                    $this->drawText(
                                            $x + $xOffset + $xSize / 2, $y2 - $offset, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit), [
                                        "r" => $displayR,
                                        "g" => $displayG,
                                        "b" => $displayB,
                                        "align" => $align,
                                        "fontSize" => $displaySize
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
                    $this->dataSet->data["series"][$serieName]["xOffset"] = $yOffset + $ySize / 2;
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
                            if (isset($data["series"][$floating0Serie]["data"][$key])) {
                                $value = $data["series"][$floating0Serie]["data"][$key];
                            } else {
                                $value = 0;
                            }
                            $yZero = $this->scaleComputeY($value, ["axisId" => $serie["axis"]]);
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
                                    "r" => $overrideColors[$id]["r"],
                                    "g" => $overrideColors[$id]["g"],
                                    "b" => $overrideColors[$id]["b"],
                                    "alpha" => $overrideColors[$id]["alpha"],
                                    "borderR" => $overrideColors[$id]["borderR"],
                                    "borderG" => $overrideColors[$id]["borderG"],
                                    "borderB" => $overrideColors[$id]["borderB"]
                                ];
                            } else {
                                $color = $this->getRandomColor();
                            }
                        }
                        if ($x2 != VOID) {
                            $barWidth = $x2 - $x1;
                            if ($serie["data"][$key] == 0) {
                                $this->drawLine(
                                        $x1, $y + $yOffset + $ySpace, $x1, $y + $yOffset + $ySize - $ySpace, $color
                                );
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    "%s,%s,%s,%s", floor($x1 - 1), floor($y + $yOffset + $ySpace), floor($x1 + 1), floor($y + $yOffset + $ySize - $ySpace)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                    );
                                }
                            } else {
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    "%s,%s,%s,%s", floor($x1), floor($y + $yOffset + $ySpace), floor($x2), floor($y + $yOffset + $ySize - $ySpace)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                    );
                                }
                                if ($rounded) {
                                    $this->drawRoundedFilledRectangle(
                                            $x1 + 1, $y + $yOffset + $ySpace, $x2, $y + $yOffset + $ySize - $ySpace, $roundRadius, $color
                                    );
                                } else {
                                    $this->drawFilledRectangle(
                                            $x1, $y + $yOffset + $ySpace, $x2, $y + $yOffset + $ySize - $ySpace, $color
                                    );
                                    if ($InnerColor != null) {
                                        $this->drawRectangle(
                                                min($x1, $x2) + 1, $y + $yOffset + $ySpace + 1, max($x1, $x2) - 1, $y + $yOffset + $ySize - $ySpace - 1, $InnerColor
                                        );
                                    }
                                    if ($gradient) {
                                        $this->shadow = false;
                                        if ($gradientMode == GRADIENT_SIMPLE) {
                                            if ($serie["data"][$key] >= 0) {
                                                $gradienColor = [
                                                    "StartR" => $gradientStartR,
                                                    "StartG" => $gradientStartG,
                                                    "StartB" => $gradientStartB,
                                                    "endR" => $gradientEndR,
                                                    "endG" => $gradientEndG,
                                                    "endB" => $gradientEndB,
                                                    "alpha" => $gradientalpha
                                                ];
                                            } else {
                                                $gradienColor = [
                                                    "StartR" => $gradientEndR,
                                                    "StartG" => $gradientEndG,
                                                    "StartB" => $gradientEndB,
                                                    "endR" => $gradientStartR,
                                                    "endG" => $gradientStartG,
                                                    "endB" => $gradientStartB,
                                                    "alpha" => $gradientalpha
                                                ];
                                            }
                                            $this->drawGradientArea(
                                                    $x1, $y + $yOffset + $ySpace, $x2, $y + $yOffset + $ySize - $ySpace, DIRECTION_HORIZONTAL, $gradienColor
                                            );
                                        } elseif ($gradientMode == GRADIENT_EFFECT_CAN) {
                                            $gradienColor1 = [
                                                "StartR" => $gradientEndR,
                                                "StartG" => $gradientEndG,
                                                "StartB" => $gradientEndB,
                                                "endR" => $gradientStartR,
                                                "endG" => $gradientStartG,
                                                "endB" => $gradientStartB,
                                                "alpha" => $gradientalpha
                                            ];
                                            $gradienColor2 = [
                                                "StartR" => $gradientStartR,
                                                "StartG" => $gradientStartG,
                                                "StartB" => $gradientStartB,
                                                "endR" => $gradientEndR,
                                                "endG" => $gradientEndG,
                                                "endB" => $gradientEndB,
                                                "alpha" => $gradientalpha
                                            ];
                                            $ySpan = floor($ySize / 3);
                                            $this->drawGradientArea(
                                                    $x1, $y + $yOffset + $ySpace, $x2, $y + $yOffset + $ySpan - $ySpace, DIRECTION_VERTICAL, $gradienColor1
                                            );
                                            $this->drawGradientArea(
                                                    $x1, $y + $yOffset + $ySpan, $x2, $y + $yOffset + $ySize - $ySpace, DIRECTION_VERTICAL, $gradienColor2
                                            );
                                        }
                                        $this->shadow = $restoreShadow;
                                    }
                                }
                                if ($draw0Line) {
                                    $line0Color = ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20];
                                    if (abs($x1 - $x2) > 3) {
                                        $line0Width = 3;
                                    } else {
                                        $line0Width = 1;
                                    }
                                    if ($x2 - $x1 < 0) {
                                        $line0Width = -$line0Width;
                                    }
                                    $this->drawFilledRectangle(
                                            floor($x1), $y + $yOffset + $ySpace, floor($x1) + $line0Width, $y + $yOffset + $ySize - $ySpace, $line0Color
                                    );
                                    $this->drawLine(
                                            floor($x1), $y + $yOffset + $ySpace, floor($x1), $y + $yOffset + $ySize - $ySpace, $line0Color
                                    );
                                }
                            }
                            if ($displayValues && $serie["data"][$key] != VOID) {
                                if ($displayShadow) {
                                    $this->shadow = true;
                                }
                                $caption = $this->scaleFormat($serie["data"][$key], $mode, $format, $unit);
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 0, $caption);
                                $txtWidth = $txtPos[1]["x"] - $txtPos[0]["x"] + $txtMargin;
                                if ($displayPos == LABEL_POS_INSIDE && abs($txtWidth) < abs($barWidth)) {
                                    $CenterX = ($x2 - $x1) / 2 + $x1;
                                    $CenterY = (($y + $yOffset + $ySize - $ySpace) - ($y + $yOffset + $ySpace)) / 2 + ($y + $yOffset + $ySpace)
                                    ;
                                    $this->drawText(
                                            $CenterX, $CenterY, $caption, [
                                        "r" => $displayR,
                                        "g" => $displayG,
                                        "b" => $displayB,
                                        "align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                        "fontSize" => $displaySize
                                            ]
                                    );
                                } else {
                                    if ($serie["data"][$key] >= 0) {
                                        $align = TEXT_ALIGN_MIDDLELEFT;
                                        $offset = $displayOffset;
                                    } else {
                                        $align = TEXT_ALIGN_MIDDLERIGHT;
                                        $offset = -$displayOffset;
                                    }
                                    $this->drawText(
                                            $x2 + $offset, $y + $yOffset + $ySize / 2, $caption, [
                                        "r" => $displayR,
                                        "g" => $displayG,
                                        "b" => $displayB,
                                        "align" => $align,
                                        "fontSize" => $displaySize
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
     * @param array $format
     */
    public function drawStackedBarChart(array $format = []) {
        $displayValues = isset($format["displayValues"]) ? $format["displayValues"] : false;
        $displayOrientation = isset($format["displayOrientation"]) ? $format["displayOrientation"] : ORIENTATION_AUTO;
        $displayRound = isset($format["displayRound"]) ? $format["displayRound"] : 0;
        $displayColor = isset($format["displayColor"]) ? $format["displayColor"] : DISPLAY_MANUAL;
        $displayFont = isset($format["displayFont"]) ? $format["displayFont"] : $this->fontName;
        $displaySize = isset($format["displaySize"]) ? $format["displaySize"] : $this->fontSize;
        $displayR = isset($format["displayR"]) ? $format["displayR"] : 0;
        $displayG = isset($format["displayG"]) ? $format["displayG"] : 0;
        $displayB = isset($format["displayB"]) ? $format["displayB"] : 0;
        $Interleave = isset($format["interleave"]) ? $format["interleave"] : .5;
        $rounded = isset($format["rounded"]) ? $format["rounded"] : false;
        $roundRadius = isset($format["roundRadius"]) ? $format["roundRadius"] : 4;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : -1;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : -1;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : -1;
        $gradient = isset($format["gradient"]) ? $format["gradient"] : false;
        $gradientMode = isset($format["gradientMode"]) ? $format["gradientMode"] : GRADIENT_SIMPLE;
        $gradientalpha = isset($format["gradientalpha"]) ? $format["gradientalpha"] : 20;
        $gradientStartR = isset($format["gradientStartR"]) ? $format["gradientStartR"] : 255;
        $gradientStartG = isset($format["gradientStartG"]) ? $format["gradientStartG"] : 255;
        $gradientStartB = isset($format["gradientStartB"]) ? $format["gradientStartB"] : 255;
        $gradientEndR = isset($format["gradientEndR"]) ? $format["gradientEndR"] : 0;
        $gradientEndG = isset($format["gradientEndG"]) ? $format["gradientEndG"] : 0;
        $gradientEndB = isset($format["gradientEndB"]) ? $format["gradientEndB"] : 0;
        $InnerSurrounding = isset($format["innerSurrounding"]) ? $format["innerSurrounding"] : null;
        $InnerborderR = isset($format["innerborderR"]) ? $format["innerborderR"] : -1;
        $InnerborderG = isset($format["innerborderG"]) ? $format["innerborderG"] : -1;
        $InnerborderB = isset($format["innerborderB"]) ? $format["innerborderB"] : -1;
        $recordImageMap = isset($format["recordImageMap"]) ? $format["recordImageMap"] : false;
        $fontFactor = isset($format["fontFactor"]) ? $format["fontFactor"] : 8;
        $this->lastChartLayout = CHART_LAST_LAYOUT_STACKED;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        $restoreShadow = $this->shadow;
        $lastX = [];
        $lastY = [];
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                if ($displayColor == DISPLAY_AUTO) {
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
                        "r" => $InnerborderR,
                        "g" => $InnerborderG,
                        "b" => $InnerborderB
                    ];
                }
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["display"];
                $format = $data["axis"][$axisId]["format"];
                $unit = $data["axis"][$axisId]["unit"];
                if (isset($serie["description"])) {
                    $serieDescription = $serie["description"];
                } else {
                    $serieDescription = $serieName;
                }
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisId" => $serie["axis"]], true
                );
                $yZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                $this->dataSet->data["series"][$serieName]["xOffset"] = 0;
                $color = [
                    "TransCorner" => true,
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "borderR" => $borderR,
                    "borderG" => $borderG,
                    "borderB" => $borderB
                ];
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                        if ($height != VOID && $serie["data"][$key] != 0) {
                            if ($serie["data"][$key] > 0) {
                                $pos = "+";
                            } else {
                                $pos = "-";
                            }
                            if (!isset($lastY[$key])) {
                                $lastY[$key] = [];
                            }
                            if (!isset($lastY[$key][$pos])) {
                                $lastY[$key][$pos] = $yZero;
                            }
                            $y1 = $lastY[$key][$pos];
                            $y2 = $y1 - $height;
                            if (($rounded || $borderR != -1) && ($pos == "+" && $y1 != $yZero)) {
                                $ySpaceUp = 1;
                            } else {
                                $ySpaceUp = 0;
                            }
                            if (($rounded || $borderR != -1) && ($pos == "-" && $y1 != $yZero)) {
                                $ySpaceDown = 1;
                            } else {
                                $ySpaceDown = 0;
                            }
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                "%s,%s,%s,%s", floor($x + $xOffset), floor($y1 - $ySpaceUp + $ySpaceDown), floor($x + $xOffset + $xSize), floor($y2)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                );
                            }
                            if ($rounded) {
                                $this->drawRoundedFilledRectangle(
                                        $x + $xOffset, $y1 - $ySpaceUp + $ySpaceDown, $x + $xOffset + $xSize, $y2, $roundRadius, $color
                                );
                            } else {
                                $this->drawFilledRectangle(
                                        $x + $xOffset, $y1 - $ySpaceUp + $ySpaceDown, $x + $xOffset + $xSize, $y2, $color
                                );
                                if ($InnerColor != null) {
                                    $restoreShadow = $this->shadow;
                                    $this->shadow = false;
                                    $this->drawRectangle(
                                            min($x + $xOffset + 1, $x + $xOffset + $xSize), min($y1 - $ySpaceUp + $ySpaceDown, $y2) + 1, max($x + $xOffset + 1, $x + $xOffset + $xSize) - 1, max($y1 - $ySpaceUp + $ySpaceDown, $y2) - 1, $InnerColor
                                    );
                                    $this->shadow = $restoreShadow;
                                }
                                if ($gradient) {
                                    $this->shadow = false;
                                    if ($gradientMode == GRADIENT_SIMPLE) {
                                        $gradientColor = [
                                            "StartR" => $gradientStartR,
                                            "StartG" => $gradientStartG,
                                            "StartB" => $gradientStartB,
                                            "endR" => $gradientEndR,
                                            "endG" => $gradientEndG,
                                            "endB" => $gradientEndB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $this->drawGradientArea(
                                                $x + $xOffset, $y1 - 1 - $ySpaceUp + $ySpaceDown, $x + $xOffset + $xSize, $y2 + 1, DIRECTION_VERTICAL, $gradientColor
                                        );
                                    } elseif ($gradientMode == GRADIENT_EFFECT_CAN) {
                                        $gradientColor1 = [
                                            "StartR" => $gradientEndR,
                                            "StartG" => $gradientEndG,
                                            "StartB" => $gradientEndB,
                                            "endR" => $gradientStartR,
                                            "endG" => $gradientStartG,
                                            "endB" => $gradientStartB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $gradientColor2 = [
                                            "StartR" => $gradientStartR,
                                            "StartG" => $gradientStartG,
                                            "StartB" => $gradientStartB,
                                            "endR" => $gradientEndR,
                                            "endG" => $gradientEndG,
                                            "endB" => $gradientEndB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $xSpan = floor($xSize / 3);
                                        $this->drawGradientArea(
                                                $x + $xOffset - .5, $y1 - .5 - $ySpaceUp + $ySpaceDown, $x + $xOffset + $xSpan, $y2 + .5, DIRECTION_HORIZONTAL, $gradientColor1
                                        );
                                        $this->drawGradientArea(
                                                $x + $xSpan + $xOffset - .5, $y1 - .5 - $ySpaceUp + $ySpaceDown, $x + $xOffset + $xSize, $y2 + .5, DIRECTION_HORIZONTAL, $gradientColor2
                                        );
                                    }
                                    $this->shadow = $restoreShadow;
                                }
                            }
                            if ($displayValues) {
                                $barHeight = abs($y2 - $y1) - 2;
                                $barWidth = $xSize + ($xOffset / 2) - $fontFactor;
                                $caption = $this->scaleFormat(
                                        round($serie["data"][$key], $displayRound), $mode, $format, $unit
                                );
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 0, $caption);
                                $txtHeight = abs($txtPos[2]["y"] - $txtPos[0]["y"]);
                                $txtWidth = abs($txtPos[1]["x"] - $txtPos[0]["x"]);
                                $xCenter = (($x + $xOffset + $xSize) - ($x + $xOffset)) / 2 + $x + $xOffset;
                                $yCenter = (($y2) - ($y1 - $ySpaceUp + $ySpaceDown)) / 2 + $y1 - $ySpaceUp + $ySpaceDown
                                ;
                                $done = false;
                                if ($displayOrientation == ORIENTATION_HORIZONTAL || $displayOrientation == ORIENTATION_AUTO
                                ) {
                                    if ($txtHeight < $barHeight && $txtWidth < $barWidth) {
                                        $this->drawText(
                                                $xCenter, $yCenter, $this->scaleFormat(
                                                        $serie["data"][$key], $mode, $format, $unit
                                                ), [
                                            "r" => $displayR,
                                            "g" => $displayG,
                                            "b" => $displayB,
                                            "align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                            "fontSize" => $displaySize,
                                            "fontName" => $displayFont
                                                ]
                                        );
                                        $done = true;
                                    }
                                }
                                if ($displayOrientation == ORIENTATION_VERTICAL || ($displayOrientation == ORIENTATION_AUTO && !$done)
                                ) {
                                    if ($txtHeight < $barWidth && $txtWidth < $barHeight) {
                                        $this->drawText(
                                                $xCenter, $yCenter, $this->scaleFormat(
                                                        $serie["data"][$key], $mode, $format, $unit
                                                ), [
                                            "r" => $displayR,
                                            "g" => $displayG,
                                            "b" => $displayB,
                                            "Angle" => 90,
                                            "align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                            "fontSize" => $displaySize,
                                            "fontName" => $displayFont
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
                        if ($width != VOID && $serie["data"][$key] != 0) {
                            if ($serie["data"][$key] > 0) {
                                $pos = "+";
                            } else {
                                $pos = "-";
                            }
                            if (!isset($lastX[$key])) {
                                $lastX[$key] = [];
                            }
                            if (!isset($lastX[$key][$pos])) {
                                $lastX[$key][$pos] = $yZero;
                            }
                            $x1 = $lastX[$key][$pos];
                            $x2 = $x1 + $width;
                            if (($rounded || $borderR != -1) && ($pos == "+" && $x1 != $yZero)) {
                                $xSpaceLeft = 2;
                            } else {
                                $xSpaceLeft = 0;
                            }
                            if (($rounded || $borderR != -1) && ($pos == "-" && $x1 != $yZero)) {
                                $xSpaceRight = 2;
                            } else {
                                $xSpaceRight = 0;
                            }
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                "%s,%s,%s,%s", floor($x1 + $xSpaceLeft), floor($y + $yOffset), floor($x2 - $xSpaceRight), floor($y + $yOffset + $ySize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["data"][$key], $mode, $format, $unit)
                                );
                            }
                            if ($rounded) {
                                $this->drawRoundedFilledRectangle(
                                        $x1 + $xSpaceLeft, $y + $yOffset, $x2 - $xSpaceRight, $y + $yOffset + $ySize, $roundRadius, $color
                                );
                            } else {
                                $this->drawFilledRectangle(
                                        $x1 + $xSpaceLeft, $y + $yOffset, $x2 - $xSpaceRight, $y + $yOffset + $ySize, $color
                                );
                                if ($InnerColor != null) {
                                    $restoreShadow = $this->shadow;
                                    $this->shadow = false;
                                    $this->drawRectangle(
                                            min($x1 + $xSpaceLeft, $x2 - $xSpaceRight) + 1, min($y + $yOffset, $y + $yOffset + $ySize) + 1, max($x1 + $xSpaceLeft, $x2 - $xSpaceRight) - 1, max($y + $yOffset, $y + $yOffset + $ySize) - 1, $InnerColor
                                    );
                                    $this->shadow = $restoreShadow;
                                }
                                if ($gradient) {
                                    $this->shadow = false;
                                    if ($gradientMode == GRADIENT_SIMPLE) {
                                        $gradientColor = [
                                            "StartR" => $gradientStartR,
                                            "StartG" => $gradientStartG,
                                            "StartB" => $gradientStartB,
                                            "endR" => $gradientEndR,
                                            "endG" => $gradientEndG,
                                            "endB" => $gradientEndB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $this->drawGradientArea(
                                                $x1 + $xSpaceLeft, $y + $yOffset, $x2 - $xSpaceRight, $y + $yOffset + $ySize, DIRECTION_HORIZONTAL, $gradientColor
                                        );
                                    } elseif ($gradientMode == GRADIENT_EFFECT_CAN) {
                                        $gradientColor1 = [
                                            "StartR" => $gradientEndR,
                                            "StartG" => $gradientEndG,
                                            "StartB" => $gradientEndB,
                                            "endR" => $gradientStartR,
                                            "endG" => $gradientStartG,
                                            "endB" => $gradientStartB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $gradientColor2 = [
                                            "StartR" => $gradientStartR,
                                            "StartG" => $gradientStartG,
                                            "StartB" => $gradientStartB,
                                            "endR" => $gradientEndR,
                                            "endG" => $gradientEndG,
                                            "endB" => $gradientEndB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $ySpan = floor($ySize / 3);
                                        $this->drawGradientArea(
                                                $x1 + $xSpaceLeft, $y + $yOffset, $x2 - $xSpaceRight, $y + $yOffset + $ySpan, DIRECTION_VERTICAL, $gradientColor1
                                        );
                                        $this->drawGradientArea(
                                                $x1 + $xSpaceLeft, $y + $yOffset + $ySpan, $x2 - $xSpaceRight, $y + $yOffset + $ySize, DIRECTION_VERTICAL, $gradientColor2
                                        );
                                    }
                                    $this->shadow = $restoreShadow;
                                }
                            }
                            if ($displayValues) {
                                $barWidth = abs($x2 - $x1) - $fontFactor;
                                $barHeight = $ySize + ($yOffset / 2) - $fontFactor / 2;
                                $caption = $this->scaleFormat(
                                        round($serie["data"][$key], $displayRound), $mode, $format, $unit
                                );
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 0, $caption);
                                $txtHeight = abs($txtPos[2]["y"] - $txtPos[0]["y"]);
                                $txtWidth = abs($txtPos[1]["x"] - $txtPos[0]["x"]);
                                $xCenter = ($x2 - $x1) / 2 + $x1;
                                $yCenter = (($y + $yOffset + $ySize) - ($y + $yOffset)) / 2 + $y + $yOffset;
                                $done = false;
                                if ($displayOrientation == ORIENTATION_HORIZONTAL || $displayOrientation == ORIENTATION_AUTO
                                ) {
                                    if ($txtHeight < $barHeight && $txtWidth < $barWidth) {
                                        $this->drawText(
                                                $xCenter, $yCenter, $this->scaleFormat(
                                                        $serie["data"][$key], $mode, $format, $unit
                                                ), [
                                            "r" => $displayR,
                                            "g" => $displayG,
                                            "b" => $displayB,
                                            "align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                            "fontSize" => $displaySize,
                                            "fontName" => $displayFont
                                                ]
                                        );
                                        $done = true;
                                    }
                                }
                                if ($displayOrientation == ORIENTATION_VERTICAL || ($displayOrientation == ORIENTATION_AUTO && !$done)
                                ) {
                                    if ($txtHeight < $barWidth && $txtWidth < $barHeight) {
                                        $this->drawText(
                                                $xCenter, $yCenter, $this->scaleFormat(
                                                        $serie["data"][$key], $mode, $format, $unit
                                                ), [
                                            "r" => $displayR,
                                            "g" => $displayG,
                                            "b" => $displayB,
                                            "Angle" => 90,
                                            "align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                            "fontSize" => $displaySize,
                                            "fontName" => $displayFont
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
     * @param array $format
     */
    public function drawStackedAreaChart(array $format = []) {
        $drawLine = isset($format["drawLine"]) ? $format["drawLine"] : false;
        $lineSurrounding = isset($format["lineSurrounding"]) ? $format["lineSurrounding"] : null;
        $lineR = isset($format["lineR"]) ? $format["lineR"] : VOID;
        $lineG = isset($format["lineG"]) ? $format["lineG"] : VOID;
        $lineB = isset($format["lineB"]) ? $format["lineB"] : VOID;
        $linealpha = isset($format["linealpha"]) ? $format["linealpha"] : 100;
        $drawPlot = isset($format["drawPlot"]) ? $format["drawPlot"] : false;
        $plotRadius = isset($format["plotRadius"]) ? $format["plotRadius"] : 2;
        $plotBorder = isset($format["plotBorder"]) ? $format["plotBorder"] : 1;
        $plotBorderSurrounding = isset($format["plotBorderSurrounding"]) ? $format["plotBorderSurrounding"] : null;
        $plotborderR = isset($format["plotborderR"]) ? $format["plotborderR"] : 0;
        $plotborderG = isset($format["plotborderG"]) ? $format["plotborderG"] : 0;
        $plotborderB = isset($format["plotborderB"]) ? $format["plotborderB"] : 0;
        $plotBorderalpha = isset($format["plotBorderalpha"]) ? $format["plotBorderalpha"] : 50;
        $forceTransparency = isset($format["forceTransparency"]) ? $format["forceTransparency"] : null;
        $this->lastChartLayout = CHART_LAST_LAYOUT_STACKED;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        $restoreShadow = $this->shadow;
        $this->shadow = false;
        /* Build the offset data series */
        $overallOffset = [];
        $serieOrder = [];
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $serieOrder[] = $serieName;
                foreach ($serie["data"] as $key => $value) {
                    if ($value == VOID) {
                        $value = 0;
                    }
                    if ($value >= 0) {
                        $sign = "+";
                    } else {
                        $sign = "-";
                    }
                    if (!isset($overallOffset[$key]) || !isset($overallOffset[$key][$sign])) {
                        $overallOffset[$key][$sign] = 0;
                    }
                    if ($sign == "+") {
                        $data["series"][$serieName]["data"][$key] = $value + $overallOffset[$key][$sign];
                    } else {
                        $data["series"][$serieName]["data"][$key] = $value - $overallOffset[$key][$sign];
                    }
                    $overallOffset[$key][$sign] = $overallOffset[$key][$sign] + abs($value);
                }
            }
        }
        $serieOrder = array_reverse($serieOrder);
        foreach ($serieOrder as $key => $serieName) {
            $serie = $data["series"][$serieName];
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                if ($forceTransparency != null) {
                    $alpha = $forceTransparency;
                }
                $color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha];
                if ($lineSurrounding != null) {
                    $lineColor = [
                        "r" => $r + $lineSurrounding,
                        "g" => $g + $lineSurrounding,
                        "b" => $b + $lineSurrounding,
                        "alpha" => $alpha
                    ];
                } elseif ($lineR != VOID) {
                    $lineColor = [
                        "r" => $lineR,
                        "g" => $lineG,
                        "b" => $lineB,
                        "alpha" => $linealpha
                    ];
                } else {
                    $lineColor = $color;
                }
                if ($plotBorderSurrounding != null) {
                    $plotBorderColor = [
                        "r" => $r + $plotBorderSurrounding,
                        "g" => $g + $plotBorderSurrounding,
                        "b" => $b + $plotBorderSurrounding,
                        "alpha" => $plotBorderalpha
                    ];
                } else {
                    $plotBorderColor = [
                        "r" => $plotborderR,
                        "g" => $plotborderG,
                        "b" => $plotborderB,
                        "alpha" => $plotBorderalpha
                    ];
                }
                $axisId = $serie["axis"];
                $format = $data["axis"][$axisId]["format"];
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisId" => $serie["axis"]], true
                );
                $yZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                $this->dataSet->data["series"][$serieName]["xOffset"] = 0;
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                        if ($height != VOID) {
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
                                    $plots[$i], $plots[$i + 1], $plots[$i + 2], $plots[$i + 3], $lineColor
                            );
                        }
                    }
                    if ($drawPlot) {
                        for ($i = 2; $i <= count($plots) - 4; $i = $i + 2) {
                            if ($plotBorder != 0) {
                                $this->drawFilledCircle(
                                        $plots[$i], $plots[$i + 1], $plotRadius + $plotBorder, $plotBorderColor
                                );
                            }
                            $this->drawFilledCircle($plots[$i], $plots[$i + 1], $plotRadius, $color);
                        }
                    }
                    $this->shadow = false;
                } elseif ($data["orientation"] == SCALE_POS_TOPBOTTOM) {
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
                        if ($height != VOID) {
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
                                    $plots[$i], $plots[$i + 1], $plots[$i + 2], $plots[$i + 3], $lineColor
                            );
                        }
                    }
                    if ($drawPlot) {
                        for ($i = 2; $i <= count($plots) - 4; $i = $i + 2) {
                            if ($plotBorder != 0) {
                                $this->drawFilledCircle(
                                        $plots[$i], $plots[$i + 1], $plotRadius + $plotBorder, $plotBorderColor
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
     * Draw the derivative chart associated to the data series
     * @param array $format
     */
    public function drawDerivative(array $format = []) {
        $offset = isset($format["offset"]) ? $format["offset"] : 10;
        $serieSpacing = isset($format["serieSpacing"]) ? $format["serieSpacing"] : 3;
        $derivativeHeight = isset($format["derivativeHeight"]) ? $format["derivativeHeight"] : 4;
        $shadedSlopeBox = isset($format["shadedSlopeBox"]) ? $format["shadedSlopeBox"] : false;
        $drawBackground = isset($format["drawBackground"]) ? $format["drawBackground"] : true;
        $backgroundR = isset($format["backgroundR"]) ? $format["backgroundR"] : 255;
        $backgroundG = isset($format["backgroundG"]) ? $format["backgroundG"] : 255;
        $backgroundB = isset($format["backgroundB"]) ? $format["backgroundB"] : 255;
        $backgroundalpha = isset($format["backgroundalpha"]) ? $format["backgroundalpha"] : 20;
        $drawBorder = isset($format["drawBorder"]) ? $format["drawBorder"] : true;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : 0;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : 0;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : 0;
        $borderalpha = isset($format["borderalpha"]) ? $format["borderalpha"] : 100;
        $caption = isset($format["caption"]) ? $format["caption"] : true;
        $captionHeight = isset($format["captionHeight"]) ? $format["captionHeight"] : 10;
        $captionWidth = isset($format["captionWidth"]) ? $format["captionWidth"] : 20;
        $captionMargin = isset($format["captionMargin"]) ? $format["captionMargin"] : 4;
        $captionLine = isset($format["captionLine"]) ? $format["captionLine"] : false;
        $captionBox = isset($format["captionBox"]) ? $format["captionBox"] : false;
        $captionborderR = isset($format["captionborderR"]) ? $format["captionborderR"] : 0;
        $captionborderG = isset($format["captionborderG"]) ? $format["captionborderG"] : 0;
        $captionborderB = isset($format["captionborderB"]) ? $format["captionborderB"] : 0;
        $captionFillR = isset($format["captionFillR"]) ? $format["captionFillR"] : 255;
        $captionFillG = isset($format["captionFillG"]) ? $format["captionFillG"] : 255;
        $captionFillB = isset($format["captionFillB"]) ? $format["captionFillB"] : 255;
        $captionFillalpha = isset($format["captionFillalpha"]) ? $format["captionFillalpha"] : 80;
        $positiveSlopeStartR = isset($format["positiveSlopeStartR"]) ? $format["positiveSlopeStartR"] : 184;
        $positiveSlopeStartG = isset($format["positiveSlopeStartG"]) ? $format["positiveSlopeStartG"] : 234;
        $positiveSlopeStartB = isset($format["positiveSlopeStartB"]) ? $format["positiveSlopeStartB"] : 88;
        $positiveSlopeEndR = isset($format["positiveSlopeStartR"]) ? $format["positiveSlopeStartR"] : 239;
        $positiveSlopeEndG = isset($format["positiveSlopeStartG"]) ? $format["positiveSlopeStartG"] : 31;
        $positiveSlopeEndB = isset($format["positiveSlopeStartB"]) ? $format["positiveSlopeStartB"] : 36;
        $NegativeSlopeStartR = isset($format["negativeSlopeStartR"]) ? $format["negativeSlopeStartR"] : 184;
        $NegativeSlopeStartG = isset($format["negativeSlopeStartG"]) ? $format["negativeSlopeStartG"] : 234;
        $NegativeSlopeStartB = isset($format["negativeSlopeStartB"]) ? $format["negativeSlopeStartB"] : 88;
        $NegativeSlopeEndR = isset($format["negativeSlopeStartR"]) ? $format["negativeSlopeStartR"] : 67;
        $NegativeSlopeEndG = isset($format["negativeSlopeStartG"]) ? $format["negativeSlopeStartG"] : 124;
        $NegativeSlopeEndB = isset($format["negativeSlopeStartB"]) ? $format["negativeSlopeStartB"] : 227;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
            $yPos = $this->dataSet->data["graphArea"]["y2"] + $offset;
        } else {
            $xPos = $this->dataSet->data["graphArea"]["x2"] + $offset;
        }
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                $weight = $serie["weight"];
                $axisId = $serie["axis"];
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisId" => $serie["axis"]]
                );
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($caption) {
                        if ($captionLine) {
                            $startX = floor($this->graphAreaX1 - $captionWidth + $xMargin - $captionMargin);
                            $endX = floor($this->graphAreaX1 - $captionMargin + $xMargin);
                            $captionSettings = [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                            ];
                            if ($captionBox) {
                                $this->drawFilledRectangle(
                                        $startX, $yPos, $endX, $yPos + $captionHeight, [
                                    "r" => $captionFillR,
                                    "g" => $captionFillG,
                                    "b" => $captionFillB,
                                    "borderR" => $captionborderR,
                                    "borderG" => $captionborderG,
                                    "borderB" => $captionborderB,
                                    "alpha" => $captionFillalpha
                                        ]
                                );
                            }
                            $this->drawLine(
                                    $startX + 2, $yPos + ($captionHeight / 2), $endX - 2, $yPos + ($captionHeight / 2), $captionSettings
                            );
                        } else {
                            $this->drawFilledRectangle(
                                    $this->graphAreaX1 - $captionWidth + $xMargin - $captionMargin, $yPos, $this->graphAreaX1 - $captionMargin + $xMargin, $yPos + $captionHeight, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "borderR" => $captionborderR,
                                "borderG" => $captionborderG,
                                "borderB" => $captionborderB
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
                                $startX - 1, $topY - 1, $endX + 1, $bottomY + 1, [
                            "r" => $backgroundR,
                            "g" => $backgroundG,
                            "b" => $backgroundB,
                            "alpha" => $backgroundalpha
                                ]
                        );
                    }
                    if ($drawBorder) {
                        $this->drawRectangle(
                                $startX - 1, $topY - 1, $endX + 1, $bottomY + 1, [
                            "r" => $borderR,
                            "g" => $borderG,
                            "b" => $borderB,
                            "alpha" => $borderalpha
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
                        if ($y != VOID && $lastX != null) {
                            $slope = ($lastY - $y);
                            if ($slope > $maxSlope) {
                                $maxSlope = $slope;
                            } if ($slope < $minSlope) {
                                $minSlope = $slope;
                            }
                        }
                        if ($y == VOID) {
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
                        if ($y != VOID && $lastY != null) {
                            $slope = ($lastY - $y);
                            if ($slope >= 0) {
                                $slopeIndex = (100 / $maxSlope) * $slope;
                                $r = (($positiveSlopeEndR - $positiveSlopeStartR) / 100) * $slopeIndex + $positiveSlopeStartR
                                ;
                                $g = (($positiveSlopeEndG - $positiveSlopeStartG) / 100) * $slopeIndex + $positiveSlopeStartG
                                ;
                                $b = (($positiveSlopeEndB - $positiveSlopeStartB) / 100) * $slopeIndex + $positiveSlopeStartB
                                ;
                            } elseif ($slope < 0) {
                                $slopeIndex = (100 / abs($minSlope)) * abs($slope);
                                $r = (($NegativeSlopeEndR - $NegativeSlopeStartR) / 100) * $slopeIndex + $NegativeSlopeStartR
                                ;
                                $g = (($NegativeSlopeEndG - $NegativeSlopeStartG) / 100) * $slopeIndex + $NegativeSlopeStartG
                                ;
                                $b = (($NegativeSlopeEndB - $NegativeSlopeStartB) / 100) * $slopeIndex + $NegativeSlopeStartB
                                ;
                            }
                            $color = ["r" => $r, "g" => $g, "b" => $b];
                            if ($shadedSlopeBox && $lastColor != null) {// && $slope != 0
                                $gradientSettings = [
                                    "StartR" => $lastColor["r"],
                                    "StartG" => $lastColor["g"],
                                    "StartB" => $lastColor["b"],
                                    "endR" => $r,
                                    "endG" => $g,
                                    "endB" => $b
                                ];
                                $this->drawGradientArea(
                                        $lastX, $topY, $x, $bottomY, DIRECTION_HORIZONTAL, $gradientSettings
                                );
                            } elseif (!$shadedSlopeBox || $lastColor == null) { // || $slope == 0
                                $this->drawFilledRectangle(
                                        floor($lastX), $topY, floor($x), $bottomY, $color
                                );
                            }
                            $lastColor = $color;
                        }
                        if ($y == VOID) {
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
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                            ];
                            if ($captionBox) {
                                $this->drawFilledRectangle(
                                        $xPos, $startY, $xPos + $captionHeight, $endY, [
                                    "r" => $captionFillR,
                                    "g" => $captionFillG,
                                    "b" => $captionFillB,
                                    "borderR" => $captionborderR,
                                    "borderG" => $captionborderG,
                                    "borderB" => $captionborderB,
                                    "alpha" => $captionFillalpha
                                        ]
                                );
                            }
                            $this->drawLine(
                                    $xPos + ($captionHeight / 2), $startY + 2, $xPos + ($captionHeight / 2), $endY - 2, $captionSettings
                            );
                        } else {
                            $this->drawFilledRectangle(
                                    $xPos, $startY, $xPos + $captionHeight, $endY, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "borderR" => $captionborderR,
                                "borderG" => $captionborderG,
                                "borderB" => $captionborderB
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
                                $topX - 1, $startY - 1, $bottomX + 1, $endY + 1, [
                            "r" => $backgroundR,
                            "g" => $backgroundG,
                            "b" => $backgroundB,
                            "alpha" => $backgroundalpha
                                ]
                        );
                    }
                    if ($drawBorder) {
                        $this->drawRectangle(
                                $topX - 1, $startY - 1, $bottomX + 1, $endY + 1, [
                            "r" => $borderR,
                            "g" => $borderG,
                            "b" => $borderB,
                            "alpha" => $borderalpha
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
                        if ($x != VOID && $lastX != null) {
                            $slope = ($x - $lastX);
                            if ($slope > $maxSlope) {
                                $maxSlope = $slope;
                            }
                            if ($slope < $minSlope) {
                                $minSlope = $slope;
                            }
                        }
                        if ($x == VOID) {
                            $lastX = null;
                        } else {
                            $lastX = $x;
                        }
                    }
                    $lastX = null;
                    $lastY = null;
                    $lastColor = null;
                    foreach ($posArray as $key => $x) {
                        if ($x != VOID && $lastX != null) {
                            $slope = ($x - $lastX);
                            if ($slope >= 0) {
                                $slopeIndex = (100 / $maxSlope) * $slope;
                                $r = (($positiveSlopeEndR - $positiveSlopeStartR) / 100) * $slopeIndex + $positiveSlopeStartR
                                ;
                                $g = (($positiveSlopeEndG - $positiveSlopeStartG) / 100) * $slopeIndex + $positiveSlopeStartG
                                ;
                                $b = (($positiveSlopeEndB - $positiveSlopeStartB) / 100) * $slopeIndex + $positiveSlopeStartB
                                ;
                            } elseif ($slope < 0) {
                                $slopeIndex = (100 / abs($minSlope)) * abs($slope);
                                $r = (($NegativeSlopeEndR - $NegativeSlopeStartR) / 100) * $slopeIndex + $NegativeSlopeStartR
                                ;
                                $g = (($NegativeSlopeEndG - $NegativeSlopeStartG) / 100) * $slopeIndex + $NegativeSlopeStartG
                                ;
                                $b = (($NegativeSlopeEndB - $NegativeSlopeStartB) / 100) * $slopeIndex + $NegativeSlopeStartB
                                ;
                            }
                            $color = ["r" => $r, "g" => $g, "b" => $b];
                            if ($shadedSlopeBox && $lastColor != null) {
                                $gradientSettings = [
                                    "StartR" => $lastColor["r"],
                                    "StartG" => $lastColor["g"],
                                    "StartB" => $lastColor["b"],
                                    "endR" => $r,
                                    "endG" => $g,
                                    "endB" => $b
                                ];
                                $this->drawGradientArea(
                                        $topX, $lastY, $bottomX, $y, DIRECTION_VERTICAL, $gradientSettings
                                );
                            } elseif (!$shadedSlopeBox || $lastColor == null) {
                                $this->drawFilledRectangle(
                                        $topX, floor($lastY), $bottomX, floor($y), $color
                                );
                            }
                            $lastColor = $color;
                        }
                        if ($x == VOID) {
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
     * @param array $format
     */
    public function drawBestFit(array $format = []) {
        $overrideTicks = isset($format["ticks"]) ? $format["ticks"] : null;
        $overrideR = isset($format["r"]) ? $format["r"] : VOID;
        $overrideG = isset($format["g"]) ? $format["g"] : VOID;
        $overrideB = isset($format["b"]) ? $format["b"] : VOID;
        $overridealpha = isset($format["alpha"]) ? $format["alpha"] : VOID;
        $data = $this->dataSet->getData();
        list($xMargin, $xDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["abscissa"]) {
                if ($overrideR != VOID && $overrideG != VOID && $overrideB != VOID) {
                    $r = $overrideR;
                    $g = $overrideG;
                    $b = $overrideB;
                } else {
                    $r = $serie["color"]["r"];
                    $g = $serie["color"]["g"];
                    $b = $serie["color"]["b"];
                }
                if ($overrideTicks == null) {
                    $ticks = $serie["ticks"];
                } else {
                    $ticks = $overrideTicks;
                }
                if ($overridealpha == VOID) {
                    $alpha = $serie["color"]["alpha"];
                } else {
                    $alpha = $overridealpha;
                }
                $color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks];
                $posArray = $this->scaleComputeY(
                        $serie["data"], ["axisId" => $serie["axis"]]
                );
                if ($data["orientation"] == SCALE_POS_LEFTRIGHT) {
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
                        if ($y != VOID) {
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
                        if ($x != VOID) {
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
     * Draw a label box
     * @param int $x
     * @param int $y
     * @param string $title
     * @param array $captions
     * @param array $format
     */
    public function drawLabelBox($x, $y, $title, array $captions, array $format = []) {
        $NoTitle = isset($format["noTitle"]) ? $format["noTitle"] : null;
        $boxWidth = isset($format["boxWidth"]) ? $format["boxWidth"] : 50;
        $drawSerieColor = isset($format["drawSerieColor"]) ? $format["drawSerieColor"] : true;
        $serieBoxSize = isset($format["serieBoxSize"]) ? $format["serieBoxSize"] : 6;
        $serieBoxSpacing = isset($format["serieBoxSpacing"]) ? $format["serieBoxSpacing"] : 4;
        $verticalMargin = isset($format["verticalMargin"]) ? $format["verticalMargin"] : 10;
        $horizontalMargin = isset($format["horizontalMargin"]) ? $format["horizontalMargin"] : 8;
        $r = isset($format["r"]) ? $format["r"] : $this->fontColorR;
        $g = isset($format["g"]) ? $format["g"] : $this->fontColorG;
        $b = isset($format["b"]) ? $format["b"] : $this->fontColorB;
        $fontName = isset($format["fontName"]) ? $this->loadFont($format["fontName"], 'fonts') : $this->fontName;
        $fontSize = isset($format["fontSize"]) ? $format["fontSize"] : $this->fontSize;
        $titleMode = isset($format["TitleMode"]) ? $format["TitleMode"] : LABEL_TITLE_NOBACKGROUND;
        $titleR = isset($format["TitleR"]) ? $format["TitleR"] : $r;
        $titleG = isset($format["TitleG"]) ? $format["TitleG"] : $g;
        $titleB = isset($format["TitleB"]) ? $format["TitleB"] : $b;
        $titleBackgroundR = isset($format["TitleBackgroundR"]) ? $format["TitleBackgroundR"] : 0;
        $titleBackgroundG = isset($format["TitleBackgroundG"]) ? $format["TitleBackgroundG"] : 0;
        $titleBackgroundB = isset($format["TitleBackgroundB"]) ? $format["TitleBackgroundB"] : 0;
        $gradientStartR = isset($format["gradientStartR"]) ? $format["gradientStartR"] : 255;
        $gradientStartG = isset($format["gradientStartG"]) ? $format["gradientStartG"] : 255;
        $gradientStartB = isset($format["gradientStartB"]) ? $format["gradientStartB"] : 255;
        $gradientEndR = isset($format["gradientEndR"]) ? $format["gradientEndR"] : 220;
        $gradientEndG = isset($format["gradientEndG"]) ? $format["gradientEndG"] : 220;
        $gradientEndB = isset($format["gradientEndB"]) ? $format["gradientEndB"] : 220;
        $boxalpha = isset($format["boxalpha"]) ? $format["boxalpha"] : 100;
        if (!$drawSerieColor) {
            $serieBoxSize = 0;
            $serieBoxSpacing = 0;
        }
        $txtPos = $this->getTextBox($x, $y, $fontName, $fontSize, 0, $title);
        $titleWidth = ($txtPos[1]["x"] - $txtPos[0]["x"]) + $verticalMargin * 2;
        $titleHeight = ($txtPos[0]["y"] - $txtPos[2]["y"]);
        if ($NoTitle) {
            $titleWidth = 0;
            $titleHeight = 0;
        }
        $captionWidth = 0;
        $captionHeight = -$horizontalMargin;
        foreach ($captions as $key => $caption) {
            $txtPos = $this->getTextBox(
                    $x, $y, $fontName, $fontSize, 0, $caption["caption"]
            );
            $captionWidth = max(
                    $captionWidth, ($txtPos[1]["x"] - $txtPos[0]["x"]) + $verticalMargin * 2
            );
            $captionHeight = $captionHeight + max(($txtPos[0]["y"] - $txtPos[2]["y"]), ($serieBoxSize + 2)) + $horizontalMargin
            ;
        }
        if ($captionHeight <= 5) {
            $captionHeight = $captionHeight + $horizontalMargin / 2;
        }
        if ($drawSerieColor) {
            $captionWidth = $captionWidth + $serieBoxSize + $serieBoxSpacing;
        }
        $boxWidth = max($boxWidth, $titleWidth, $captionWidth);
        $xMin = $x - 5 - floor(($boxWidth - 10) / 2);
        $xMax = $x + 5 + floor(($boxWidth - 10) / 2);
        $restoreShadow = $this->shadow;
        if ($this->shadow == true) {
            $this->shadow = false;
            $poly = [];
            $poly[] = $x + $this->shadowX;
            $poly[] = $y + $this->shadowX;
            $poly[] = $x + 5 + $this->shadowX;
            $poly[] = $y - 5 + $this->shadowX;
            $poly[] = $xMax + $this->shadowX;
            $poly[] = $y - 5 + $this->shadowX;
            if ($NoTitle) {
                $poly[] = $xMax + $this->shadowX;
                $poly[] = $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 2 + $this->shadowX;
                $poly[] = $xMin + $this->shadowX;
                $poly[] = $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 2 + $this->shadowX;
            } else {
                $poly[] = $xMax + $this->shadowX;
                $poly[] = $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 3 + $this->shadowX;
                $poly[] = $xMin + $this->shadowX;
                $poly[] = $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 3 + $this->shadowX;
            }
            $poly[] = $xMin + $this->shadowX;
            $poly[] = $y - 5 + $this->shadowX;
            $poly[] = $x - 5 + $this->shadowX;
            $poly[] = $y - 5 + $this->shadowX;
            $this->drawPolygon(
                    $poly, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowA
                    ]
            );
        }
        /* Draw the background */
        $gradientSettings = [
            "StartR" => $gradientStartR,
            "StartG" => $gradientStartG,
            "StartB" => $gradientStartB,
            "endR" => $gradientEndR,
            "endG" => $gradientEndG,
            "endB" => $gradientEndB,
            "alpha" => $boxalpha
        ];
        if ($NoTitle) {
            $this->drawGradientArea(
                    $xMin, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 2, $xMax, $y - 6, DIRECTION_VERTICAL, $gradientSettings
            );
        } else {
            $this->drawGradientArea(
                    $xMin, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 3, $xMax, $y - 6, DIRECTION_VERTICAL, $gradientSettings
            );
        }
        $poly = [];
        $poly[] = $x;
        $poly[] = $y;
        $poly[] = $x - 5;
        $poly[] = $y - 5;
        $poly[] = $x + 5;
        $poly[] = $y - 5;
        $this->drawPolygon(
                $poly, [
            "r" => $gradientEndR,
            "g" => $gradientEndG,
            "b" => $gradientEndB,
            "alpha" => $boxalpha,
            "noBorder" => true
                ]
        );
        /* Outer border */
        $outerBorderColor = $this->allocateColor($this->picture, 100, 100, 100, $boxalpha);
        imageline($this->picture, $xMin, $y - 5, $x - 5, $y - 5, $outerBorderColor);
        imageline($this->picture, $x, $y, $x - 5, $y - 5, $outerBorderColor);
        imageline($this->picture, $x, $y, $x + 5, $y - 5, $outerBorderColor);
        imageline($this->picture, $x + 5, $y - 5, $xMax, $y - 5, $outerBorderColor);
        if ($NoTitle) {
            imageline(
                    $this->picture, $xMin, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 2, $xMin, $y - 5, $outerBorderColor
            );
            imageline(
                    $this->picture, $xMax, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 2, $xMax, $y - 5, $outerBorderColor
            );
            imageline(
                    $this->picture, $xMin, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 2, $xMax, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 2, $outerBorderColor
            );
        } else {
            imageline(
                    $this->picture, $xMin, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 3, $xMin, $y - 5, $outerBorderColor
            );
            imageline(
                    $this->picture, $xMax, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 3, $xMax, $y - 5, $outerBorderColor
            );
            imageline(
                    $this->picture, $xMin, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 3, $xMax, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 3, $outerBorderColor
            );
        }
        /* Inner border */
        $InnerBorderColor = $this->allocateColor($this->picture, 255, 255, 255, $boxalpha);
        imageline($this->picture, $xMin + 1, $y - 6, $x - 5, $y - 6, $InnerBorderColor);
        imageline($this->picture, $x, $y - 1, $x - 5, $y - 6, $InnerBorderColor);
        imageline($this->picture, $x, $y - 1, $x + 5, $y - 6, $InnerBorderColor);
        imageline($this->picture, $x + 5, $y - 6, $xMax - 1, $y - 6, $InnerBorderColor);
        if ($NoTitle) {
            imageline(
                    $this->picture, $xMin + 1, $y - 4 - $titleHeight - $captionHeight - $horizontalMargin * 2, $xMin + 1, $y - 6, $InnerBorderColor
            );
            imageline(
                    $this->picture, $xMax - 1, $y - 4 - $titleHeight - $captionHeight - $horizontalMargin * 2, $xMax - 1, $y - 6, $InnerBorderColor
            );
            imageline(
                    $this->picture, $xMin + 1, $y - 4 - $titleHeight - $captionHeight - $horizontalMargin * 2, $xMax - 1, $y - 4 - $titleHeight - $captionHeight - $horizontalMargin * 2, $InnerBorderColor
            );
        } else {
            imageline(
                    $this->picture, $xMin + 1, $y - 4 - $titleHeight - $captionHeight - $horizontalMargin * 3, $xMin + 1, $y - 6, $InnerBorderColor
            );
            imageline(
                    $this->picture, $xMax - 1, $y - 4 - $titleHeight - $captionHeight - $horizontalMargin * 3, $xMax - 1, $y - 6, $InnerBorderColor
            );
            imageline(
                    $this->picture, $xMin + 1, $y - 4 - $titleHeight - $captionHeight - $horizontalMargin * 3, $xMax - 1, $y - 4 - $titleHeight - $captionHeight - $horizontalMargin * 3, $InnerBorderColor
            );
        }
        /* Draw the separator line */
        if ($titleMode == LABEL_TITLE_NOBACKGROUND && !$NoTitle) {
            $yPos = $y - 7 - $captionHeight - $horizontalMargin - $horizontalMargin / 2;
            $xMargin = $verticalMargin / 2;
            $this->drawLine(
                    $xMin + $xMargin, $yPos + 1, $xMax - $xMargin, $yPos + 1, [
                "r" => $gradientEndR,
                "g" => $gradientEndG,
                "b" => $gradientEndB,
                "alpha" => $boxalpha
                    ]
            );
            $this->drawLine(
                    $xMin + $xMargin, $yPos, $xMax - $xMargin, $yPos, [
                "r" => $gradientStartR,
                "g" => $gradientStartG,
                "b" => $gradientStartB,
                "alpha" => $boxalpha
                    ]
            );
        } elseif ($titleMode == LABEL_TITLE_BACKGROUND) {
            $this->drawFilledRectangle(
                    $xMin, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin * 3, $xMax, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin + $horizontalMargin / 2, [
                "r" => $titleBackgroundR,
                "g" => $titleBackgroundG,
                "b" => $titleBackgroundB,
                "alpha" => $boxalpha
                    ]
            );
            imageline(
                    $this->picture, $xMin + 1, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin + $horizontalMargin / 2 + 1, $xMax - 1, $y - 5 - $titleHeight - $captionHeight - $horizontalMargin + $horizontalMargin / 2 + 1, $InnerBorderColor
            );
        }
        /* Write the description */
        if (!$NoTitle) {
            $this->drawText(
                    $xMin + $verticalMargin, $y - 7 - $captionHeight - $horizontalMargin * 2, $title, [
                "align" => TEXT_ALIGN_BOTTOMLEFT,
                "r" => $titleR,
                "g" => $titleG,
                "b" => $titleB
                    ]
            );
        }
        /* Write the value */
        $yPos = $y - 5 - $horizontalMargin;
        $xPos = $xMin + $verticalMargin + $serieBoxSize + $serieBoxSpacing;
        foreach ($captions as $key => $caption) {
            $captionTxt = $caption["caption"];
            $txtPos = $this->getTextBox($xPos, $yPos, $fontName, $fontSize, 0, $captionTxt);
            $captionHeight = ($txtPos[0]["y"] - $txtPos[2]["y"]);
            /* Write the serie color if needed */
            if ($drawSerieColor) {
                $boxSettings = [
                    "r" => $caption["format"]["r"],
                    "g" => $caption["format"]["g"],
                    "b" => $caption["format"]["b"],
                    "alpha" => $caption["format"]["alpha"],
                    "borderR" => 0,
                    "borderG" => 0,
                    "borderB" => 0
                ];
                $this->drawFilledRectangle(
                        $xMin + $verticalMargin, $yPos - $serieBoxSize, $xMin + $verticalMargin + $serieBoxSize, $yPos, $boxSettings
                );
            }
            $this->drawText($xPos, $yPos, $captionTxt, ["align" => TEXT_ALIGN_BOTTOMLEFT]);
            $yPos = $yPos - $captionHeight - $horizontalMargin;
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a basic shape
     * @param int $x
     * @param int $y
     * @param int $shape
     * @param int $plotSize
     * @param int $plotBorder
     * @param int $borderSize
     * @param int $r
     * @param int $g
     * @param int $b
     * @param int|float $alpha
     * @param int $borderR
     * @param int $borderG
     * @param int $borderB
     * @param int|float $borderalpha
     */
    public function drawShape(
    $x, $y, $shape, $plotSize, $plotBorder, $borderSize, $r, $g, $b, $alpha, $borderR, $borderG, $borderB, $borderalpha
    ) {
        if ($shape == SERIE_SHAPE_FILLEDCIRCLE) {
            if ($plotBorder) {
                $this->drawFilledCircle(
                        $x, $y, $plotSize + $borderSize, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $borderalpha]
                );
            }
            $this->drawFilledCircle(
                    $x, $y, $plotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($shape == SERIE_SHAPE_FILLEDSQUARE) {
            if ($plotBorder) {
                $this->drawFilledRectangle(
                        $x - $plotSize - $borderSize, $y - $plotSize - $borderSize, $x + $plotSize + $borderSize, $y + $plotSize + $borderSize, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $borderalpha]
                );
            }
            $this->drawFilledRectangle(
                    $x - $plotSize, $y - $plotSize, $x + $plotSize, $y + $plotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($shape == SERIE_SHAPE_FILLEDTRIANGLE) {
            if ($plotBorder) {
                $pos = [];
                $pos[] = $x;
                $pos[] = $y - $plotSize - $borderSize;
                $pos[] = $x - $plotSize - $borderSize;
                $pos[] = $y + $plotSize + $borderSize;
                $pos[] = $x + $plotSize + $borderSize;
                $pos[] = $y + $plotSize + $borderSize;
                $this->drawPolygon(
                        $pos, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $borderalpha]
                );
            }
            $pos = [];
            $pos[] = $x;
            $pos[] = $y - $plotSize;
            $pos[] = $x - $plotSize;
            $pos[] = $y + $plotSize;
            $pos[] = $x + $plotSize;
            $pos[] = $y + $plotSize;
            $this->drawPolygon($pos, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
        } elseif ($shape == SERIE_SHAPE_TRIANGLE) {
            $this->drawLine(
                    $x, $y - $plotSize, $x - $plotSize, $y + $plotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            $this->drawLine(
                    $x - $plotSize, $y + $plotSize, $x + $plotSize, $y + $plotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            $this->drawLine(
                    $x + $plotSize, $y + $plotSize, $x, $y - $plotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($shape == SERIE_SHAPE_SQUARE) {
            $this->drawRectangle(
                    $x - $plotSize, $y - $plotSize, $x + $plotSize, $y + $plotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($shape == SERIE_SHAPE_CIRCLE) {
            $this->drawCircle(
                    $x, $y, $plotSize, $plotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($shape == SERIE_SHAPE_DIAMOND) {
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
                    $pos, [
                "noFill" => true,
                "borderR" => $r,
                "borderG" => $g,
                "borderB" => $b,
                "borderalpha" => $alpha
                    ]
            );
        } elseif ($shape == SERIE_SHAPE_FILLEDDIAMOND) {
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
                        $pos, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $borderalpha]
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
            $this->drawPolygon($pos, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
        }
    }

    /**
     *
     * @param array $points
     * @param array $format
     * @return null|integer
     */
    public function drawPolygonChart(array $points, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $noFill = isset($format["noFill"]) ? $format["noFill"] : false;
        $noBorder = isset($format["noBorder"]) ? $format["noBorder"] : false;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $r;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $g;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $b;
        $borderalpha = isset($format["borderalpha"]) ? $format["borderalpha"] : $alpha / 2;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $threshold = isset($format["Threshold"]) ? $format["Threshold"] : null;
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        $restoreShadow = $this->shadow;
        $this->shadow = false;
        $allIntegers = true;
        for ($i = 0; $i <= count($points) - 2; $i = $i + 2) {
            if ($this->getFirstDecimal($points[$i + 1]) != 0) {
                $allIntegers = false;
            }
        }
        /* Convert polygon to segments */
        $segments = [];
        for ($i = 2; $i <= count($points) - 2; $i = $i + 2) {
            $segments[] = [
                "X1" => $points[$i - 2],
                "Y1" => $points[$i - 1],
                "X2" => $points[$i],
                "Y2" => $points[$i + 1]
            ];
        }
        $segments[] = [
            "X1" => $points[$i - 2],
            "Y1" => $points[$i - 1],
            "X2" => $points[0],
            "Y2" => $points[1]
        ];
        /* Simplify straight lines */
        $result = [];
        $inHorizon = false;
        $lastX = VOID;
        foreach ($segments as $key => $pos) {
            if ($pos["y1"] != $pos["y2"]) {
                if ($inHorizon) {
                    $inHorizon = false;
                    $result[] = [
                        "X1" => $lastX,
                        "Y1" => $pos["y1"],
                        "X2" => $pos["x1"],
                        "Y2" => $pos["y1"]
                    ];
                }
                $result[] = [
                    "X1" => $pos["x1"],
                    "Y1" => $pos["y1"],
                    "X2" => $pos["x2"],
                    "Y2" => $pos["y2"]
                ];
            } else {
                if (!$inHorizon) {
                    $inHorizon = true;
                    $lastX = $pos["x1"];
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
        $minY = OUT_OF_SIGHT;
        $maxY = OUT_OF_SIGHT;
        foreach ($segments as $key => $Coords) {
            if ($minY == OUT_OF_SIGHT || $minY > min($Coords["y1"], $Coords["y2"])) {
                $minY = min($Coords["y1"], $Coords["y2"]);
            }
            if ($maxY == OUT_OF_SIGHT || $maxY < max($Coords["y1"], $Coords["y2"])) {
                $maxY = max($Coords["y1"], $Coords["y2"]);
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
                $Intersections = [];
                $lastSlope = null;
                $restoreLast = "-";
                foreach ($segments as $key => $Coords) {
                    $x1 = $Coords["x1"];
                    $x2 = $Coords["x2"];
                    $y1 = $Coords["y1"];
                    $y2 = $Coords["y2"];
                    if (min($y1, $y2) <= $y && max($y1, $y2) >= $y) {
                        if ($y1 == $y2) {
                            $x = $x1;
                        } else {
                            $x = $x1 + (($y - $y1) * $x2 - ($y - $y1) * $x1) / ($y2 - $y1);
                        }
                        $x = floor($x);
                        if ($x2 == $x1) {
                            $slope = "!";
                        } else {
                            $slopeC = ($y2 - $y1) / ($x2 - $x1);
                            if ($slopeC == 0) {
                                $slope = "=";
                            } elseif ($slopeC > 0) {
                                $slope = "+";
                            } elseif ($slopeC < 0) {
                                $slope = "-";
                            }
                        }
                        if (!is_array($Intersections)) {
                            $Intersections[] = $x;
                        } elseif (!in_array($x, $Intersections)) {
                            $Intersections[] = $x;
                        } elseif (in_array($x, $Intersections)) {
                            if ($y == $debugLine) {
                                echo $slope . "/" . $lastSlope . "(" . $x . ") ";
                            }
                            if ($slope == "=" && $lastSlope == "-") {
                                $Intersections[] = $x;
                            }
                            if ($slope != $lastSlope && $lastSlope != "!" && $lastSlope != "=") {
                                $Intersections[] = $x;
                            }
                            if ($slope != $lastSlope && $lastSlope == "!" && $slope == "+") {
                                $Intersections[] = $x;
                            }
                        }
                        if (is_array($Intersections) && in_array($x, $Intersections) && $lastSlope == "=" && ($slope == "-")
                        ) {
                            $Intersections[] = $x;
                        }
                        $lastSlope = $slope;
                    }
                }
                if ($restoreLast != "-") {
                    $Intersections[] = $restoreLast;
                    echo "@" . $y . "\r\n";
                }
                if (is_array($Intersections)) {
                    sort($Intersections);
                    if ($y == $debugLine) {
                        print_r($Intersections);
                    }
                    /* Remove null plots */
                    $result = [];
                    for ($i = 0; $i <= count($Intersections) - 1; $i = $i + 2) {
                        if (isset($Intersections[$i + 1])) {
                            if ($Intersections[$i] != $Intersections[$i + 1]) {
                                $result[] = $Intersections[$i];
                                $result[] = $Intersections[$i + 1];
                            }
                        }
                    }
                    if (is_array($result)) {
                        $Intersections = $result;
                        $lastX = Constant::OUT_OF_SIGHT;
                        foreach ($Intersections as $key => $x) {
                            if ($lastX == OUT_OF_SIGHT) {
                                $lastX = $x;
                            } elseif ($lastX != OUT_OF_SIGHT) {
                                if ($this->getFirstDecimal($lastX) > 1) {
                                    $lastX++;
                                }
                                $color = $defaultColor;
                                if ($threshold != null) {
                                    foreach ($threshold as $key => $parameters) {
                                        if ($y <= $parameters["minX"] && $y >= $parameters["maxX"]
                                        ) {
                                            if (isset($parameters["r"])) {
                                                $r = $parameters["r"];
                                            } else {
                                                $r = 0;
                                            }
                                            if (isset($parameters["g"])) {
                                                $g = $parameters["g"];
                                            } else {
                                                $g = 0;
                                            }
                                            if (isset($parameters["b"])) {
                                                $b = $parameters["b"];
                                            } else {
                                                $b = 0;
                                            }
                                            if (isset($parameters["alpha"])) {
                                                $alpha = $parameters["alpha"];
                                            } else {
                                                $alpha = 100;
                                            }
                                            $color = $this->allocateColor(
                                                    $this->picture, $r, $g, $b, $alpha
                                            );
                                        }
                                    }
                                }
                                imageline($this->picture, $lastX, $y, $x, $y, $color);
                                if ($y == $debugLine) {
                                    imageline($this->picture, $lastX, $y, $x, $y, $debugColor);
                                }
                                $lastX = OUT_OF_SIGHT;
                            }
                        }
                    }
                }
            }
        }
        /* Draw the polygon border, if required */
        if (!$noBorder) {
            foreach ($segments as $key => $Coords) {
                $this->drawLine(
                        $Coords["x1"], $Coords["y1"], $Coords["x2"], $Coords["y2"], [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "threshold" => $threshold
                        ]
                );
            }
        }
        $this->shadow = $restoreShadow;
    }

}
