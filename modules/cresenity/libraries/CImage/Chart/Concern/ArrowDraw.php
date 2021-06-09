<?php
use CImage_Chart_Constant as Constant;

trait CImage_Chart_Concern_ArrowDraw {
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
}
