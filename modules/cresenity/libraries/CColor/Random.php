<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 3:04:48 AM
 */
class CColor_Random {
    use CColor_Trait_OptionsTrait,
        CColor_Trait_ColorInfoTrait;

    public function __construct($options = []) {
        $optionsDefault = [
            'format' => 'hex',
        ];
        $this->options = array_merge($optionsDefault, $options);
    }

    public function one() {
        $h = $this->pickHue();
        $s = $this->pickSaturation($h);
        $v = $this->pickBrightness($h, $s);
        return $this->formatType(compact('h', 's', 'v'), $this->getOption('format'));
    }

    public function many($count) {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $this->one();
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
                $hsl = $hsv->toHsl()->value;
                return 'hsl(' . $hsl['h'] . ',' . $hsl['s'] . '%,' . $hsl['l'] . '%)';
            case 'rgb':
                return $hsv->toRgb();
            case 'rgbCss':
                return 'rgb(' . implode(',', $hsv->toRgb()->value()) . ')';
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
            return $this->rand([0, 100]);
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
            $range = [0, 100];
        } else {
            $range = [$this->getMinimumBrightness($h, $s), 100];
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
        $ranges = [];
        if ($this->haveOption('hue')) {
            if (!is_array($this->getOption('hue'))) {
                $this->setOption('hue', [$this->getOption('hue')]);
            }
            foreach ($this->getOption('hue') as $hue) {
                if ($hue === 'random') {
                    $ranges[] = [0, 360];
                } elseif (isset(self::$dictionary[$hue])) {
                    $ranges[] = self::$dictionary[$hue]['h'];
                } elseif (is_numeric($hue)) {
                    $hue = intval($hue);
                    if ($hue <= 360 && $hue >= 0) {
                        $ranges[] = [$hue, $hue];
                    }
                }
            }
        }
        if (($l = count($ranges)) === 0) {
            return [0, 360];
        } elseif ($l === 1) {
            return $ranges[0];
        } else {
            return $ranges[$this->rand([0, $l - 1])];
        }
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
