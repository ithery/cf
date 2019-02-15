<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:04:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CColor_Random {

    use CColor_Trait_OptionsTrait,
        CColor_Trait_ColorInfoTrait;

    public function __construct($options = array()) {
        $optionsDefault = array(
            'format' => 'hex',
        );
        $this->options = array_merge($optionsDefault, $options);
    }

    public function one() {
        $h = $this->pickHue();
        $s = $this->pickSaturation($h);
        $v = $this->pickBrightness($h, $s);
        return $this->formatType(compact('h', 's', 'v'), $this->getOption('format'));
    }

    public function many($count) {
        $colors = array();
        for ($i = 0; $i < $count; $i++) {
            $colors[] = self::one();
        }
        return $colors;
    }

    public function formatType($hsvArray, $format = 'hex') {
        $hsv = new CColor_Format_Type_Hsv($hsvArray);
        switch ($format) {
            case 'hsv':
                return $hsv;
            case 'hsl':
                return $hsv->toHsl();
            case 'hslCss':

                $hslValue = $hsv->toHsl()->value;
                return 'hsl(' . $hsl['h'] . ',' . $hsl['s'] . '%,' . $hsl['l'] . '%)';
            case 'rgb':
                return $hsv->toRgb();
            case 'rgbCss':
                return 'rgb(' . implode(',', $hsv->toRgb()) . ')';
            case 'hex':
            default:
                return $hsv->toHex();
        }
    }

    private function pickHue() {
        $range = $this->getHueRange();
        if (empty($range)) {
            return 0;
        }
        $hue = $this->rand($range);
        // Instead of storing red as two separate ranges,
        // we group them, using negative numbers
        if ($hue < 0) {
            $hue = 360 + $hue;
        }
        return $hue;
    }

    private function pickSaturation($h) {
        if ($this->getOption('hue') === 'monochrome') {
            return 0;
        }
        if ($this->getOption('luminosity') === 'random') {
            return $this->rand(array(0, 100));
        }
        $colorInfo = $this->getColorInfo($h);
        $range = $colorInfo['s'];
        if ($this->haveOption('luminosity')) {
            switch ($this->getOption('luminosity')) {
                case 'bright':
                    $range[0] = 55;
                    break;
                case 'dark':
                    $range[0] = $range[1] - 10;
                    break;
                case 'light':
                    $range[1] = 55;
                    break;
            }
        }

        return $this->rand($range);
    }

    private function pickBrightness($h, $s) {
        if ($this->getOption('luminosity') === 'random') {
            $range = array(0, 100);
        } else {
            $range = array($this->getMinimumBrightness($h, $s), 100);
            if ($this->haveOption('luminosity')) {
                switch ($this->getOption('luminosity')) {
                    case 'dark':
                        $range[1] = $range[0] + 20;
                        break;
                    case 'light':
                        $range[0] = ($range[1] + $range[0]) / 2;
                        break;
                }
            }
        }
        return $this->rand($range);
    }

    private function getHueRange() {
        $ranges = array();
        if ($this->haveOption('hue')) {
            if (!is_array($this->getOption('hue'))) {
                $this->setOption('hue', array($this->getOption('hue')));
            }
            foreach ($this->getOption('hue') as $hue) {
                if ($hue === 'random') {
                    $ranges[] = array(0, 360);
                } else if (isset(self::$dictionary[$hue])) {
                    $ranges[] = self::$dictionary[$hue]['h'];
                } else if (is_numeric($hue)) {
                    $hue = intval($hue);
                    if ($hue <= 360 && $hue >= 0) {
                        $ranges[] = array($hue, $hue);
                    }
                }
            }
        }
        if (($l = count($ranges)) === 0) {
            return array(0, 360);
        } else if ($l === 1) {
            return $ranges[0];
        } else {
            return $ranges[$this->rand(array(0, $l - 1))];
        }
    }

    private function getMinimumBrightness($h, $s) {
        $colorInfo = $this->getColorInfo($h);
        $bounds = $colorInfo['bounds'];
        for ($i = 0, $l = count($bounds); $i < $l - 1; $i++) {
            $s1 = $bounds[$i][0];
            $v1 = $bounds[$i][1];
            $s2 = $bounds[$i + 1][0];
            $v2 = $bounds[$i + 1][1];
            if ($s >= $s1 && $s <= $s2) {
                $m = ($v2 - $v1) / ($s2 - $s1);
                $b = $v1 - $m * $s1;
                return $m * $s + $b;
            }
        }
        return 0;
    }

    private function rand($bounds) {

        if ($this->haveOption('prng')) {
            $prng = $this->getOption('prng');
            return $prng($bounds[0], $bounds[1]);
        } else {
            return mt_rand($bounds[0], $bounds[1]);
        }
    }

}
