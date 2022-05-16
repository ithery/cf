<?php
use CImage_Chart_Constant as Constant;

trait CImage_Chart_Concern_ShapeDraw {
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
     * Draw a rectangle with rounded corners
     *
     * @param int       $x1
     * @param int       $y1
     * @param int       $x2
     * @param int       $y2
     * @param int|float $radius
     * @param array     $format
     *
     * @return null|integer
     */
    public function drawRoundedRectangle($x1, $y1, $x2, $y2, $radius, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        list($x1, $y1, $x2, $y2) = $this->fixBoxCoordinates($x1, $y1, $x2, $y2);
        if ($x2 - $x1 < $radius) {
            $radius = floor((($x2 - $x1)) / 2);
        }
        if ($y2 - $y1 < $radius) {
            $radius = floor((($y2 - $y1)) / 2);
        }
        $color = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'noBorder' => true];
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
        $step = 360 / (2 * Constant::PI * $radius);
        for ($i = 0; $i <= 90; $i = $i + $step) {
            $x = cos(($i + 180) * Constant::PI / 180) * $radius + $x1 + $radius;
            $y = sin(($i + 180) * Constant::PI / 180) * $radius + $y1 + $radius;
            $this->drawAntialiasPixel($x, $y, ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]);
            $x = cos(($i + 90) * Constant::PI / 180) * $radius + $x1 + $radius;
            $y = sin(($i + 90) * Constant::PI / 180) * $radius + $y2 - $radius;
            $this->drawAntialiasPixel($x, $y, ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]);
            $x = cos($i * Constant::PI / 180) * $radius + $x2 - $radius;
            $y = sin($i * Constant::PI / 180) * $radius + $y2 - $radius;
            $this->drawAntialiasPixel($x, $y, ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]);
            $x = cos(($i + 270) * Constant::PI / 180) * $radius + $x2 - $radius;
            $y = sin(($i + 270) * Constant::PI / 180) * $radius + $y1 + $radius;
            $this->drawAntialiasPixel($x, $y, ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha]);
        }
    }

    /**
     * Draw a rectangle with rounded corners
     *
     * @param int       $x1
     * @param int       $y1
     * @param int       $x2
     * @param int       $y2
     * @param int|float $radius
     * @param array     $format
     *
     * @return null|integer
     */
    public function drawRoundedFilledRectangle($x1, $y1, $x2, $y2, $radius, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $borderR = isset($format['borderR']) ? $format['borderR'] : -1;
        $borderG = isset($format['borderG']) ? $format['borderG'] : -1;
        $borderB = isset($format['borderB']) ? $format['borderB'] : -1;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
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
                $x1 + $this->shadowX,
                $y1 + $this->shadowY,
                $x2 + $this->shadowX,
                $y2 + $this->shadowY,
                $radius,
                [
                    'r' => $this->shadowR,
                    'g' => $this->shadowG,
                    'b' => $this->shadowB,
                    'alpha' => $this->shadowA
                ]
            );
        }
        $color = ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'noBorder' => true];
        if ($radius <= 0) {
            $this->drawFilledRectangle($x1, $y1, $x2, $y2, $color);
            return 0;
        }
        $yTop = $y1 + $radius;
        $yBottom = $y2 - $radius;
        $step = 360 / (2 * Constant::PI * $radius);
        $positions = [];
        $radius--;
        $minY = null;
        $maxY = null;
        for ($i = 0; $i <= 90; $i = $i + $step) {
            $xp1 = cos(($i + 180) * Constant::PI / 180) * $radius + $x1 + $radius;
            $xp2 = cos(((90 - $i) + 270) * Constant::PI / 180) * $radius + $x2 - $radius;
            $yp = floor(sin(($i + 180) * Constant::PI / 180) * $radius + $yTop);
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
                $positions[$yp]['x1'] = $xp1;
                $positions[$yp]['x2'] = $xp2;
            } else {
                $positions[$yp]['x1'] = ($positions[$yp]['x1'] + $xp1) / 2;
                $positions[$yp]['x2'] = ($positions[$yp]['x2'] + $xp2) / 2;
            }
            $xp1 = cos(($i + 90) * Constant::PI / 180) * $radius + $x1 + $radius;
            $xp2 = cos((90 - $i) * Constant::PI / 180) * $radius + $x2 - $radius;
            $yp = floor(sin(($i + 90) * Constant::PI / 180) * $radius + $yBottom);
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
                $positions[$yp]['x1'] = $xp1;
                $positions[$yp]['x2'] = $xp2;
            } else {
                $positions[$yp]['x1'] = ($positions[$yp]['x1'] + $xp1) / 2;
                $positions[$yp]['x2'] = ($positions[$yp]['x2'] + $xp2) / 2;
            }
        }
        $manualColor = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
        foreach ($positions as $yp => $bounds) {
            $x1 = $bounds['x1'];
            $x1Dec = $this->getFirstDecimal($x1);
            if ($x1Dec != 0) {
                $x1 = floor($x1) + 1;
            }
            $x2 = $bounds['x2'];
            $x2Dec = $this->getFirstDecimal($x2);
            if ($x2Dec != 0) {
                $x2 = floor($x2) - 1;
            }
            imageline($this->picture, $x1, $yp, $x2, $yp, $manualColor);
        }
        $this->drawFilledRectangle($x1, $minY + 1, floor($x2), $maxY - 1, $color);
        $radius++;
        $this->drawRoundedRectangle(
            $x1,
            $y1,
            $x2 + 1,
            $y2 - 1,
            $radius,
            ['r' => $borderR, 'g' => $borderG, 'b' => $borderB, 'alpha' => $alpha]
        );
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a rectangle
     *
     * @param int   $x1
     * @param int   $y1
     * @param int   $x2
     * @param int   $y2
     * @param array $format
     */
    public function drawRectangle($x1, $y1, $x2, $y2, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $ticks = isset($format['ticks']) ? $format['ticks'] : null;
        $noAngle = isset($format['noAngle']) ? $format['noAngle'] : false;
        if ($x1 > $x2) {
            list($x1, $x2) = [$x2, $x1];
        }
        if ($y1 > $y2) {
            list($y1, $y2) = [$y2, $y1];
        }
        if ($this->antialias) {
            if ($noAngle) {
                $this->drawLine(
                    $x1 + 1,
                    $y1,
                    $x2 - 1,
                    $y1,
                    ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks]
                );
                $this->drawLine(
                    $x2,
                    $y1 + 1,
                    $x2,
                    $y2 - 1,
                    ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks]
                );
                $this->drawLine(
                    $x2 - 1,
                    $y2,
                    $x1 + 1,
                    $y2,
                    ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks]
                );
                $this->drawLine(
                    $x1,
                    $y1 + 1,
                    $x1,
                    $y2 - 1,
                    ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks]
                );
            } else {
                $this->drawLine(
                    $x1 + 1,
                    $y1,
                    $x2 - 1,
                    $y1,
                    ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks]
                );
                $this->drawLine(
                    $x2,
                    $y1,
                    $x2,
                    $y2,
                    ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks]
                );
                $this->drawLine(
                    $x2 - 1,
                    $y2,
                    $x1 + 1,
                    $y2,
                    ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks]
                );
                $this->drawLine(
                    $x1,
                    $y1,
                    $x1,
                    $y2,
                    ['r' => $r, 'g' => $g, 'b' => $b, 'alpha' => $alpha, 'ticks' => $ticks]
                );
            }
        } else {
            $color = $this->allocateColor($this->picture, $r, $g, $b, $alpha);
            imagerectangle($this->picture, $x1, $y1, $x2, $y2, $color);
        }
    }

    /**
     * Draw a filled rectangle
     *
     * @param int   $x1
     * @param int   $y1
     * @param int   $x2
     * @param int   $y2
     * @param array $format
     */
    public function drawFilledRectangle($x1, $y1, $x2, $y2, array $format = []) {
        $r = isset($format['r']) ? $format['r'] : 0;
        $g = isset($format['g']) ? $format['g'] : 0;
        $b = isset($format['b']) ? $format['b'] : 0;
        $alpha = isset($format['alpha']) ? $format['alpha'] : 100;
        $borderR = isset($format['borderR']) ? $format['borderR'] : -1;
        $borderG = isset($format['borderG']) ? $format['borderG'] : -1;
        $borderB = isset($format['borderB']) ? $format['borderB'] : -1;
        $borderalpha = isset($format['borderalpha']) ? $format['borderalpha'] : $alpha;
        $surrounding = isset($format['surrounding']) ? $format['surrounding'] : null;
        $ticks = isset($format['ticks']) ? $format['ticks'] : null;
        $noAngle = isset($format['noAngle']) ? $format['noAngle'] : null;
        $dash = isset($format['dash']) ? $format['dash'] : false;
        $dashStep = isset($format['dashStep']) ? $format['dashStep'] : 4;
        $dashR = isset($format['dashR']) ? $format['dashR'] : 0;
        $dashG = isset($format['dashG']) ? $format['dashG'] : 0;
        $dashB = isset($format['dashB']) ? $format['dashB'] : 0;
        $noBorder = isset($format['noBorder']) ? $format['noBorder'] : false;
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
                $x1 + $this->shadowX,
                $y1 + $this->shadowY,
                $x2 + $this->shadowX,
                $y2 + $this->shadowY,
                [
                    'r' => $this->shadowR,
                    'g' => $this->shadowG,
                    'b' => $this->shadowB,
                    'alpha' => $this->shadowA,
                    'ticks' => $ticks,
                    'noAngle' => $noAngle
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
                $x1,
                $y1,
                $x2,
                $y2,
                [
                    'r' => $borderR,
                    'g' => $borderG,
                    'b' => $borderB,
                    'alpha' => $borderalpha,
                    'ticks' => $ticks,
                    'noAngle' => $noAngle
                ]
            );
        }
        $this->shadow = $restoreShadow;
    }

    /**
     * Draw a rectangular marker of the specified size
     *
     * @param int   $x
     * @param int   $y
     * @param array $format
     */
    public function drawRectangleMarker($x, $y, array $format = []) {
        $size = isset($format['size']) ? $format['size'] : 4;
        $halfSize = floor($size / 2);
        $this->drawFilledRectangle($x - $halfSize, $y - $halfSize, $x + $halfSize, $y + $halfSize, $format);
    }
}
