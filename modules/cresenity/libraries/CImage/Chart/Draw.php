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
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param int|float $radius
     * @param array $format
     * @return null|integer
     */
    public function drawRoundedRectangle($X1, $Y1, $X2, $Y2, $radius, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        list($X1, $Y1, $X2, $Y2) = $this->fixBoxCoordinates($X1, $Y1, $X2, $Y2);
        if ($X2 - $X1 < $radius) {
            $radius = floor((($X2 - $X1)) / 2);
        }
        if ($Y2 - $Y1 < $radius) {
            $radius = floor((($Y2 - $Y1)) / 2);
        }
        $Color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "noBorder" => true];
        if ($radius <= 0) {
            $this->drawRectangle($X1, $Y1, $X2, $Y2, $Color);
            return 0;
        }
        if ($this->antialias) {
            $this->drawLine($X1 + $radius, $Y1, $X2 - $radius, $Y1, $Color);
            $this->drawLine($X2, $Y1 + $radius, $X2, $Y2 - $radius, $Color);
            $this->drawLine($X2 - $radius, $Y2, $X1 + $radius, $Y2, $Color);
            $this->drawLine($X1, $Y1 + $radius, $X1, $Y2 - $radius, $Color);
        } else {
            $Color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            imageline($this->picture, $X1 + $radius, $Y1, $X2 - $radius, $Y1, $Color);
            imageline($this->picture, $X2, $Y1 + $radius, $X2, $Y2 - $radius, $Color);
            imageline($this->picture, $X2 - $radius, $Y2, $X1 + $radius, $Y2, $Color);
            imageline($this->picture, $X1, $Y1 + $radius, $X1, $Y2 - $radius, $Color);
        }
        $step = 360 / (2 * PI * $radius);
        for ($i = 0; $i <= 90; $i = $i + $step) {
            $X = cos(($i + 180) * PI / 180) * $radius + $X1 + $radius;
            $Y = sin(($i + 180) * PI / 180) * $radius + $Y1 + $radius;
            $this->drawAntialiasPixel($X, $Y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
            $X = cos(($i + 90) * PI / 180) * $radius + $X1 + $radius;
            $Y = sin(($i + 90) * PI / 180) * $radius + $Y2 - $radius;
            $this->drawAntialiasPixel($X, $Y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
            $X = cos($i * PI / 180) * $radius + $X2 - $radius;
            $Y = sin($i * PI / 180) * $radius + $Y2 - $radius;
            $this->drawAntialiasPixel($X, $Y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
            $X = cos(($i + 270) * PI / 180) * $radius + $X2 - $radius;
            $Y = sin(($i + 270) * PI / 180) * $radius + $Y1 + $radius;
            $this->drawAntialiasPixel($X, $Y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
        }
    }

    /**
     * Draw a rectangle with rounded corners
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param int|float $radius
     * @param array $format
     * @return null|integer
     */
    public function drawRoundedFilledRectangle($X1, $Y1, $X2, $Y2, $radius, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : -1;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : -1;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : -1;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        /* Temporary fix for AA issue */
        $Y1 = floor($Y1);
        $Y2 = floor($Y2);
        $X1 = floor($X1);
        $X2 = floor($X2);
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
        list($X1, $Y1, $X2, $Y2) = $this->fixBoxCoordinates($X1, $Y1, $X2, $Y2);
        if ($X2 - $X1 < $radius * 2) {
            $radius = floor((($X2 - $X1)) / 4);
        }
        if ($Y2 - $Y1 < $radius * 2) {
            $radius = floor((($Y2 - $Y1)) / 4);
        }
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawRoundedFilledRectangle(
                    $X1 + $this->shadowX, $Y1 + $this->shadowY, $X2 + $this->shadowX, $Y2 + $this->shadowY, $radius, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowa
                    ]
            );
        }
        $Color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "noBorder" => true];
        if ($radius <= 0) {
            $this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Color);
            return 0;
        }
        $YTop = $Y1 + $radius;
        $YBottom = $Y2 - $radius;
        $step = 360 / (2 * PI * $radius);
        $positions = [];
        $radius--;
        $MinY = null;
        $MaxY = null;
        for ($i = 0; $i <= 90; $i = $i + $step) {
            $Xp1 = cos(($i + 180) * PI / 180) * $radius + $X1 + $radius;
            $Xp2 = cos(((90 - $i) + 270) * PI / 180) * $radius + $X2 - $radius;
            $Yp = floor(sin(($i + 180) * PI / 180) * $radius + $YTop);
            if (null === $MinY || $Yp > $MinY) {
                $MinY = $Yp;
            }
            if ($Xp1 <= floor($X1)) {
                $Xp1++;
            }
            if ($Xp2 >= floor($X2)) {
                $Xp2--;
            }
            $Xp1++;
            if (!isset($positions[$Yp])) {
                $positions[$Yp]["X1"] = $Xp1;
                $positions[$Yp]["X2"] = $Xp2;
            } else {
                $positions[$Yp]["X1"] = ($positions[$Yp]["X1"] + $Xp1) / 2;
                $positions[$Yp]["X2"] = ($positions[$Yp]["X2"] + $Xp2) / 2;
            }
            $Xp1 = cos(($i + 90) * PI / 180) * $radius + $X1 + $radius;
            $Xp2 = cos((90 - $i) * PI / 180) * $radius + $X2 - $radius;
            $Yp = floor(sin(($i + 90) * PI / 180) * $radius + $YBottom);
            if (null === $MaxY || $Yp < $MaxY) {
                $MaxY = $Yp;
            }
            if ($Xp1 <= floor($X1)) {
                $Xp1++;
            }
            if ($Xp2 >= floor($X2)) {
                $Xp2--;
            }
            $Xp1++;
            if (!isset($positions[$Yp])) {
                $positions[$Yp]["X1"] = $Xp1;
                $positions[$Yp]["X2"] = $Xp2;
            } else {
                $positions[$Yp]["X1"] = ($positions[$Yp]["X1"] + $Xp1) / 2;
                $positions[$Yp]["X2"] = ($positions[$Yp]["X2"] + $Xp2) / 2;
            }
        }
        $ManualColor = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        foreach ($positions as $Yp => $bounds) {
            $X1 = $bounds["X1"];
            $X1Dec = $this->getFirstDecimal($X1);
            if ($X1Dec != 0) {
                $X1 = floor($X1) + 1;
            }
            $X2 = $bounds["X2"];
            $X2Dec = $this->getFirstDecimal($X2);
            if ($X2Dec != 0) {
                $X2 = floor($X2) - 1;
            }
            imageline($this->picture, $X1, $Yp, $X2, $Yp, $ManualColor);
        }
        $this->drawFilledRectangle($X1, $MinY + 1, floor($X2), $MaxY - 1, $Color);
        $radius++;
        $this->drawRoundedRectangle(
                $X1, $Y1, $X2 + 1, $Y2 - 1, $radius, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
        );
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a rectangle
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param array $format
     */
    public function drawRectangle($X1, $Y1, $X2, $Y2, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $noAngle = isset($format["noAngle"]) ? $format["noAngle"] : false;
        if ($X1 > $X2) {
            list($X1, $X2) = [$X2, $X1];
        }
        if ($Y1 > $Y2) {
            list($Y1, $Y2) = [$Y2, $Y1];
        }
        if ($this->antialias) {
            if ($noAngle) {
                $this->drawLine(
                        $X1 + 1, $Y1, $X2 - 1, $Y1, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $X2, $Y1 + 1, $X2, $Y2 - 1, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $X2 - 1, $Y2, $X1 + 1, $Y2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $X1, $Y1 + 1, $X1, $Y2 - 1, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
            } else {
                $this->drawLine(
                        $X1 + 1, $Y1, $X2 - 1, $Y1, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $X2, $Y1, $X2, $Y2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $X2 - 1, $Y2, $X1 + 1, $Y2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
                $this->drawLine(
                        $X1, $Y1, $X1, $Y2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
                );
            }
        } else {
            $Color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            imagerectangle($this->picture, $X1, $Y1, $X2, $Y2, $Color);
        }
    }

    /**
     * Draw a filled rectangle
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param array $format
     */
    public function drawFilledRectangle($X1, $Y1, $X2, $Y2, array $format = []) {
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
        $Dash = isset($format["Dash"]) ? $format["Dash"] : false;
        $DashStep = isset($format["DashStep"]) ? $format["DashStep"] : 4;
        $DashR = isset($format["DashR"]) ? $format["DashR"] : 0;
        $DashG = isset($format["DashG"]) ? $format["DashG"] : 0;
        $DashB = isset($format["DashB"]) ? $format["DashB"] : 0;
        $noBorder = isset($format["noBorder"]) ? $format["noBorder"] : false;
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        if ($X1 > $X2) {
            list($X1, $X2) = [$X2, $X1];
        }
        if ($Y1 > $Y2) {
            list($Y1, $Y2) = [$Y2, $Y1];
        }
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawFilledRectangle(
                    $X1 + $this->shadowX, $Y1 + $this->shadowY, $X2 + $this->shadowX, $Y2 + $this->shadowY, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowa,
                "ticks" => $ticks,
                "noAngle" => $noAngle
                    ]
            );
        }
        $Color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        if ($noAngle) {
            imagefilledrectangle($this->picture, ceil($X1) + 1, ceil($Y1), floor($X2) - 1, floor($Y2), $Color);
            imageline($this->picture, ceil($X1), ceil($Y1) + 1, ceil($X1), floor($Y2) - 1, $Color);
            imageline($this->picture, floor($X2), ceil($Y1) + 1, floor($X2), floor($Y2) - 1, $Color);
        } else {
            imagefilledrectangle($this->picture, ceil($X1), ceil($Y1), floor($X2), floor($Y2), $Color);
        }
        if ($Dash) {
            if ($borderR != -1) {
                $iX1 = $X1 + 1;
                $iY1 = $Y1 + 1;
                $iX2 = $X2 - 1;
                $iY2 = $Y2 - 1;
            } else {
                $iX1 = $X1;
                $iY1 = $Y1;
                $iX2 = $X2;
                $iY2 = $Y2;
            }
            $Color = $this->allocateColor($this->picture, $DashR, $DashG, $DashB, $alpha);
            $Y = $iY1 - $DashStep;
            for ($X = $iX1; $X <= $iX2 + ($iY2 - $iY1); $X = $X + $DashStep) {
                $Y = $Y + $DashStep;
                if ($X > $iX2) {
                    $Xa = $X - ($X - $iX2);
                    $Ya = $iY1 + ($X - $iX2);
                } else {
                    $Xa = $X;
                    $Ya = $iY1;
                }
                if ($Y > $iY2) {
                    $Xb = $iX1 + ($Y - $iY2);
                    $Yb = $Y - ($Y - $iY2);
                } else {
                    $Xb = $iX1;
                    $Yb = $Y;
                }
                imageline($this->picture, $Xa, $Ya, $Xb, $Yb, $Color);
            }
        }
        if ($this->antialias && !$noBorder) {
            if ($X1 < ceil($X1)) {
                $alphaA = $alpha * (ceil($X1) - $X1);
                $Color = $this->allocateColor($this->picture, $r, $g, $b, $alphaA);
                imageline($this->picture, ceil($X1) - 1, ceil($Y1), ceil($X1) - 1, floor($Y2), $Color);
            }
            if ($Y1 < ceil($Y1)) {
                $alphaA = $alpha * (ceil($Y1) - $Y1);
                $Color = $this->allocateColor($this->picture, $r, $g, $b, $alphaA);
                imageline($this->picture, ceil($X1), ceil($Y1) - 1, floor($X2), ceil($Y1) - 1, $Color);
            }
            if ($X2 > floor($X2)) {
                $alphaA = $alpha * (.5 - ($X2 - floor($X2)));
                $Color = $this->allocateColor($this->picture, $r, $g, $b, $alphaA);
                imageline($this->picture, floor($X2) + 1, ceil($Y1), floor($X2) + 1, floor($Y2), $Color);
            }
            if ($Y2 > floor($Y2)) {
                $alphaA = $alpha * (.5 - ($Y2 - floor($Y2)));
                $Color = $this->allocateColor($this->picture, $r, $g, $b, $alphaA);
                imageline($this->picture, ceil($X1), floor($Y2) + 1, floor($X2), floor($Y2) + 1, $Color);
            }
        }
        if ($borderR != -1) {
            $this->drawRectangle(
                    $X1, $Y1, $X2, $Y2, [
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
     * @param int $X
     * @param int $Y
     * @param array $format
     */
    public function drawRectangleMarker($X, $Y, array $format = []) {
        $Size = isset($format["Size"]) ? $format["Size"] : 4;
        $HalfSize = floor($Size / 2);
        $this->drawFilledRectangle($X - $HalfSize, $Y - $HalfSize, $X + $HalfSize, $Y + $HalfSize, $format);
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
        $force = isset($format["Force"]) ? $format["Force"] : 30;
        $forces = isset($format["Forces"]) ? $format["Forces"] : null;
        $showC = isset($format["showControl"]) ? $format["showControl"] : false;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $PathOnly = isset($format["PathOnly"]) ? $format["PathOnly"] : false;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
        $Cpt = null;
        $mode = null;
        $result = [];
        for ($i = 1; $i <= count($coordinates) - 1; $i++) {
            $X1 = $coordinates[$i - 1][0];
            $Y1 = $coordinates[$i - 1][1];
            $X2 = $coordinates[$i][0];
            $Y2 = $coordinates[$i][1];
            if ($forces != null) {
                $force = $forces[$i];
            }
            /* First segment */
            if ($i == 1) {
                $Xv1 = $X1;
                $Yv1 = $Y1;
            } else {
                $Angle1 = $this->getAngle($XLast, $YLast, $X1, $Y1);
                $Angle2 = $this->getAngle($X1, $Y1, $X2, $Y2);
                $XOff = cos($Angle2 * PI / 180) * $force + $X1;
                $YOff = sin($Angle2 * PI / 180) * $force + $Y1;
                $Xv1 = cos($Angle1 * PI / 180) * $force + $XOff;
                $Yv1 = sin($Angle1 * PI / 180) * $force + $YOff;
            }
            /* Last segment */
            if ($i == count($coordinates) - 1) {
                $Xv2 = $X2;
                $Yv2 = $Y2;
            } else {
                $Angle1 = $this->getAngle($X2, $Y2, $coordinates[$i + 1][0], $coordinates[$i + 1][1]);
                $Angle2 = $this->getAngle($X1, $Y1, $X2, $Y2);
                $XOff = cos(($Angle2 + 180) * PI / 180) * $force + $X2;
                $YOff = sin(($Angle2 + 180) * PI / 180) * $force + $Y2;
                $Xv2 = cos(($Angle1 + 180) * PI / 180) * $force + $XOff;
                $Yv2 = sin(($Angle1 + 180) * PI / 180) * $force + $YOff;
            }
            $Path = $this->drawBezier($X1, $Y1, $X2, $Y2, $Xv1, $Yv1, $Xv2, $Yv2, $format);
            if ($PathOnly) {
                $result[] = $Path;
            }
            $XLast = $X1;
            $YLast = $Y1;
        }
        return $result;
    }

    /**
     * Draw a bezier curve with two controls points
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param int $Xv1
     * @param int $Yv1
     * @param int $Xv2
     * @param int $Yv2
     * @param array $format
     * @return array
     */
    public function drawBezier($X1, $Y1, $X2, $Y2, $Xv1, $Yv1, $Xv2, $Yv2, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $showC = isset($format["showControl"]) ? $format["showControl"] : false;
        $segments = isset($format["segments"]) ? $format["segments"] : null;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $NoDraw = isset($format["NoDraw"]) ? $format["NoDraw"] : false;
        $PathOnly = isset($format["PathOnly"]) ? $format["PathOnly"] : false;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
        $drawArrow = isset($format["drawArrow"]) ? $format["drawArrow"] : false;
        $arrowSize = isset($format["arrowSize"]) ? $format["arrowSize"] : 10;
        $arrowRatio = isset($format["arrowRatio"]) ? $format["arrowRatio"] : .5;
        $arrowTwoHeads = isset($format["arrowTwoHeads"]) ? $format["arrowTwoHeads"] : false;
        if ($segments == null) {
            $length = $this->getLength($X1, $Y1, $X2, $Y2);
            $Precision = ($length * 125) / 1000;
        } else {
            $Precision = $segments;
        }
        $P[0]["X"] = $X1;
        $P[0]["Y"] = $Y1;
        $P[1]["X"] = $Xv1;
        $P[1]["Y"] = $Yv1;
        $P[2]["X"] = $Xv2;
        $P[2]["Y"] = $Yv2;
        $P[3]["X"] = $X2;
        $P[3]["Y"] = $Y2;
        /* Compute the bezier points */
        $Q = [];
        $ID = 0;
        for ($i = 0; $i <= $Precision; $i = $i + 1) {
            $u = $i / $Precision;
            $C = [];
            $C[0] = (1 - $u) * (1 - $u) * (1 - $u);
            $C[1] = ($u * 3) * (1 - $u) * (1 - $u);
            $C[2] = 3 * $u * $u * (1 - $u);
            $C[3] = $u * $u * $u;
            for ($j = 0; $j <= 3; $j++) {
                if (!isset($Q[$ID])) {
                    $Q[$ID] = [];
                }
                if (!isset($Q[$ID]["X"])) {
                    $Q[$ID]["X"] = 0;
                }
                if (!isset($Q[$ID]["Y"])) {
                    $Q[$ID]["Y"] = 0;
                }
                $Q[$ID]["X"] = $Q[$ID]["X"] + $P[$j]["X"] * $C[$j];
                $Q[$ID]["Y"] = $Q[$ID]["Y"] + $P[$j]["Y"] * $C[$j];
            }
            $ID++;
        }
        $Q[$ID]["X"] = $X2;
        $Q[$ID]["Y"] = $Y2;
        if (!$NoDraw) {
            /* Display the control points */
            if ($showC && !$PathOnly) {
                $Xv1 = floor($Xv1);
                $Yv1 = floor($Yv1);
                $Xv2 = floor($Xv2);
                $Yv2 = floor($Yv2);
                $this->drawLine($X1, $Y1, $X2, $Y2, ["r" => 0, "g" => 0, "b" => 0, "alpha" => 30]);
                $MyMarkerSettings = [
                    "r" => 255,
                    "g" => 0,
                    "b" => 0,
                    "borderR" => 255,
                    "borderB" => 255,
                    "borderG" => 255,
                    "Size" => 4
                ];
                $this->drawRectangleMarker($Xv1, $Yv1, $MyMarkerSettings);
                $this->drawText($Xv1 + 4, $Yv1, "v1");
                $MyMarkerSettings = [
                    "r" => 0,
                    "g" => 0,
                    "b" => 255,
                    "borderR" => 255,
                    "borderB" => 255,
                    "borderG" => 255,
                    "Size" => 4
                ];
                $this->drawRectangleMarker($Xv2, $Yv2, $MyMarkerSettings);
                $this->drawText($Xv2 + 4, $Yv2, "v2");
            }
            /* Draw the bezier */
            $LastX = null;
            $LastY = null;
            $Cpt = null;
            $mode = null;
            $ArrowS = [];
            foreach ($Q as $Point) {
                $X = $Point["X"];
                $Y = $Point["Y"];
                /* Get the first segment */
                if (!count($ArrowS) && $LastX != null && $LastY != null) {
                    $ArrowS["X2"] = $LastX;
                    $ArrowS["Y2"] = $LastY;
                    $ArrowS["X1"] = $X;
                    $ArrowS["Y1"] = $Y;
                }
                if ($LastX != null && $LastY != null && !$PathOnly) {
                    list($Cpt, $mode) = $this->drawLine(
                            $LastX, $LastY, $X, $Y, [
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
                $ArrowE["X1"] = $LastX;
                $ArrowE["Y1"] = $LastY;
                $ArrowE["X2"] = $X;
                $ArrowE["Y2"] = $Y;
                $LastX = $X;
                $LastY = $Y;
            }
            if ($drawArrow && !$PathOnly) {
                $ArrowSettings = [
                    "FillR" => $r,
                    "FillG" => $g,
                    "FillB" => $b,
                    "alpha" => $alpha,
                    "Size" => $arrowSize,
                    "Ratio" => $arrowRatio
                ];
                if ($arrowTwoHeads) {
                    $this->drawArrow($ArrowS["X1"], $ArrowS["Y1"], $ArrowS["X2"], $ArrowS["Y2"], $ArrowSettings);
                }
                $this->drawArrow($ArrowE["X1"], $ArrowE["Y1"], $ArrowE["X2"], $ArrowE["Y2"], $ArrowSettings);
            }
        }
        return $Q;
    }

    /**
     * Draw a line between two points
     * @param int|float $X1
     * @param int|float $Y1
     * @param int|float $X2
     * @param int|float $Y2
     * @param array $format
     * @return array|int
     */
    public function drawLine($X1, $Y1, $X2, $Y2, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $Cpt = isset($format["Cpt"]) ? $format["Cpt"] : 1;
        $mode = isset($format["mode"]) ? $format["mode"] : 1;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
        $Threshold = isset($format["Threshold"]) ? $format["Threshold"] : null;
        if ($this->antialias == false && $ticks == null) {
            if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
                $shadowColor = $this->allocateColor(
                        $this->picture, $this->shadowR, $this->shadowG, $this->shadowB, $this->shadowa
                );
                imageline(
                        $this->picture, $X1 + $this->shadowX, $Y1 + $this->shadowY, $X2 + $this->shadowX, $Y2 + $this->shadowY, $shadowColor
                );
            }
            $Color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            imageline($this->picture, $X1, $Y1, $X2, $Y2, $Color);
            return 0;
        }
        $Distance = sqrt(($X2 - $X1) * ($X2 - $X1) + ($Y2 - $Y1) * ($Y2 - $Y1));
        if ($Distance == 0) {
            return -1;
        }
        /* Derivative algorithm for overweighted lines, re-route to polygons primitives */
        if ($weight != null) {
            $Angle = $this->getAngle($X1, $Y1, $X2, $Y2);
            $PolySettings = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "borderalpha" => $alpha];
            if ($ticks == null) {
                $points = [];
                $points[] = cos(deg2rad($Angle - 90)) * $weight + $X1;
                $points[] = sin(deg2rad($Angle - 90)) * $weight + $Y1;
                $points[] = cos(deg2rad($Angle + 90)) * $weight + $X1;
                $points[] = sin(deg2rad($Angle + 90)) * $weight + $Y1;
                $points[] = cos(deg2rad($Angle + 90)) * $weight + $X2;
                $points[] = sin(deg2rad($Angle + 90)) * $weight + $Y2;
                $points[] = cos(deg2rad($Angle - 90)) * $weight + $X2;
                $points[] = sin(deg2rad($Angle - 90)) * $weight + $Y2;
                $this->drawPolygon($points, $PolySettings);
            } else {
                for ($i = 0; $i <= $Distance; $i = $i + $ticks * 2) {
                    $Xa = (($X2 - $X1) / $Distance) * $i + $X1;
                    $Ya = (($Y2 - $Y1) / $Distance) * $i + $Y1;
                    $Xb = (($X2 - $X1) / $Distance) * ($i + $ticks) + $X1;
                    $Yb = (($Y2 - $Y1) / $Distance) * ($i + $ticks) + $Y1;
                    $points = [];
                    $points[] = cos(deg2rad($Angle - 90)) * $weight + $Xa;
                    $points[] = sin(deg2rad($Angle - 90)) * $weight + $Ya;
                    $points[] = cos(deg2rad($Angle + 90)) * $weight + $Xa;
                    $points[] = sin(deg2rad($Angle + 90)) * $weight + $Ya;
                    $points[] = cos(deg2rad($Angle + 90)) * $weight + $Xb;
                    $points[] = sin(deg2rad($Angle + 90)) * $weight + $Yb;
                    $points[] = cos(deg2rad($Angle - 90)) * $weight + $Xb;
                    $points[] = sin(deg2rad($Angle - 90)) * $weight + $Yb;
                    $this->drawPolygon($points, $PolySettings);
                }
            }
            return 1;
        }
        $XStep = ($X2 - $X1) / $Distance;
        $YStep = ($Y2 - $Y1) / $Distance;
        for ($i = 0; $i <= $Distance; $i++) {
            $X = $i * $XStep + $X1;
            $Y = $i * $YStep + $Y1;
            $Color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha];
            if ($Threshold != null) {
                foreach ($Threshold as $Key => $parameters) {
                    if ($Y <= $parameters["minX"] && $Y >= $parameters["maxX"]) {
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
                        $Color = ["r" => $rT, "g" => $gT, "b" => $bT, "alpha" => $alphaT];
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
                    $this->drawAntialiasPixel($X, $Y, $Color);
                }
                $Cpt++;
            } else {
                $this->drawAntialiasPixel($X, $Y, $Color);
            }
        }
        return [$Cpt, $mode];
    }

    /**
     * Draw a circle
     * @param int $Xc
     * @param int $Yc
     * @param int|float $Height
     * @param int|float $Width
     * @param array $format
     */
    public function drawCircle($Xc, $Yc, $Height, $Width, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $Height = abs($Height);
        $Width = abs($Width);
        if ($Height == 0) {
            $Height = 1;
        }
        if ($Width == 0) {
            $Width = 1;
        }
        $Xc = floor($Xc);
        $Yc = floor($Yc);
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawCircle(
                    $Xc + $this->shadowX, $Yc + $this->shadowY, $Height, $Width, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowa,
                "ticks" => $ticks
                    ]
            );
        }
        if ($Width == 0) {
            $Width = $Height;
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
        $step = 360 / (2 * PI * max($Width, $Height));
        $mode = 1;
        $Cpt = 1;
        for ($i = 0; $i <= 360; $i = $i + $step) {
            $X = cos($i * PI / 180) * $Height + $Xc;
            $Y = sin($i * PI / 180) * $Width + $Yc;
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
                    $this->drawAntialiasPixel($X, $Y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
                }
                $Cpt++;
            } else {
                $this->drawAntialiasPixel($X, $Y, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
            }
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a filled circle
     * @param int $X
     * @param int $Y
     * @param int|float $radius
     * @param array $format
     */
    public function drawFilledCircle($X, $Y, $radius, array $format = []) {
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
        $X = floor($X);
        $Y = floor($Y);
        $radius = abs($radius);
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawFilledCircle(
                    $X + $this->shadowX, $Y + $this->shadowY, $radius, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowa,
                "ticks" => $ticks
                    ]
            );
        }
        $this->Mask = [];
        $Color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        for ($i = 0; $i <= $radius * 2; $i++) {
            $Slice = sqrt($radius * $radius - ($radius - $i) * ($radius - $i));
            $XPos = floor($Slice);
            $YPos = $Y + $i - $radius;
            $AAlias = $Slice - floor($Slice);
            $this->Mask[$X - $XPos][$YPos] = true;
            $this->Mask[$X + $XPos][$YPos] = true;
            imageline($this->picture, $X - $XPos, $YPos, $X + $XPos, $YPos, $Color);
        }
        if ($this->antialias) {
            $this->drawCircle(
                    $X, $Y, $radius, $radius, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks]
            );
        }
        $this->Mask = [];
        if ($borderR != -1) {
            $this->drawCircle(
                    $X, $Y, $radius, $radius, [
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
     * @param int|float $X
     * @param int|float $Y
     * @param string $text
     * @param array $format
     * @return array
     */
    public function drawText($X, $Y, $text, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : $this->fontColorR;
        $g = isset($format["g"]) ? $format["g"] : $this->fontColorG;
        $b = isset($format["b"]) ? $format["b"] : $this->fontColorB;
        $Angle = isset($format["Angle"]) ? $format["Angle"] : 0;
        $align = isset($format["Align"]) ? $format["Align"] : Constant::TEXT_ALIGN_BOTTOMLEFT;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : $this->fontColorA;
        $fontName = isset($format["fontName"]) ? $this->loadFont($format["fontName"], 'fonts') : $this->fontName;
        $fontSize = isset($format["fontSize"]) ? $format["fontSize"] : $this->fontSize;
        $showOrigine = isset($format["showOrigine"]) ? $format["showOrigine"] : false;
        $TOffset = isset($format["TOffset"]) ? $format["TOffset"] : 2;
        $drawBox = isset($format["DrawBox"]) ? $format["DrawBox"] : false;
        $borderOffset = isset($format["borderOffset"]) ? $format["borderOffset"] : 6;
        $boxRounded = isset($format["BoxRounded"]) ? $format["BoxRounded"] : false;
        $roundedRadius = isset($format["RoundedRadius"]) ? $format["RoundedRadius"] : 6;
        $boxR = isset($format["BoxR"]) ? $format["BoxR"] : 255;
        $boxG = isset($format["BoxG"]) ? $format["BoxG"] : 255;
        $boxB = isset($format["BoxB"]) ? $format["BoxB"] : 255;
        $boxalpha = isset($format["Boxalpha"]) ? $format["Boxalpha"] : 50;
        $boxSurrounding = isset($format["BoxSurrounding"]) ? $format["BoxSurrounding"] : "";
        $boxborderR = isset($format["BoxR"]) ? $format["BoxR"] : 0;
        $boxborderG = isset($format["BoxG"]) ? $format["BoxG"] : 0;
        $boxborderB = isset($format["BoxB"]) ? $format["BoxB"] : 0;
        $boxBorderalpha = isset($format["Boxalpha"]) ? $format["Boxalpha"] : 50;
        $NoShadow = isset($format["NoShadow"]) ? $format["NoShadow"] : false;
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
            $MyMarkerSettings = [
                "r" => 255,
                "g" => 0,
                "b" => 0,
                "borderR" => 255,
                "borderB" => 255,
                "borderG" => 255,
                "Size" => 4
            ];
            $this->drawRectangleMarker($X, $Y, $MyMarkerSettings);
        }
        $txtPos = $this->getTextBox($X, $Y, $fontName, $fontSize, $Angle, $text);
        if ($drawBox && ($Angle == 0 || $Angle == 90 || $Angle == 180 || $Angle == 270)) {
            $T[0]["X"] = 0;
            $T[0]["Y"] = 0;
            $T[1]["X"] = 0;
            $T[1]["Y"] = 0;
            $T[2]["X"] = 0;
            $T[2]["Y"] = 0;
            $T[3]["X"] = 0;
            $T[3]["Y"] = 0;
            if ($Angle == 0) {
                $T[0]["X"] = -$TOffset;
                $T[0]["Y"] = $TOffset;
                $T[1]["X"] = $TOffset;
                $T[1]["Y"] = $TOffset;
                $T[2]["X"] = $TOffset;
                $T[2]["Y"] = -$TOffset;
                $T[3]["X"] = -$TOffset;
                $T[3]["Y"] = -$TOffset;
            }
            $X1 = min($txtPos[0]["X"], $txtPos[1]["X"], $txtPos[2]["X"], $txtPos[3]["X"]) - $borderOffset + 3;
            $Y1 = min($txtPos[0]["Y"], $txtPos[1]["Y"], $txtPos[2]["Y"], $txtPos[3]["Y"]) - $borderOffset;
            $X2 = max($txtPos[0]["X"], $txtPos[1]["X"], $txtPos[2]["X"], $txtPos[3]["X"]) + $borderOffset + 3;
            $Y2 = max($txtPos[0]["Y"], $txtPos[1]["Y"], $txtPos[2]["Y"], $txtPos[3]["Y"]) + $borderOffset - 3;
            $X1 = $X1 - $txtPos[$align]["X"] + $X + $T[0]["X"];
            $Y1 = $Y1 - $txtPos[$align]["Y"] + $Y + $T[0]["Y"];
            $X2 = $X2 - $txtPos[$align]["X"] + $X + $T[0]["X"];
            $Y2 = $Y2 - $txtPos[$align]["Y"] + $Y + $T[0]["Y"];
            $Settings = [
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
                $this->drawRoundedFilledRectangle($X1, $Y1, $X2, $Y2, $roundedRadius, $Settings);
            } else {
                $this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Settings);
            }
        }
        $X = $X - $txtPos[$align]["X"] + $X;
        $Y = $Y - $txtPos[$align]["Y"] + $Y;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $C_ShadowColor = $this->allocateColor(
                    $this->picture, $this->shadowR, $this->shadowG, $this->shadowB, $this->shadowa
            );
            imagettftext(
                    $this->picture, $fontSize, $Angle, $X + $this->shadowX, $Y + $this->shadowY, $C_ShadowColor, $fontName, $text
            );
        }
        $C_TextColor = $this->AllocateColor($this->picture, $r, $g, $b, $alpha);
        imagettftext($this->picture, $fontSize, $Angle, $X, $Y, $C_TextColor, $fontName, $text);
        $this->shadow = $shadow;
        return $txtPos;
    }

    /**
     * Draw a gradient within a defined area
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param int $Direction
     * @param array $format
     * @return null|integer
     */
    public function drawGradientArea($X1, $Y1, $X2, $Y2, $Direction, array $format = []) {
        $StartR = isset($format["StartR"]) ? $format["StartR"] : 90;
        $StartG = isset($format["StartG"]) ? $format["StartG"] : 90;
        $StartB = isset($format["StartB"]) ? $format["StartB"] : 90;
        $EndR = isset($format["EndR"]) ? $format["EndR"] : 0;
        $EndG = isset($format["EndG"]) ? $format["EndG"] : 0;
        $EndB = isset($format["EndB"]) ? $format["EndB"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $Levels = isset($format["Levels"]) ? $format["Levels"] : null;
        $shadow = $this->shadow;
        $this->shadow = false;
        if ($StartR == $EndR && $StartG == $EndG && $StartB == $EndB) {
            $this->drawFilledRectangle(
                    $X1, $Y1, $X2, $Y2, ["r" => $StartR, "g" => $StartG, "b" => $StartB, "alpha" => $alpha]
            );
            return 0;
        }
        if ($Levels != null) {
            $EndR = $StartR + $Levels;
            $EndG = $StartG + $Levels;
            $EndB = $StartB + $Levels;
        }
        if ($X1 > $X2) {
            list($X1, $X2) = [$X2, $X1];
        }
        if ($Y1 > $Y2) {
            list($Y1, $Y2) = [$Y2, $Y1];
        }
        if ($Direction == DIRECTION_VERTICAL) {
            $Width = abs($Y2 - $Y1);
        }
        if ($Direction == DIRECTION_HORIZONTAL) {
            $Width = abs($X2 - $X1);
        }
        $step = max(abs($EndR - $StartR), abs($EndG - $StartG), abs($EndB - $StartB));
        $stepSize = $Width / $step;
        $rStep = ($EndR - $StartR) / $step;
        $gStep = ($EndG - $StartG) / $step;
        $bStep = ($EndB - $StartB) / $step;
        $r = $StartR;
        $g = $StartG;
        $b = $StartB;
        switch ($Direction) {
            case DIRECTION_VERTICAL:
                $StartY = $Y1;
                $EndY = floor($Y2) + 1;
                $LastY2 = $StartY;
                for ($i = 0; $i <= $step; $i++) {
                    $Y2 = floor($StartY + ($i * $stepSize));
                    if ($Y2 > $EndY) {
                        $Y2 = $EndY;
                    }
                    if (($Y1 != $Y2 && $Y1 < $Y2) || $Y2 == $EndY) {
                        $Color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha];
                        $this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Color);
                        $LastY2 = max($LastY2, $Y2);
                        $Y1 = $Y2 + 1;
                    }
                    $r = $r + $rStep;
                    $g = $g + $gStep;
                    $b = $b + $bStep;
                }
                if ($LastY2 < $EndY && isset($Color)) {
                    for ($i = $LastY2 + 1; $i <= $EndY; $i++) {
                        $this->drawLine($X1, $i, $X2, $i, $Color);
                    }
                }
                break;
            case DIRECTION_HORIZONTAL:
                $StartX = $X1;
                $EndX = $X2;
                for ($i = 0; $i <= $step; $i++) {
                    $X2 = floor($StartX + ($i * $stepSize));
                    if ($X2 > $EndX) {
                        $X2 = $EndX;
                    }
                    if (($X1 != $X2 && $X1 < $X2) || $X2 == $EndX) {
                        $Color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha];
                        $this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Color);
                        $X1 = $X2 + 1;
                    }
                    $r = $r + $rStep;
                    $g = $g + $gStep;
                    $b = $b + $bStep;
                }
                if ($X2 < $EndX && isset($Color)) {
                    $this->drawFilledRectangle($X2, $Y1, $EndX, $Y2, $Color);
                }
                break;
        }
        $this->shadow = $shadow;
    }

    /**
     * Draw an aliased pixel
     * @param int $X
     * @param int $Y
     * @param array $format
     * @return int|null
     */
    public function drawAntialiasPixel($X, $Y, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 0;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        if ($X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize) {
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
                        $this->picture, $this->shadowR, $this->shadowG, $this->shadowB, $this->shadowa
                );
                imagesetpixel($this->picture, $X + $this->shadowX, $Y + $this->shadowY, $shadowColor);
            }
            $PlotColor = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            imagesetpixel($this->picture, $X, $Y, $PlotColor);
            return 0;
        }
        $Xi = floor($X);
        $Yi = floor($Y);
        if ($Xi == $X && $Yi == $Y) {
            if ($alpha == 100) {
                $this->drawalphaPixel($X, $Y, 100, $r, $g, $b);
            } else {
                $this->drawalphaPixel($X, $Y, $alpha, $r, $g, $b);
            }
        } else {
            $alpha1 = (((1 - ($X - floor($X))) * (1 - ($Y - floor($Y))) * 100) / 100) * $alpha;
            if ($alpha1 > $this->antialiasQuality) {
                $this->drawalphaPixel($Xi, $Yi, $alpha1, $r, $g, $b);
            }
            $alpha2 = ((($X - floor($X)) * (1 - ($Y - floor($Y))) * 100) / 100) * $alpha;
            if ($alpha2 > $this->antialiasQuality) {
                $this->drawalphaPixel($Xi + 1, $Yi, $alpha2, $r, $g, $b);
            }
            $alpha3 = (((1 - ($X - floor($X))) * ($Y - floor($Y)) * 100) / 100) * $alpha;
            if ($alpha3 > $this->antialiasQuality) {
                $this->drawalphaPixel($Xi, $Yi + 1, $alpha3, $r, $g, $b);
            }
            $alpha4 = ((($X - floor($X)) * ($Y - floor($Y)) * 100) / 100) * $alpha;
            if ($alpha4 > $this->antialiasQuality) {
                $this->drawalphaPixel($Xi + 1, $Yi + 1, $alpha4, $r, $g, $b);
            }
        }
    }

    /**
     * Draw a semi-transparent pixel
     * @param int $X
     * @param int $Y
     * @param int $alpha
     * @param int $r
     * @param int $g
     * @param int $b
     * @return null|integer
     */
    public function drawalphaPixel($X, $Y, $alpha, $r, $g, $b) {
        if (isset($this->Mask[$X]) && isset($this->Mask[$X][$Y])) {
            return 0;
        }
        if ($X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize) {
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
            $alphaFactor = floor(($alpha / 100) * $this->shadowa);
            $shadowColor = $this->allocateColor(
                    $this->picture, $this->shadowR, $this->shadowG, $this->shadowB, $alphaFactor
            );
            imagesetpixel($this->picture, $X + $this->shadowX, $Y + $this->shadowY, $shadowColor);
        }
        $C_Aliased = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        imagesetpixel($this->picture, $X, $Y, $C_Aliased);
    }

    /**
     * Load a PNG file and draw it over the chart
     * @param int $X
     * @param int $Y
     * @param string $FileName
     */
    public function drawFromPNG($X, $Y, $FileName) {
        $this->drawFromPicture(1, $FileName, $X, $Y);
    }

    /**
     * Load a GIF file and draw it over the chart
     * @param int $X
     * @param int $Y
     * @param string $FileName
     */
    public function drawFromGIF($X, $Y, $FileName) {
        $this->drawFromPicture(2, $FileName, $X, $Y);
    }

    /**
     * Load a JPEG file and draw it over the chart
     * @param int $X
     * @param int $Y
     * @param string $FileName
     */
    public function drawFromJPG($X, $Y, $FileName) {
        $this->drawFromPicture(3, $FileName, $X, $Y);
    }

    /**
     * Generic loader public function for external pictures
     * @param int $picType
     * @param string $FileName
     * @param int $X
     * @param int $Y
     * @return null|integer
     */
    public function drawFromPicture($picType, $FileName, $X, $Y) {
        if (file_exists($FileName)) {
            list($Width, $Height) = $this->getPicInfo($FileName);
            if ($picType == 1) {
                $raster = imagecreatefrompng($FileName);
            } elseif ($picType == 2) {
                $raster = imagecreatefromgif($FileName);
            } elseif ($picType == 3) {
                $raster = imagecreatefromjpeg($FileName);
            } else {
                return 0;
            }
            $restoreShadow = $this->shadow;
            if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
                $this->shadow = false;
                if ($picType == 3) {
                    $this->drawFilledRectangle(
                            $X + $this->shadowX, $Y + $this->shadowY, $X + $Width + $this->shadowX, $Y + $Height + $this->shadowY, [
                        "r" => $this->shadowR,
                        "g" => $this->shadowG,
                        "b" => $this->shadowB,
                        "alpha" => $this->shadowa
                            ]
                    );
                } else {
                    $TranparentID = imagecolortransparent($raster);
                    for ($Xc = 0; $Xc <= $Width - 1; $Xc++) {
                        for ($Yc = 0; $Yc <= $Height - 1; $Yc++) {
                            $rGBa = imagecolorat($raster, $Xc, $Yc);
                            $Values = imagecolorsforindex($raster, $rGBa);
                            if ($Values["alpha"] < 120) {
                                $alphaFactor = floor(
                                        ($this->shadowa / 100) * ((100 / 127) * (127 - $Values["alpha"]))
                                );
                                $this->drawalphaPixel(
                                        $X + $Xc + $this->shadowX, $Y + $Yc + $this->shadowY, $alphaFactor, $this->shadowR, $this->shadowG, $this->shadowB
                                );
                            }
                        }
                    }
                }
            }
            $this->shadow = $restoreShadow;
            imagecopy($this->picture, $raster, $X, $Y, 0, 0, $Width, $Height);
            imagedestroy($raster);
        }
    }

    /**
     * Draw an arrow
     * @param int $X1
     * @param int $Y1
     * @param int $X2
     * @param int $Y2
     * @param array $format
     */
    public function drawArrow($X1, $Y1, $X2, $Y2, array $format = []) {
        $fillR = isset($format["FillR"]) ? $format["FillR"] : 0;
        $fillG = isset($format["FillG"]) ? $format["FillG"] : 0;
        $fillB = isset($format["FillB"]) ? $format["FillB"] : 0;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $fillR;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $fillG;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $fillB;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $Size = isset($format["Size"]) ? $format["Size"] : 10;
        $ratio = isset($format["Ratio"]) ? $format["Ratio"] : .5;
        $TwoHeads = isset($format["TwoHeads"]) ? $format["TwoHeads"] : false;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : false;
        /* Calculate the line angle */
        $Angle = $this->getAngle($X1, $Y1, $X2, $Y2);
        /* Override Shadow support, this will be managed internally */
        $restoreShadow = $this->shadow;
        if ($this->shadow && $this->shadowX != 0 && $this->shadowY != 0) {
            $this->shadow = false;
            $this->drawArrow(
                    $X1 + $this->shadowX, $Y1 + $this->shadowY, $X2 + $this->shadowX, $Y2 + $this->shadowY, [
                "FillR" => $this->shadowR,
                "FillG" => $this->shadowG,
                "FillB" => $this->shadowB,
                "alpha" => $this->shadowa,
                "Size" => $Size,
                "Ratio" => $ratio,
                "TwoHeads" => $TwoHeads,
                "ticks" => $ticks
                    ]
            );
        }
        /* Draw the 1st Head */
        $TailX = cos(($Angle - 180) * PI / 180) * $Size + $X2;
        $TailY = sin(($Angle - 180) * PI / 180) * $Size + $Y2;
        $points = [];
        $points[] = $X2;
        $points[] = $Y2;
        $points[] = cos(($Angle - 90) * PI / 180) * $Size * $ratio + $TailX;
        $points[] = sin(($Angle - 90) * PI / 180) * $Size * $ratio + $TailY;
        $points[] = cos(($Angle - 270) * PI / 180) * $Size * $ratio + $TailX;
        $points[] = sin(($Angle - 270) * PI / 180) * $Size * $ratio + $TailY;
        $points[] = $X2;
        $points[] = $Y2;
        /* Visual correction */
        if ($Angle == 180 || $Angle == 360) {
            $points[4] = $points[2];
        }
        if ($Angle == 90 || $Angle == 270) {
            $points[5] = $points[3];
        }
        $ArrowColor = $this->allocateColor($this->picture, $fillR, $fillG, $fillB, $alpha);
        ImageFilledPolygon($this->picture, $points, 4, $ArrowColor);
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
        if ($TwoHeads) {
            $Angle = $this->getAngle($X2, $Y2, $X1, $Y1);
            $TailX2 = cos(($Angle - 180) * PI / 180) * $Size + $X1;
            $TailY2 = sin(($Angle - 180) * PI / 180) * $Size + $Y1;
            $points = [];
            $points[] = $X1;
            $points[] = $Y1;
            $points[] = cos(($Angle - 90) * PI / 180) * $Size * $ratio + $TailX2;
            $points[] = sin(($Angle - 90) * PI / 180) * $Size * $ratio + $TailY2;
            $points[] = cos(($Angle - 270) * PI / 180) * $Size * $ratio + $TailX2;
            $points[] = sin(($Angle - 270) * PI / 180) * $Size * $ratio + $TailY2;
            $points[] = $X1;
            $points[] = $Y1;
            /* Visual correction */
            if ($Angle == 180 || $Angle == 360) {
                $points[4] = $points[2];
            }
            if ($Angle == 90 || $Angle == 270) {
                $points[5] = $points[3];
            }
            $ArrowColor = $this->allocateColor($this->picture, $fillR, $fillG, $fillB, $alpha);
            ImageFilledPolygon($this->picture, $points, 4, $ArrowColor);
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
                    $TailX, $TailY, $TailX2, $TailY2, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha, "ticks" => $ticks]
            );
        } else {
            $this->drawLine(
                    $X1, $Y1, $TailX, $TailY, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha, "ticks" => $ticks]
            );
        }
        /* Re-enable shadows */
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a label with associated arrow
     * @param int $X1
     * @param int $Y1
     * @param string $text
     * @param array $format
     */
    public function drawArrowLabel($X1, $Y1, $text, array $format = []) {
        $fillR = isset($format["FillR"]) ? $format["FillR"] : 0;
        $fillG = isset($format["FillG"]) ? $format["FillG"] : 0;
        $fillB = isset($format["FillB"]) ? $format["FillB"] : 0;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $fillR;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $fillG;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $fillB;
        $fontName = isset($format["fontName"]) ? $this->loadFont($format["fontName"], 'fonts') : $this->fontName;
        $fontSize = isset($format["fontSize"]) ? $format["fontSize"] : $this->fontSize;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $length = isset($format["Length"]) ? $format["Length"] : 50;
        $Angle = isset($format["Angle"]) ? $format["Angle"] : 315;
        $Size = isset($format["Size"]) ? $format["Size"] : 10;
        $Position = isset($format["Position"]) ? $format["Position"] : POSITION_TOP;
        $roundPos = isset($format["RoundPos"]) ? $format["RoundPos"] : false;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : null;
        $Angle = $Angle % 360;
        $X2 = sin(($Angle + 180) * PI / 180) * $length + $X1;
        $Y2 = cos(($Angle + 180) * PI / 180) * $length + $Y1;
        if ($roundPos && $Angle > 0 && $Angle < 180) {
            $Y2 = ceil($Y2);
        }
        if ($roundPos && $Angle > 180) {
            $Y2 = floor($Y2);
        }
        $this->drawArrow($X2, $Y2, $X1, $Y1, $format);
        $Size = imagettfbbox($fontSize, 0, $fontName, $text);
        $txtWidth = max(abs($Size[2] - $Size[0]), abs($Size[0] - $Size[6]));
        if ($Angle > 0 && $Angle < 180) {
            $this->drawLine(
                    $X2, $Y2, $X2 - $txtWidth, $Y2, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha, "ticks" => $ticks]
            );
            if ($Position == POSITION_TOP) {
                $this->drawText(
                        $X2, $Y2 - 2, $text, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $alpha,
                    "Align" => TEXT_ALIGN_BOTTOMRIGHT
                        ]
                );
            } else {
                $this->drawText(
                        $X2, $Y2 + 4, $text, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $alpha,
                    "Align" => TEXT_ALIGN_TOPRIGHT
                        ]
                );
            }
        } else {
            $this->drawLine(
                    $X2, $Y2, $X2 + $txtWidth, $Y2, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha, "ticks" => $ticks]
            );
            if ($Position == POSITION_TOP) {
                $this->drawText(
                        $X2, $Y2 - 2, $text, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $alpha]
                );
            } else {
                $this->drawText(
                        $X2, $Y2 + 4, $text, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $alpha,
                    "Align" => TEXT_ALIGN_TOPLEFT
                        ]
                );
            }
        }
    }

    /**
     * Draw a progress bar filled with specified %
     * @param int $X
     * @param int $Y
     * @param int|float $Percent
     * @param array $format
     */
    public function drawProgress($X, $Y, $Percent, array $format = []) {
        if ($Percent > 100) {
            $Percent = 100;
        }
        if ($Percent < 0) {
            $Percent = 0;
        }
        $Width = isset($format["Width"]) ? $format["Width"] : 200;
        $Height = isset($format["Height"]) ? $format["Height"] : 20;
        $Orientation = isset($format["Orientation"]) ? $format["Orientation"] : ORIENTATION_HORIZONTAL;
        $showLabel = isset($format["showLabel"]) ? $format["showLabel"] : false;
        $LabelPos = isset($format["LabelPos"]) ? $format["LabelPos"] : LABEL_POS_INSIDE;
        $Margin = isset($format["Margin"]) ? $format["Margin"] : 10;
        $r = isset($format["r"]) ? $format["r"] : 130;
        $g = isset($format["g"]) ? $format["g"] : 130;
        $b = isset($format["b"]) ? $format["b"] : 130;
        $rFade = isset($format["RFade"]) ? $format["RFade"] : -1;
        $gFade = isset($format["GFade"]) ? $format["GFade"] : -1;
        $bFade = isset($format["BFade"]) ? $format["BFade"] : -1;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : $r;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : $g;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : $b;
        $boxborderR = isset($format["BoxborderR"]) ? $format["BoxborderR"] : 0;
        $boxborderG = isset($format["BoxborderG"]) ? $format["BoxborderG"] : 0;
        $boxborderB = isset($format["BoxborderB"]) ? $format["BoxborderB"] : 0;
        $boxBackR = isset($format["BoxBackR"]) ? $format["BoxBackR"] : 255;
        $boxBackG = isset($format["BoxBackG"]) ? $format["BoxBackG"] : 255;
        $boxBackB = isset($format["BoxBackB"]) ? $format["BoxBackB"] : 255;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $boxSurrounding = isset($format["BoxSurrounding"]) ? $format["BoxSurrounding"] : null;
        $noAngle = isset($format["noAngle"]) ? $format["noAngle"] : false;
        if ($rFade != -1 && $gFade != -1 && $bFade != -1) {
            $rFade = (($rFade - $r) / 100) * $Percent + $r;
            $gFade = (($gFade - $g) / 100) * $Percent + $g;
            $bFade = (($bFade - $b) / 100) * $Percent + $b;
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
        if ($Orientation == ORIENTATION_VERTICAL) {
            $InnerHeight = (($Height - 2) / 100) * $Percent;
            $this->drawFilledRectangle(
                    $X, $Y, $X + $Width, $Y - $Height, [
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
                    "EndR" => $r,
                    "EndG" => $g,
                    "EndB" => $b
                ];
                $this->drawGradientArea(
                        $X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, DIRECTION_VERTICAL, $gradientOptions
                );
                if ($surrounding) {
                    $this->drawRectangle(
                            $X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, ["r" => 255, "g" => 255, "b" => 255, "alpha" => $surrounding]
                    );
                }
            } else {
                $this->drawFilledRectangle(
                        $X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, [
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
            if ($showLabel && $LabelPos == LABEL_POS_BOTTOM) {
                $this->drawText(
                        $X + ($Width / 2), $Y + $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_TOPMIDDLE]
                );
            }
            if ($showLabel && $LabelPos == LABEL_POS_TOP) {
                $this->drawText(
                        $X + ($Width / 2), $Y - $Height - $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]
                );
            }
            if ($showLabel && $LabelPos == LABEL_POS_INSIDE) {
                $this->drawText(
                        $X + ($Width / 2), $Y - $InnerHeight - $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT, "Angle" => 90]
                );
            }
            if ($showLabel && $LabelPos == LABEL_POS_CENTER) {
                $this->drawText(
                        $X + ($Width / 2), $Y - ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLEMIDDLE, "Angle" => 90]
                );
            }
        } else {
            if ($Percent == 100) {
                $InnerWidth = $Width - 1;
            } else {
                $InnerWidth = (($Width - 2) / 100) * $Percent;
            }
            $this->drawFilledRectangle(
                    $X, $Y, $X + $Width, $Y + $Height, [
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
                    "EndR" => $rFade,
                    "EndG" => $gFade,
                    "EndB" => $bFade
                ];
                $this->drawGradientArea(
                        $X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, DIRECTION_HORIZONTAL, $gradientOptions
                );
                if ($surrounding) {
                    $this->drawRectangle(
                            $X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, ["r" => 255, "g" => 255, "b" => 255, "alpha" => $surrounding]
                    );
                }
            } else {
                $this->drawFilledRectangle(
                        $X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "borderR" => $borderR, "borderG" => $borderG, "borderB" => $borderB
                        ]
                );
            }
            $this->shadow = $restoreShadow;
            if ($showLabel && $LabelPos == LABEL_POS_LEFT) {
                $this->drawText(
                        $X - $Margin, $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLERIGHT]
                );
            }
            if ($showLabel && $LabelPos == LABEL_POS_RIGHT) {
                $this->drawText(
                        $X + $Width + $Margin, $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT]
                );
            }
            if ($showLabel && $LabelPos == LABEL_POS_CENTER) {
                $this->drawText(
                        $X + ($Width / 2), $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLEMIDDLE]
                );
            }
            if ($showLabel && $LabelPos == LABEL_POS_INSIDE) {
                $this->drawText(
                        $X + $InnerWidth + $Margin, $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT]
                );
            }
        }
    }

    /**
     * Draw the legend of the active series
     * @param int $X
     * @param int $Y
     * @param array $format
     */
    public function drawLegend($X, $Y, array $format = []) {
        $Family = isset($format["Family"]) ? $format["Family"] : LEGEND_FAMILY_BOX;
        $fontName = isset($format["fontName"]) ? $this->loadFont($format["fontName"], 'fonts') : $this->fontName;
        $fontSize = isset($format["fontSize"]) ? $format["fontSize"] : $this->fontSize;
        $fontR = isset($format["fontR"]) ? $format["fontR"] : $this->fontColorR;
        $fontG = isset($format["fontG"]) ? $format["fontG"] : $this->fontColorG;
        $fontB = isset($format["fontB"]) ? $format["fontB"] : $this->fontColorB;
        $boxWidth = isset($format["BoxWidth"]) ? $format["BoxWidth"] : 5;
        $boxHeight = isset($format["BoxHeight"]) ? $format["BoxHeight"] : 5;
        $iconAreaWidth = isset($format["iconAreaWidth"]) ? $format["iconAreaWidth"] : $boxWidth;
        $iconAreaHeight = isset($format["iconAreaHeight"]) ? $format["iconAreaHeight"] : $boxHeight;
        $xSpacing = isset($format["xSpacing"]) ? $format["xSpacing"] : 5;
        $Margin = isset($format["Margin"]) ? $format["Margin"] : 5;
        $r = isset($format["r"]) ? $format["r"] : 200;
        $g = isset($format["g"]) ? $format["g"] : 200;
        $b = isset($format["b"]) ? $format["b"] : 200;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 100;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : 255;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : 255;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : 255;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $Style = isset($format["style"]) ? $format["style"] : Constant::LEGEND_ROUND;
        $mode = isset($format["mode"]) ? $format["mode"] : Constant::LEGEND_VERTICAL;
        if ($surrounding != null) {
            $borderR = $r + $surrounding;
            $borderG = $g + $surrounding;
            $borderB = $b + $surrounding;
        }
        $data = $this->dataSet->getData();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"] && isset($serie["picture"])
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
        $YStep = max($this->fontSize, $iconAreaHeight) + 5;
        $XStep = $iconAreaWidth + 5;
        $XStep = $xSpacing;
        $boundaries = [];
        $boundaries["L"] = $X;
        $boundaries["T"] = $Y;
        $boundaries["r"] = 0;
        $boundaries["b"] = 0;
        $vY = $Y;
        $vX = $X;
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
                if ($mode == LEGEND_VERTICAL) {
                    $boxArray = $this->getTextBox(
                            $vX + $iconAreaWidth + 4, $vY + $iconAreaHeight / 2, $fontName, $fontSize, 0, $serie["Description"]
                    );
                    if ($boundaries["T"] > $boxArray[2]["Y"] + $iconAreaHeight / 2) {
                        $boundaries["T"] = $boxArray[2]["Y"] + $iconAreaHeight / 2;
                    }
                    if ($boundaries["r"] < $boxArray[1]["X"] + 2) {
                        $boundaries["r"] = $boxArray[1]["X"] + 2;
                    }
                    if ($boundaries["b"] < $boxArray[1]["Y"] + 2 + $iconAreaHeight / 2) {
                        $boundaries["b"] = $boxArray[1]["Y"] + 2 + $iconAreaHeight / 2;
                    }
                    $Lines = preg_split("/\n/", $serie["Description"]);
                    $vY = $vY + max($this->fontSize * count($Lines), $iconAreaHeight) + 5;
                } elseif ($mode == LEGEND_HORIZONTAL) {
                    $Lines = preg_split("/\n/", $serie["Description"]);
                    $Width = [];
                    foreach ($Lines as $Key => $Value) {
                        $boxArray = $this->getTextBox(
                                $vX + $iconAreaWidth + 6, $Y + $iconAreaHeight / 2 + (($this->fontSize + 3) * $Key), $fontName, $fontSize, 0, $Value
                        );
                        if ($boundaries["T"] > $boxArray[2]["Y"] + $iconAreaHeight / 2) {
                            $boundaries["T"] = $boxArray[2]["Y"] + $iconAreaHeight / 2;
                        }
                        if ($boundaries["r"] < $boxArray[1]["X"] + 2) {
                            $boundaries["r"] = $boxArray[1]["X"] + 2;
                        }
                        if ($boundaries["b"] < $boxArray[1]["Y"] + 2 + $iconAreaHeight / 2) {
                            $boundaries["b"] = $boxArray[1]["Y"] + 2 + $iconAreaHeight / 2;
                        }
                        $Width[] = $boxArray[1]["X"];
                    }
                    $vX = max($Width) + $XStep;
                }
            }
        }
        $vY = $vY - $YStep;
        $vX = $vX - $XStep;
        $TopOffset = $Y - $boundaries["T"];
        if ($boundaries["b"] - ($vY + $iconAreaHeight) < $TopOffset) {
            $boundaries["b"] = $vY + $iconAreaHeight + $TopOffset;
        }
        if ($Style == LEGEND_ROUND) {
            $this->drawRoundedFilledRectangle(
                    $boundaries["L"] - $Margin, $boundaries["T"] - $Margin, $boundaries["r"] + $Margin, $boundaries["b"] + $Margin, $Margin, [
                "r" => $r,
                "g" => $g,
                "b" => $b,
                "alpha" => $alpha,
                "borderR" => $borderR,
                "borderG" => $borderG,
                "borderB" => $borderB
                    ]
            );
        } elseif ($Style == LEGEND_BOX) {
            $this->drawFilledRectangle(
                    $boundaries["L"] - $Margin, $boundaries["T"] - $Margin, $boundaries["r"] + $Margin, $boundaries["b"] + $Margin, [
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
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $ticks = $serie["ticks"];
                $weight = $serie["weight"];
                if (isset($serie["picture"])) {
                    $picture = $serie["picture"];
                    list($picWidth, $picHeight) = $this->getPicInfo($picture);
                    $picX = $X + $iconAreaWidth / 2;
                    $picY = $Y + $iconAreaHeight / 2;
                    $this->drawFromPNG($picX - $picWidth / 2, $picY - $picHeight / 2, $picture);
                } else {
                    if ($Family == LEGEND_FAMILY_BOX) {
                        $XOffset = 0;
                        if ($boxWidth != $iconAreaWidth) {
                            $XOffset = floor(($iconAreaWidth - $boxWidth) / 2);
                        }
                        $YOffset = 0;
                        if ($boxHeight != $iconAreaHeight) {
                            $YOffset = floor(($iconAreaHeight - $boxHeight) / 2);
                        }
                        $this->drawFilledRectangle(
                                $X + 1 + $XOffset, $Y + 1 + $YOffset, $X + $boxWidth + $XOffset + 1, $Y + $boxHeight + 1 + $YOffset, ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20]
                        );
                        $this->drawFilledRectangle(
                                $X + $XOffset, $Y + $YOffset, $X + $boxWidth + $XOffset, $Y + $boxHeight + $YOffset, ["r" => $r, "g" => $g, "b" => $b, "surrounding" => 20]
                        );
                    } elseif ($Family == LEGEND_FAMILY_CIRCLE) {
                        $this->drawFilledCircle(
                                $X + 1 + $iconAreaWidth / 2, $Y + 1 + $iconAreaHeight / 2, min($iconAreaHeight / 2, $iconAreaWidth / 2), ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20]
                        );
                        $this->drawFilledCircle(
                                $X + $iconAreaWidth / 2, $Y + $iconAreaHeight / 2, min($iconAreaHeight / 2, $iconAreaWidth / 2), ["r" => $r, "g" => $g, "b" => $b, "surrounding" => 20]
                        );
                    } elseif ($Family == LEGEND_FAMILY_LINE) {
                        $this->drawLine(
                                $X + 1, $Y + 1 + $iconAreaHeight / 2, $X + 1 + $iconAreaWidth, $Y + 1 + $iconAreaHeight / 2, ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20, "ticks" => $ticks, "weight" => $weight]
                        );
                        $this->drawLine(
                                $X, $Y + $iconAreaHeight / 2, $X + $iconAreaWidth, $Y + $iconAreaHeight / 2, ["r" => $r, "g" => $g, "b" => $b, "ticks" => $ticks, "weight" => $weight]
                        );
                    }
                }
                if ($mode == LEGEND_VERTICAL) {
                    $Lines = preg_split("/\n/", $serie["Description"]);
                    foreach ($Lines as $Key => $Value) {
                        $this->drawText(
                                $X + $iconAreaWidth + 4, $Y + $iconAreaHeight / 2 + (($this->fontSize + 3) * $Key), $Value, [
                            "r" => $fontR,
                            "g" => $fontG,
                            "b" => $fontB,
                            "Align" => TEXT_ALIGN_MIDDLELEFT,
                            "fontSize" => $fontSize,
                            "fontName" => $fontName
                                ]
                        );
                    }
                    $Y = $Y + max($this->fontSize * count($Lines), $iconAreaHeight) + 5;
                } elseif ($mode == LEGEND_HORIZONTAL) {
                    $Lines = preg_split("/\n/", $serie["Description"]);
                    $Width = [];
                    foreach ($Lines as $Key => $Value) {
                        $boxArray = $this->drawText(
                                $X + $iconAreaWidth + 4, $Y + $iconAreaHeight / 2 + (($this->fontSize + 3) * $Key), $Value, [
                            "r" => $fontR,
                            "g" => $fontG,
                            "b" => $fontB,
                            "Align" => TEXT_ALIGN_MIDDLELEFT,
                            "fontSize" => $fontSize,
                            "fontName" => $fontName
                                ]
                        );
                        $Width[] = $boxArray[1]["X"];
                    }
                    $X = max($Width) + 2 + $XStep;
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
        $Pos = isset($format["Pos"]) ? $format["Pos"] : SCALE_POS_LEFTRIGHT;
        $Floating = isset($format["Floating"]) ? $format["Floating"] : false;
        $mode = isset($format["mode"]) ? $format["mode"] : SCALE_MODE_FLOATING;
        $removeXAxis = isset($format["RemoveXAxis"]) ? $format["RemoveXAxis"] : false;
        $removeYAxis = isset($format["RemoveYAxis"]) ? $format["RemoveYAxis"] : false;
        $removeYAxiValues = isset($format["RemoveYAxisValues"]) ? $format["RemoveYAxisValues"] : false;
        $MinDivHeight = isset($format["minDivHeight"]) ? $format["minDivHeight"] : 20;
        $Factors = isset($format["Factors"]) ? $format["Factors"] : [1, 2, 5];
        $ManualScale = isset($format["ManualScale"]) ? $format["ManualScale"] : ["0" => ["min" => -100, "max" => 100]]
        ;
        $XMargin = isset($format["XMargin"]) ? $format["XMargin"] : AUTO;
        $YMargin = isset($format["YMargin"]) ? $format["YMargin"] : 0;
        $ScaleSpacing = isset($format["ScaleSpacing"]) ? $format["ScaleSpacing"] : 15;
        $InnerTickWidth = isset($format["InnerTickWidth"]) ? $format["InnerTickWidth"] : 2;
        $OuterTickWidth = isset($format["OuterTickWidth"]) ? $format["OuterTickWidth"] : 2;
        $drawXLines = isset($format["DrawXLines"]) ? $format["DrawXLines"] : true;
        $drawYLines = isset($format["DrawYLines"]) ? $format["DrawYLines"] : ALL;
        $gridTicks = isset($format["GridTicks"]) ? $format["GridTicks"] : 4;
        $gridR = isset($format["GridR"]) ? $format["GridR"] : 255;
        $gridG = isset($format["GridG"]) ? $format["GridG"] : 255;
        $gridB = isset($format["GridB"]) ? $format["GridB"] : 255;
        $gridalpha = isset($format["Gridalpha"]) ? $format["Gridalpha"] : 40;
        $AxisRo = isset($format["axisR"]) ? $format["axisR"] : 0;
        $AxisGo = isset($format["axisG"]) ? $format["axisG"] : 0;
        $AxisBo = isset($format["axisB"]) ? $format["axisB"] : 0;
        $Axisalpha = isset($format["axisalpha"]) ? $format["axisalpha"] : 100;
        $TickRo = isset($format["TickR"]) ? $format["TickR"] : 0;
        $TickGo = isset($format["TickG"]) ? $format["TickG"] : 0;
        $TickBo = isset($format["TickB"]) ? $format["TickB"] : 0;
        $Tickalpha = isset($format["Tickalpha"]) ? $format["Tickalpha"] : 100;
        $drawSubTicks = isset($format["DrawSubTicks"]) ? $format["DrawSubTicks"] : false;
        $InnerSubTickWidth = isset($format["InnerSubTickWidth"]) ? $format["InnerSubTickWidth"] : 0;
        $OuterSubTickWidth = isset($format["OuterSubTickWidth"]) ? $format["OuterSubTickWidth"] : 2;
        $SubTickR = isset($format["SubTickR"]) ? $format["SubTickR"] : 255;
        $SubTickG = isset($format["SubTickG"]) ? $format["SubTickG"] : 0;
        $SubTickB = isset($format["SubTickB"]) ? $format["SubTickB"] : 0;
        $SubTickalpha = isset($format["SubTickalpha"]) ? $format["SubTickalpha"] : 100;
        $AutoAxisLabels = isset($format["AutoAxisLabels"]) ? $format["AutoAxisLabels"] : true;
        $XReleasePercent = isset($format["XReleasePercent"]) ? $format["XReleasePercent"] : 1;
        $drawArrows = isset($format["drawArrows"]) ? $format["drawArrows"] : false;
        $arrowSize = isset($format["arrowSize"]) ? $format["arrowSize"] : 8;
        $CycleBackground = isset($format["CycleBackground"]) ? $format["CycleBackground"] : false;
        $backgroundR1 = isset($format["BackgroundR1"]) ? $format["BackgroundR1"] : 255;
        $backgroundG1 = isset($format["BackgroundG1"]) ? $format["BackgroundG1"] : 255;
        $backgroundB1 = isset($format["BackgroundB1"]) ? $format["BackgroundB1"] : 255;
        $backgroundalpha1 = isset($format["Backgroundalpha1"]) ? $format["Backgroundalpha1"] : 20;
        $backgroundR2 = isset($format["BackgroundR2"]) ? $format["BackgroundR2"] : 230;
        $backgroundG2 = isset($format["BackgroundG2"]) ? $format["BackgroundG2"] : 230;
        $backgroundB2 = isset($format["BackgroundB2"]) ? $format["BackgroundB2"] : 230;
        $backgroundalpha2 = isset($format["Backgroundalpha2"]) ? $format["Backgroundalpha2"] : 20;
        $LabelingMethod = isset($format["LabelingMethod"]) ? $format["LabelingMethod"] : LABELING_ALL;
        $LabelSkip = isset($format["LabelSkip"]) ? $format["LabelSkip"] : 0;
        $LabelRotation = isset($format["LabelRotation"]) ? $format["LabelRotation"] : 0;
        $removeSkippedAxis = isset($format["RemoveSkippedAxis"]) ? $format["RemoveSkippedAxis"] : false;
        $SkippedAxisTicks = isset($format["SkippedAxisTicks"]) ? $format["SkippedAxisTicks"] : $gridTicks + 2;
        $SkippedAxisR = isset($format["SkippedAxisR"]) ? $format["SkippedAxisR"] : $gridR;
        $SkippedAxisG = isset($format["SkippedAxisG"]) ? $format["SkippedAxisG"] : $gridG;
        $SkippedAxisB = isset($format["SkippedAxisB"]) ? $format["SkippedAxisB"] : $gridB;
        $SkippedAxisalpha = isset($format["SkippedAxisalpha"]) ? $format["SkippedAxisalpha"] : $gridalpha - 30;
        $SkippedTickR = isset($format["SkippedTickR"]) ? $format["SkippedTickR"] : $TickRo;
        $SkippedTickG = isset($format["SkippedTickG"]) ? $format["SkippedTickG"] : $TickGo;
        $SkippedTickB = isset($format["SkippedTicksB"]) ? $format["SkippedTickB"] : $TickBo;
        $SkippedTickalpha = isset($format["SkippedTickalpha"]) ? $format["SkippedTickalpha"] : $Tickalpha - 80;
        $SkippedInnerTickWidth = isset($format["SkippedInnerTickWidth"]) ? $format["SkippedInnerTickWidth"] : 0;
        $SkippedOuterTickWidth = isset($format["SkippedOuterTickWidth"]) ? $format["SkippedOuterTickWidth"] : 2;
        /* Floating scale require X & Y margins to be set manually */
        if ($Floating && ($XMargin == AUTO || $YMargin == 0)) {
            $Floating = false;
        }
        /* Skip a NOTICE event in case of an empty array */
        if ($drawYLines == NONE || $drawYLines == false) {
            $drawYLines = ["zarma" => "31"];
        }
        /* Define the color for the skipped elements */
        $SkippedAxisColor = [
            "r" => $SkippedAxisR,
            "g" => $SkippedAxisG,
            "b" => $SkippedAxisB,
            "alpha" => $SkippedAxisalpha,
            "ticks" => $SkippedAxisTicks
        ];
        $SkippedTickColor = [
            "r" => $SkippedTickR,
            "g" => $SkippedTickG,
            "b" => $SkippedTickB,
            "alpha" => $SkippedTickalpha
        ];
        $data = $this->dataSet->getData();
        $Abscissa = null;
        if (isset($data["Abscissa"])) {
            $Abscissa = $data["Abscissa"];
        }
        /* Unset the abscissa axis, needed if we display multiple charts on the same picture */
        if ($Abscissa != null) {
            foreach ($data["axis"] as $axisId => $parameters) {
                if ($parameters["Identity"] == Constant::AXIS_X) {
                    unset($data["axis"][$axisId]);
                }
            }
        }
        /* Build the scale settings */
        $gotXAxis = false;
        foreach ($data["axis"] as $axisId => $AxisParameter) {
            if ($AxisParameter["Identity"] == AXIS_X) {
                $gotXAxis = true;
            }
            if ($Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_Y) {
                $Height = $this->GraphAreaY2 - $this->GraphAreaY1 - $YMargin * 2;
            } elseif ($Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_X) {
                $Height = $this->GraphAreaX2 - $this->GraphAreaX1;
            } elseif ($Pos == SCALE_POS_TOPBOTTOM && $AxisParameter["Identity"] == AXIS_Y) {
                $Height = $this->GraphAreaX2 - $this->GraphAreaX1 - $YMargin * 2;
                ;
            } else {
                $Height = $this->GraphAreaY2 - $this->GraphAreaY1;
            }
            $AxisMin = ABSOLUTE_MAX;
            $AxisMax = OUT_OF_SIGHT;
            if ($mode == SCALE_MODE_FLOATING || $mode == SCALE_MODE_START0) {
                foreach ($data["series"] as $serieID => $serieParameter) {
                    if ($serieParameter["axis"] == $axisId && $data["series"][$serieID]["isDrawable"] && $data["Abscissa"] != $serieID
                    ) {
                        $AxisMax = max($AxisMax, $data["series"][$serieID]["max"]);
                        $AxisMin = min($AxisMin, $data["series"][$serieID]["min"]);
                    }
                }
                $AutoMargin = (($AxisMax - $AxisMin) / 100) * $XReleasePercent;
                $data["axis"][$axisId]["min"] = $AxisMin - $AutoMargin;
                $data["axis"][$axisId]["max"] = $AxisMax + $AutoMargin;
                if ($mode == SCALE_MODE_START0) {
                    $data["axis"][$axisId]["min"] = 0;
                }
            } elseif ($mode == SCALE_MODE_MANUAL) {
                if (isset($ManualScale[$axisId]["min"]) && isset($ManualScale[$axisId]["max"])) {
                    $data["axis"][$axisId]["min"] = $ManualScale[$axisId]["min"];
                    $data["axis"][$axisId]["max"] = $ManualScale[$axisId]["max"];
                } else {
                    throw new Exception("Manual scale boundaries not set.");
                }
            } elseif ($mode == SCALE_MODE_ADDALL || $mode == SCALE_MODE_ADDALL_START0) {
                $series = [];
                foreach ($data["series"] as $serieID => $serieParameter) {
                    if ($serieParameter["axis"] == $axisId && $serieParameter["isDrawable"] && $data["Abscissa"] != $serieID
                    ) {
                        $series[$serieID] = count($data["series"][$serieID]["Data"]);
                    }
                }
                for ($ID = 0; $ID <= max($series) - 1; $ID++) {
                    $PointMin = 0;
                    $PointMax = 0;
                    foreach ($series as $serieID => $ValuesCount) {
                        if (isset($data["series"][$serieID]["Data"][$ID]) && $data["series"][$serieID]["Data"][$ID] != null
                        ) {
                            $Value = $data["series"][$serieID]["Data"][$ID];
                            if ($Value > 0) {
                                $PointMax = $PointMax + $Value;
                            } else {
                                $PointMin = $PointMin + $Value;
                            }
                        }
                    }
                    $AxisMax = max($AxisMax, $PointMax);
                    $AxisMin = min($AxisMin, $PointMin);
                }
                $AutoMargin = (($AxisMax - $AxisMin) / 100) * $XReleasePercent;
                $data["axis"][$axisId]["min"] = $AxisMin - $AutoMargin;
                $data["axis"][$axisId]["max"] = $AxisMax + $AutoMargin;
            }
            $MaxDivs = floor($Height / $MinDivHeight);
            if ($mode == SCALE_MODE_ADDALL_START0) {
                $data["axis"][$axisId]["min"] = 0;
            }
            $Scale = $this->computeScale(
                    $data["axis"][$axisId]["min"], $data["axis"][$axisId]["max"], $MaxDivs, $Factors, $axisId
            );
            $data["axis"][$axisId]["Margin"] = $AxisParameter["Identity"] == AXIS_X ? $XMargin : $YMargin;
            $data["axis"][$axisId]["ScaleMin"] = $Scale["XMin"];
            $data["axis"][$axisId]["ScaleMax"] = $Scale["XMax"];
            $data["axis"][$axisId]["Rows"] = $Scale["Rows"];
            $data["axis"][$axisId]["RowHeight"] = $Scale["RowHeight"];
            if (isset($Scale["Format"])) {
                $data["axis"][$axisId]["Format"] = $Scale["Format"];
            }
            if (!isset($data["axis"][$axisId]["Display"])) {
                $data["axis"][$axisId]["Display"] = null;
            }
            if (!isset($data["axis"][$axisId]["Format"])) {
                $data["axis"][$axisId]["Format"] = null;
            }
            if (!isset($data["axis"][$axisId]["Unit"])) {
                $data["axis"][$axisId]["Unit"] = null;
            }
        }
        /* Still no X axis */
        if ($gotXAxis == false) {
            if ($Abscissa != null) {
                $points = count($data["series"][$Abscissa]["Data"]);
                $AxisName = null;
                if ($AutoAxisLabels) {
                    $AxisName = isset($data["series"][$Abscissa]["Description"]) ? $data["series"][$Abscissa]["Description"] : null
                    ;
                }
            } else {
                $points = 0;
                $AxisName = isset($data["XAxisName"]) ? $data["XAxisName"] : null;
                foreach ($data["series"] as $serieID => $serieParameter) {
                    if ($serieParameter["isDrawable"]) {
                        $points = max($points, count($serieParameter["Data"]));
                    }
                }
            }
            $axisId = count($data["axis"]);
            $data["axis"][$axisId]["Identity"] = AXIS_X;
            if ($Pos == SCALE_POS_LEFTRIGHT) {
                $data["axis"][$axisId]["Position"] = AXIS_POSITION_BOTTOM;
            } else {
                $data["axis"][$axisId]["Position"] = AXIS_POSITION_LEFT;
            }
            if (isset($data["AbscissaName"])) {
                $data["axis"][$axisId]["Name"] = $data["AbscissaName"];
            }
            if ($XMargin == AUTO) {
                if ($Pos == SCALE_POS_LEFTRIGHT) {
                    $Height = $this->GraphAreaX2 - $this->GraphAreaX1;
                } else {
                    $Height = $this->GraphAreaY2 - $this->GraphAreaY1;
                }
                if ($points == 0 || $points == 1) {
                    $data["axis"][$axisId]["Margin"] = $Height / 2;
                } else {
                    $data["axis"][$axisId]["Margin"] = ($Height / $points) / 2;
                }
            } else {
                $data["axis"][$axisId]["Margin"] = $XMargin;
            }
            $data["axis"][$axisId]["Rows"] = $points - 1;
            if (!isset($data["axis"][$axisId]["Display"])) {
                $data["axis"][$axisId]["Display"] = null;
            }
            if (!isset($data["axis"][$axisId]["Format"])) {
                $data["axis"][$axisId]["Format"] = null;
            }
            if (!isset($data["axis"][$axisId]["Unit"])) {
                $data["axis"][$axisId]["Unit"] = null;
            }
        }
        /* Do we need to reverse the abscissa position? */
        if ($Pos != SCALE_POS_LEFTRIGHT) {
            $data["AbsicssaPosition"] = AXIS_POSITION_RIGHT;
            if ($data["AbsicssaPosition"] == AXIS_POSITION_BOTTOM) {
                $data["AbsicssaPosition"] = AXIS_POSITION_LEFT;
            }
        }
        $data["axis"][$axisId]["Position"] = $data["AbsicssaPosition"];
        $this->dataSet->saveOrientation($Pos);
        $this->dataSet->saveAxisConfig($data["axis"]);
        $this->dataSet->saveYMargin($YMargin);
        $fontColorRo = $this->fontColorR;
        $fontColorGo = $this->fontColorG;
        $fontColorBo = $this->fontColorB;
        $AxisPos["L"] = $this->GraphAreaX1;
        $AxisPos["r"] = $this->GraphAreaX2;
        $AxisPos["T"] = $this->GraphAreaY1;
        $AxisPos["b"] = $this->GraphAreaY2;
        foreach ($data["axis"] as $axisId => $parameters) {
            if (isset($parameters["color"])) {
                $AxisR = $parameters["color"]["r"];
                $AxisG = $parameters["color"]["g"];
                $AxisB = $parameters["color"]["b"];
                $TickR = $parameters["color"]["r"];
                $TickG = $parameters["color"]["g"];
                $TickB = $parameters["color"]["b"];
                $this->setFontProperties(
                        [
                            "r" => $parameters["color"]["r"],
                            "g" => $parameters["color"]["g"],
                            "b" => $parameters["color"]["b"]
                        ]
                );
            } else {
                $AxisR = $AxisRo;
                $AxisG = $AxisGo;
                $AxisB = $AxisBo;
                $TickR = $TickRo;
                $TickG = $TickGo;
                $TickB = $TickBo;
                $this->setFontProperties(["r" => $fontColorRo, "g" => $fontColorGo, "b" => $fontColorBo]);
            }
            $LastValue = "w00t";
            $ID = 1;
            if ($parameters["Identity"] == AXIS_X) {
                if ($Pos == SCALE_POS_LEFTRIGHT) {
                    if ($parameters["Position"] == AXIS_POSITION_BOTTOM) {
                        if ($LabelRotation == 0) {
                            $LabelAlign = TEXT_ALIGN_TOPMIDDLE;
                            $YLabelOffset = 2;
                        }
                        if ($LabelRotation > 0 && $LabelRotation < 190) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $YLabelOffset = 5;
                        }
                        if ($LabelRotation == 180) {
                            $LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
                            $YLabelOffset = 5;
                        }
                        if ($LabelRotation > 180 && $LabelRotation < 360) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $YLabelOffset = 2;
                        }
                        if (!$removeXAxis) {
                            if ($Floating) {
                                $FloatingOffset = $YMargin;
                                $this->drawLine(
                                        $this->GraphAreaX1 + $parameters["Margin"], $AxisPos["b"], $this->GraphAreaX2 - $parameters["Margin"], $AxisPos["b"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                                );
                            } else {
                                $FloatingOffset = 0;
                                $this->drawLine(
                                        $this->GraphAreaX1, $AxisPos["b"], $this->GraphAreaX2, $AxisPos["b"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                        $this->GraphAreaX2 - $parameters["Margin"], $AxisPos["b"], $this->GraphAreaX2 + ($arrowSize * 2), $AxisPos["b"], ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $arrowSize]
                                );
                            }
                        }
                        $Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $parameters["Margin"] * 2;
                        if ($parameters["Rows"] == 0) {
                            $step = $Width;
                        } else {
                            $step = $Width / ($parameters["Rows"]);
                        }
                        $MaxBottom = $AxisPos["b"];
                        for ($i = 0; $i <= $parameters["Rows"]; $i++) {
                            $XPos = $this->GraphAreaX1 + $parameters["Margin"] + $step * $i;
                            $YPos = $AxisPos["b"];
                            if ($Abscissa != null) {
                                $Value = "";
                                if (isset($data["series"][$Abscissa]["Data"][$i])) {
                                    $Value = $this->scaleFormat(
                                            $data["series"][$Abscissa]["Data"][$i], $data["XAxisDisplay"], $data["XAxisFormat"], $data["XAxisUnit"]
                                    );
                                }
                            } else {
                                $Value = $i;
                                if (isset($parameters["ScaleMin"]) && isset($parameters["RowHeight"])) {
                                    $Value = $this->scaleFormat(
                                            $parameters["ScaleMin"] + $parameters["RowHeight"] * $i, $data["XAxisDisplay"], $data["XAxisFormat"], $data["XAxisUnit"]
                                    );
                                }
                            }
                            $ID++;
                            $Skipped = true;
                            if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                        $XPos, $YPos + $OuterTickWidth + $YLabelOffset, $Value, ["Angle" => $LabelRotation, "Align" => $LabelAlign]
                                );
                                $txtBottom = $YPos + $OuterTickWidth + 2 + ($bounds[0]["Y"] - $bounds[2]["Y"]);
                                $MaxBottom = max($MaxBottom, $txtBottom);
                                $LastValue = $Value;
                                $Skipped = false;
                            }
                            if ($removeXAxis) {
                                $Skipped = false;
                            }
                            if ($Skipped) {
                                if ($drawXLines && !$removeSkippedAxis) {
                                    $this->drawLine(
                                            $XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $SkippedAxisColor
                                    );
                                }
                                if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                            $XPos, $YPos - $SkippedInnerTickWidth, $XPos, $YPos + $SkippedOuterTickWidth, $SkippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines && ($XPos != $this->GraphAreaX1 && $XPos != $this->GraphAreaX2)
                                ) {
                                    $this->drawLine(
                                            $XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, [
                                        "r" => $gridR,
                                        "g" => $gridG,
                                        "b" => $gridB,
                                        "alpha" => $gridalpha,
                                        "ticks" => $gridTicks
                                            ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                            $XPos, $YPos - $InnerTickWidth, $XPos, $YPos + $OuterTickWidth, ["r" => $TickR, "g" => $TickG, "b" => $TickB, "alpha" => $Tickalpha]
                                    );
                                }
                            }
                        }
                        if (isset($parameters["Name"]) && !$removeXAxis) {
                            $YPos = $MaxBottom + 2;
                            $XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
                            $bounds = $this->drawText(
                                    $XPos, $YPos, $parameters["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]
                            );
                            $MaxBottom = $bounds[0]["Y"];
                            $this->dataSet->data["GraphArea"]["Y2"] = $MaxBottom + $this->fontSize;
                        }
                        $AxisPos["b"] = $MaxBottom + $ScaleSpacing;
                    } elseif ($parameters["Position"] == AXIS_POSITION_TOP) {
                        if ($LabelRotation == 0) {
                            $LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
                            $YLabelOffset = 2;
                        }
                        if ($LabelRotation > 0 && $LabelRotation < 190) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $YLabelOffset = 2;
                        }
                        if ($LabelRotation == 180) {
                            $LabelAlign = TEXT_ALIGN_TOPMIDDLE;
                            $YLabelOffset = 5;
                        }
                        if ($LabelRotation > 180 && $LabelRotation < 360) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $YLabelOffset = 5;
                        }
                        if (!$removeXAxis) {
                            if ($Floating) {
                                $FloatingOffset = $YMargin;
                                $this->drawLine(
                                        $this->GraphAreaX1 + $parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 - $parameters["Margin"], $AxisPos["T"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                                );
                            } else {
                                $FloatingOffset = 0;
                                $this->drawLine(
                                        $this->GraphAreaX1, $AxisPos["T"], $this->GraphAreaX2, $AxisPos["T"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                        $this->GraphAreaX2 - $parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 + ($arrowSize * 2), $AxisPos["T"], ["FillR" => $AxisR, "FillG" => $AxisG, "FillB" => $AxisB, "Size" => $arrowSize]
                                );
                            }
                        }
                        $Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $parameters["Margin"] * 2;
                        if ($parameters["Rows"] == 0) {
                            $step = $Width;
                        } else {
                            $step = $Width / $parameters["Rows"];
                        }
                        $MinTop = $AxisPos["T"];
                        for ($i = 0; $i <= $parameters["Rows"]; $i++) {
                            $XPos = $this->GraphAreaX1 + $parameters["Margin"] + $step * $i;
                            $YPos = $AxisPos["T"];
                            if ($Abscissa != null) {
                                $Value = "";
                                if (isset($data["series"][$Abscissa]["Data"][$i])) {
                                    $Value = $this->scaleFormat(
                                            $data["series"][$Abscissa]["Data"][$i], $data["XAxisDisplay"], $data["XAxisFormat"], $data["XAxisUnit"]
                                    );
                                }
                            } else {
                                $Value = $i;
                                if (isset($parameters["ScaleMin"]) && isset($parameters["RowHeight"])) {
                                    $Value = $this->scaleFormat(
                                            $parameters["ScaleMin"] + $parameters["RowHeight"] * $i, $data["XAxisDisplay"], $data["XAxisFormat"], $data["XAxisUnit"]
                                    );
                                }
                            }
                            $ID++;
                            $Skipped = true;
                            if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                        $XPos, $YPos - $OuterTickWidth - $YLabelOffset, $Value, ["Angle" => $LabelRotation, "Align" => $LabelAlign]
                                );
                                $txtBox = $YPos - $OuterTickWidth - 2 - ($bounds[0]["Y"] - $bounds[2]["Y"]);
                                $MinTop = min($MinTop, $txtBox);
                                $LastValue = $Value;
                                $Skipped = false;
                            }
                            if ($removeXAxis) {
                                $Skipped = false;
                            }
                            if ($Skipped) {
                                if ($drawXLines && !$removeSkippedAxis) {
                                    $this->drawLine(
                                            $XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $SkippedAxisColor
                                    );
                                }
                                if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                            $XPos, $YPos + $SkippedInnerTickWidth, $XPos, $YPos - $SkippedOuterTickWidth, $SkippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines) {
                                    $this->drawLine(
                                            $XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, [
                                        "r" => $gridR,
                                        "g" => $gridG,
                                        "b" => $gridB,
                                        "alpha" => $gridalpha,
                                        "ticks" => $gridTicks
                                            ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                            $XPos, $YPos + $InnerTickWidth, $XPos, $YPos - $OuterTickWidth, [
                                        "r" => $TickR,
                                        "g" => $TickG,
                                        "b" => $TickB,
                                        "alpha" => $Tickalpha
                                            ]
                                    );
                                }
                            }
                        }
                        if (isset($parameters["Name"]) && !$removeXAxis) {
                            $YPos = $MinTop - 2;
                            $XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
                            $bounds = $this->drawText(
                                    $XPos, $YPos, $parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]
                            );
                            $MinTop = $bounds[2]["Y"];
                            $this->dataSet->data["GraphArea"]["Y1"] = $MinTop;
                        }
                        $AxisPos["T"] = $MinTop - $ScaleSpacing;
                    }
                } elseif ($Pos == SCALE_POS_TOPBOTTOM) {
                    if ($parameters["Position"] == AXIS_POSITION_LEFT) {
                        if ($LabelRotation == 0) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $XLabelOffset = -2;
                        }
                        if ($LabelRotation > 0 && $LabelRotation < 190) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $XLabelOffset = -6;
                        }
                        if ($LabelRotation == 180) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $XLabelOffset = -2;
                        }
                        if ($LabelRotation > 180 && $LabelRotation < 360) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $XLabelOffset = -5;
                        }
                        if (!$removeXAxis) {
                            if ($Floating) {
                                $FloatingOffset = $YMargin;
                                $this->drawLine(
                                        $AxisPos["L"], $this->GraphAreaY1 + $parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 - $parameters["Margin"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                                );
                            } else {
                                $FloatingOffset = 0;
                                $this->drawLine(
                                        $AxisPos["L"], $this->GraphAreaY1, $AxisPos["L"], $this->GraphAreaY2, ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                        $AxisPos["L"], $this->GraphAreaY2 - $parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 + ($arrowSize * 2), [
                                    "FillR" => $AxisR,
                                    "FillG" => $AxisG,
                                    "FillB" => $AxisB,
                                    "Size" => $arrowSize
                                        ]
                                );
                            }
                        }
                        $Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $parameters["Margin"] * 2;
                        if ($parameters["Rows"] == 0) {
                            $step = $Height;
                        } else {
                            $step = $Height / $parameters["Rows"];
                        }
                        $MinLeft = $AxisPos["L"];
                        for ($i = 0; $i <= $parameters["Rows"]; $i++) {
                            $YPos = $this->GraphAreaY1 + $parameters["Margin"] + $step * $i;
                            $XPos = $AxisPos["L"];
                            if ($Abscissa != null) {
                                $Value = "";
                                if (isset($data["series"][$Abscissa]["Data"][$i])) {
                                    $Value = $this->scaleFormat(
                                            $data["series"][$Abscissa]["Data"][$i], $data["XAxisDisplay"], $data["XAxisFormat"], $data["XAxisUnit"]
                                    );
                                }
                            } else {
                                $Value = $i;
                                if (isset($parameters["ScaleMin"]) && isset($parameters["RowHeight"])) {
                                    $Value = $this->scaleFormat(
                                            $parameters["ScaleMin"] + $parameters["RowHeight"] * $i, $data["XAxisDisplay"], $data["XAxisFormat"], $data["XAxisUnit"]
                                    );
                                }
                            }
                            $ID++;
                            $Skipped = true;
                            if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                        $XPos - $OuterTickWidth + $XLabelOffset, $YPos, $Value, ["Angle" => $LabelRotation, "Align" => $LabelAlign]
                                );
                                $txtBox = $XPos - $OuterTickWidth - 2 - ($bounds[1]["X"] - $bounds[0]["X"]);
                                $MinLeft = min($MinLeft, $txtBox);
                                $LastValue = $Value;
                                $Skipped = false;
                            }
                            if ($removeXAxis) {
                                $Skipped = false;
                            }
                            if ($Skipped) {
                                if ($drawXLines && !$removeSkippedAxis) {
                                    $this->drawLine(
                                            $this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $SkippedAxisColor
                                    );
                                }
                                if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                            $XPos - $SkippedOuterTickWidth, $YPos, $XPos + $SkippedInnerTickWidth, $YPos, $SkippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines &&
                                        ($YPos != $this->GraphAreaY1 && $YPos != $this->GraphAreaY2)
                                ) {
                                    $this->drawLine(
                                            $this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, [
                                        "r" => $gridR,
                                        "g" => $gridG,
                                        "b" => $gridB,
                                        "alpha" => $gridalpha,
                                        "ticks" => $gridTicks
                                            ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                            $XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["r" => $TickR, "g" => $TickG, "b" => $TickB, "alpha" => $Tickalpha]
                                    );
                                }
                            }
                        }
                        if (isset($parameters["Name"]) && !$removeXAxis) {
                            $XPos = $MinLeft - 2;
                            $YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
                            $bounds = $this->drawText(
                                    $XPos, $YPos, $parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 90]
                            );
                            $MinLeft = $bounds[0]["X"];
                            $this->dataSet->data["GraphArea"]["X1"] = $MinLeft;
                        }
                        $AxisPos["L"] = $MinLeft - $ScaleSpacing;
                    } elseif ($parameters["Position"] == AXIS_POSITION_RIGHT) {
                        if ($LabelRotation == 0) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $XLabelOffset = 2;
                        }
                        if ($LabelRotation > 0 && $LabelRotation < 190) {
                            $LabelAlign = TEXT_ALIGN_MIDDLELEFT;
                            $XLabelOffset = 6;
                        }
                        if ($LabelRotation == 180) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $XLabelOffset = 5;
                        }
                        if ($LabelRotation > 180 && $LabelRotation < 360) {
                            $LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
                            $XLabelOffset = 7;
                        }
                        if (!$removeXAxis) {
                            if ($Floating) {
                                $FloatingOffset = $YMargin;
                                $this->drawLine(
                                        $AxisPos["r"], $this->GraphAreaY1 + $parameters["Margin"], $AxisPos["r"], $this->GraphAreaY2 - $parameters["Margin"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                                );
                            } else {
                                $FloatingOffset = 0;
                                $this->drawLine(
                                        $AxisPos["r"], $this->GraphAreaY1, $AxisPos["r"], $this->GraphAreaY2, ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                                );
                            }
                            if ($drawArrows) {
                                $this->drawArrow(
                                        $AxisPos["r"], $this->GraphAreaY2 - $parameters["Margin"], $AxisPos["r"], $this->GraphAreaY2 + ($arrowSize * 2), [
                                    "FillR" => $AxisR,
                                    "FillG" => $AxisG,
                                    "FillB" => $AxisB,
                                    "Size" => $arrowSize
                                        ]
                                );
                            }
                        }
                        $Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $parameters["Margin"] * 2;
                        if ($parameters["Rows"] == 0) {
                            $step = $Height;
                        } else {
                            $step = $Height / $parameters["Rows"];
                        }
                        $MaxRight = $AxisPos["r"];
                        for ($i = 0; $i <= $parameters["Rows"]; $i++) {
                            $YPos = $this->GraphAreaY1 + $parameters["Margin"] + $step * $i;
                            $XPos = $AxisPos["r"];
                            if ($Abscissa != null) {
                                $Value = "";
                                if (isset($data["series"][$Abscissa]["Data"][$i])) {
                                    $Value = $this->scaleFormat(
                                            $data["series"][$Abscissa]["Data"][$i], $data["XAxisDisplay"], $data["XAxisFormat"], $data["XAxisUnit"]
                                    );
                                }
                            } else {
                                $Value = $i;
                                if (isset($parameters["ScaleMin"]) && isset($parameters["RowHeight"])) {
                                    $Value = $this->scaleFormat(
                                            $parameters["ScaleMin"] + $parameters["RowHeight"] * $i, $data["XAxisDisplay"], $data["XAxisFormat"], $data["XAxisUnit"]
                                    );
                                }
                            }
                            $ID++;
                            $Skipped = true;
                            if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$removeXAxis
                            ) {
                                $bounds = $this->drawText(
                                        $XPos + $OuterTickWidth + $XLabelOffset, $YPos, $Value, ["Angle" => $LabelRotation, "Align" => $LabelAlign]
                                );
                                $txtBox = $XPos + $OuterTickWidth + 2 + ($bounds[1]["X"] - $bounds[0]["X"]);
                                $MaxRight = max($MaxRight, $txtBox);
                                $LastValue = $Value;
                                $Skipped = false;
                            }
                            if ($removeXAxis) {
                                $Skipped = false;
                            }
                            if ($Skipped) {
                                if ($drawXLines && !$removeSkippedAxis) {
                                    $this->drawLine(
                                            $this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $SkippedAxisColor
                                    );
                                }
                                if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$removeXAxis && !$removeSkippedAxis
                                ) {
                                    $this->drawLine(
                                            $XPos + $SkippedOuterTickWidth, $YPos, $XPos - $SkippedInnerTickWidth, $YPos, $SkippedTickColor
                                    );
                                }
                            } else {
                                if ($drawXLines) {
                                    $this->drawLine(
                                            $this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, [
                                        "r" => $gridR,
                                        "g" => $gridG,
                                        "b" => $gridB,
                                        "alpha" => $gridalpha,
                                        "ticks" => $gridTicks
                                            ]
                                    );
                                }
                                if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$removeXAxis) {
                                    $this->drawLine(
                                            $XPos + $OuterTickWidth, $YPos, $XPos - $InnerTickWidth, $YPos, [
                                        "r" => $TickR,
                                        "g" => $TickG,
                                        "b" => $TickB,
                                        "alpha" => $Tickalpha
                                            ]
                                    );
                                }
                            }
                        }
                        if (isset($parameters["Name"]) && !$removeXAxis) {
                            $XPos = $MaxRight + 4;
                            $YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
                            $bounds = $this->drawText(
                                    $XPos, $YPos, $parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 270]
                            );
                            $MaxRight = $bounds[1]["X"];
                            $this->dataSet->data["GraphArea"]["X2"] = $MaxRight + $this->fontSize;
                        }
                        $AxisPos["r"] = $MaxRight + $ScaleSpacing;
                    }
                }
            }
            if ($parameters["Identity"] == AXIS_Y && !$removeYAxis) {
                if ($Pos == SCALE_POS_LEFTRIGHT) {
                    if ($parameters["Position"] == AXIS_POSITION_LEFT) {
                        if ($Floating) {
                            $FloatingOffset = $XMargin;
                            $this->drawLine(
                                    $AxisPos["L"], $this->GraphAreaY1 + $parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 - $parameters["Margin"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                            );
                        } else {
                            $FloatingOffset = 0;
                            $this->drawLine(
                                    $AxisPos["L"], $this->GraphAreaY1, $AxisPos["L"], $this->GraphAreaY2, ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                    $AxisPos["L"], $this->GraphAreaY1 + $parameters["Margin"], $AxisPos["L"], $this->GraphAreaY1 - ($arrowSize * 2), [
                                "FillR" => $AxisR,
                                "FillG" => $AxisG,
                                "FillB" => $AxisB,
                                "Size" => $arrowSize
                                    ]
                            );
                        }
                        $Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $parameters["Margin"] * 2;
                        $step = $Height / $parameters["Rows"];
                        $SubTicksSize = $step / 2;
                        $MinLeft = $AxisPos["L"];
                        $LastY = null;
                        for ($i = 0; $i <= $parameters["Rows"]; $i++) {
                            $YPos = $this->GraphAreaY2 - $parameters["Margin"] - $step * $i;
                            $XPos = $AxisPos["L"];
                            $Value = $this->scaleFormat(
                                    $parameters["ScaleMin"] + $parameters["RowHeight"] * $i, $parameters["Display"], $parameters["Format"], $parameters["Unit"]
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
                            if ($LastY != null && $CycleBackground && ($drawYLines == ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                        $this->GraphAreaX1 + $FloatingOffset, $LastY, $this->GraphAreaX2 - $FloatingOffset, $YPos, $bGColor
                                );
                            }
                            if ($drawYLines == ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                        $this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, [
                                    "r" => $gridR,
                                    "g" => $gridG,
                                    "b" => $gridB,
                                    "alpha" => $gridalpha,
                                    "ticks" => $gridTicks
                                        ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters["Rows"]) {
                                $this->drawLine(
                                        $XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, [
                                    "r" => $SubTickR,
                                    "g" => $SubTickG,
                                    "b" => $SubTickB,
                                    "alpha" => $SubTickalpha
                                        ]
                                );
                            }
                            if (!$removeYAxiValues) {
                                $this->drawLine(
                                        $XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["r" => $TickR, "g" => $TickG, "b" => $TickB, "alpha" => $Tickalpha]
                                );
                                $bounds = $this->drawText(
                                        $XPos - $OuterTickWidth - 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLERIGHT]
                                );
                                $txtLeft = $XPos - $OuterTickWidth - 2 - ($bounds[1]["X"] - $bounds[0]["X"]);
                                $MinLeft = min($MinLeft, $txtLeft);
                            }
                            $LastY = $YPos;
                        }
                        if (isset($parameters["Name"])) {
                            $XPos = $MinLeft - 2;
                            $YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
                            $bounds = $this->drawText(
                                    $XPos, $YPos, $parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 90]
                            );
                            $MinLeft = $bounds[2]["X"];
                            $this->dataSet->data["GraphArea"]["X1"] = $MinLeft;
                        }
                        $AxisPos["L"] = $MinLeft - $ScaleSpacing;
                    } elseif ($parameters["Position"] == AXIS_POSITION_RIGHT) {
                        if ($Floating) {
                            $FloatingOffset = $XMargin;
                            $this->drawLine(
                                    $AxisPos["r"], $this->GraphAreaY1 + $parameters["Margin"], $AxisPos["r"], $this->GraphAreaY2 - $parameters["Margin"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                            );
                        } else {
                            $FloatingOffset = 0;
                            $this->drawLine(
                                    $AxisPos["r"], $this->GraphAreaY1, $AxisPos["r"], $this->GraphAreaY2, ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                    $AxisPos["r"], $this->GraphAreaY1 + $parameters["Margin"], $AxisPos["r"], $this->GraphAreaY1 - ($arrowSize * 2), [
                                "FillR" => $AxisR,
                                "FillG" => $AxisG,
                                "FillB" => $AxisB,
                                "Size" => $arrowSize
                                    ]
                            );
                        }
                        $Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $parameters["Margin"] * 2;
                        $step = $Height / $parameters["Rows"];
                        $SubTicksSize = $step / 2;
                        $MaxLeft = $AxisPos["r"];
                        $LastY = null;
                        for ($i = 0; $i <= $parameters["Rows"]; $i++) {
                            $YPos = $this->GraphAreaY2 - $parameters["Margin"] - $step * $i;
                            $XPos = $AxisPos["r"];
                            $Value = $this->scaleFormat(
                                    $parameters["ScaleMin"] + $parameters["RowHeight"] * $i, $parameters["Display"], $parameters["Format"], $parameters["Unit"]
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
                            if ($LastY != null && $CycleBackground && ($drawYLines == ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                        $this->GraphAreaX1 + $FloatingOffset, $LastY, $this->GraphAreaX2 - $FloatingOffset, $YPos, $bGColor
                                );
                            }
                            if ($drawYLines == ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                        $this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, [
                                    "r" => $gridR,
                                    "g" => $gridG,
                                    "b" => $gridB,
                                    "alpha" => $gridalpha,
                                    "ticks" => $gridTicks
                                        ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters["Rows"]) {
                                $this->drawLine(
                                        $XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, [
                                    "r" => $SubTickR,
                                    "g" => $SubTickG,
                                    "b" => $SubTickB,
                                    "alpha" => $SubTickalpha
                                        ]
                                );
                            }
                            $this->drawLine(
                                    $XPos - $InnerTickWidth, $YPos, $XPos + $OuterTickWidth, $YPos, ["r" => $TickR, "g" => $TickG, "b" => $TickB, "alpha" => $Tickalpha]
                            );
                            $bounds = $this->drawText(
                                    $XPos + $OuterTickWidth + 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLELEFT]
                            );
                            $txtLeft = $XPos + $OuterTickWidth + 2 + ($bounds[1]["X"] - $bounds[0]["X"]);
                            $MaxLeft = max($MaxLeft, $txtLeft);
                            $LastY = $YPos;
                        }
                        if (isset($parameters["Name"])) {
                            $XPos = $MaxLeft + 6;
                            $YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
                            $bounds = $this->drawText(
                                    $XPos, $YPos, $parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE, "Angle" => 270]
                            );
                            $MaxLeft = $bounds[2]["X"];
                            $this->dataSet->data["GraphArea"]["X2"] = $MaxLeft + $this->fontSize;
                        }
                        $AxisPos["r"] = $MaxLeft + $ScaleSpacing;
                    }
                } elseif ($Pos == SCALE_POS_TOPBOTTOM) {
                    if ($parameters["Position"] == AXIS_POSITION_TOP) {
                        if ($Floating) {
                            $FloatingOffset = $XMargin;
                            $this->drawLine(
                                    $this->GraphAreaX1 + $parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 - $parameters["Margin"], $AxisPos["T"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                            );
                        } else {
                            $FloatingOffset = 0;
                            $this->drawLine(
                                    $this->GraphAreaX1, $AxisPos["T"], $this->GraphAreaX2, $AxisPos["T"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                    $this->GraphAreaX2 - $parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 + ($arrowSize * 2), $AxisPos["T"], [
                                "FillR" => $AxisR,
                                "FillG" => $AxisG,
                                "FillB" => $AxisB,
                                "Size" => $arrowSize
                                    ]
                            );
                        }
                        $Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $parameters["Margin"] * 2;
                        $step = $Width / $parameters["Rows"];
                        $SubTicksSize = $step / 2;
                        $MinTop = $AxisPos["T"];
                        $LastX = null;
                        for ($i = 0; $i <= $parameters["Rows"]; $i++) {
                            $XPos = $this->GraphAreaX1 + $parameters["Margin"] + $step * $i;
                            $YPos = $AxisPos["T"];
                            $Value = $this->scaleFormat(
                                    $parameters["ScaleMin"] + $parameters["RowHeight"] * $i, $parameters["Display"], $parameters["Format"], $parameters["Unit"]
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
                            if ($LastX != null && $CycleBackground && ($drawYLines == ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                        $LastX, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $bGColor
                                );
                            }
                            if ($drawYLines == ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                        $XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, [
                                    "r" => $gridR,
                                    "g" => $gridG,
                                    "b" => $gridB,
                                    "alpha" => $gridalpha,
                                    "ticks" => $gridTicks
                                        ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters["Rows"]) {
                                $this->drawLine(
                                        $XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, [
                                    "r" => $SubTickR,
                                    "g" => $SubTickG,
                                    "b" => $SubTickB,
                                    "alpha" => $SubTickalpha
                                        ]
                                );
                            }
                            $this->drawLine(
                                    $XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["r" => $TickR, "g" => $TickG, "b" => $TickB, "alpha" => $Tickalpha]
                            );
                            $bounds = $this->drawText(
                                    $XPos, $YPos - $OuterTickWidth - 2, $Value, ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]
                            );
                            $txtHeight = $YPos - $OuterTickWidth - 2 - ($bounds[1]["Y"] - $bounds[2]["Y"]);
                            $MinTop = min($MinTop, $txtHeight);
                            $LastX = $XPos;
                        }
                        if (isset($parameters["Name"])) {
                            $YPos = $MinTop - 2;
                            $XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
                            $bounds = $this->drawText(
                                    $XPos, $YPos, $parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]
                            );
                            $MinTop = $bounds[2]["Y"];
                            $this->dataSet->data["GraphArea"]["Y1"] = $MinTop;
                        }
                        $AxisPos["T"] = $MinTop - $ScaleSpacing;
                    } elseif ($parameters["Position"] == AXIS_POSITION_BOTTOM) {
                        if ($Floating) {
                            $FloatingOffset = $XMargin;
                            $this->drawLine(
                                    $this->GraphAreaX1 + $parameters["Margin"], $AxisPos["b"], $this->GraphAreaX2 - $parameters["Margin"], $AxisPos["b"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                            );
                        } else {
                            $FloatingOffset = 0;
                            $this->drawLine(
                                    $this->GraphAreaX1, $AxisPos["b"], $this->GraphAreaX2, $AxisPos["b"], ["r" => $AxisR, "g" => $AxisG, "b" => $AxisB, "alpha" => $Axisalpha]
                            );
                        }
                        if ($drawArrows) {
                            $this->drawArrow(
                                    $this->GraphAreaX2 - $parameters["Margin"], $AxisPos["b"], $this->GraphAreaX2 + ($arrowSize * 2), $AxisPos["b"], [
                                "FillR" => $AxisR,
                                "FillG" => $AxisG,
                                "FillB" => $AxisB,
                                "Size" => $arrowSize
                                    ]
                            );
                        }
                        $Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $parameters["Margin"] * 2;
                        $step = $Width / $parameters["Rows"];
                        $SubTicksSize = $step / 2;
                        $MaxBottom = $AxisPos["b"];
                        $LastX = null;
                        for ($i = 0; $i <= $parameters["Rows"]; $i++) {
                            $XPos = $this->GraphAreaX1 + $parameters["Margin"] + $step * $i;
                            $YPos = $AxisPos["b"];
                            $Value = $this->scaleFormat(
                                    $parameters["ScaleMin"] + $parameters["RowHeight"] * $i, $parameters["Display"], $parameters["Format"], $parameters["Unit"]
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
                            if ($LastX != null && $CycleBackground && ($drawYLines == ALL || in_array($axisId, $drawYLines))
                            ) {
                                $this->drawFilledRectangle(
                                        $LastX, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $bGColor
                                );
                            }
                            if ($drawYLines == ALL || in_array($axisId, $drawYLines)) {
                                $this->drawLine(
                                        $XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, [
                                    "r" => $gridR,
                                    "g" => $gridG,
                                    "b" => $gridB,
                                    "alpha" => $gridalpha,
                                    "ticks" => $gridTicks
                                        ]
                                );
                            }
                            if ($drawSubTicks && $i != $parameters["Rows"]) {
                                $this->drawLine(
                                        $XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, [
                                    "r" => $SubTickR,
                                    "g" => $SubTickG,
                                    "b" => $SubTickB,
                                    "alpha" => $SubTickalpha
                                        ]
                                );
                            }
                            $this->drawLine(
                                    $XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["r" => $TickR, "g" => $TickG, "b" => $TickB, "alpha" => $Tickalpha]
                            );
                            $bounds = $this->drawText(
                                    $XPos, $YPos + $OuterTickWidth + 2, $Value, ["Align" => TEXT_ALIGN_TOPMIDDLE]
                            );
                            $txtHeight = $YPos + $OuterTickWidth + 2 + ($bounds[1]["Y"] - $bounds[2]["Y"]);
                            $MaxBottom = max($MaxBottom, $txtHeight);
                            $LastX = $XPos;
                        }
                        if (isset($parameters["Name"])) {
                            $YPos = $MaxBottom + 2;
                            $XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
                            $bounds = $this->drawText(
                                    $XPos, $YPos, $parameters["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]
                            );
                            $MaxBottom = $bounds[0]["Y"];
                            $this->dataSet->data["GraphArea"]["Y2"] = $MaxBottom + $this->fontSize;
                        }
                        $AxisPos["b"] = $MaxBottom + $ScaleSpacing;
                    }
                }
            }
        }
    }

    /**
     * Draw an X threshold
     * @param mixed $Value
     * @param boolean $format
     * @return array|null|integer
     */
    public function drawXThreshold($Value, array $format = []) {
        $r = isset($format["r"]) ? $format["r"] : 255;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 50;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : 6;
        $wide = isset($format["wide"]) ? $format["wide"] : false;
        $wideFactor = isset($format["wideFactor"]) ? $format["wideFactor"] : 5;
        $WriteCaption = isset($format["writeCaption"]) ? $format["writeCaption"] : false;
        $caption = isset($format["caption"]) ? $format["caption"] : null;
        $captionAlign = isset($format["captionAlign"]) ? $format["captionAlign"] : CAPTION_LEFT_TOP;
        $captionOffset = isset($format["captionOffset"]) ? $format["captionOffset"] : 5;
        $captionR = isset($format["captionR"]) ? $format["captionR"] : 255;
        $captionG = isset($format["captionG"]) ? $format["captionG"] : 255;
        $captionB = isset($format["captionB"]) ? $format["captionB"] : 255;
        $captionalpha = isset($format["captionalpha"]) ? $format["captionalpha"] : 100;
        $drawBox = isset($format["DrawBox"]) ? $format["DrawBox"] : true;
        $drawBoxBorder = isset($format["DrawBoxBorder"]) ? $format["DrawBoxBorder"] : false;
        $borderOffset = isset($format["borderOffset"]) ? $format["borderOffset"] : 3;
        $boxRounded = isset($format["BoxRounded"]) ? $format["BoxRounded"] : true;
        $roundedRadius = isset($format["RoundedRadius"]) ? $format["RoundedRadius"] : 3;
        $boxR = isset($format["BoxR"]) ? $format["BoxR"] : 0;
        $boxG = isset($format["BoxG"]) ? $format["BoxG"] : 0;
        $boxB = isset($format["BoxB"]) ? $format["BoxB"] : 0;
        $boxalpha = isset($format["Boxalpha"]) ? $format["Boxalpha"] : 30;
        $boxSurrounding = isset($format["BoxSurrounding"]) ? $format["BoxSurrounding"] : "";
        $boxborderR = isset($format["BoxborderR"]) ? $format["BoxborderR"] : 255;
        $boxborderG = isset($format["BoxborderG"]) ? $format["BoxborderG"] : 255;
        $boxborderB = isset($format["BoxborderB"]) ? $format["BoxborderB"] : 255;
        $boxBorderalpha = isset($format["BoxBorderalpha"]) ? $format["BoxBorderalpha"] : 100;
        $ValueIsLabel = isset($format["ValueIsLabel"]) ? $format["ValueIsLabel"] : false;
        $data = $this->dataSet->getData();
        $AbscissaMargin = $this->getAbscissaMargin($data);
        $XScale = $this->scaleGetXSettings();
        if (is_array($Value)) {
            foreach ($Value as $Key => $ID) {
                $this->drawXThreshold($ID, $format);
            }
            return 0;
        }
        if ($ValueIsLabel) {
            $format["ValueIsLabel"] = false;
            foreach ($data["series"][$data["Abscissa"]]["Data"] as $Key => $serieValue) {
                if ($serieValue == $Value) {
                    $this->drawXThreshold($Key, $format);
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
            $caption = $Value;
            if (isset($data["Abscissa"]) && isset($data["series"][$data["Abscissa"]]["Data"][$Value])
            ) {
                $caption = $data["series"][$data["Abscissa"]]["Data"][$Value];
            }
        }
        if ($data["Orientation"] == Constant::SCALE_POS_LEFTRIGHT) {
            $XStep = (($this->GraphAreaX2 - $this->GraphAreaX1) - $XScale[0] * 2) / $XScale[1];
            $XPos = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value;
            $YPos1 = $this->GraphAreaY1 + $data["YMargin"];
            $YPos2 = $this->GraphAreaY2 - $data["YMargin"];
            if ($XPos >= $this->GraphAreaX1 + $AbscissaMargin && $XPos <= $this->GraphAreaX2 - $AbscissaMargin
            ) {
                $this->drawLine(
                        $XPos, $YPos1, $XPos, $YPos2, [
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
                            $XPos - 1, $YPos1, $XPos - 1, $YPos2, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                    $this->drawLine(
                            $XPos + 1, $YPos1, $XPos + 1, $YPos2, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                }
                if ($WriteCaption) {
                    if ($captionAlign == CAPTION_LEFT_TOP) {
                        $Y = $YPos1 + $captionOffset;
                        $captionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
                    } else {
                        $Y = $YPos2 - $captionOffset;
                        $captionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
                    }
                    $this->drawText($XPos, $Y, $caption, $captionSettings);
                }
                return ["X" => $XPos];
            }
        } elseif ($data["Orientation"] == SCALE_POS_TOPBOTTOM) {
            $XStep = (($this->GraphAreaY2 - $this->GraphAreaY1) - $XScale[0] * 2) / $XScale[1];
            $XPos = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value;
            $YPos1 = $this->GraphAreaX1 + $data["YMargin"];
            $YPos2 = $this->GraphAreaX2 - $data["YMargin"];
            if ($XPos >= $this->GraphAreaY1 + $AbscissaMargin && $XPos <= $this->GraphAreaY2 - $AbscissaMargin
            ) {
                $this->drawLine(
                        $YPos1, $XPos, $YPos2, $XPos, [
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
                            $YPos1, $XPos - 1, $YPos2, $XPos - 1, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                    $this->drawLine(
                            $YPos1, $XPos + 1, $YPos2, $XPos + 1, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                }
                if ($WriteCaption) {
                    if ($captionAlign == CAPTION_LEFT_TOP) {
                        $Y = $YPos1 + $captionOffset;
                        $captionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
                    } else {
                        $Y = $YPos2 - $captionOffset;
                        $captionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
                    }
                    $this->drawText($Y, $XPos, $caption, $captionSettings);
                }
                return ["X" => $XPos];
            }
        }
    }

    /**
     * Draw an X threshold area
     * @param mixed $Value1
     * @param mixed $Value2
     * @param array $format
     * @return array|null
     */
    public function drawXThresholdArea($Value1, $Value2, array $format = []) {
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
        $AreaName = isset($format["AreaName"]) ? $format["AreaName"] : null;
        $NameAngle = isset($format["NameAngle"]) ? $format["NameAngle"] : ZONE_NAME_ANGLE_AUTO;
        $NameR = isset($format["NameR"]) ? $format["NameR"] : 255;
        $NameG = isset($format["NameG"]) ? $format["NameG"] : 255;
        $NameB = isset($format["NameB"]) ? $format["NameB"] : 255;
        $Namealpha = isset($format["Namealpha"]) ? $format["Namealpha"] : 100;
        $DisableShadowOnArea = isset($format["DisableShadowOnArea"]) ? $format["DisableShadowOnArea"] : true;
        $restoreShadow = $this->shadow;
        if ($DisableShadowOnArea && $this->shadow) {
            $this->shadow = false;
        }
        if ($borderalpha > 100) {
            $borderalpha = 100;
        }
        $data = $this->dataSet->getData();
        $XScale = $this->scaleGetXSettings();
        if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $XStep = (($this->GraphAreaX2 - $this->GraphAreaX1) - $XScale[0] * 2) / $XScale[1];
            $XPos1 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value1;
            $XPos2 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value2;
            $YPos1 = $this->GraphAreaY1 + $data["YMargin"];
            $YPos2 = $this->GraphAreaY2 - $data["YMargin"];
            if ($XPos1 < $this->GraphAreaX1 + $XScale[0]) {
                $XPos1 = $this->GraphAreaX1 + $XScale[0];
            }
            if ($XPos1 > $this->GraphAreaX2 - $XScale[0]) {
                $XPos1 = $this->GraphAreaX2 - $XScale[0];
            }
            if ($XPos2 < $this->GraphAreaX1 + $XScale[0]) {
                $XPos2 = $this->GraphAreaX1 + $XScale[0];
            }
            if ($XPos2 > $this->GraphAreaX2 - $XScale[0]) {
                $XPos2 = $this->GraphAreaX2 - $XScale[0];
            }
            $this->drawFilledRectangle(
                    $XPos1, $YPos1, $XPos2, $YPos2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            if ($border) {
                $this->drawLine(
                        $XPos1, $YPos1, $XPos1, $YPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
                $this->drawLine(
                        $XPos2, $YPos1, $XPos2, $YPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
            }
            if ($AreaName != null) {
                $XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
                $YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
                if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
                    $txtPos = $this->getTextBox(
                            $XPos, $YPos, $this->fontName, $this->fontSize, 0, $AreaName
                    );
                    $txtWidth = $txtPos[1]["X"] - $txtPos[0]["X"];
                    $NameAngle = 90;
                    if (abs($XPos2 - $XPos1) > $txtWidth) {
                        $NameAngle = 0;
                    }
                }
                $this->shadow = $restoreShadow;
                $this->drawText(
                        $XPos, $YPos, $AreaName, [
                    "r" => $NameR,
                    "g" => $NameG,
                    "b" => $NameB,
                    "alpha" => $Namealpha,
                    "Angle" => $NameAngle,
                    "Align" => TEXT_ALIGN_MIDDLEMIDDLE
                        ]
                );
                if ($DisableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ["X1" => $XPos1, "X2" => $XPos2];
        } elseif ($data["Orientation"] == SCALE_POS_TOPBOTTOM) {
            $XStep = (($this->GraphAreaY2 - $this->GraphAreaY1) - $XScale[0] * 2) / $XScale[1];
            $XPos1 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value1;
            $XPos2 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value2;
            $YPos1 = $this->GraphAreaX1 + $data["YMargin"];
            $YPos2 = $this->GraphAreaX2 - $data["YMargin"];
            if ($XPos1 < $this->GraphAreaY1 + $XScale[0]) {
                $XPos1 = $this->GraphAreaY1 + $XScale[0];
            }
            if ($XPos1 > $this->GraphAreaY2 - $XScale[0]) {
                $XPos1 = $this->GraphAreaY2 - $XScale[0];
            }
            if ($XPos2 < $this->GraphAreaY1 + $XScale[0]) {
                $XPos2 = $this->GraphAreaY1 + $XScale[0];
            }
            if ($XPos2 > $this->GraphAreaY2 - $XScale[0]) {
                $XPos2 = $this->GraphAreaY2 - $XScale[0];
            }
            $this->drawFilledRectangle(
                    $YPos1, $XPos1, $YPos2, $XPos2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            if ($border) {
                $this->drawLine(
                        $YPos1, $XPos1, $YPos2, $XPos1, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
                $this->drawLine(
                        $YPos1, $XPos2, $YPos2, $XPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
            }
            if ($AreaName != null) {
                $XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
                $YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
                $this->shadow = $restoreShadow;
                $this->drawText(
                        $YPos, $XPos, $AreaName, [
                    "r" => $NameR,
                    "g" => $NameG,
                    "b" => $NameB,
                    "alpha" => $Namealpha,
                    "Angle" => 0,
                    "Align" => TEXT_ALIGN_MIDDLEMIDDLE
                        ]
                );
                if ($DisableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ["X1" => $XPos1, "X2" => $XPos2];
        }
    }

    /**
     * Draw an Y threshold with the computed scale
     * @param mixed $Value
     * @param array $format
     * @return array|int
     */
    public function drawThreshold($Value, array $format = []) {
        $axisId = isset($format["axisId"]) ? $format["axisId"] : 0;
        $r = isset($format["r"]) ? $format["r"] : 255;
        $g = isset($format["g"]) ? $format["g"] : 0;
        $b = isset($format["b"]) ? $format["b"] : 0;
        $alpha = isset($format["alpha"]) ? $format["alpha"] : 50;
        $weight = isset($format["weight"]) ? $format["weight"] : null;
        $ticks = isset($format["ticks"]) ? $format["ticks"] : 6;
        $wide = isset($format["wide"]) ? $format["wide"] : false;
        $wideFactor = isset($format["wideFactor"]) ? $format["wideFactor"] : 5;
        $WriteCaption = isset($format["writeCaption"]) ? $format["writeCaption"] : false;
        $caption = isset($format["caption"]) ? $format["caption"] : null;
        $captionAlign = isset($format["captionAlign"]) ? $format["captionAlign"] : CAPTION_LEFT_TOP;
        $captionOffset = isset($format["captionOffset"]) ? $format["captionOffset"] : 10;
        $captionR = isset($format["captionR"]) ? $format["captionR"] : 255;
        $captionG = isset($format["captionG"]) ? $format["captionG"] : 255;
        $captionB = isset($format["captionB"]) ? $format["captionB"] : 255;
        $captionalpha = isset($format["captionalpha"]) ? $format["captionalpha"] : 100;
        $drawBox = isset($format["DrawBox"]) ? $format["DrawBox"] : true;
        $drawBoxBorder = isset($format["DrawBoxBorder"]) ? $format["DrawBoxBorder"] : false;
        $borderOffset = isset($format["borderOffset"]) ? $format["borderOffset"] : 5;
        $boxRounded = isset($format["BoxRounded"]) ? $format["BoxRounded"] : true;
        $roundedRadius = isset($format["RoundedRadius"]) ? $format["RoundedRadius"] : 3;
        $boxR = isset($format["BoxR"]) ? $format["BoxR"] : 0;
        $boxG = isset($format["BoxG"]) ? $format["BoxG"] : 0;
        $boxB = isset($format["BoxB"]) ? $format["BoxB"] : 0;
        $boxalpha = isset($format["Boxalpha"]) ? $format["Boxalpha"] : 20;
        $boxSurrounding = isset($format["BoxSurrounding"]) ? $format["BoxSurrounding"] : "";
        $boxborderR = isset($format["BoxborderR"]) ? $format["BoxborderR"] : 255;
        $boxborderG = isset($format["BoxborderG"]) ? $format["BoxborderG"] : 255;
        $boxborderB = isset($format["BoxborderB"]) ? $format["BoxborderB"] : 255;
        $boxBorderalpha = isset($format["BoxBorderalpha"]) ? $format["BoxBorderalpha"] : 100;
        $NoMargin = isset($format["NoMargin"]) ? $format["NoMargin"] : false;
        if (is_array($Value)) {
            foreach ($Value as $Key => $ID) {
                $this->drawThreshold($ID, $format);
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
        $AbscissaMargin = $this->getAbscissaMargin($data);
        if ($NoMargin) {
            $AbscissaMargin = 0;
        }
        if (!isset($data["axis"][$axisId])) {
            return -1;
        }
        if ($caption == null) {
            $caption = $Value;
        }
        if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $YPos = $this->scaleComputeY($Value, ["axisId" => $axisId]);
            if ($YPos >= $this->GraphAreaY1 + $data["axis"][$axisId]["Margin"] && $YPos <= $this->GraphAreaY2 - $data["axis"][$axisId]["Margin"]
            ) {
                $X1 = $this->GraphAreaX1 + $AbscissaMargin;
                $X2 = $this->GraphAreaX2 - $AbscissaMargin;
                $this->drawLine(
                        $X1, $YPos, $X2, $YPos, [
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
                            $X1, $YPos - 1, $X2, $YPos - 1, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                    $this->drawLine(
                            $X1, $YPos + 1, $X2, $YPos + 1, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                }
                if ($WriteCaption) {
                    if ($captionAlign == CAPTION_LEFT_TOP) {
                        $X = $X1 + $captionOffset;
                        $captionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
                    } else {
                        $X = $X2 - $captionOffset;
                        $captionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
                    }
                    $this->drawText($X, $YPos, $caption, $captionSettings);
                }
            }
            return ["Y" => $YPos];
        }
        if ($data["Orientation"] == SCALE_POS_TOPBOTTOM) {
            $XPos = $this->scaleComputeY($Value, ["axisId" => $axisId]);
            if ($XPos >= $this->GraphAreaX1 + $data["axis"][$axisId]["Margin"] && $XPos <= $this->GraphAreaX2 - $data["axis"][$axisId]["Margin"]
            ) {
                $Y1 = $this->GraphAreaY1 + $AbscissaMargin;
                $Y2 = $this->GraphAreaY2 - $AbscissaMargin;
                $this->drawLine(
                        $XPos, $Y1, $XPos, $Y2, [
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
                            $XPos - 1, $Y1, $XPos - 1, $Y2, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                    $this->drawLine(
                            $XPos + 1, $Y1, $XPos + 1, $Y2, [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha / $wideFactor,
                        "ticks" => $ticks
                            ]
                    );
                }
                if ($WriteCaption) {
                    if ($captionAlign == CAPTION_LEFT_TOP) {
                        $Y = $Y1 + $captionOffset;
                        $captionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
                    } else {
                        $Y = $Y2 - $captionOffset;
                        $captionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
                    }
                    $captionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
                    $this->drawText($XPos, $Y, $caption, $captionSettings);
                }
            }
            return ["Y" => $XPos];
        }
    }

    /**
     * Draw a threshold with the computed scale
     * @param mixed $Value1
     * @param mixed $Value2
     * @param array $format
     * @return array|int|null
     */
    public function drawThresholdArea($Value1, $Value2, array $format = []) {
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
        $AreaName = isset($format["AreaName"]) ? $format["AreaName"] : null;
        $NameAngle = isset($format["NameAngle"]) ? $format["NameAngle"] : ZONE_NAME_ANGLE_AUTO;
        $NameR = isset($format["NameR"]) ? $format["NameR"] : 255;
        $NameG = isset($format["NameG"]) ? $format["NameG"] : 255;
        $NameB = isset($format["NameB"]) ? $format["NameB"] : 255;
        $Namealpha = isset($format["Namealpha"]) ? $format["Namealpha"] : 100;
        $DisableShadowOnArea = isset($format["DisableShadowOnArea"]) ? $format["DisableShadowOnArea"] : true;
        $NoMargin = isset($format["NoMargin"]) ? $format["NoMargin"] : false;
        if ($Value1 > $Value2) {
            list($Value1, $Value2) = [$Value2, $Value1];
        }
        $restoreShadow = $this->shadow;
        if ($DisableShadowOnArea && $this->shadow) {
            $this->shadow = false;
        }
        if ($borderalpha > 100) {
            $borderalpha = 100;
        }
        $data = $this->dataSet->getData();
        $AbscissaMargin = $this->getAbscissaMargin($data);
        if ($NoMargin) {
            $AbscissaMargin = 0;
        }
        if (!isset($data["axis"][$axisId])) {
            return -1;
        }
        if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $XPos1 = $this->GraphAreaX1 + $AbscissaMargin;
            $XPos2 = $this->GraphAreaX2 - $AbscissaMargin;
            $YPos1 = $this->scaleComputeY($Value1, ["axisId" => $axisId]);
            $YPos2 = $this->scaleComputeY($Value2, ["axisId" => $axisId]);
            if ($YPos1 < $this->GraphAreaY1 + $data["axis"][$axisId]["Margin"]) {
                $YPos1 = $this->GraphAreaY1 + $data["axis"][$axisId]["Margin"];
            }
            if ($YPos1 > $this->GraphAreaY2 - $data["axis"][$axisId]["Margin"]) {
                $YPos1 = $this->GraphAreaY2 - $data["axis"][$axisId]["Margin"];
            }
            if ($YPos2 < $this->GraphAreaY1 + $data["axis"][$axisId]["Margin"]) {
                $YPos2 = $this->GraphAreaY1 + $data["axis"][$axisId]["Margin"];
            }
            if ($YPos2 > $this->GraphAreaY2 - $data["axis"][$axisId]["Margin"]) {
                $YPos2 = $this->GraphAreaY2 - $data["axis"][$axisId]["Margin"];
            }
            $this->drawFilledRectangle(
                    $XPos1, $YPos1, $XPos2, $YPos2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            if ($border) {
                $this->drawLine(
                        $XPos1, $YPos1, $XPos2, $YPos1, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
                $this->drawLine(
                        $XPos1, $YPos2, $XPos2, $YPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
            }
            if ($AreaName != null) {
                $XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
                $YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
                $this->shadow = $restoreShadow;
                $this->drawText(
                        $XPos, $YPos, $AreaName, [
                    "r" => $NameR,
                    "g" => $NameG,
                    "b" => $NameB,
                    "alpha" => $Namealpha,
                    "Angle" => 0,
                    "Align" => TEXT_ALIGN_MIDDLEMIDDLE
                        ]
                );
                if ($DisableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ["Y1" => $YPos1, "Y2" => $YPos2];
        } elseif ($data["Orientation"] == SCALE_POS_TOPBOTTOM) {
            $YPos1 = $this->GraphAreaY1 + $AbscissaMargin;
            $YPos2 = $this->GraphAreaY2 - $AbscissaMargin;
            $XPos1 = $this->scaleComputeY($Value1, ["axisId" => $axisId]);
            $XPos2 = $this->scaleComputeY($Value2, ["axisId" => $axisId]);
            if ($XPos1 < $this->GraphAreaX1 + $data["axis"][$axisId]["Margin"]) {
                $XPos1 = $this->GraphAreaX1 + $data["axis"][$axisId]["Margin"];
            }
            if ($XPos1 > $this->GraphAreaX2 - $data["axis"][$axisId]["Margin"]) {
                $XPos1 = $this->GraphAreaX2 - $data["axis"][$axisId]["Margin"];
            }
            if ($XPos2 < $this->GraphAreaX1 + $data["axis"][$axisId]["Margin"]) {
                $XPos2 = $this->GraphAreaX1 + $data["axis"][$axisId]["Margin"];
            }
            if ($XPos2 > $this->GraphAreaX2 - $data["axis"][$axisId]["Margin"]) {
                $XPos2 = $this->GraphAreaX2 - $data["axis"][$axisId]["Margin"];
            }
            $this->drawFilledRectangle(
                    $XPos1, $YPos1, $XPos2, $YPos2, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            if ($border) {
                $this->drawLine(
                        $XPos1, $YPos1, $XPos1, $YPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
                $this->drawLine(
                        $XPos2, $YPos1, $XPos2, $YPos2, [
                    "r" => $borderR,
                    "g" => $borderG,
                    "b" => $borderB,
                    "alpha" => $borderalpha,
                    "ticks" => $borderTicks
                        ]
                );
            }
            if ($AreaName != null) {
                $XPos = ($YPos2 - $YPos1) / 2 + $YPos1;
                $YPos = ($XPos2 - $XPos1) / 2 + $XPos1;
                if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
                    $txtPos = $this->getTextBox(
                            $XPos, $YPos, $this->fontName, $this->fontSize, 0, $AreaName
                    );
                    $txtWidth = $txtPos[1]["X"] - $txtPos[0]["X"];
                    $NameAngle = 90;
                    if (abs($XPos2 - $XPos1) > $txtWidth) {
                        $NameAngle = 0;
                    }
                }
                $this->shadow = $restoreShadow;
                $this->drawText(
                        $YPos, $XPos, $AreaName, [
                    "r" => $NameR,
                    "g" => $NameG,
                    "b" => $NameB,
                    "alpha" => $Namealpha,
                    "Angle" => $NameAngle,
                    "Align" => TEXT_ALIGN_MIDDLEMIDDLE
                        ]
                );
                if ($DisableShadowOnArea) {
                    $this->shadow = false;
                }
            }
            $this->shadow = $restoreShadow;
            return ["Y1" => $XPos1, "Y2" => $XPos2];
        }
    }

    /**
     * Draw a plot chart
     * @param array $format
     */
    public function drawPlotChart(array $format = []) {
        $PlotSize = isset($format["PlotSize"]) ? $format["PlotSize"] : null;
        $PlotBorder = isset($format["PlotBorder"]) ? $format["PlotBorder"] : false;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : 50;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : 50;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : 50;
        $borderalpha = isset($format["borderalpha"]) ? $format["borderalpha"] : 30;
        $borderSize = isset($format["borderSize"]) ? $format["borderSize"] : 2;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $displayValues = isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
        $displayOffset = isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 4;
        $displayColor = isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
        $displayG = isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
        $displayB = isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
        $recordImageMap = isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
                if (isset($serie["weight"])) {
                    $serieWeight = $serie["weight"] + 2;
                } else {
                    $serieWeight = 2;
                }
                if ($PlotSize != null) {
                    $serieWeight = $PlotSize;
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
                $Shape = $serie["Shape"];
                $mode = $data["axis"][$axisId]["Display"];
                $format = $data["axis"][$axisId]["Format"];
                $Unit = $data["axis"][$axisId]["Unit"];
                if (isset($serie["Description"])) {
                    $serieDescription = $serie["Description"];
                } else {
                    $serieDescription = $serieName;
                }
                $PosArray = $this->scaleComputeY($serie["Data"], ["axisId" => $serie["axis"]]);
                $this->dataSet->data["series"][$serieName]["XOffset"] = 0;
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    if ($picture != null) {
                        $picOffset = $picHeight / 2;
                        $serieWeight = 0;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Y) {
                        if ($displayValues) {
                            $this->drawText(
                                    $X, $Y - $displayOffset - $serieWeight - $borderSize - $picOffset, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($Y != VOID) {
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "CIRCLE", floor($X) . "," . floor($Y) . "," . $serieWeight, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat(
                                                $serie["Data"][$Key], $mode, $format, $Unit
                                        )
                                );
                            }
                            if ($picture != null) {
                                $this->drawFromPicture(
                                        $picType, $picture, $X - $picWidth / 2, $Y - $picHeight / 2
                                );
                            } else {
                                $this->drawShape(
                                        $X, $Y, $Shape, $serieWeight, $PlotBorder, $borderSize, $r, $g, $b, $alpha, $borderR, $borderG, $borderB, $borderalpha
                                );
                            }
                        }
                        $X = $X + $XStep;
                    }
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    if ($picture != null) {
                        $picOffset = $picWidth / 2;
                        $serieWeight = 0;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $X) {
                        if ($displayValues) {
                            $this->drawText(
                                    $X + $displayOffset + $serieWeight + $borderSize + $picOffset, $Y, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($X != VOID) {
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "CIRCLE", floor($X) . "," . floor($Y) . "," . $serieWeight, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                );
                            }
                            if ($picture != null) {
                                $this->drawFromPicture(
                                        $picType, $picture, $X - $picWidth / 2, $Y - $picHeight / 2
                                );
                            } else {
                                $this->drawShape(
                                        $X, $Y, $Shape, $serieWeight, $PlotBorder, $borderSize, $r, $g, $b, $alpha, $borderR, $borderG, $borderB, $borderalpha
                                );
                            }
                        }
                        $Y = $Y + $YStep;
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
        $breakVoid = isset($format["BreakVoid"]) ? $format["BreakVoid"] : true;
        $VoidTicks = isset($format["VoidTicks"]) ? $format["VoidTicks"] : 4;
        $breakR = isset($format["BreakR"]) ? $format["BreakR"] : null; // 234
        $breakG = isset($format["BreakG"]) ? $format["BreakG"] : null; // 55
        $breakB = isset($format["BreakB"]) ? $format["BreakB"] : null; // 26
        $displayValues = isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
        $displayOffset = isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
        $displayColor = isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
        $displayG = isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
        $displayB = isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
        $recordImageMap = isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
        $ImageMapPlotSize = isset($format["ImageMapPlotSize"]) ? $format["ImageMapPlotSize"] : 5;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
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
                        "ticks" => $VoidTicks
                    ];
                } else {
                    $breakSettings = [
                        "r" => $breakR,
                        "g" => $breakG,
                        "b" => $breakB,
                        "alpha" => $alpha,
                        "ticks" => $VoidTicks,
                        "weight" => $weight
                    ];
                }
                if ($displayColor == DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["Display"];
                $format = $data["axis"][$axisId]["Format"];
                $Unit = $data["axis"][$axisId]["Unit"];
                if (isset($serie["Description"])) {
                    $serieDescription = $serie["Description"];
                } else {
                    $serieDescription = $serieName;
                }
                $PosArray = $this->scaleComputeY(
                        $serie["Data"], ["axisId" => $serie["axis"]]
                );
                $this->dataSet->data["series"][$serieName]["XOffset"] = 0;
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $WayPoints = [];
                    $force = $XStep / 5;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $LastX = 1;
                    $LastY = 1;
                    foreach ($PosArray as $Key => $Y) {
                        if ($displayValues) {
                            $this->drawText(
                                    $X, $Y - $displayOffset, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($recordImageMap && $Y != VOID) {
                            $this->addToImageMap(
                                    "CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                            );
                        }
                        if ($Y == VOID && $LastY != null) {
                            $this->drawSpline(
                                    $WayPoints, [
                                "Force" => $force,
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                                    ]
                            );
                            $WayPoints = [];
                        }
                        if ($Y != VOID && $LastY == null && $LastGoodY != null && !$breakVoid) {
                            $this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $breakSettings);
                        }
                        if ($Y != VOID) {
                            $WayPoints[] = [$X, $Y];
                        }
                        if ($Y != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($Y == VOID) {
                            $Y = null;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $X = $X + $XStep;
                    }
                    $this->drawSpline(
                            $WayPoints, [
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
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $WayPoints = [];
                    $force = $YStep / 5;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $LastX = 1;
                    $LastY = 1;
                    foreach ($PosArray as $Key => $X) {
                        if ($displayValues) {
                            $this->drawText(
                                    $X + $displayOffset, $Y, $this->scaleFormat(
                                            $serie["Data"][$Key], $mode, $format, $Unit
                                    ), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($recordImageMap && $X != VOID) {
                            $this->addToImageMap(
                                    "CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                            );
                        }
                        if ($X == VOID && $LastX != null) {
                            $this->drawSpline(
                                    $WayPoints, [
                                "Force" => $force,
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                                    ]
                            );
                            $WayPoints = [];
                        }
                        if ($X != VOID && $LastX == null && $LastGoodX != null && !$breakVoid) {
                            $this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $breakSettings);
                        }
                        if ($X != VOID) {
                            $WayPoints[] = [$X, $Y];
                        }
                        if ($X != VOID) {
                            $LastGoodX = $X;
                            $LastGoodY = $Y;
                        }
                        if ($X == VOID) {
                            $X = null;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $Y = $Y + $YStep;
                    }
                    $this->drawSpline(
                            $WayPoints, [
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
        $displayValues = isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
        $displayOffset = isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
        $displayColor = isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
        $displayG = isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
        $displayB = isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
        $AroundZero = isset($format["AroundZero"]) ? $format["AroundZero"] : true;
        $Threshold = isset($format["Threshold"]) ? $format["Threshold"] : null;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
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
                $mode = $data["axis"][$axisId]["Display"];
                $format = $data["axis"][$axisId]["Format"];
                $Unit = $data["axis"][$axisId]["Unit"];
                $PosArray = $this->scaleComputeY(
                        $serie["Data"], ["axisId" => $serie["axis"]]
                );
                if ($AroundZero) {
                    $YZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                }
                if ($Threshold != null) {
                    foreach ($Threshold as $Key => $Params) {
                        $Threshold[$Key]["minX"] = $this->scaleComputeY(
                                $Params["min"], ["axisId" => $serie["axis"]]
                        );
                        $Threshold[$Key]["maxX"] = $this->scaleComputeY(
                                $Params["max"], ["axisId" => $serie["axis"]]
                        );
                    }
                }
                $this->dataSet->data["series"][$serieName]["XOffset"] = 0;
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $WayPoints = [];
                    $force = $XStep / 5;
                    if (!$AroundZero) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero < $this->GraphAreaY1 + 1) {
                        $YZero = $this->GraphAreaY1 + 1;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Y) {
                        if ($displayValues) {
                            $this->drawText(
                                    $X, $Y - $displayOffset, $this->scaleFormat(
                                            $serie["Data"][$Key], $mode, $format, $Unit
                                    ), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($Y == VOID) {
                            $Area = $this->drawSpline(
                                    $WayPoints, ["Force" => $force, "PathOnly" => true]
                            );
                            if (count($Area)) {
                                foreach ($Area as $key => $points) {
                                    $Corners = [];
                                    $Corners[] = $Area[$key][0]["X"];
                                    $Corners[] = $YZero;
                                    foreach ($points as $subKey => $Point) {
                                        if ($subKey == count($points) - 1) {
                                            $Corners[] = $Point["X"] - 1;
                                        } else {
                                            $Corners[] = $Point["X"];
                                        }
                                        $Corners[] = $Point["Y"] + 1;
                                    }
                                    $Corners[] = $points[$subKey]["X"] - 1;
                                    $Corners[] = $YZero;
                                    $this->drawPolygonChart(
                                            $Corners, [
                                        "r" => $r,
                                        "g" => $g,
                                        "b" => $b,
                                        "alpha" => $alpha / 2,
                                        "noBorder" => true,
                                        "Threshold" => $Threshold
                                            ]
                                    );
                                }
                                $this->drawSpline(
                                        $WayPoints, [
                                    "Force" => $force,
                                    "r" => $r,
                                    "g" => $g,
                                    "b" => $b,
                                    "alpha" => $alpha,
                                    "ticks" => $ticks
                                        ]
                                );
                            }
                            $WayPoints = [];
                        } else {
                            $WayPoints[] = [$X, $Y - .5]; /* -.5 for AA visual fix */
                        }
                        $X = $X + $XStep;
                    }
                    $Area = $this->drawSpline($WayPoints, ["Force" => $force, "PathOnly" => true]);
                    if (count($Area)) {
                        foreach ($Area as $key => $points) {
                            $Corners = [];
                            $Corners[] = $Area[$key][0]["X"];
                            $Corners[] = $YZero;
                            foreach ($points as $subKey => $Point) {
                                if ($subKey == count($points) - 1) {
                                    $Corners[] = $Point["X"] - 1;
                                } else {
                                    $Corners[] = $Point["X"];
                                }
                                $Corners[] = $Point["Y"] + 1;
                            }
                            $Corners[] = $points[$subKey]["X"] - 1;
                            $Corners[] = $YZero;
                            $this->drawPolygonChart(
                                    $Corners, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha / 2,
                                "noBorder" => true,
                                "Threshold" => $Threshold
                                    ]
                            );
                        }
                        $this->drawSpline(
                                $WayPoints, [
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
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $WayPoints = [];
                    $force = $YStep / 5;
                    if (!$AroundZero) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $X) {
                        if ($displayValues) {
                            $this->drawText(
                                    $X + $displayOffset, $Y, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($X == VOID) {
                            $Area = $this->drawSpline(
                                    $WayPoints, ["Force" => $force, "PathOnly" => true]
                            );
                            if (count($Area)) {
                                foreach ($Area as $key => $points) {
                                    $Corners = [];
                                    $Corners[] = $YZero;
                                    $Corners[] = $Area[$key][0]["Y"];
                                    foreach ($points as $subKey => $Point) {
                                        if ($subKey == count($points) - 1) {
                                            $Corners[] = $Point["X"] - 1;
                                        } else {
                                            $Corners[] = $Point["X"];
                                        }
                                        $Corners[] = $Point["Y"];
                                    }
                                    $Corners[] = $YZero;
                                    $Corners[] = $points[$subKey]["Y"] - 1;
                                    $this->drawPolygonChart(
                                            $Corners, [
                                        "r" => $r,
                                        "g" => $g,
                                        "b" => $b,
                                        "alpha" => $alpha / 2,
                                        "noBorder" => true,
                                        "Threshold" => $Threshold
                                            ]
                                    );
                                }
                                $this->drawSpline(
                                        $WayPoints, [
                                    "Force" => $force,
                                    "r" => $r,
                                    "g" => $g,
                                    "b" => $b,
                                    "alpha" => $alpha,
                                    "ticks" => $ticks
                                        ]
                                );
                            }
                            $WayPoints = [];
                        } else {
                            $WayPoints[] = [$X, $Y];
                        }
                        $Y = $Y + $YStep;
                    }
                    $Area = $this->drawSpline(
                            $WayPoints, ["Force" => $force, "PathOnly" => true]
                    );
                    if (count($Area)) {
                        foreach ($Area as $key => $points) {
                            $Corners = [];
                            $Corners[] = $YZero;
                            $Corners[] = $Area[$key][0]["Y"];
                            foreach ($points as $subKey => $Point) {
                                if ($subKey == count($points) - 1) {
                                    $Corners[] = $Point["X"] - 1;
                                } else {
                                    $Corners[] = $Point["X"];
                                }
                                $Corners[] = $Point["Y"];
                            }
                            $Corners[] = $YZero;
                            $Corners[] = $points[$subKey]["Y"] - 1;
                            $this->drawPolygonChart(
                                    $Corners, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha / 2,
                                "noBorder" => true,
                                "Threshold" => $Threshold
                                    ]
                            );
                        }
                        $this->drawSpline(
                                $WayPoints, [
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
        $breakVoid = isset($format["BreakVoid"]) ? $format["BreakVoid"] : true;
        $VoidTicks = isset($format["VoidTicks"]) ? $format["VoidTicks"] : 4;
        $breakR = isset($format["BreakR"]) ? $format["BreakR"] : null;
        $breakG = isset($format["BreakG"]) ? $format["BreakG"] : null;
        $breakB = isset($format["BreakB"]) ? $format["BreakB"] : null;
        $displayValues = isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
        $displayOffset = isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
        $displayColor = isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
        $displayG = isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
        $displayB = isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
        $recordImageMap = isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
        $ImageMapPlotSize = isset($format["ImageMapPlotSize"]) ? $format["ImageMapPlotSize"] : 5;
        $forceColor = isset($format["ForceColor"]) ? $format["ForceColor"] : false;
        $forceR = isset($format["ForceR"]) ? $format["ForceR"] : 0;
        $forceG = isset($format["ForceG"]) ? $format["ForceG"] : 0;
        $forceB = isset($format["ForceB"]) ? $format["ForceB"] : 0;
        $forcealpha = isset($format["Forcealpha"]) ? $format["Forcealpha"] : 100;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
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
                        "ticks" => $VoidTicks,
                        "weight" => $weight
                    ];
                } else {
                    $breakSettings = [
                        "r" => $breakR,
                        "g" => $breakG,
                        "b" => $breakB,
                        "alpha" => $alpha,
                        "ticks" => $VoidTicks,
                        "weight" => $weight
                    ];
                }
                if ($displayColor == DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["Display"];
                $format = $data["axis"][$axisId]["Format"];
                $Unit = $data["axis"][$axisId]["Unit"];
                if (isset($serie["Description"])) {
                    $serieDescription = $serie["Description"];
                } else {
                    $serieDescription = $serieName;
                }
                $PosArray = $this->scaleComputeY(
                        $serie["Data"], ["axisId" => $serie["axis"]]
                );
                $this->dataSet->data["series"][$serieName]["XOffset"] = 0;
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    foreach ($PosArray as $Key => $Y) {
                        if ($displayValues && $serie["Data"][$Key] != VOID) {
                            if ($serie["Data"][$Key] > 0) {
                                $align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $Offset = $displayOffset;
                            } else {
                                $align = TEXT_ALIGN_TOPMIDDLE;
                                $Offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $X, $Y - $Offset - $weight, $this->scaleFormat(
                                            $serie["Data"][$Key], $mode, $format, $Unit
                                    ), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => $align
                                    ]
                            );
                        }
                        if ($recordImageMap && $Y != VOID) {
                            $this->addToImageMap(
                                    "CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                            );
                        }
                        if ($Y != VOID && $LastX != null && $LastY != null) {
                            $this->drawLine(
                                    $LastX, $LastY, $X, $Y, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                                    ]
                            );
                        }
                        if ($Y != VOID && $LastY == null && $LastGoodY != null && !$breakVoid) {
                            $this->drawLine(
                                    $LastGoodX, $LastGoodY, $X, $Y, $breakSettings
                            );
                            $LastGoodY = null;
                        }
                        if ($Y != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($Y == VOID) {
                            $Y = null;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $X = $X + $XStep;
                    }
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    foreach ($PosArray as $Key => $X) {
                        if ($displayValues && $serie["Data"][$Key] != VOID) {
                            $this->drawText(
                                    $X + $displayOffset + $weight, $Y, $this->scaleFormat(
                                            $serie["Data"][$Key], $mode, $format, $Unit
                                    ), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => TEXT_ALIGN_BOTTOMMIDDLE
                                    ]
                            );
                        }
                        if ($recordImageMap && $X != VOID) {
                            $this->addToImageMap(
                                    "CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                            );
                        }
                        if ($X != VOID && $LastX != null && $LastY != null) {
                            $this->drawLine(
                                    $LastX, $LastY, $X, $Y, [
                                "r" => $r,
                                "g" => $g,
                                "b" => $b,
                                "alpha" => $alpha,
                                "ticks" => $ticks,
                                "weight" => $weight
                                    ]
                            );
                        }
                        if ($X != VOID && $LastX == null && $LastGoodY != null && !$breakVoid) {
                            $this->drawLine(
                                    $LastGoodX, $LastGoodY, $X, $Y, $breakSettings
                            );
                            $LastGoodY = null;
                        }
                        if ($X != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($X == VOID) {
                            $X = null;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $Y = $Y + $YStep;
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
        $LineR = isset($format["LineR"]) ? $format["LineR"] : 150;
        $LineG = isset($format["LineG"]) ? $format["LineG"] : 150;
        $LineB = isset($format["LineB"]) ? $format["LineB"] : 150;
        $Linealpha = isset($format["Linealpha"]) ? $format["Linealpha"] : 50;
        $LineTicks = isset($format["LineTicks"]) ? $format["LineTicks"] : 1;
        $AreaR = isset($format["AreaR"]) ? $format["AreaR"] : 150;
        $AreaG = isset($format["AreaG"]) ? $format["AreaG"] : 150;
        $AreaB = isset($format["AreaB"]) ? $format["AreaB"] : 150;
        $Areaalpha = isset($format["Areaalpha"]) ? $format["Areaalpha"] : 5;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        if (!isset($data["series"][$serieA]["Data"]) || !isset($data["series"][$serieB]["Data"])
        ) {
            return 0;
        }
        $serieAData = $data["series"][$serieA]["Data"];
        $serieBData = $data["series"][$serieB]["Data"];
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        $mode = $data["axis"][$axisId]["Display"];
        $format = $data["axis"][$axisId]["Format"];
        $PosArrayA = $this->scaleComputeY($serieAData, ["axisId" => $axisId]);
        $PosArrayB = $this->scaleComputeY($serieBData, ["axisId" => $axisId]);
        if (count($PosArrayA) != count($PosArrayB)) {
            return 0;
        }
        if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            if ($XDivs == 0) {
                $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
            } else {
                $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
            }
            $X = $this->GraphAreaX1 + $XMargin;
            $LastX = null;
            $LastY = null;
            $LastY1 = null;
            $LastY2 = null;
            $boundsA = [];
            $boundsB = [];
            foreach ($PosArrayA as $Key => $Y1) {
                $Y2 = $PosArrayB[$Key];
                $boundsA[] = $X;
                $boundsA[] = $Y1;
                $boundsB[] = $X;
                $boundsB[] = $Y2;
                $LastX = $X;
                $LastY1 = $Y1;
                $LastY2 = $Y2;
                $X = $X + $XStep;
            }
            $bounds = array_merge($boundsA, $this->reversePlots($boundsB));
            $this->drawPolygonChart(
                    $bounds, [
                "r" => $AreaR,
                "g" => $AreaG,
                "b" => $AreaB,
                "alpha" => $Areaalpha
                    ]
            );
            for ($i = 0; $i <= count($boundsA) - 4; $i = $i + 2) {
                $this->drawLine(
                        $boundsA[$i], $boundsA[$i + 1], $boundsA[$i + 2], $boundsA[$i + 3], [
                    "r" => $LineR,
                    "g" => $LineG,
                    "b" => $LineB,
                    "alpha" => $Linealpha,
                    "ticks" => $LineTicks
                        ]
                );
                $this->drawLine(
                        $boundsB[$i], $boundsB[$i + 1], $boundsB[$i + 2], $boundsB[$i + 3], [
                    "r" => $LineR,
                    "g" => $LineG,
                    "b" => $LineB,
                    "alpha" => $Linealpha,
                    "ticks" => $LineTicks
                        ]
                );
            }
        } else {
            if ($XDivs == 0) {
                $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
            } else {
                $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
            }
            $Y = $this->GraphAreaY1 + $XMargin;
            $LastX = null;
            $LastY = null;
            $LastX1 = null;
            $LastX2 = null;
            $boundsA = [];
            $boundsB = [];
            foreach ($PosArrayA as $Key => $X1) {
                $X2 = $PosArrayB[$Key];
                $boundsA[] = $X1;
                $boundsA[] = $Y;
                $boundsB[] = $X2;
                $boundsB[] = $Y;
                $LastY = $Y;
                $LastX1 = $X1;
                $LastX2 = $X2;
                $Y = $Y + $YStep;
            }
            $bounds = array_merge($boundsA, $this->reversePlots($boundsB));
            $this->drawPolygonChart(
                    $bounds, ["r" => $AreaR, "g" => $AreaG, "b" => $AreaB, "alpha" => $Areaalpha]
            );
            for ($i = 0; $i <= count($boundsA) - 4; $i = $i + 2) {
                $this->drawLine(
                        $boundsA[$i], $boundsA[$i + 1], $boundsA[$i + 2], $boundsA[$i + 3], [
                    "r" => $LineR,
                    "g" => $LineG,
                    "b" => $LineB,
                    "alpha" => $Linealpha,
                    "ticks" => $LineTicks
                        ]
                );
                $this->drawLine(
                        $boundsB[$i], $boundsB[$i + 1], $boundsB[$i + 2], $boundsB[$i + 3], [
                    "r" => $LineR,
                    "g" => $LineG,
                    "b" => $LineB,
                    "alpha" => $Linealpha,
                    "ticks" => $LineTicks
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
        $breakVoid = isset($format["BreakVoid"]) ? $format["BreakVoid"] : false;
        $reCenter = isset($format["ReCenter"]) ? $format["ReCenter"] : true;
        $VoidTicks = isset($format["VoidTicks"]) ? $format["VoidTicks"] : 4;
        $breakR = isset($format["BreakR"]) ? $format["BreakR"] : null;
        $breakG = isset($format["BreakG"]) ? $format["BreakG"] : null;
        $breakB = isset($format["BreakB"]) ? $format["BreakB"] : null;
        $displayValues = isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
        $displayOffset = isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
        $displayColor = isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
        $displayG = isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
        $displayB = isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
        $recordImageMap = isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
        $ImageMapPlotSize = isset($format["ImageMapPlotSize"]) ? $format["ImageMapPlotSize"] : 5;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                $weight = $serie["weight"];
                if (isset($serie["Description"])) {
                    $serieDescription = $serie["Description"];
                } else {
                    $serieDescription = $serieName;
                }
                if ($breakR == null) {
                    $breakSettings = [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha,
                        "ticks" => $VoidTicks,
                        "weight" => $weight
                    ];
                } else {
                    $breakSettings = [
                        "r" => $breakR,
                        "g" => $breakG,
                        "b" => $breakB,
                        "alpha" => $alpha,
                        "ticks" => $VoidTicks,
                        "weight" => $weight
                    ];
                }
                if ($displayColor == DISPLAY_AUTO) {
                    $displayR = $r;
                    $displayG = $g;
                    $displayB = $b;
                }
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["Display"];
                $format = $data["axis"][$axisId]["Format"];
                $Unit = $data["axis"][$axisId]["Unit"];
                $Color = [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "ticks" => $ticks,
                    "weight" => $weight
                ];
                $PosArray = $this->scaleComputeY(
                        $serie["Data"], ["axisId" => $serie["axis"]]
                );
                $this->dataSet->data["series"][$serieName]["XOffset"] = 0;
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $Init = false;
                    foreach ($PosArray as $Key => $Y) {
                        if ($displayValues && $serie["Data"][$Key] != VOID) {
                            if ($Y <= $LastY) {
                                $align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $Offset = $displayOffset;
                            } else {
                                $align = TEXT_ALIGN_TOPMIDDLE;
                                $Offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $X, $Y - $Offset - $weight, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit), ["r" => $displayR, "g" => $displayG, "b" => $displayB, "Align" => $align]
                            );
                        }
                        if ($Y != VOID && $LastX != null && $LastY != null) {
                            $this->drawLine($LastX, $LastY, $X, $LastY, $Color);
                            $this->drawLine($X, $LastY, $X, $Y, $Color);
                            if ($reCenter && $X + $XStep < $this->GraphAreaX2 - $XMargin) {
                                $this->drawLine($X, $Y, $X + $XStep, $Y, $Color);
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    '%s,%s,%s,%s', floor($X - $ImageMapPlotSize), floor($Y - $ImageMapPlotSize), floor($X + $XStep + $ImageMapPlotSize), floor($Y + $ImageMapPlotSize)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                    );
                                }
                            } else {
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    '%s,%s,%s,%s', floor($LastX - $ImageMapPlotSize), floor($LastY - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($LastY + $ImageMapPlotSize)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                    );
                                }
                            }
                        }
                        if ($Y != VOID && $LastY == null && $LastGoodY != null && !$breakVoid) {
                            if ($reCenter) {
                                $this->drawLine($LastGoodX + $XStep, $LastGoodY, $X, $LastGoodY, $breakSettings);
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    '%s,%s,%s,%s', floor($LastGoodX + $XStep - $ImageMapPlotSize), floor($LastGoodY - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($LastGoodY + $ImageMapPlotSize)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                    );
                                }
                            } else {
                                $this->drawLine($LastGoodX, $LastGoodY, $X, $LastGoodY, $breakSettings);
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    '%s,%s,%s,%s', floor($LastGoodX - $ImageMapPlotSize), floor($LastGoodY - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($LastGoodY + $ImageMapPlotSize)
                                            ), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                    );
                                }
                            }
                            $this->drawLine($X, $LastGoodY, $X, $Y, $breakSettings);
                            $LastGoodY = null;
                        } elseif (!$breakVoid && $LastGoodY == null && $Y != VOID) {
                            $this->drawLine($this->GraphAreaX1 + $XMargin, $Y, $X, $Y, $breakSettings);
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($this->GraphAreaX1 + $XMargin - $ImageMapPlotSize), floor($Y - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($Y + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                );
                            }
                        }
                        if ($Y != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($Y == VOID) {
                            $Y = null;
                        }
                        if (!$Init && $reCenter) {
                            $X = $X - $XStep / 2;
                            $Init = true;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        if ($LastX < $this->GraphAreaX1 + $XMargin) {
                            $LastX = $this->GraphAreaX1 + $XMargin;
                        }
                        $X = $X + $XStep;
                    }
                    if ($reCenter) {
                        $this->drawLine($LastX, $LastY, $this->GraphAreaX2 - $XMargin, $LastY, $Color);
                        if ($recordImageMap) {
                            $this->addToImageMap(
                                    "RECT", sprintf(
                                            '%s,%s,%s,%s', floor($LastX - $ImageMapPlotSize), floor($LastY - $ImageMapPlotSize), floor($this->GraphAreaX2 - $XMargin + $ImageMapPlotSize), floor($LastY + $ImageMapPlotSize)
                                    ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                            );
                        }
                    }
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $Init = false;
                    foreach ($PosArray as $Key => $X) {
                        if ($displayValues && $serie["Data"][$Key] != VOID) {
                            if ($X >= $LastX) {
                                $align = TEXT_ALIGN_MIDDLELEFT;
                                $Offset = $displayOffset;
                            } else {
                                $align = TEXT_ALIGN_MIDDLERIGHT;
                                $Offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $X + $Offset + $weight, $Y, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit), [
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => $align
                                    ]
                            );
                        }
                        if ($X != VOID && $LastX != null && $LastY != null) {
                            $this->drawLine($LastX, $LastY, $LastX, $Y, $Color);
                            $this->drawLine($LastX, $Y, $X, $Y, $Color);
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($LastX - $ImageMapPlotSize), floor($LastY - $ImageMapPlotSize), floor($LastX + $XStep + $ImageMapPlotSize), floor($Y + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                );
                            }
                        }
                        if ($X != VOID && $LastX == null && $LastGoodY != null && !$breakVoid) {
                            $this->drawLine(
                                    $LastGoodX, $LastGoodY, $LastGoodX, $LastGoodY + $YStep, $Color
                            );
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($LastGoodX - $ImageMapPlotSize), floor($LastGoodY - $ImageMapPlotSize), floor($LastGoodX + $ImageMapPlotSize), floor($LastGoodY + $YStep + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                );
                            }
                            $this->drawLine(
                                    $LastGoodX, $LastGoodY + $YStep, $LastGoodX, $Y, $breakSettings
                            );
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($LastGoodX - $ImageMapPlotSize), floor($LastGoodY + $YStep - $ImageMapPlotSize), floor($LastGoodX + $ImageMapPlotSize), floor($YStep + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                );
                            }
                            $this->drawLine($LastGoodX, $Y, $X, $Y, $breakSettings);
                            $LastGoodY = null;
                        } elseif ($X != VOID && $LastGoodY == null && !$breakVoid) {
                            $this->drawLine($X, $this->GraphAreaY1 + $XMargin, $X, $Y, $breakSettings);
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                '%s,%s,%s,%s', floor($X - $ImageMapPlotSize), floor($this->GraphAreaY1 + $XMargin - $ImageMapPlotSize), floor($X + $ImageMapPlotSize), floor($Y + $ImageMapPlotSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                );
                            }
                        }
                        if ($X != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($X == VOID) {
                            $X = null;
                        }
                        if (!$Init && $reCenter) {
                            $Y = $Y - $YStep / 2;
                            $Init = true;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        if ($LastY < $this->GraphAreaY1 + $XMargin) {
                            $LastY = $this->GraphAreaY1 + $XMargin;
                        }
                        $Y = $Y + $YStep;
                    }
                    if ($reCenter) {
                        $this->drawLine($LastX, $LastY, $LastX, $this->GraphAreaY2 - $XMargin, $Color);
                        if ($recordImageMap) {
                            $this->addToImageMap(
                                    "RECT", sprintf(
                                            '%s,%s,%s,%s', floor($LastX - $ImageMapPlotSize), floor($LastY - $ImageMapPlotSize), floor($LastX + $ImageMapPlotSize), floor($this->GraphAreaY2 - $XMargin + $ImageMapPlotSize)
                                    ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
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
        $reCenter = isset($format["ReCenter"]) ? $format["ReCenter"] : true;
        $displayValues = isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
        $displayOffset = isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
        $displayColor = isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
        $forceTransparency = isset($format["ForceTransparency"]) ? $format["ForceTransparency"] : null;
        $displayR = isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
        $displayG = isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
        $displayB = isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
        $AroundZero = isset($format["AroundZero"]) ? $format["AroundZero"] : true;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
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
                $format = $data["axis"][$axisId]["Format"];
                $Color = ["r" => $r, "g" => $g, "b" => $b];
                if ($forceTransparency != null) {
                    $Color["alpha"] = $forceTransparency;
                } else {
                    $Color["alpha"] = $alpha;
                }
                $PosArray = $this->scaleComputeY($serie["Data"], ["axisId" => $serie["axis"]]);
                $YZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                $this->dataSet->data["series"][$serieName]["XOffset"] = 0;
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero < $this->GraphAreaY1 + 1) {
                        $YZero = $this->GraphAreaY1 + 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!$AroundZero) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $points = [];
                    $Init = false;
                    foreach ($PosArray as $Key => $Y) {
                        if ($Y == VOID && $LastX != null && $LastY != null && count($points)) {
                            $points[] = $LastX;
                            $points[] = $LastY;
                            $points[] = $X;
                            $points[] = $LastY;
                            $points[] = $X;
                            $points[] = $YZero;
                            $this->drawPolygon($points, $Color);
                            $points = [];
                        }
                        if ($Y != VOID && $LastX != null && $LastY != null) {
                            if (count($points)) {
                                $points[] = $LastX;
                                $points[] = $YZero;
                            }
                            $points[] = $LastX;
                            $points[] = $LastY;
                            $points[] = $X;
                            $points[] = $LastY;
                            $points[] = $X;
                            $points[] = $Y;
                        }
                        if ($Y != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($Y == VOID) {
                            $Y = null;
                        }
                        if (!$Init && $reCenter) {
                            $X = $X - $XStep / 2;
                            $Init = true;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        if ($LastX < $this->GraphAreaX1 + $XMargin) {
                            $LastX = $this->GraphAreaX1 + $XMargin;
                        }
                        $X = $X + $XStep;
                    }
                    if ($reCenter) {
                        $points[] = $LastX + $XStep / 2;
                        $points[] = $LastY;
                        $points[] = $LastX + $XStep / 2;
                        $points[] = $YZero;
                    } else {
                        $points[] = $LastX;
                        $points[] = $YZero;
                    }
                    $this->drawPolygon($points, $Color);
                } else {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $LastGoodY = null;
                    $LastGoodX = null;
                    $points = [];
                    foreach ($PosArray as $Key => $X) {
                        if ($X == VOID && $LastX != null && $LastY != null && count($points)) {
                            $points[] = $LastX;
                            $points[] = $LastY;
                            $points[] = $LastX;
                            $points[] = $Y;
                            $points[] = $YZero;
                            $points[] = $Y;
                            $this->drawPolygon($points, $Color);
                            $points = [];
                        }
                        if ($X != VOID && $LastX != null && $LastY != null) {
                            if (count($points)) {
                                $points[] = $YZero;
                                $points[] = $LastY;
                            }
                            $points[] = $LastX;
                            $points[] = $LastY;
                            $points[] = $LastX;
                            $points[] = $Y;
                            $points[] = $X;
                            $points[] = $Y;
                        }
                        if ($X != VOID) {
                            $LastGoodY = $Y;
                            $LastGoodX = $X;
                        }
                        if ($X == VOID) {
                            $X = null;
                        }
                        if ($LastX == null && $reCenter) {
                            $Y = $Y - $YStep / 2;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        if ($LastY < $this->GraphAreaY1 + $XMargin) {
                            $LastY = $this->GraphAreaY1 + $XMargin;
                        }
                        $Y = $Y + $YStep;
                    }
                    if ($reCenter) {
                        $points[] = $LastX;
                        $points[] = $LastY + $YStep / 2;
                        $points[] = $YZero;
                        $points[] = $LastY + $YStep / 2;
                    } else {
                        $points[] = $YZero;
                        $points[] = $LastY;
                    }
                    $this->drawPolygon($points, $Color);
                }
            }
        }
    }

    /**
     * Draw an area chart
     * @param array $format
     */
    public function drawAreaChart(array $format = []) {
        $displayValues = isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
        $displayOffset = isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
        $displayColor = isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
        $displayR = isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
        $displayG = isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
        $displayB = isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
        $forceTransparency = isset($format["ForceTransparency"]) ? $format["ForceTransparency"] : 25;
        $AroundZero = isset($format["AroundZero"]) ? $format["AroundZero"] : true;
        $Threshold = isset($format["Threshold"]) ? $format["Threshold"] : null;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
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
                $mode = $data["axis"][$axisId]["Display"];
                $format = $data["axis"][$axisId]["Format"];
                $Unit = $data["axis"][$axisId]["Unit"];
                $PosArray = $this->scaleComputeY($serie["Data"], ["axisId" => $serie["axis"]]);
                $YZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                if ($Threshold != null) {
                    foreach ($Threshold as $Key => $Params) {
                        $Threshold[$Key]["minX"] = $this->scaleComputeY(
                                $Params["min"], ["axisId" => $serie["axis"]]
                        );
                        $Threshold[$Key]["maxX"] = $this->scaleComputeY(
                                $Params["max"], ["axisId" => $serie["axis"]]
                        );
                    }
                }
                $this->dataSet->data["series"][$serieName]["XOffset"] = 0;
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    $Areas = [];
                    $AreaID = 0;
                    $Areas[$AreaID][] = $this->GraphAreaX1 + $XMargin;
                    if ($AroundZero) {
                        $Areas[$AreaID][] = $YZero;
                    } else {
                        $Areas[$AreaID][] = $this->GraphAreaY2 - 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Y) {
                        if ($displayValues && $serie["Data"][$Key] != VOID) {
                            if ($serie["Data"][$Key] > 0) {
                                $align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $Offset = $displayOffset;
                            } else {
                                $align = TEXT_ALIGN_TOPMIDDLE;
                                $Offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $X, $Y - $Offset, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit), ["r" => $displayR, "g" => $displayG, "b" => $displayB, "Align" => $align]
                            );
                        }
                        if ($Y == VOID && isset($Areas[$AreaID])) {
                            if ($LastX == null) {
                                $Areas[$AreaID][] = $X;
                            } else {
                                $Areas[$AreaID][] = $LastX;
                            }
                            if ($AroundZero) {
                                $Areas[$AreaID][] = $YZero;
                            } else {
                                $Areas[$AreaID][] = $this->GraphAreaY2 - 1;
                            }
                            $AreaID++;
                        } elseif ($Y != VOID) {
                            if (!isset($Areas[$AreaID])) {
                                $Areas[$AreaID][] = $X;
                                if ($AroundZero) {
                                    $Areas[$AreaID][] = $YZero;
                                } else {
                                    $Areas[$AreaID][] = $this->GraphAreaY2 - 1;
                                }
                            }
                            $Areas[$AreaID][] = $X;
                            $Areas[$AreaID][] = $Y;
                        }
                        $LastX = $X;
                        $X = $X + $XStep;
                    }
                    $Areas[$AreaID][] = $LastX;
                    if ($AroundZero) {
                        $Areas[$AreaID][] = $YZero;
                    } else {
                        $Areas[$AreaID][] = $this->GraphAreaY2 - 1;
                    }
                    /* Handle shadows in the areas */
                    if ($this->shadow) {
                        $shadowArea = [];
                        foreach ($Areas as $Key => $points) {
                            $shadowArea[$Key] = [];
                            foreach ($points as $Key2 => $Value) {
                                if ($Key2 % 2 == 0) {
                                    $shadowArea[$Key][] = $Value + $this->shadowX;
                                } else {
                                    $shadowArea[$Key][] = $Value + $this->shadowY;
                                }
                            }
                        }
                        foreach ($shadowArea as $Key => $points) {
                            $this->drawPolygonChart(
                                    $points, [
                                "r" => $this->shadowR,
                                "g" => $this->shadowG,
                                "b" => $this->shadowB,
                                "alpha" => $this->shadowa
                                    ]
                            );
                        }
                    }
                    $alpha = $forceTransparency != null ? $forceTransparency : $alpha;
                    $Color = [
                        "r" => $r,
                        "g" => $g,
                        "b" => $b,
                        "alpha" => $alpha,
                        "Threshold" => $Threshold
                    ];
                    foreach ($Areas as $Key => $points) {
                        $this->drawPolygonChart($points, $Color);
                    }
                } else {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    $Areas = [];
                    $AreaID = 0;
                    if ($AroundZero) {
                        $Areas[$AreaID][] = $YZero;
                    } else {
                        $Areas[$AreaID][] = $this->GraphAreaX1 + 1;
                    }
                    $Areas[$AreaID][] = $this->GraphAreaY1 + $XMargin;
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $LastX = null;
                    $LastY = null;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $X) {
                        if ($displayValues && $serie["Data"][$Key] != VOID) {
                            if ($serie["Data"][$Key] > 0) {
                                $align = TEXT_ALIGN_BOTTOMMIDDLE;
                                $Offset = $displayOffset;
                            } else {
                                $align = TEXT_ALIGN_TOPMIDDLE;
                                $Offset = -$displayOffset;
                            }
                            $this->drawText(
                                    $X + $Offset, $Y, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit), [
                                "Angle" => 270,
                                "r" => $displayR,
                                "g" => $displayG,
                                "b" => $displayB,
                                "Align" => $align
                                    ]
                            );
                        }
                        if ($X == VOID && isset($Areas[$AreaID])) {
                            if ($AroundZero) {
                                $Areas[$AreaID][] = $YZero;
                            } else {
                                $Areas[$AreaID][] = $this->GraphAreaX1 + 1;
                            }
                            if ($LastY == null) {
                                $Areas[$AreaID][] = $Y;
                            } else {
                                $Areas[$AreaID][] = $LastY;
                            }
                            $AreaID++;
                        } elseif ($X != VOID) {
                            if (!isset($Areas[$AreaID])) {
                                if ($AroundZero) {
                                    $Areas[$AreaID][] = $YZero;
                                } else {
                                    $Areas[$AreaID][] = $this->GraphAreaX1 + 1;
                                }
                                $Areas[$AreaID][] = $Y;
                            }
                            $Areas[$AreaID][] = $X;
                            $Areas[$AreaID][] = $Y;
                        }
                        $LastX = $X;
                        $LastY = $Y;
                        $Y = $Y + $YStep;
                    }
                    if ($AroundZero) {
                        $Areas[$AreaID][] = $YZero;
                    } else {
                        $Areas[$AreaID][] = $this->GraphAreaX1 + 1;
                    }
                    $Areas[$AreaID][] = $LastY;
                    /* Handle shadows in the areas */
                    if ($this->shadow) {
                        $shadowArea = [];
                        foreach ($Areas as $Key => $points) {
                            $shadowArea[$Key] = [];
                            foreach ($points as $Key2 => $Value) {
                                if ($Key2 % 2 == 0) {
                                    $shadowArea[$Key][] = $Value + $this->shadowX;
                                } else {
                                    $shadowArea[$Key][] = $Value + $this->shadowY;
                                }
                            }
                        }
                        foreach ($shadowArea as $Key => $points) {
                            $this->drawPolygonChart(
                                    $points, [
                                "r" => $this->shadowR,
                                "g" => $this->shadowG,
                                "b" => $this->shadowB,
                                "alpha" => $this->shadowa
                                    ]
                            );
                        }
                    }
                    $alpha = $forceTransparency != null ? $forceTransparency : $alpha;
                    $Color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "Threshold" => $Threshold];
                    foreach ($Areas as $Key => $points) {
                        $this->drawPolygonChart($points, $Color);
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
        $Floating0Serie = isset($format["Floating0Serie"]) ? $format["Floating0Serie"] : null;
        $Floating0Value = isset($format["Floating0Value"]) ? $format["Floating0Value"] : null;
        $draw0Line = isset($format["Draw0Line"]) ? $format["Draw0Line"] : false;
        $displayValues = isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
        $displayOffset = isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
        $displayColor = isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
        $displayFont = isset($format["DisplayFont"]) ? $format["DisplayFont"] : $this->fontName;
        $displaySize = isset($format["DisplaySize"]) ? $format["DisplaySize"] : $this->fontSize;
        $displayPos = isset($format["DisplayPos"]) ? $format["DisplayPos"] : LABEL_POS_OUTSIDE;
        $displayShadow = isset($format["DisplayShadow"]) ? $format["DisplayShadow"] : true;
        $displayR = isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
        $displayG = isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
        $displayB = isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
        $AroundZero = isset($format["AroundZero"]) ? $format["AroundZero"] : true;
        $Interleave = isset($format["Interleave"]) ? $format["Interleave"] : .5;
        $rounded = isset($format["Rounded"]) ? $format["Rounded"] : false;
        $roundRadius = isset($format["RoundRadius"]) ? $format["RoundRadius"] : 4;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : -1;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : -1;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : -1;
        $gradient = isset($format["Gradient"]) ? $format["Gradient"] : false;
        $gradientMode = isset($format["GradientMode"]) ? $format["GradientMode"] : GRADIENT_SIMPLE;
        $gradientalpha = isset($format["Gradientalpha"]) ? $format["Gradientalpha"] : 20;
        $gradientStartR = isset($format["GradientStartR"]) ? $format["GradientStartR"] : 255;
        $gradientStartG = isset($format["GradientStartG"]) ? $format["GradientStartG"] : 255;
        $gradientStartB = isset($format["GradientStartB"]) ? $format["GradientStartB"] : 255;
        $gradientEndR = isset($format["GradientEndR"]) ? $format["GradientEndR"] : 0;
        $gradientEndG = isset($format["GradientEndG"]) ? $format["GradientEndG"] : 0;
        $gradientEndB = isset($format["GradientEndB"]) ? $format["GradientEndB"] : 0;
        $txtMargin = isset($format["TxtMargin"]) ? $format["TxtMargin"] : 6;
        $OverrideColors = isset($format["OverrideColors"]) ? $format["OverrideColors"] : null;
        $OverrideSurrounding = isset($format["OverrideSurrounding"]) ? $format["OverrideSurrounding"] : 30;
        $InnerSurrounding = isset($format["InnerSurrounding"]) ? $format["InnerSurrounding"] : null;
        $InnerborderR = isset($format["InnerborderR"]) ? $format["InnerborderR"] : -1;
        $InnerborderG = isset($format["InnerborderG"]) ? $format["InnerborderG"] : -1;
        $InnerborderB = isset($format["InnerborderB"]) ? $format["InnerborderB"] : -1;
        $recordImageMap = isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
        $this->lastChartLayout = Constant::CHART_LAST_LAYOUT_REGULAR;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        if ($OverrideColors != null) {
            $OverrideColors = $this->validatePalette($OverrideColors, $OverrideSurrounding);
            $this->dataSet->saveExtendedData("Palette", $OverrideColors);
        }
        $restoreShadow = $this->shadow;
        $seriesCount = $this->countDrawableSeries();
        $CurrentSerie = 0;
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
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
                $Color = [
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "borderR" => $borderR,
                    "borderG" => $borderG,
                    "borderB" => $borderB
                ];
                $axisId = $serie["axis"];
                $mode = $data["axis"][$axisId]["Display"];
                $format = $data["axis"][$axisId]["Format"];
                $Unit = $data["axis"][$axisId]["Unit"];
                if (isset($serie["Description"])) {
                    $serieDescription = $serie["Description"];
                } else {
                    $serieDescription = $serieName;
                }
                $PosArray = $this->scaleComputeY(
                        $serie["Data"], ["axisId" => $serie["axis"]]
                );
                if ($Floating0Value != null) {
                    $YZero = $this->scaleComputeY(
                            $Floating0Value, ["axisId" => $serie["axis"]]
                    );
                } else {
                    $YZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                }
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero < $this->GraphAreaY1 + 1) {
                        $YZero = $this->GraphAreaY1 + 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = 0;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    if ($AroundZero) {
                        $Y1 = $YZero;
                    } else {
                        $Y1 = $this->GraphAreaY2 - 1;
                    }
                    if ($XDivs == 0) {
                        $XSize = ($this->GraphAreaX2 - $this->GraphAreaX1) / ($seriesCount + $Interleave);
                    } else {
                        $XSize = ($XStep / ($seriesCount + $Interleave));
                    }
                    $XOffset = -($XSize * $seriesCount) / 2 + $CurrentSerie * $XSize;
                    if ($X + $XOffset <= $this->GraphAreaX1) {
                        $XOffset = $this->GraphAreaX1 - $X + 1;
                    }
                    $this->dataSet->data["series"][$serieName]["XOffset"] = $XOffset + $XSize / 2;
                    if ($rounded || $borderR != -1) {
                        $XSpace = 1;
                    } else {
                        $XSpace = 0;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $ID = 0;
                    foreach ($PosArray as $Key => $Y2) {
                        if ($Floating0Serie != null) {
                            if (isset($data["series"][$Floating0Serie]["Data"][$Key])) {
                                $Value = $data["series"][$Floating0Serie]["Data"][$Key];
                            } else {
                                $Value = 0;
                            }
                            $YZero = $this->scaleComputeY($Value, ["axisId" => $serie["axis"]]);
                            if ($YZero > $this->GraphAreaY2 - 1) {
                                $YZero = $this->GraphAreaY2 - 1;
                            }
                            if ($YZero < $this->GraphAreaY1 + 1) {
                                $YZero = $this->GraphAreaY1 + 1;
                            }
                            if ($AroundZero) {
                                $Y1 = $YZero;
                            } else {
                                $Y1 = $this->GraphAreaY2 - 1;
                            }
                        }
                        if ($OverrideColors != null) {
                            if (isset($OverrideColors[$ID])) {
                                $Color = [
                                    "r" => $OverrideColors[$ID]["r"],
                                    "g" => $OverrideColors[$ID]["g"],
                                    "b" => $OverrideColors[$ID]["b"],
                                    "alpha" => $OverrideColors[$ID]["alpha"],
                                    "borderR" => $OverrideColors[$ID]["borderR"],
                                    "borderG" => $OverrideColors[$ID]["borderG"],
                                    "borderB" => $OverrideColors[$ID]["borderB"]
                                ];
                            } else {
                                $Color = $this->getRandomColor();
                            }
                        }
                        if ($Y2 != VOID) {
                            $barHeight = $Y1 - $Y2;
                            if ($serie["Data"][$Key] == 0) {
                                $this->drawLine(
                                        $X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y1, $Color
                                );
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    "%s,%s,%s,%s", floor($X + $XOffset + $XSpace), floor($Y1 - 1), floor($X + $XOffset + $XSize - $XSpace), floor($Y1 + 1)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                    );
                                }
                            } else {
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    "%s,%s,%s,%s", floor($X + $XOffset + $XSpace), floor($Y1), floor($X + $XOffset + $XSize - $XSpace), floor($Y2)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                    );
                                }
                                if ($rounded) {
                                    $this->drawRoundedFilledRectangle(
                                            $X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, $roundRadius, $Color
                                    );
                                } else {
                                    $this->drawFilledRectangle(
                                            $X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, $Color
                                    );
                                    if ($InnerColor != null) {
                                        $this->drawRectangle(
                                                $X + $XOffset + $XSpace + 1, min($Y1, $Y2) + 1, $X + $XOffset + $XSize - $XSpace - 1, max($Y1, $Y2) - 1, $InnerColor
                                        );
                                    }
                                    if ($gradient) {
                                        $this->shadow = false;
                                        if ($gradientMode == GRADIENT_SIMPLE) {
                                            if ($serie["Data"][$Key] >= 0) {
                                                $gradienColor = [
                                                    "StartR" => $gradientStartR,
                                                    "StartG" => $gradientStartG,
                                                    "StartB" => $gradientStartB,
                                                    "EndR" => $gradientEndR,
                                                    "EndG" => $gradientEndG,
                                                    "EndB" => $gradientEndB,
                                                    "alpha" => $gradientalpha
                                                ];
                                            } else {
                                                $gradienColor = [
                                                    "StartR" => $gradientEndR,
                                                    "StartG" => $gradientEndG,
                                                    "StartB" => $gradientEndB,
                                                    "EndR" => $gradientStartR,
                                                    "EndG" => $gradientStartG,
                                                    "EndB" => $gradientStartB,
                                                    "alpha" => $gradientalpha
                                                ];
                                            }
                                            $this->drawGradientArea(
                                                    $X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, DIRECTION_VERTICAL, $gradienColor
                                            );
                                        } elseif ($gradientMode == GRADIENT_EFFECT_CAN) {
                                            $gradienColor1 = [
                                                "StartR" => $gradientEndR,
                                                "StartG" => $gradientEndG,
                                                "StartB" => $gradientEndB,
                                                "EndR" => $gradientStartR,
                                                "EndG" => $gradientStartG,
                                                "EndB" => $gradientStartB,
                                                "alpha" => $gradientalpha
                                            ];
                                            $gradienColor2 = [
                                                "StartR" => $gradientStartR,
                                                "StartG" => $gradientStartG,
                                                "StartB" => $gradientStartB,
                                                "EndR" => $gradientEndR,
                                                "EndG" => $gradientEndG,
                                                "EndB" => $gradientEndB,
                                                "alpha" => $gradientalpha
                                            ];
                                            $XSpan = floor($XSize / 3);
                                            $this->drawGradientArea(
                                                    $X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSpan - $XSpace, $Y2, DIRECTION_HORIZONTAL, $gradienColor1
                                            );
                                            $this->drawGradientArea(
                                                    $X + $XOffset + $XSpan + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, DIRECTION_HORIZONTAL, $gradienColor2
                                            );
                                        }
                                        $this->shadow = $restoreShadow;
                                    }
                                }
                                if ($draw0Line) {
                                    $Line0Color = ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20];
                                    if (abs($Y1 - $Y2) > 3) {
                                        $Line0Width = 3;
                                    } else {
                                        $Line0Width = 1;
                                    }
                                    if ($Y1 - $Y2 < 0) {
                                        $Line0Width = -$Line0Width;
                                    }
                                    $this->drawFilledRectangle(
                                            $X + $XOffset + $XSpace, floor($Y1), $X + $XOffset + $XSize - $XSpace, floor($Y1) - $Line0Width, $Line0Color
                                    );
                                    $this->drawLine(
                                            $X + $XOffset + $XSpace, floor($Y1), $X + $XOffset + $XSize - $XSpace, floor($Y1), $Line0Color
                                    );
                                }
                            }
                            if ($displayValues && $serie["Data"][$Key] != VOID) {
                                if ($displayShadow) {
                                    $this->shadow = true;
                                }
                                $caption = $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit);
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 90, $caption);
                                $txtHeight = $txtPos[0]["Y"] - $txtPos[1]["Y"] + $txtMargin;
                                if ($displayPos == LABEL_POS_INSIDE && abs($txtHeight) < abs($barHeight)) {
                                    $CenterX = (($X + $XOffset + $XSize - $XSpace) - ($X + $XOffset + $XSpace)) / 2 + $X + $XOffset + $XSpace
                                    ;
                                    $CenterY = ($Y2 - $Y1) / 2 + $Y1;
                                    $this->drawText(
                                            $CenterX, $CenterY, $caption, [
                                        "r" => $displayR,
                                        "g" => $displayG,
                                        "b" => $displayB,
                                        "Align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                        "fontSize" => $displaySize,
                                        "Angle" => 90
                                            ]
                                    );
                                } else {
                                    if ($serie["Data"][$Key] >= 0) {
                                        $align = TEXT_ALIGN_BOTTOMMIDDLE;
                                        $Offset = $displayOffset;
                                    } else {
                                        $align = TEXT_ALIGN_TOPMIDDLE;
                                        $Offset = -$displayOffset;
                                    }
                                    $this->drawText(
                                            $X + $XOffset + $XSize / 2, $Y2 - $Offset, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit), [
                                        "r" => $displayR,
                                        "g" => $displayG,
                                        "b" => $displayB,
                                        "Align" => $align,
                                        "fontSize" => $displaySize
                                            ]
                                    );
                                }
                                $this->shadow = $restoreShadow;
                            }
                        }
                        $X = $X + $XStep;
                        $ID++;
                    }
                } else {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($XDivs == 0) {
                        $YStep = 0;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    if ($AroundZero) {
                        $X1 = $YZero;
                    } else {
                        $X1 = $this->GraphAreaX1 + 1;
                    }
                    if ($XDivs == 0) {
                        $YSize = ($this->GraphAreaY2 - $this->GraphAreaY1) / ($seriesCount + $Interleave);
                    } else {
                        $YSize = ($YStep / ($seriesCount + $Interleave));
                    }
                    $YOffset = -($YSize * $seriesCount) / 2 + $CurrentSerie * $YSize;
                    if ($Y + $YOffset <= $this->GraphAreaY1) {
                        $YOffset = $this->GraphAreaY1 - $Y + 1;
                    }
                    $this->dataSet->data["series"][$serieName]["XOffset"] = $YOffset + $YSize / 2;
                    if ($rounded || $borderR != -1) {
                        $YSpace = 1;
                    } else {
                        $YSpace = 0;
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $ID = 0;
                    foreach ($PosArray as $Key => $X2) {
                        if ($Floating0Serie != null) {
                            if (isset($data["series"][$Floating0Serie]["Data"][$Key])) {
                                $Value = $data["series"][$Floating0Serie]["Data"][$Key];
                            } else {
                                $Value = 0;
                            }
                            $YZero = $this->scaleComputeY($Value, ["axisId" => $serie["axis"]]);
                            if ($YZero < $this->GraphAreaX1 + 1) {
                                $YZero = $this->GraphAreaX1 + 1;
                            }
                            if ($YZero > $this->GraphAreaX2 - 1) {
                                $YZero = $this->GraphAreaX2 - 1;
                            }
                            if ($AroundZero) {
                                $X1 = $YZero;
                            } else {
                                $X1 = $this->GraphAreaX1 + 1;
                            }
                        }
                        if ($OverrideColors != null) {
                            if (isset($OverrideColors[$ID])) {
                                $Color = [
                                    "r" => $OverrideColors[$ID]["r"],
                                    "g" => $OverrideColors[$ID]["g"],
                                    "b" => $OverrideColors[$ID]["b"],
                                    "alpha" => $OverrideColors[$ID]["alpha"],
                                    "borderR" => $OverrideColors[$ID]["borderR"],
                                    "borderG" => $OverrideColors[$ID]["borderG"],
                                    "borderB" => $OverrideColors[$ID]["borderB"]
                                ];
                            } else {
                                $Color = $this->getRandomColor();
                            }
                        }
                        if ($X2 != VOID) {
                            $barWidth = $X2 - $X1;
                            if ($serie["Data"][$Key] == 0) {
                                $this->drawLine(
                                        $X1, $Y + $YOffset + $YSpace, $X1, $Y + $YOffset + $YSize - $YSpace, $Color
                                );
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    "%s,%s,%s,%s", floor($X1 - 1), floor($Y + $YOffset + $YSpace), floor($X1 + 1), floor($Y + $YOffset + $YSize - $YSpace)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                    );
                                }
                            } else {
                                if ($recordImageMap) {
                                    $this->addToImageMap(
                                            "RECT", sprintf(
                                                    "%s,%s,%s,%s", floor($X1), floor($Y + $YOffset + $YSpace), floor($X2), floor($Y + $YOffset + $YSize - $YSpace)
                                            ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                    );
                                }
                                if ($rounded) {
                                    $this->drawRoundedFilledRectangle(
                                            $X1 + 1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSize - $YSpace, $roundRadius, $Color
                                    );
                                } else {
                                    $this->drawFilledRectangle(
                                            $X1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSize - $YSpace, $Color
                                    );
                                    if ($InnerColor != null) {
                                        $this->drawRectangle(
                                                min($X1, $X2) + 1, $Y + $YOffset + $YSpace + 1, max($X1, $X2) - 1, $Y + $YOffset + $YSize - $YSpace - 1, $InnerColor
                                        );
                                    }
                                    if ($gradient) {
                                        $this->shadow = false;
                                        if ($gradientMode == GRADIENT_SIMPLE) {
                                            if ($serie["Data"][$Key] >= 0) {
                                                $gradienColor = [
                                                    "StartR" => $gradientStartR,
                                                    "StartG" => $gradientStartG,
                                                    "StartB" => $gradientStartB,
                                                    "EndR" => $gradientEndR,
                                                    "EndG" => $gradientEndG,
                                                    "EndB" => $gradientEndB,
                                                    "alpha" => $gradientalpha
                                                ];
                                            } else {
                                                $gradienColor = [
                                                    "StartR" => $gradientEndR,
                                                    "StartG" => $gradientEndG,
                                                    "StartB" => $gradientEndB,
                                                    "EndR" => $gradientStartR,
                                                    "EndG" => $gradientStartG,
                                                    "EndB" => $gradientStartB,
                                                    "alpha" => $gradientalpha
                                                ];
                                            }
                                            $this->drawGradientArea(
                                                    $X1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSize - $YSpace, DIRECTION_HORIZONTAL, $gradienColor
                                            );
                                        } elseif ($gradientMode == GRADIENT_EFFECT_CAN) {
                                            $gradienColor1 = [
                                                "StartR" => $gradientEndR,
                                                "StartG" => $gradientEndG,
                                                "StartB" => $gradientEndB,
                                                "EndR" => $gradientStartR,
                                                "EndG" => $gradientStartG,
                                                "EndB" => $gradientStartB,
                                                "alpha" => $gradientalpha
                                            ];
                                            $gradienColor2 = [
                                                "StartR" => $gradientStartR,
                                                "StartG" => $gradientStartG,
                                                "StartB" => $gradientStartB,
                                                "EndR" => $gradientEndR,
                                                "EndG" => $gradientEndG,
                                                "EndB" => $gradientEndB,
                                                "alpha" => $gradientalpha
                                            ];
                                            $YSpan = floor($YSize / 3);
                                            $this->drawGradientArea(
                                                    $X1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSpan - $YSpace, DIRECTION_VERTICAL, $gradienColor1
                                            );
                                            $this->drawGradientArea(
                                                    $X1, $Y + $YOffset + $YSpan, $X2, $Y + $YOffset + $YSize - $YSpace, DIRECTION_VERTICAL, $gradienColor2
                                            );
                                        }
                                        $this->shadow = $restoreShadow;
                                    }
                                }
                                if ($draw0Line) {
                                    $Line0Color = ["r" => 0, "g" => 0, "b" => 0, "alpha" => 20];
                                    if (abs($X1 - $X2) > 3) {
                                        $Line0Width = 3;
                                    } else {
                                        $Line0Width = 1;
                                    }
                                    if ($X2 - $X1 < 0) {
                                        $Line0Width = -$Line0Width;
                                    }
                                    $this->drawFilledRectangle(
                                            floor($X1), $Y + $YOffset + $YSpace, floor($X1) + $Line0Width, $Y + $YOffset + $YSize - $YSpace, $Line0Color
                                    );
                                    $this->drawLine(
                                            floor($X1), $Y + $YOffset + $YSpace, floor($X1), $Y + $YOffset + $YSize - $YSpace, $Line0Color
                                    );
                                }
                            }
                            if ($displayValues && $serie["Data"][$Key] != VOID) {
                                if ($displayShadow) {
                                    $this->shadow = true;
                                }
                                $caption = $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit);
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 0, $caption);
                                $txtWidth = $txtPos[1]["X"] - $txtPos[0]["X"] + $txtMargin;
                                if ($displayPos == LABEL_POS_INSIDE && abs($txtWidth) < abs($barWidth)) {
                                    $CenterX = ($X2 - $X1) / 2 + $X1;
                                    $CenterY = (($Y + $YOffset + $YSize - $YSpace) - ($Y + $YOffset + $YSpace)) / 2 + ($Y + $YOffset + $YSpace)
                                    ;
                                    $this->drawText(
                                            $CenterX, $CenterY, $caption, [
                                        "r" => $displayR,
                                        "g" => $displayG,
                                        "b" => $displayB,
                                        "Align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                        "fontSize" => $displaySize
                                            ]
                                    );
                                } else {
                                    if ($serie["Data"][$Key] >= 0) {
                                        $align = TEXT_ALIGN_MIDDLELEFT;
                                        $Offset = $displayOffset;
                                    } else {
                                        $align = TEXT_ALIGN_MIDDLERIGHT;
                                        $Offset = -$displayOffset;
                                    }
                                    $this->drawText(
                                            $X2 + $Offset, $Y + $YOffset + $YSize / 2, $caption, [
                                        "r" => $displayR,
                                        "g" => $displayG,
                                        "b" => $displayB,
                                        "Align" => $align,
                                        "fontSize" => $displaySize
                                            ]
                                    );
                                }
                                $this->shadow = $restoreShadow;
                            }
                        }
                        $Y = $Y + $YStep;
                        $ID++;
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
        $displayValues = isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
        $displayOrientation = isset($format["DisplayOrientation"]) ? $format["DisplayOrientation"] : ORIENTATION_AUTO;
        $displayRound = isset($format["DisplayRound"]) ? $format["DisplayRound"] : 0;
        $displayColor = isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
        $displayFont = isset($format["DisplayFont"]) ? $format["DisplayFont"] : $this->fontName;
        $displaySize = isset($format["DisplaySize"]) ? $format["DisplaySize"] : $this->fontSize;
        $displayR = isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
        $displayG = isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
        $displayB = isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
        $Interleave = isset($format["Interleave"]) ? $format["Interleave"] : .5;
        $rounded = isset($format["Rounded"]) ? $format["Rounded"] : false;
        $roundRadius = isset($format["RoundRadius"]) ? $format["RoundRadius"] : 4;
        $surrounding = isset($format["surrounding"]) ? $format["surrounding"] : null;
        $borderR = isset($format["borderR"]) ? $format["borderR"] : -1;
        $borderG = isset($format["borderG"]) ? $format["borderG"] : -1;
        $borderB = isset($format["borderB"]) ? $format["borderB"] : -1;
        $gradient = isset($format["Gradient"]) ? $format["Gradient"] : false;
        $gradientMode = isset($format["GradientMode"]) ? $format["GradientMode"] : GRADIENT_SIMPLE;
        $gradientalpha = isset($format["Gradientalpha"]) ? $format["Gradientalpha"] : 20;
        $gradientStartR = isset($format["GradientStartR"]) ? $format["GradientStartR"] : 255;
        $gradientStartG = isset($format["GradientStartG"]) ? $format["GradientStartG"] : 255;
        $gradientStartB = isset($format["GradientStartB"]) ? $format["GradientStartB"] : 255;
        $gradientEndR = isset($format["GradientEndR"]) ? $format["GradientEndR"] : 0;
        $gradientEndG = isset($format["GradientEndG"]) ? $format["GradientEndG"] : 0;
        $gradientEndB = isset($format["GradientEndB"]) ? $format["GradientEndB"] : 0;
        $InnerSurrounding = isset($format["InnerSurrounding"]) ? $format["InnerSurrounding"] : null;
        $InnerborderR = isset($format["InnerborderR"]) ? $format["InnerborderR"] : -1;
        $InnerborderG = isset($format["InnerborderG"]) ? $format["InnerborderG"] : -1;
        $InnerborderB = isset($format["InnerborderB"]) ? $format["InnerborderB"] : -1;
        $recordImageMap = isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
        $fontFactor = isset($format["fontFactor"]) ? $format["fontFactor"] : 8;
        $this->lastChartLayout = CHART_LAST_LAYOUT_STACKED;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        $restoreShadow = $this->shadow;
        $LastX = [];
        $LastY = [];
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
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
                $mode = $data["axis"][$axisId]["Display"];
                $format = $data["axis"][$axisId]["Format"];
                $Unit = $data["axis"][$axisId]["Unit"];
                if (isset($serie["Description"])) {
                    $serieDescription = $serie["Description"];
                } else {
                    $serieDescription = $serieName;
                }
                $PosArray = $this->scaleComputeY(
                        $serie["Data"], ["axisId" => $serie["axis"]], true
                );
                $YZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                $this->dataSet->data["series"][$serieName]["XOffset"] = 0;
                $Color = [
                    "TransCorner" => true,
                    "r" => $r,
                    "g" => $g,
                    "b" => $b,
                    "alpha" => $alpha,
                    "borderR" => $borderR,
                    "borderG" => $borderG,
                    "borderB" => $borderB
                ];
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $XSize = ($XStep / (1 + $Interleave));
                    $XOffset = -($XSize / 2);
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Height) {
                        if ($Height != VOID && $serie["Data"][$Key] != 0) {
                            if ($serie["Data"][$Key] > 0) {
                                $Pos = "+";
                            } else {
                                $Pos = "-";
                            }
                            if (!isset($LastY[$Key])) {
                                $LastY[$Key] = [];
                            }
                            if (!isset($LastY[$Key][$Pos])) {
                                $LastY[$Key][$Pos] = $YZero;
                            }
                            $Y1 = $LastY[$Key][$Pos];
                            $Y2 = $Y1 - $Height;
                            if (($rounded || $borderR != -1) && ($Pos == "+" && $Y1 != $YZero)) {
                                $YSpaceUp = 1;
                            } else {
                                $YSpaceUp = 0;
                            }
                            if (($rounded || $borderR != -1) && ($Pos == "-" && $Y1 != $YZero)) {
                                $YSpaceDown = 1;
                            } else {
                                $YSpaceDown = 0;
                            }
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                "%s,%s,%s,%s", floor($X + $XOffset), floor($Y1 - $YSpaceUp + $YSpaceDown), floor($X + $XOffset + $XSize), floor($Y2)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                );
                            }
                            if ($rounded) {
                                $this->drawRoundedFilledRectangle(
                                        $X + $XOffset, $Y1 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2, $roundRadius, $Color
                                );
                            } else {
                                $this->drawFilledRectangle(
                                        $X + $XOffset, $Y1 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2, $Color
                                );
                                if ($InnerColor != null) {
                                    $restoreShadow = $this->shadow;
                                    $this->shadow = false;
                                    $this->drawRectangle(
                                            min($X + $XOffset + 1, $X + $XOffset + $XSize), min($Y1 - $YSpaceUp + $YSpaceDown, $Y2) + 1, max($X + $XOffset + 1, $X + $XOffset + $XSize) - 1, max($Y1 - $YSpaceUp + $YSpaceDown, $Y2) - 1, $InnerColor
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
                                            "EndR" => $gradientEndR,
                                            "EndG" => $gradientEndG,
                                            "EndB" => $gradientEndB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $this->drawGradientArea(
                                                $X + $XOffset, $Y1 - 1 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2 + 1, DIRECTION_VERTICAL, $gradientColor
                                        );
                                    } elseif ($gradientMode == GRADIENT_EFFECT_CAN) {
                                        $gradientColor1 = [
                                            "StartR" => $gradientEndR,
                                            "StartG" => $gradientEndG,
                                            "StartB" => $gradientEndB,
                                            "EndR" => $gradientStartR,
                                            "EndG" => $gradientStartG,
                                            "EndB" => $gradientStartB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $gradientColor2 = [
                                            "StartR" => $gradientStartR,
                                            "StartG" => $gradientStartG,
                                            "StartB" => $gradientStartB,
                                            "EndR" => $gradientEndR,
                                            "EndG" => $gradientEndG,
                                            "EndB" => $gradientEndB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $XSpan = floor($XSize / 3);
                                        $this->drawGradientArea(
                                                $X + $XOffset - .5, $Y1 - .5 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSpan, $Y2 + .5, DIRECTION_HORIZONTAL, $gradientColor1
                                        );
                                        $this->drawGradientArea(
                                                $X + $XSpan + $XOffset - .5, $Y1 - .5 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2 + .5, DIRECTION_HORIZONTAL, $gradientColor2
                                        );
                                    }
                                    $this->shadow = $restoreShadow;
                                }
                            }
                            if ($displayValues) {
                                $barHeight = abs($Y2 - $Y1) - 2;
                                $barWidth = $XSize + ($XOffset / 2) - $fontFactor;
                                $caption = $this->scaleFormat(
                                        round($serie["Data"][$Key], $displayRound), $mode, $format, $Unit
                                );
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 0, $caption);
                                $txtHeight = abs($txtPos[2]["Y"] - $txtPos[0]["Y"]);
                                $txtWidth = abs($txtPos[1]["X"] - $txtPos[0]["X"]);
                                $XCenter = (($X + $XOffset + $XSize) - ($X + $XOffset)) / 2 + $X + $XOffset;
                                $YCenter = (($Y2) - ($Y1 - $YSpaceUp + $YSpaceDown)) / 2 + $Y1 - $YSpaceUp + $YSpaceDown
                                ;
                                $Done = false;
                                if ($displayOrientation == ORIENTATION_HORIZONTAL || $displayOrientation == ORIENTATION_AUTO
                                ) {
                                    if ($txtHeight < $barHeight && $txtWidth < $barWidth) {
                                        $this->drawText(
                                                $XCenter, $YCenter, $this->scaleFormat(
                                                        $serie["Data"][$Key], $mode, $format, $Unit
                                                ), [
                                            "r" => $displayR,
                                            "g" => $displayG,
                                            "b" => $displayB,
                                            "Align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                            "fontSize" => $displaySize,
                                            "fontName" => $displayFont
                                                ]
                                        );
                                        $Done = true;
                                    }
                                }
                                if ($displayOrientation == ORIENTATION_VERTICAL || ($displayOrientation == ORIENTATION_AUTO && !$Done)
                                ) {
                                    if ($txtHeight < $barWidth && $txtWidth < $barHeight) {
                                        $this->drawText(
                                                $XCenter, $YCenter, $this->scaleFormat(
                                                        $serie["Data"][$Key], $mode, $format, $Unit
                                                ), [
                                            "r" => $displayR,
                                            "g" => $displayG,
                                            "b" => $displayB,
                                            "Angle" => 90,
                                            "Align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                            "fontSize" => $displaySize,
                                            "fontName" => $displayFont
                                                ]
                                        );
                                    }
                                }
                            }
                            $LastY[$Key][$Pos] = $Y2;
                        }
                        $X = $X + $XStep;
                    }
                } else {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $YSize = $YStep / (1 + $Interleave);
                    $YOffset = -($YSize / 2);
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    foreach ($PosArray as $Key => $Width) {
                        if ($Width != VOID && $serie["Data"][$Key] != 0) {
                            if ($serie["Data"][$Key] > 0) {
                                $Pos = "+";
                            } else {
                                $Pos = "-";
                            }
                            if (!isset($LastX[$Key])) {
                                $LastX[$Key] = [];
                            }
                            if (!isset($LastX[$Key][$Pos])) {
                                $LastX[$Key][$Pos] = $YZero;
                            }
                            $X1 = $LastX[$Key][$Pos];
                            $X2 = $X1 + $Width;
                            if (($rounded || $borderR != -1) && ($Pos == "+" && $X1 != $YZero)) {
                                $XSpaceLeft = 2;
                            } else {
                                $XSpaceLeft = 0;
                            }
                            if (($rounded || $borderR != -1) && ($Pos == "-" && $X1 != $YZero)) {
                                $XSpaceRight = 2;
                            } else {
                                $XSpaceRight = 0;
                            }
                            if ($recordImageMap) {
                                $this->addToImageMap(
                                        "RECT", sprintf(
                                                "%s,%s,%s,%s", floor($X1 + $XSpaceLeft), floor($Y + $YOffset), floor($X2 - $XSpaceRight), floor($Y + $YOffset + $YSize)
                                        ), $this->toHTMLColor($r, $g, $b), $serieDescription, $this->scaleFormat($serie["Data"][$Key], $mode, $format, $Unit)
                                );
                            }
                            if ($rounded) {
                                $this->drawRoundedFilledRectangle(
                                        $X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, $roundRadius, $Color
                                );
                            } else {
                                $this->drawFilledRectangle(
                                        $X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, $Color
                                );
                                if ($InnerColor != null) {
                                    $restoreShadow = $this->shadow;
                                    $this->shadow = false;
                                    $this->drawRectangle(
                                            min($X1 + $XSpaceLeft, $X2 - $XSpaceRight) + 1, min($Y + $YOffset, $Y + $YOffset + $YSize) + 1, max($X1 + $XSpaceLeft, $X2 - $XSpaceRight) - 1, max($Y + $YOffset, $Y + $YOffset + $YSize) - 1, $InnerColor
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
                                            "EndR" => $gradientEndR,
                                            "EndG" => $gradientEndG,
                                            "EndB" => $gradientEndB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $this->drawGradientArea(
                                                $X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, DIRECTION_HORIZONTAL, $gradientColor
                                        );
                                    } elseif ($gradientMode == GRADIENT_EFFECT_CAN) {
                                        $gradientColor1 = [
                                            "StartR" => $gradientEndR,
                                            "StartG" => $gradientEndG,
                                            "StartB" => $gradientEndB,
                                            "EndR" => $gradientStartR,
                                            "EndG" => $gradientStartG,
                                            "EndB" => $gradientStartB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $gradientColor2 = [
                                            "StartR" => $gradientStartR,
                                            "StartG" => $gradientStartG,
                                            "StartB" => $gradientStartB,
                                            "EndR" => $gradientEndR,
                                            "EndG" => $gradientEndG,
                                            "EndB" => $gradientEndB,
                                            "alpha" => $gradientalpha
                                        ];
                                        $YSpan = floor($YSize / 3);
                                        $this->drawGradientArea(
                                                $X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSpan, DIRECTION_VERTICAL, $gradientColor1
                                        );
                                        $this->drawGradientArea(
                                                $X1 + $XSpaceLeft, $Y + $YOffset + $YSpan, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, DIRECTION_VERTICAL, $gradientColor2
                                        );
                                    }
                                    $this->shadow = $restoreShadow;
                                }
                            }
                            if ($displayValues) {
                                $barWidth = abs($X2 - $X1) - $fontFactor;
                                $barHeight = $YSize + ($YOffset / 2) - $fontFactor / 2;
                                $caption = $this->scaleFormat(
                                        round($serie["Data"][$Key], $displayRound), $mode, $format, $Unit
                                );
                                $txtPos = $this->getTextBox(0, 0, $displayFont, $displaySize, 0, $caption);
                                $txtHeight = abs($txtPos[2]["Y"] - $txtPos[0]["Y"]);
                                $txtWidth = abs($txtPos[1]["X"] - $txtPos[0]["X"]);
                                $XCenter = ($X2 - $X1) / 2 + $X1;
                                $YCenter = (($Y + $YOffset + $YSize) - ($Y + $YOffset)) / 2 + $Y + $YOffset;
                                $Done = false;
                                if ($displayOrientation == ORIENTATION_HORIZONTAL || $displayOrientation == ORIENTATION_AUTO
                                ) {
                                    if ($txtHeight < $barHeight && $txtWidth < $barWidth) {
                                        $this->drawText(
                                                $XCenter, $YCenter, $this->scaleFormat(
                                                        $serie["Data"][$Key], $mode, $format, $Unit
                                                ), [
                                            "r" => $displayR,
                                            "g" => $displayG,
                                            "b" => $displayB,
                                            "Align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                            "fontSize" => $displaySize,
                                            "fontName" => $displayFont
                                                ]
                                        );
                                        $Done = true;
                                    }
                                }
                                if ($displayOrientation == ORIENTATION_VERTICAL || ($displayOrientation == ORIENTATION_AUTO && !$Done)
                                ) {
                                    if ($txtHeight < $barWidth && $txtWidth < $barHeight) {
                                        $this->drawText(
                                                $XCenter, $YCenter, $this->scaleFormat(
                                                        $serie["Data"][$Key], $mode, $format, $Unit
                                                ), [
                                            "r" => $displayR,
                                            "g" => $displayG,
                                            "b" => $displayB,
                                            "Angle" => 90,
                                            "Align" => TEXT_ALIGN_MIDDLEMIDDLE,
                                            "fontSize" => $displaySize,
                                            "fontName" => $displayFont
                                                ]
                                        );
                                    }
                                }
                            }
                            $LastX[$Key][$Pos] = $X2;
                        }
                        $Y = $Y + $YStep;
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
        $drawLine = isset($format["DrawLine"]) ? $format["DrawLine"] : false;
        $LineSurrounding = isset($format["LineSurrounding"]) ? $format["LineSurrounding"] : null;
        $LineR = isset($format["LineR"]) ? $format["LineR"] : VOID;
        $LineG = isset($format["LineG"]) ? $format["LineG"] : VOID;
        $LineB = isset($format["LineB"]) ? $format["LineB"] : VOID;
        $Linealpha = isset($format["Linealpha"]) ? $format["Linealpha"] : 100;
        $drawPlot = isset($format["DrawPlot"]) ? $format["DrawPlot"] : false;
        $PlotRadius = isset($format["PlotRadius"]) ? $format["PlotRadius"] : 2;
        $PlotBorder = isset($format["PlotBorder"]) ? $format["PlotBorder"] : 1;
        $PlotBorderSurrounding = isset($format["PlotBorderSurrounding"]) ? $format["PlotBorderSurrounding"] : null;
        $PlotborderR = isset($format["PlotborderR"]) ? $format["PlotborderR"] : 0;
        $PlotborderG = isset($format["PlotborderG"]) ? $format["PlotborderG"] : 0;
        $PlotborderB = isset($format["PlotborderB"]) ? $format["PlotborderB"] : 0;
        $PlotBorderalpha = isset($format["PlotBorderalpha"]) ? $format["PlotBorderalpha"] : 50;
        $forceTransparency = isset($format["ForceTransparency"]) ? $format["ForceTransparency"] : null;
        $this->lastChartLayout = CHART_LAST_LAYOUT_STACKED;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        $restoreShadow = $this->shadow;
        $this->shadow = false;
        /* Build the offset data series */
        $OverallOffset = [];
        $serieOrder = [];
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
                $serieOrder[] = $serieName;
                foreach ($serie["Data"] as $Key => $Value) {
                    if ($Value == VOID) {
                        $Value = 0;
                    }
                    if ($Value >= 0) {
                        $Sign = "+";
                    } else {
                        $Sign = "-";
                    }
                    if (!isset($OverallOffset[$Key]) || !isset($OverallOffset[$Key][$Sign])) {
                        $OverallOffset[$Key][$Sign] = 0;
                    }
                    if ($Sign == "+") {
                        $data["series"][$serieName]["Data"][$Key] = $Value + $OverallOffset[$Key][$Sign];
                    } else {
                        $data["series"][$serieName]["Data"][$Key] = $Value - $OverallOffset[$Key][$Sign];
                    }
                    $OverallOffset[$Key][$Sign] = $OverallOffset[$Key][$Sign] + abs($Value);
                }
            }
        }
        $serieOrder = array_reverse($serieOrder);
        foreach ($serieOrder as $Key => $serieName) {
            $serie = $data["series"][$serieName];
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                if ($forceTransparency != null) {
                    $alpha = $forceTransparency;
                }
                $Color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha];
                if ($LineSurrounding != null) {
                    $LineColor = [
                        "r" => $r + $LineSurrounding,
                        "g" => $g + $LineSurrounding,
                        "b" => $b + $LineSurrounding,
                        "alpha" => $alpha
                    ];
                } elseif ($LineR != VOID) {
                    $LineColor = [
                        "r" => $LineR,
                        "g" => $LineG,
                        "b" => $LineB,
                        "alpha" => $Linealpha
                    ];
                } else {
                    $LineColor = $Color;
                }
                if ($PlotBorderSurrounding != null) {
                    $PlotBorderColor = [
                        "r" => $r + $PlotBorderSurrounding,
                        "g" => $g + $PlotBorderSurrounding,
                        "b" => $b + $PlotBorderSurrounding,
                        "alpha" => $PlotBorderalpha
                    ];
                } else {
                    $PlotBorderColor = [
                        "r" => $PlotborderR,
                        "g" => $PlotborderG,
                        "b" => $PlotborderB,
                        "alpha" => $PlotBorderalpha
                    ];
                }
                $axisId = $serie["axis"];
                $format = $data["axis"][$axisId]["Format"];
                $PosArray = $this->scaleComputeY(
                        $serie["Data"], ["axisId" => $serie["axis"]], true
                );
                $YZero = $this->scaleComputeY(0, ["axisId" => $serie["axis"]]);
                $this->dataSet->data["series"][$serieName]["XOffset"] = 0;
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($YZero < $this->GraphAreaY1 + 1) {
                        $YZero = $this->GraphAreaY1 + 1;
                    }
                    if ($YZero > $this->GraphAreaY2 - 1) {
                        $YZero = $this->GraphAreaY2 - 1;
                    }
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $Plots = [];
                    $Plots[] = $X;
                    $Plots[] = $YZero;
                    foreach ($PosArray as $Key => $Height) {
                        if ($Height != VOID) {
                            $Plots[] = $X;
                            $Plots[] = $YZero - $Height;
                        }
                        $X = $X + $XStep;
                    }
                    $Plots[] = $X - $XStep;
                    $Plots[] = $YZero;
                    $this->drawPolygon($Plots, $Color);
                    $this->shadow = $restoreShadow;
                    if ($drawLine) {
                        for ($i = 2; $i <= count($Plots) - 6; $i = $i + 2) {
                            $this->drawLine(
                                    $Plots[$i], $Plots[$i + 1], $Plots[$i + 2], $Plots[$i + 3], $LineColor
                            );
                        }
                    }
                    if ($drawPlot) {
                        for ($i = 2; $i <= count($Plots) - 4; $i = $i + 2) {
                            if ($PlotBorder != 0) {
                                $this->drawFilledCircle(
                                        $Plots[$i], $Plots[$i + 1], $PlotRadius + $PlotBorder, $PlotBorderColor
                                );
                            }
                            $this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius, $Color);
                        }
                    }
                    $this->shadow = false;
                } elseif ($data["Orientation"] == SCALE_POS_TOPBOTTOM) {
                    if ($YZero < $this->GraphAreaX1 + 1) {
                        $YZero = $this->GraphAreaX1 + 1;
                    }
                    if ($YZero > $this->GraphAreaX2 - 1) {
                        $YZero = $this->GraphAreaX2 - 1;
                    }
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $Plots = [];
                    $Plots[] = $YZero;
                    $Plots[] = $Y;
                    foreach ($PosArray as $Key => $Height) {
                        if ($Height != VOID) {
                            $Plots[] = $YZero + $Height;
                            $Plots[] = $Y;
                        }
                        $Y = $Y + $YStep;
                    }
                    $Plots[] = $YZero;
                    $Plots[] = $Y - $YStep;
                    $this->drawPolygon($Plots, $Color);
                    $this->shadow = $restoreShadow;
                    if ($drawLine) {
                        for ($i = 2; $i <= count($Plots) - 6; $i = $i + 2) {
                            $this->drawLine(
                                    $Plots[$i], $Plots[$i + 1], $Plots[$i + 2], $Plots[$i + 3], $LineColor
                            );
                        }
                    }
                    if ($drawPlot) {
                        for ($i = 2; $i <= count($Plots) - 4; $i = $i + 2) {
                            if ($PlotBorder != 0) {
                                $this->drawFilledCircle(
                                        $Plots[$i], $Plots[$i + 1], $PlotRadius + $PlotBorder, $PlotBorderColor
                                );
                            }
                            $this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius, $Color);
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
        $Offset = isset($format["Offset"]) ? $format["Offset"] : 10;
        $serieSpacing = isset($format["SerieSpacing"]) ? $format["SerieSpacing"] : 3;
        $DerivativeHeight = isset($format["DerivativeHeight"]) ? $format["DerivativeHeight"] : 4;
        $ShadedSlopeBox = isset($format["ShadedSlopeBox"]) ? $format["ShadedSlopeBox"] : false;
        $drawBackground = isset($format["DrawBackground"]) ? $format["DrawBackground"] : true;
        $backgroundR = isset($format["BackgroundR"]) ? $format["BackgroundR"] : 255;
        $backgroundG = isset($format["BackgroundG"]) ? $format["BackgroundG"] : 255;
        $backgroundB = isset($format["BackgroundB"]) ? $format["BackgroundB"] : 255;
        $backgroundalpha = isset($format["Backgroundalpha"]) ? $format["Backgroundalpha"] : 20;
        $drawBorder = isset($format["DrawBorder"]) ? $format["DrawBorder"] : true;
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
        $PositiveSlopeStartR = isset($format["PositiveSlopeStartR"]) ? $format["PositiveSlopeStartR"] : 184;
        $PositiveSlopeStartG = isset($format["PositiveSlopeStartG"]) ? $format["PositiveSlopeStartG"] : 234;
        $PositiveSlopeStartB = isset($format["PositiveSlopeStartB"]) ? $format["PositiveSlopeStartB"] : 88;
        $PositiveSlopeEndR = isset($format["PositiveSlopeStartR"]) ? $format["PositiveSlopeStartR"] : 239;
        $PositiveSlopeEndG = isset($format["PositiveSlopeStartG"]) ? $format["PositiveSlopeStartG"] : 31;
        $PositiveSlopeEndB = isset($format["PositiveSlopeStartB"]) ? $format["PositiveSlopeStartB"] : 36;
        $NegativeSlopeStartR = isset($format["NegativeSlopeStartR"]) ? $format["NegativeSlopeStartR"] : 184;
        $NegativeSlopeStartG = isset($format["NegativeSlopeStartG"]) ? $format["NegativeSlopeStartG"] : 234;
        $NegativeSlopeStartB = isset($format["NegativeSlopeStartB"]) ? $format["NegativeSlopeStartB"] : 88;
        $NegativeSlopeEndR = isset($format["NegativeSlopeStartR"]) ? $format["NegativeSlopeStartR"] : 67;
        $NegativeSlopeEndG = isset($format["NegativeSlopeStartG"]) ? $format["NegativeSlopeStartG"] : 124;
        $NegativeSlopeEndB = isset($format["NegativeSlopeStartB"]) ? $format["NegativeSlopeStartB"] : 227;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
            $YPos = $this->dataSet->data["GraphArea"]["Y2"] + $Offset;
        } else {
            $XPos = $this->dataSet->data["GraphArea"]["X2"] + $Offset;
        }
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
                $r = $serie["color"]["r"];
                $g = $serie["color"]["g"];
                $b = $serie["color"]["b"];
                $alpha = $serie["color"]["alpha"];
                $ticks = $serie["ticks"];
                $weight = $serie["weight"];
                $axisId = $serie["axis"];
                $PosArray = $this->scaleComputeY(
                        $serie["Data"], ["axisId" => $serie["axis"]]
                );
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($caption) {
                        if ($captionLine) {
                            $StartX = floor($this->GraphAreaX1 - $captionWidth + $XMargin - $captionMargin);
                            $EndX = floor($this->GraphAreaX1 - $captionMargin + $XMargin);
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
                                        $StartX, $YPos, $EndX, $YPos + $captionHeight, [
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
                                    $StartX + 2, $YPos + ($captionHeight / 2), $EndX - 2, $YPos + ($captionHeight / 2), $captionSettings
                            );
                        } else {
                            $this->drawFilledRectangle(
                                    $this->GraphAreaX1 - $captionWidth + $XMargin - $captionMargin, $YPos, $this->GraphAreaX1 - $captionMargin + $XMargin, $YPos + $captionHeight, [
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
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    $TopY = $YPos + ($captionHeight / 2) - ($DerivativeHeight / 2);
                    $bottomY = $YPos + ($captionHeight / 2) + ($DerivativeHeight / 2);
                    $StartX = floor($this->GraphAreaX1 + $XMargin);
                    $EndX = floor($this->GraphAreaX2 - $XMargin);
                    if ($drawBackground) {
                        $this->drawFilledRectangle(
                                $StartX - 1, $TopY - 1, $EndX + 1, $bottomY + 1, [
                            "r" => $backgroundR,
                            "g" => $backgroundG,
                            "b" => $backgroundB,
                            "alpha" => $backgroundalpha
                                ]
                        );
                    }
                    if ($drawBorder) {
                        $this->drawRectangle(
                                $StartX - 1, $TopY - 1, $EndX + 1, $bottomY + 1, [
                            "r" => $borderR,
                            "g" => $borderG,
                            "b" => $borderB,
                            "alpha" => $borderalpha
                                ]
                        );
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $restoreShadow = $this->shadow;
                    $this->shadow = false;
                    /* Determine the Max slope index */
                    $LastX = null;
                    $LastY = null;
                    $MinSlope = 0;
                    $MaxSlope = 1;
                    foreach ($PosArray as $Key => $Y) {
                        if ($Y != VOID && $LastX != null) {
                            $Slope = ($LastY - $Y);
                            if ($Slope > $MaxSlope) {
                                $MaxSlope = $Slope;
                            } if ($Slope < $MinSlope) {
                                $MinSlope = $Slope;
                            }
                        }
                        if ($Y == VOID) {
                            $LastX = null;
                            $LastY = null;
                        } else {
                            $LastX = $X;
                            $LastY = $Y;
                        }
                    }
                    $LastX = null;
                    $LastY = null;
                    $LastColor = null;
                    foreach ($PosArray as $Key => $Y) {
                        if ($Y != VOID && $LastY != null) {
                            $Slope = ($LastY - $Y);
                            if ($Slope >= 0) {
                                $SlopeIndex = (100 / $MaxSlope) * $Slope;
                                $r = (($PositiveSlopeEndR - $PositiveSlopeStartR) / 100) * $SlopeIndex + $PositiveSlopeStartR
                                ;
                                $g = (($PositiveSlopeEndG - $PositiveSlopeStartG) / 100) * $SlopeIndex + $PositiveSlopeStartG
                                ;
                                $b = (($PositiveSlopeEndB - $PositiveSlopeStartB) / 100) * $SlopeIndex + $PositiveSlopeStartB
                                ;
                            } elseif ($Slope < 0) {
                                $SlopeIndex = (100 / abs($MinSlope)) * abs($Slope);
                                $r = (($NegativeSlopeEndR - $NegativeSlopeStartR) / 100) * $SlopeIndex + $NegativeSlopeStartR
                                ;
                                $g = (($NegativeSlopeEndG - $NegativeSlopeStartG) / 100) * $SlopeIndex + $NegativeSlopeStartG
                                ;
                                $b = (($NegativeSlopeEndB - $NegativeSlopeStartB) / 100) * $SlopeIndex + $NegativeSlopeStartB
                                ;
                            }
                            $Color = ["r" => $r, "g" => $g, "b" => $b];
                            if ($ShadedSlopeBox && $LastColor != null) {// && $Slope != 0
                                $gradientSettings = [
                                    "StartR" => $LastColor["r"],
                                    "StartG" => $LastColor["g"],
                                    "StartB" => $LastColor["b"],
                                    "EndR" => $r,
                                    "EndG" => $g,
                                    "EndB" => $b
                                ];
                                $this->drawGradientArea(
                                        $LastX, $TopY, $X, $bottomY, DIRECTION_HORIZONTAL, $gradientSettings
                                );
                            } elseif (!$ShadedSlopeBox || $LastColor == null) { // || $Slope == 0
                                $this->drawFilledRectangle(
                                        floor($LastX), $TopY, floor($X), $bottomY, $Color
                                );
                            }
                            $LastColor = $Color;
                        }
                        if ($Y == VOID) {
                            $LastY = null;
                        } else {
                            $LastX = $X;
                            $LastY = $Y;
                        }
                        $X = $X + $XStep;
                    }
                    $YPos = $YPos + $captionHeight + $serieSpacing;
                } else {
                    if ($caption) {
                        $StartY = floor($this->GraphAreaY1 - $captionWidth + $XMargin - $captionMargin);
                        $EndY = floor($this->GraphAreaY1 - $captionMargin + $XMargin);
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
                                        $XPos, $StartY, $XPos + $captionHeight, $EndY, [
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
                                    $XPos + ($captionHeight / 2), $StartY + 2, $XPos + ($captionHeight / 2), $EndY - 2, $captionSettings
                            );
                        } else {
                            $this->drawFilledRectangle(
                                    $XPos, $StartY, $XPos + $captionHeight, $EndY, [
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
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    $TopX = $XPos + ($captionHeight / 2) - ($DerivativeHeight / 2);
                    $bottomX = $XPos + ($captionHeight / 2) + ($DerivativeHeight / 2);
                    $StartY = floor($this->GraphAreaY1 + $XMargin);
                    $EndY = floor($this->GraphAreaY2 - $XMargin);
                    if ($drawBackground) {
                        $this->drawFilledRectangle(
                                $TopX - 1, $StartY - 1, $bottomX + 1, $EndY + 1, [
                            "r" => $backgroundR,
                            "g" => $backgroundG,
                            "b" => $backgroundB,
                            "alpha" => $backgroundalpha
                                ]
                        );
                    }
                    if ($drawBorder) {
                        $this->drawRectangle(
                                $TopX - 1, $StartY - 1, $bottomX + 1, $EndY + 1, [
                            "r" => $borderR,
                            "g" => $borderG,
                            "b" => $borderB,
                            "alpha" => $borderalpha
                                ]
                        );
                    }
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $restoreShadow = $this->shadow;
                    $this->shadow = false;
                    /* Determine the Max slope index */
                    $LastX = null;
                    $LastY = null;
                    $MinSlope = 0;
                    $MaxSlope = 1;
                    foreach ($PosArray as $Key => $X) {
                        if ($X != VOID && $LastX != null) {
                            $Slope = ($X - $LastX);
                            if ($Slope > $MaxSlope) {
                                $MaxSlope = $Slope;
                            }
                            if ($Slope < $MinSlope) {
                                $MinSlope = $Slope;
                            }
                        }
                        if ($X == VOID) {
                            $LastX = null;
                        } else {
                            $LastX = $X;
                        }
                    }
                    $LastX = null;
                    $LastY = null;
                    $LastColor = null;
                    foreach ($PosArray as $Key => $X) {
                        if ($X != VOID && $LastX != null) {
                            $Slope = ($X - $LastX);
                            if ($Slope >= 0) {
                                $SlopeIndex = (100 / $MaxSlope) * $Slope;
                                $r = (($PositiveSlopeEndR - $PositiveSlopeStartR) / 100) * $SlopeIndex + $PositiveSlopeStartR
                                ;
                                $g = (($PositiveSlopeEndG - $PositiveSlopeStartG) / 100) * $SlopeIndex + $PositiveSlopeStartG
                                ;
                                $b = (($PositiveSlopeEndB - $PositiveSlopeStartB) / 100) * $SlopeIndex + $PositiveSlopeStartB
                                ;
                            } elseif ($Slope < 0) {
                                $SlopeIndex = (100 / abs($MinSlope)) * abs($Slope);
                                $r = (($NegativeSlopeEndR - $NegativeSlopeStartR) / 100) * $SlopeIndex + $NegativeSlopeStartR
                                ;
                                $g = (($NegativeSlopeEndG - $NegativeSlopeStartG) / 100) * $SlopeIndex + $NegativeSlopeStartG
                                ;
                                $b = (($NegativeSlopeEndB - $NegativeSlopeStartB) / 100) * $SlopeIndex + $NegativeSlopeStartB
                                ;
                            }
                            $Color = ["r" => $r, "g" => $g, "b" => $b];
                            if ($ShadedSlopeBox && $LastColor != null) {
                                $gradientSettings = [
                                    "StartR" => $LastColor["r"],
                                    "StartG" => $LastColor["g"],
                                    "StartB" => $LastColor["b"],
                                    "EndR" => $r,
                                    "EndG" => $g,
                                    "EndB" => $b
                                ];
                                $this->drawGradientArea(
                                        $TopX, $LastY, $bottomX, $Y, DIRECTION_VERTICAL, $gradientSettings
                                );
                            } elseif (!$ShadedSlopeBox || $LastColor == null) {
                                $this->drawFilledRectangle(
                                        $TopX, floor($LastY), $bottomX, floor($Y), $Color
                                );
                            }
                            $LastColor = $Color;
                        }
                        if ($X == VOID) {
                            $LastX = null;
                        } else {
                            $LastX = $X;
                            $LastY = $Y;
                        }
                        $Y = $Y + $XStep;
                    }
                    $XPos = $XPos + $captionHeight + $serieSpacing;
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
        $OverrideTicks = isset($format["ticks"]) ? $format["ticks"] : null;
        $OverrideR = isset($format["r"]) ? $format["r"] : VOID;
        $OverrideG = isset($format["g"]) ? $format["g"] : VOID;
        $OverrideB = isset($format["b"]) ? $format["b"] : VOID;
        $Overridealpha = isset($format["alpha"]) ? $format["alpha"] : VOID;
        $data = $this->dataSet->getData();
        list($XMargin, $XDivs) = $this->scaleGetXSettings();
        foreach ($data["series"] as $serieName => $serie) {
            if ($serie["isDrawable"] == true && $serieName != $data["Abscissa"]) {
                if ($OverrideR != VOID && $OverrideG != VOID && $OverrideB != VOID) {
                    $r = $OverrideR;
                    $g = $OverrideG;
                    $b = $OverrideB;
                } else {
                    $r = $serie["color"]["r"];
                    $g = $serie["color"]["g"];
                    $b = $serie["color"]["b"];
                }
                if ($OverrideTicks == null) {
                    $ticks = $serie["ticks"];
                } else {
                    $ticks = $OverrideTicks;
                }
                if ($Overridealpha == VOID) {
                    $alpha = $serie["color"]["alpha"];
                } else {
                    $alpha = $Overridealpha;
                }
                $Color = ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha, "ticks" => $ticks];
                $PosArray = $this->scaleComputeY(
                        $serie["Data"], ["axisId" => $serie["axis"]]
                );
                if ($data["Orientation"] == SCALE_POS_LEFTRIGHT) {
                    if ($XDivs == 0) {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
                    } else {
                        $XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
                    }
                    $X = $this->GraphAreaX1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $Sxy = 0;
                    $Sx = 0;
                    $Sy = 0;
                    $Sxx = 0;
                    foreach ($PosArray as $Key => $Y) {
                        if ($Y != VOID) {
                            $Sxy = $Sxy + $X * $Y;
                            $Sx = $Sx + $X;
                            $Sy = $Sy + $Y;
                            $Sxx = $Sxx + $X * $X;
                        }
                        $X = $X + $XStep;
                    }
                    $n = count($this->dataSet->stripVOID($PosArray)); //$n = count($PosArray);
                    $M = (($n * $Sxy) - ($Sx * $Sy)) / (($n * $Sxx) - ($Sx * $Sx));
                    $b = (($Sy) - ($M * $Sx)) / ($n);
                    $X1 = $this->GraphAreaX1 + $XMargin;
                    $Y1 = $M * $X1 + $b;
                    $X2 = $this->GraphAreaX2 - $XMargin;
                    $Y2 = $M * $X2 + $b;
                    if ($Y1 < $this->GraphAreaY1) {
                        $X1 = $X1 + ($this->GraphAreaY1 - $Y1);
                        $Y1 = $this->GraphAreaY1;
                    }
                    if ($Y1 > $this->GraphAreaY2) {
                        $X1 = $X1 + ($Y1 - $this->GraphAreaY2);
                        $Y1 = $this->GraphAreaY2;
                    }
                    if ($Y2 < $this->GraphAreaY1) {
                        $X2 = $X2 - ($this->GraphAreaY1 - $Y2);
                        $Y2 = $this->GraphAreaY1;
                    }
                    if ($Y2 > $this->GraphAreaY2) {
                        $X2 = $X2 - ($Y2 - $this->GraphAreaY2);
                        $Y2 = $this->GraphAreaY2;
                    }
                    $this->drawLine($X1, $Y1, $X2, $Y2, $Color);
                } else {
                    if ($XDivs == 0) {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
                    } else {
                        $YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
                    }
                    $Y = $this->GraphAreaY1 + $XMargin;
                    if (!is_array($PosArray)) {
                        $Value = $PosArray;
                        $PosArray = [];
                        $PosArray[0] = $Value;
                    }
                    $Sxy = 0;
                    $Sx = 0;
                    $Sy = 0;
                    $Sxx = 0;
                    foreach ($PosArray as $Key => $X) {
                        if ($X != VOID) {
                            $Sxy = $Sxy + $X * $Y;
                            $Sx = $Sx + $Y;
                            $Sy = $Sy + $X;
                            $Sxx = $Sxx + $Y * $Y;
                        }
                        $Y = $Y + $YStep;
                    }
                    $n = count($this->dataSet->stripVOID($PosArray)); //$n = count($PosArray);
                    $M = (($n * $Sxy) - ($Sx * $Sy)) / (($n * $Sxx) - ($Sx * $Sx));
                    $b = (($Sy) - ($M * $Sx)) / ($n);
                    $Y1 = $this->GraphAreaY1 + $XMargin;
                    $X1 = $M * $Y1 + $b;
                    $Y2 = $this->GraphAreaY2 - $XMargin;
                    $X2 = $M * $Y2 + $b;
                    if ($X1 < $this->GraphAreaX1) {
                        $Y1 = $Y1 + ($this->GraphAreaX1 - $X1);
                        $X1 = $this->GraphAreaX1;
                    }
                    if ($X1 > $this->GraphAreaX2) {
                        $Y1 = $Y1 + ($X1 - $this->GraphAreaX2);
                        $X1 = $this->GraphAreaX2;
                    }
                    if ($X2 < $this->GraphAreaX1) {
                        $Y2 = $Y2 - ($this->GraphAreaY1 - $X2);
                        $X2 = $this->GraphAreaX1;
                    }
                    if ($X2 > $this->GraphAreaX2) {
                        $Y2 = $Y2 - ($X2 - $this->GraphAreaX2);
                        $X2 = $this->GraphAreaX2;
                    }
                    $this->drawLine($X1, $Y1, $X2, $Y2, $Color);
                }
            }
        }
    }

    /**
     * Draw a label box
     * @param int $X
     * @param int $Y
     * @param string $Title
     * @param array $captions
     * @param array $format
     */
    public function drawLabelBox($X, $Y, $Title, array $captions, array $format = []) {
        $NoTitle = isset($format["NoTitle"]) ? $format["NoTitle"] : null;
        $boxWidth = isset($format["BoxWidth"]) ? $format["BoxWidth"] : 50;
        $drawSerieColor = isset($format["DrawSerieColor"]) ? $format["DrawSerieColor"] : true;
        $serieBoxSize = isset($format["SerieBoxSize"]) ? $format["SerieBoxSize"] : 6;
        $serieBoxSpacing = isset($format["SerieBoxSpacing"]) ? $format["SerieBoxSpacing"] : 4;
        $VerticalMargin = isset($format["VerticalMargin"]) ? $format["VerticalMargin"] : 10;
        $HorizontalMargin = isset($format["HorizontalMargin"]) ? $format["HorizontalMargin"] : 8;
        $r = isset($format["r"]) ? $format["r"] : $this->fontColorR;
        $g = isset($format["g"]) ? $format["g"] : $this->fontColorG;
        $b = isset($format["b"]) ? $format["b"] : $this->fontColorB;
        $fontName = isset($format["fontName"]) ? $this->loadFont($format["fontName"], 'fonts') : $this->fontName;
        $fontSize = isset($format["fontSize"]) ? $format["fontSize"] : $this->fontSize;
        $TitleMode = isset($format["TitleMode"]) ? $format["TitleMode"] : LABEL_TITLE_NOBACKGROUND;
        $TitleR = isset($format["TitleR"]) ? $format["TitleR"] : $r;
        $TitleG = isset($format["TitleG"]) ? $format["TitleG"] : $g;
        $TitleB = isset($format["TitleB"]) ? $format["TitleB"] : $b;
        $TitleBackgroundR = isset($format["TitleBackgroundR"]) ? $format["TitleBackgroundR"] : 0;
        $TitleBackgroundG = isset($format["TitleBackgroundG"]) ? $format["TitleBackgroundG"] : 0;
        $TitleBackgroundB = isset($format["TitleBackgroundB"]) ? $format["TitleBackgroundB"] : 0;
        $gradientStartR = isset($format["GradientStartR"]) ? $format["GradientStartR"] : 255;
        $gradientStartG = isset($format["GradientStartG"]) ? $format["GradientStartG"] : 255;
        $gradientStartB = isset($format["GradientStartB"]) ? $format["GradientStartB"] : 255;
        $gradientEndR = isset($format["GradientEndR"]) ? $format["GradientEndR"] : 220;
        $gradientEndG = isset($format["GradientEndG"]) ? $format["GradientEndG"] : 220;
        $gradientEndB = isset($format["GradientEndB"]) ? $format["GradientEndB"] : 220;
        $boxalpha = isset($format["Boxalpha"]) ? $format["Boxalpha"] : 100;
        if (!$drawSerieColor) {
            $serieBoxSize = 0;
            $serieBoxSpacing = 0;
        }
        $txtPos = $this->getTextBox($X, $Y, $fontName, $fontSize, 0, $Title);
        $TitleWidth = ($txtPos[1]["X"] - $txtPos[0]["X"]) + $VerticalMargin * 2;
        $TitleHeight = ($txtPos[0]["Y"] - $txtPos[2]["Y"]);
        if ($NoTitle) {
            $TitleWidth = 0;
            $TitleHeight = 0;
        }
        $captionWidth = 0;
        $captionHeight = -$HorizontalMargin;
        foreach ($captions as $Key => $caption) {
            $txtPos = $this->getTextBox(
                    $X, $Y, $fontName, $fontSize, 0, $caption["caption"]
            );
            $captionWidth = max(
                    $captionWidth, ($txtPos[1]["X"] - $txtPos[0]["X"]) + $VerticalMargin * 2
            );
            $captionHeight = $captionHeight + max(($txtPos[0]["Y"] - $txtPos[2]["Y"]), ($serieBoxSize + 2)) + $HorizontalMargin
            ;
        }
        if ($captionHeight <= 5) {
            $captionHeight = $captionHeight + $HorizontalMargin / 2;
        }
        if ($drawSerieColor) {
            $captionWidth = $captionWidth + $serieBoxSize + $serieBoxSpacing;
        }
        $boxWidth = max($boxWidth, $TitleWidth, $captionWidth);
        $XMin = $X - 5 - floor(($boxWidth - 10) / 2);
        $XMax = $X + 5 + floor(($boxWidth - 10) / 2);
        $restoreShadow = $this->shadow;
        if ($this->shadow == true) {
            $this->shadow = false;
            $Poly = [];
            $Poly[] = $X + $this->shadowX;
            $Poly[] = $Y + $this->shadowX;
            $Poly[] = $X + 5 + $this->shadowX;
            $Poly[] = $Y - 5 + $this->shadowX;
            $Poly[] = $XMax + $this->shadowX;
            $Poly[] = $Y - 5 + $this->shadowX;
            if ($NoTitle) {
                $Poly[] = $XMax + $this->shadowX;
                $Poly[] = $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 2 + $this->shadowX;
                $Poly[] = $XMin + $this->shadowX;
                $Poly[] = $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 2 + $this->shadowX;
            } else {
                $Poly[] = $XMax + $this->shadowX;
                $Poly[] = $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 3 + $this->shadowX;
                $Poly[] = $XMin + $this->shadowX;
                $Poly[] = $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 3 + $this->shadowX;
            }
            $Poly[] = $XMin + $this->shadowX;
            $Poly[] = $Y - 5 + $this->shadowX;
            $Poly[] = $X - 5 + $this->shadowX;
            $Poly[] = $Y - 5 + $this->shadowX;
            $this->drawPolygon(
                    $Poly, [
                "r" => $this->shadowR,
                "g" => $this->shadowG,
                "b" => $this->shadowB,
                "alpha" => $this->shadowa
                    ]
            );
        }
        /* Draw the background */
        $gradientSettings = [
            "StartR" => $gradientStartR,
            "StartG" => $gradientStartG,
            "StartB" => $gradientStartB,
            "EndR" => $gradientEndR,
            "EndG" => $gradientEndG,
            "EndB" => $gradientEndB,
            "alpha" => $boxalpha
        ];
        if ($NoTitle) {
            $this->drawGradientArea(
                    $XMin, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 2, $XMax, $Y - 6, DIRECTION_VERTICAL, $gradientSettings
            );
        } else {
            $this->drawGradientArea(
                    $XMin, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $XMax, $Y - 6, DIRECTION_VERTICAL, $gradientSettings
            );
        }
        $Poly = [];
        $Poly[] = $X;
        $Poly[] = $Y;
        $Poly[] = $X - 5;
        $Poly[] = $Y - 5;
        $Poly[] = $X + 5;
        $Poly[] = $Y - 5;
        $this->drawPolygon(
                $Poly, [
            "r" => $gradientEndR,
            "g" => $gradientEndG,
            "b" => $gradientEndB,
            "alpha" => $boxalpha,
            "noBorder" => true
                ]
        );
        /* Outer border */
        $OuterBorderColor = $this->allocateColor($this->picture, 100, 100, 100, $boxalpha);
        imageline($this->picture, $XMin, $Y - 5, $X - 5, $Y - 5, $OuterBorderColor);
        imageline($this->picture, $X, $Y, $X - 5, $Y - 5, $OuterBorderColor);
        imageline($this->picture, $X, $Y, $X + 5, $Y - 5, $OuterBorderColor);
        imageline($this->picture, $X + 5, $Y - 5, $XMax, $Y - 5, $OuterBorderColor);
        if ($NoTitle) {
            imageline(
                    $this->picture, $XMin, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 2, $XMin, $Y - 5, $OuterBorderColor
            );
            imageline(
                    $this->picture, $XMax, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 2, $XMax, $Y - 5, $OuterBorderColor
            );
            imageline(
                    $this->picture, $XMin, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 2, $XMax, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 2, $OuterBorderColor
            );
        } else {
            imageline(
                    $this->picture, $XMin, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $XMin, $Y - 5, $OuterBorderColor
            );
            imageline(
                    $this->picture, $XMax, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $XMax, $Y - 5, $OuterBorderColor
            );
            imageline(
                    $this->picture, $XMin, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $XMax, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $OuterBorderColor
            );
        }
        /* Inner border */
        $InnerBorderColor = $this->allocateColor($this->picture, 255, 255, 255, $boxalpha);
        imageline($this->picture, $XMin + 1, $Y - 6, $X - 5, $Y - 6, $InnerBorderColor);
        imageline($this->picture, $X, $Y - 1, $X - 5, $Y - 6, $InnerBorderColor);
        imageline($this->picture, $X, $Y - 1, $X + 5, $Y - 6, $InnerBorderColor);
        imageline($this->picture, $X + 5, $Y - 6, $XMax - 1, $Y - 6, $InnerBorderColor);
        if ($NoTitle) {
            imageline(
                    $this->picture, $XMin + 1, $Y - 4 - $TitleHeight - $captionHeight - $HorizontalMargin * 2, $XMin + 1, $Y - 6, $InnerBorderColor
            );
            imageline(
                    $this->picture, $XMax - 1, $Y - 4 - $TitleHeight - $captionHeight - $HorizontalMargin * 2, $XMax - 1, $Y - 6, $InnerBorderColor
            );
            imageline(
                    $this->picture, $XMin + 1, $Y - 4 - $TitleHeight - $captionHeight - $HorizontalMargin * 2, $XMax - 1, $Y - 4 - $TitleHeight - $captionHeight - $HorizontalMargin * 2, $InnerBorderColor
            );
        } else {
            imageline(
                    $this->picture, $XMin + 1, $Y - 4 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $XMin + 1, $Y - 6, $InnerBorderColor
            );
            imageline(
                    $this->picture, $XMax - 1, $Y - 4 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $XMax - 1, $Y - 6, $InnerBorderColor
            );
            imageline(
                    $this->picture, $XMin + 1, $Y - 4 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $XMax - 1, $Y - 4 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $InnerBorderColor
            );
        }
        /* Draw the separator line */
        if ($TitleMode == LABEL_TITLE_NOBACKGROUND && !$NoTitle) {
            $YPos = $Y - 7 - $captionHeight - $HorizontalMargin - $HorizontalMargin / 2;
            $XMargin = $VerticalMargin / 2;
            $this->drawLine(
                    $XMin + $XMargin, $YPos + 1, $XMax - $XMargin, $YPos + 1, [
                "r" => $gradientEndR,
                "g" => $gradientEndG,
                "b" => $gradientEndB,
                "alpha" => $boxalpha
                    ]
            );
            $this->drawLine(
                    $XMin + $XMargin, $YPos, $XMax - $XMargin, $YPos, [
                "r" => $gradientStartR,
                "g" => $gradientStartG,
                "b" => $gradientStartB,
                "alpha" => $boxalpha
                    ]
            );
        } elseif ($TitleMode == LABEL_TITLE_BACKGROUND) {
            $this->drawFilledRectangle(
                    $XMin, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin * 3, $XMax, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin + $HorizontalMargin / 2, [
                "r" => $TitleBackgroundR,
                "g" => $TitleBackgroundG,
                "b" => $TitleBackgroundB,
                "alpha" => $boxalpha
                    ]
            );
            imageline(
                    $this->picture, $XMin + 1, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin + $HorizontalMargin / 2 + 1, $XMax - 1, $Y - 5 - $TitleHeight - $captionHeight - $HorizontalMargin + $HorizontalMargin / 2 + 1, $InnerBorderColor
            );
        }
        /* Write the description */
        if (!$NoTitle) {
            $this->drawText(
                    $XMin + $VerticalMargin, $Y - 7 - $captionHeight - $HorizontalMargin * 2, $Title, [
                "Align" => TEXT_ALIGN_BOTTOMLEFT,
                "r" => $TitleR,
                "g" => $TitleG,
                "b" => $TitleB
                    ]
            );
        }
        /* Write the value */
        $YPos = $Y - 5 - $HorizontalMargin;
        $XPos = $XMin + $VerticalMargin + $serieBoxSize + $serieBoxSpacing;
        foreach ($captions as $Key => $caption) {
            $captionTxt = $caption["caption"];
            $txtPos = $this->getTextBox($XPos, $YPos, $fontName, $fontSize, 0, $captionTxt);
            $captionHeight = ($txtPos[0]["Y"] - $txtPos[2]["Y"]);
            /* Write the serie color if needed */
            if ($drawSerieColor) {
                $boxSettings = [
                    "r" => $caption["Format"]["r"],
                    "g" => $caption["Format"]["g"],
                    "b" => $caption["Format"]["b"],
                    "alpha" => $caption["Format"]["alpha"],
                    "borderR" => 0,
                    "borderG" => 0,
                    "borderB" => 0
                ];
                $this->drawFilledRectangle(
                        $XMin + $VerticalMargin, $YPos - $serieBoxSize, $XMin + $VerticalMargin + $serieBoxSize, $YPos, $boxSettings
                );
            }
            $this->drawText($XPos, $YPos, $captionTxt, ["Align" => TEXT_ALIGN_BOTTOMLEFT]);
            $YPos = $YPos - $captionHeight - $HorizontalMargin;
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a basic shape
     * @param int $X
     * @param int $Y
     * @param int $Shape
     * @param int $PlotSize
     * @param int $PlotBorder
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
    $X, $Y, $Shape, $PlotSize, $PlotBorder, $borderSize, $r, $g, $b, $alpha, $borderR, $borderG, $borderB, $borderalpha
    ) {
        if ($Shape == SERIE_SHAPE_FILLEDCIRCLE) {
            if ($PlotBorder) {
                $this->drawFilledCircle(
                        $X, $Y, $PlotSize + $borderSize, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $borderalpha]
                );
            }
            $this->drawFilledCircle(
                    $X, $Y, $PlotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($Shape == SERIE_SHAPE_FILLEDSQUARE) {
            if ($PlotBorder) {
                $this->drawFilledRectangle(
                        $X - $PlotSize - $borderSize, $Y - $PlotSize - $borderSize, $X + $PlotSize + $borderSize, $Y + $PlotSize + $borderSize, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $borderalpha]
                );
            }
            $this->drawFilledRectangle(
                    $X - $PlotSize, $Y - $PlotSize, $X + $PlotSize, $Y + $PlotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($Shape == SERIE_SHAPE_FILLEDTRIANGLE) {
            if ($PlotBorder) {
                $Pos = [];
                $Pos[] = $X;
                $Pos[] = $Y - $PlotSize - $borderSize;
                $Pos[] = $X - $PlotSize - $borderSize;
                $Pos[] = $Y + $PlotSize + $borderSize;
                $Pos[] = $X + $PlotSize + $borderSize;
                $Pos[] = $Y + $PlotSize + $borderSize;
                $this->drawPolygon(
                        $Pos, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $borderalpha]
                );
            }
            $Pos = [];
            $Pos[] = $X;
            $Pos[] = $Y - $PlotSize;
            $Pos[] = $X - $PlotSize;
            $Pos[] = $Y + $PlotSize;
            $Pos[] = $X + $PlotSize;
            $Pos[] = $Y + $PlotSize;
            $this->drawPolygon($Pos, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
        } elseif ($Shape == SERIE_SHAPE_TRIANGLE) {
            $this->drawLine(
                    $X, $Y - $PlotSize, $X - $PlotSize, $Y + $PlotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            $this->drawLine(
                    $X - $PlotSize, $Y + $PlotSize, $X + $PlotSize, $Y + $PlotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
            $this->drawLine(
                    $X + $PlotSize, $Y + $PlotSize, $X, $Y - $PlotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($Shape == SERIE_SHAPE_SQUARE) {
            $this->drawRectangle(
                    $X - $PlotSize, $Y - $PlotSize, $X + $PlotSize, $Y + $PlotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($Shape == SERIE_SHAPE_CIRCLE) {
            $this->drawCircle(
                    $X, $Y, $PlotSize, $PlotSize, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]
            );
        } elseif ($Shape == SERIE_SHAPE_DIAMOND) {
            $Pos = [];
            $Pos[] = $X - $PlotSize;
            $Pos[] = $Y;
            $Pos[] = $X;
            $Pos[] = $Y - $PlotSize;
            $Pos[] = $X + $PlotSize;
            $Pos[] = $Y;
            $Pos[] = $X;
            $Pos[] = $Y + $PlotSize;
            $this->drawPolygon(
                    $Pos, [
                "noFill" => true,
                "borderR" => $r,
                "borderG" => $g,
                "borderB" => $b,
                "borderalpha" => $alpha
                    ]
            );
        } elseif ($Shape == SERIE_SHAPE_FILLEDDIAMOND) {
            if ($PlotBorder) {
                $Pos = [];
                $Pos[] = $X - $PlotSize - $borderSize;
                $Pos[] = $Y;
                $Pos[] = $X;
                $Pos[] = $Y - $PlotSize - $borderSize;
                $Pos[] = $X + $PlotSize + $borderSize;
                $Pos[] = $Y;
                $Pos[] = $X;
                $Pos[] = $Y + $PlotSize + $borderSize;
                $this->drawPolygon(
                        $Pos, ["r" => $borderR, "g" => $borderG, "b" => $borderB, "alpha" => $borderalpha]
                );
            }
            $Pos = [];
            $Pos[] = $X - $PlotSize;
            $Pos[] = $Y;
            $Pos[] = $X;
            $Pos[] = $Y - $PlotSize;
            $Pos[] = $X + $PlotSize;
            $Pos[] = $Y;
            $Pos[] = $X;
            $Pos[] = $Y + $PlotSize;
            $this->drawPolygon($Pos, ["r" => $r, "g" => $g, "b" => $b, "alpha" => $alpha]);
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
        $AllIntegers = true;
        for ($i = 0; $i <= count($points) - 2; $i = $i + 2) {
            if ($this->getFirstDecimal($points[$i + 1]) != 0) {
                $AllIntegers = false;
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
        $LastX = VOID;
        foreach ($segments as $Key => $Pos) {
            if ($Pos["Y1"] != $Pos["Y2"]) {
                if ($inHorizon) {
                    $inHorizon = false;
                    $result[] = [
                        "X1" => $LastX,
                        "Y1" => $Pos["Y1"],
                        "X2" => $Pos["X1"],
                        "Y2" => $Pos["Y1"]
                    ];
                }
                $result[] = [
                    "X1" => $Pos["X1"],
                    "Y1" => $Pos["Y1"],
                    "X2" => $Pos["X2"],
                    "Y2" => $Pos["Y2"]
                ];
            } else {
                if (!$inHorizon) {
                    $inHorizon = true;
                    $LastX = $Pos["X1"];
                }
            }
        }
        $segments = $result;
        /* Do we have something to draw */
        if (!count($segments)) {
            return 0;
        }
        /* For segments debugging purpose */
        //foreach($segments as $Key => $Pos)
        // echo $Pos["X1"].",".$Pos["Y1"].",".$Pos["X2"].",".$Pos["Y2"]."\r\n";
        /* Find out the min & max Y boundaries */
        $MinY = OUT_OF_SIGHT;
        $MaxY = OUT_OF_SIGHT;
        foreach ($segments as $Key => $Coords) {
            if ($MinY == OUT_OF_SIGHT || $MinY > min($Coords["Y1"], $Coords["Y2"])) {
                $MinY = min($Coords["Y1"], $Coords["Y2"]);
            }
            if ($MaxY == OUT_OF_SIGHT || $MaxY < max($Coords["Y1"], $Coords["Y2"])) {
                $MaxY = max($Coords["Y1"], $Coords["Y2"]);
            }
        }
        if ($AllIntegers) {
            $YStep = 1;
        } else {
            $YStep = .5;
        }
        $MinY = floor($MinY);
        $MaxY = floor($MaxY);
        /* Scan each Y lines */
        $DefaultColor = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        $DebugLine = 0;
        $DebugColor = $this->allocateColor($this->picture, 255, 0, 0, 100);
        $MinY = floor($MinY);
        $MaxY = floor($MaxY);
        $YStep = 1;
        if (!$noFill) {
            //if ($DebugLine ) { $MinY = $DebugLine; $MaxY = $DebugLine; }
            for ($Y = $MinY; $Y <= $MaxY; $Y = $Y + $YStep) {
                $Intersections = [];
                $LastSlope = null;
                $restoreLast = "-";
                foreach ($segments as $Key => $Coords) {
                    $X1 = $Coords["X1"];
                    $X2 = $Coords["X2"];
                    $Y1 = $Coords["Y1"];
                    $Y2 = $Coords["Y2"];
                    if (min($Y1, $Y2) <= $Y && max($Y1, $Y2) >= $Y) {
                        if ($Y1 == $Y2) {
                            $X = $X1;
                        } else {
                            $X = $X1 + (($Y - $Y1) * $X2 - ($Y - $Y1) * $X1) / ($Y2 - $Y1);
                        }
                        $X = floor($X);
                        if ($X2 == $X1) {
                            $Slope = "!";
                        } else {
                            $SlopeC = ($Y2 - $Y1) / ($X2 - $X1);
                            if ($SlopeC == 0) {
                                $Slope = "=";
                            } elseif ($SlopeC > 0) {
                                $Slope = "+";
                            } elseif ($SlopeC < 0) {
                                $Slope = "-";
                            }
                        }
                        if (!is_array($Intersections)) {
                            $Intersections[] = $X;
                        } elseif (!in_array($X, $Intersections)) {
                            $Intersections[] = $X;
                        } elseif (in_array($X, $Intersections)) {
                            if ($Y == $DebugLine) {
                                echo $Slope . "/" . $LastSlope . "(" . $X . ") ";
                            }
                            if ($Slope == "=" && $LastSlope == "-") {
                                $Intersections[] = $X;
                            }
                            if ($Slope != $LastSlope && $LastSlope != "!" && $LastSlope != "=") {
                                $Intersections[] = $X;
                            }
                            if ($Slope != $LastSlope && $LastSlope == "!" && $Slope == "+") {
                                $Intersections[] = $X;
                            }
                        }
                        if (is_array($Intersections) && in_array($X, $Intersections) && $LastSlope == "=" && ($Slope == "-")
                        ) {
                            $Intersections[] = $X;
                        }
                        $LastSlope = $Slope;
                    }
                }
                if ($restoreLast != "-") {
                    $Intersections[] = $restoreLast;
                    echo "@" . $Y . "\r\n";
                }
                if (is_array($Intersections)) {
                    sort($Intersections);
                    if ($Y == $DebugLine) {
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
                        $LastX = Constant::OUT_OF_SIGHT;
                        foreach ($Intersections as $Key => $X) {
                            if ($LastX == OUT_OF_SIGHT) {
                                $LastX = $X;
                            } elseif ($LastX != OUT_OF_SIGHT) {
                                if ($this->getFirstDecimal($LastX) > 1) {
                                    $LastX++;
                                }
                                $Color = $DefaultColor;
                                if ($Threshold != null) {
                                    foreach ($Threshold as $Key => $parameters) {
                                        if ($Y <= $parameters["minX"] && $Y >= $parameters["maxX"]
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
                                            $Color = $this->allocateColor(
                                                    $this->picture, $r, $g, $b, $alpha
                                            );
                                        }
                                    }
                                }
                                imageline($this->picture, $LastX, $Y, $X, $Y, $Color);
                                if ($Y == $DebugLine) {
                                    imageline($this->picture, $LastX, $Y, $X, $Y, $DebugColor);
                                }
                                $LastX = OUT_OF_SIGHT;
                            }
                        }
                    }
                }
            }
        }
        /* Draw the polygon border, if required */
        if (!$noBorder) {
            foreach ($segments as $Key => $Coords) {
                $this->drawLine(
                        $Coords["X1"], $Coords["Y1"], $Coords["X2"], $Coords["Y2"], [
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
