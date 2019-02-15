<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:04:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CColor_Random {

    protected $options = array();
    static public $dictionary = array(
        'monochrome' => array(
            'bounds' => array(array(0, 0), array(100, 0)),
            'h' => NULL,
            's' => array(0, 100)
        ),
        'red' => array(
            'bounds' => array(array(20, 100), array(30, 92), array(40, 89), array(50, 85), array(60, 78), array(70, 70), array(80, 60), array(90, 55), array(100, 50)),
            'h' => array(-26, 18),
            's' => array(20, 100)
        ),
        'orange' => array(
            'bounds' => array(array(20, 100), array(30, 93), array(40, 88), array(50, 86), array(60, 85), array(70, 70), array(100, 70)),
            'h' => array(19, 46),
            's' => array(20, 100)
        ),
        'yellow' => array(
            'bounds' => array(array(25, 100), array(40, 94), array(50, 89), array(60, 86), array(70, 84), array(80, 82), array(90, 80), array(100, 75)),
            'h' => array(47, 62),
            's' => array(25, 100)
        ),
        'green' => array(
            'bounds' => array(array(30, 100), array(40, 90), array(50, 85), array(60, 81), array(70, 74), array(80, 64), array(90, 50), array(100, 40)),
            'h' => array(63, 178),
            's' => array(30, 100)
        ),
        'blue' => array(
            'bounds' => array(array(20, 100), array(30, 86), array(40, 80), array(50, 74), array(60, 60), array(70, 52), array(80, 44), array(90, 39), array(100, 35)),
            'h' => array(179, 257),
            's' => array(20, 100)
        ),
        'purple' => array(
            'bounds' => array(array(20, 100), array(30, 87), array(40, 79), array(50, 70), array(60, 65), array(70, 59), array(80, 52), array(90, 45), array(100, 42)),
            'h' => array(258, 282),
            's' => array(20, 100)
        ),
        'pink' => array(
            'bounds' => array(array(20, 100), array(30, 90), array(40, 86), array(60, 84), array(80, 80), array(90, 75), array(100, 73)),
            'h' => array(283, 334),
            's' => array(20, 100)
        )
    );

    public function __construct($options) {
        $optionsDefault = array(
            'format' => 'hex',
        );
        $this->options = array_merge($optionsDefault, $options);
    }

    public function one() {
        $h = $this->pickHue();
        $s = $this->pickSaturation($h);
        $v = $this->pickBrightness($h, $s);
        return self::format(compact('h', 's', 'v'), @$options['format']);
    }

    static public function many($count) {
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

    static private function pickHue() {
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

    static private function pickSaturation($h) {
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
                } else if (isset($this->dictionary[$hue])) {
                    $ranges[] = $this->dictionary[$hue]['h'];
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

    static private function getMinimumBrightness($h, $s) {
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

    private function getColorInfo($h) {
        // Maps red colors to make picking hue easier
        if ($h >= 334 && $h <= 360) {
            $h -= 360;
        }
        foreach ($this->dictionary as $color) {
            if ($color['h'] !== null && $h >= $color['h'][0] && $h <= $color['h'][1]) {
                return $color;
            }
        }
    }

    private function rand($bounds, $options) {

        if ($this->haveOption('prng')) {
            $prng = $this->getOption('prng');
            return $prng($bounds[0], $bounds[1]);
        } else {
            return mt_rand($bounds[0], $bounds[1]);
        }
    }

    public function getOption($key) {
        return carr::get($this->option, $key);
    }

    public function setOption($key, $value) {
        $this->option[$key] = $value;
        return $this;
    }

    public function haveOption($key) {
        return $this->getOption($key) !== null;
    }

}
