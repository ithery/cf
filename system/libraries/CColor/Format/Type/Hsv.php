<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 3:22:05 AM
 */
class CColor_Format_Type_Hsv extends CColor_Format_TypeAbstract {
    /**
     * @return \CColor_Format_Type_Hex
     */
    public function toHex() {
        $rgb = $this->toRgb();
        $hex = '#';
        foreach ($rgb->value() as $c) {
            $hex .= str_pad(dechex($c), 2, '0', STR_PAD_LEFT);
        }
        return new CColor_Format_Type_Hex($hex);
    }

    /**
     * @return \CColor_Format_Type_Hsl
     */
    public function toHsl() {
        extract($this->value);
        $s /= 100;
        $v /= 100;
        $k = (2 - $s) * $v;

        $hslArray = [
            'h' => $h,
            's' => round($s * $v / ($k < 1 ? $k : 2 - $k), 4) * 100,
            'l' => $k / 2 * 100,
        ];
        return new CColor_Format_Type_Hsl($hslArray);
    }

    /**
     * @return \CColor_Format_Type_Rgb
     */
    public function toRgb() {
        extract($this->value);
        $h /= 360;
        $s /= 100;
        $v /= 100;
        $i = floor($h * 6);
        $f = $h * 6 - $i;
        $m = $v * (1 - $s);
        $n = $v * (1 - $s * $f);
        $k = $v * (1 - $s * (1 - $f));
        $r = 1;
        $g = 1;
        $b = 1;
        switch ($i) {
            case 0:
                list($r, $g, $b) = [$v, $k, $m];
                break;
            case 1:
                list($r, $g, $b) = [$n, $v, $m];
                break;
            case 2:
                list($r, $g, $b) = [$m, $v, $k];
                break;
            case 3:
                list($r, $g, $b) = [$m, $n, $v];
                break;
            case 4:
                list($r, $g, $b) = [$k, $m, $v];
                break;
            case 5:
            case 6:
                list($r, $g, $b) = [$v, $m, $n];
                break;
        }

        $rgbArray = [
            'r' => floor($r * 255),
            'g' => floor($g * 255),
            'b' => floor($b * 255),
        ];
        return new CColor_Format_Type_Rgb($rgbArray);
    }
}
