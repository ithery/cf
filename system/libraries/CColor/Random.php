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

    /**
     * @return CColor_Format_Hex
     */
    public function toHex() {
        $hsv = $this->toHsv();

        return $hsv->toHex();
    }

    /**
     * @return CColor_Format_Rgb
     */
    public function toRgb() {
        $hsv = $this->toHsv();

        return $hsv->toRgb();
    }

    /**
     * @return CColor_Format_Rgba
     */
    public function toRgba() {
        $hsv = $this->toHsv();

        return $hsv->toRgba();
    }

    /**
     * @return CColor_Format_Hsl
     */
    public function toHsl() {
        $hsv = $this->toHsv();

        return $hsv->toHsl();
    }

    /**
     * @return \CColor_Format_Hsv
     */
    public function toHsv() {
        $h = (int) $this->pickHue();
        $s = (int) $this->pickSaturation($h);
        $v = (int) $this->pickBrightness($h, $s);

        return new CColor_Format_Hsv($h . ',' . $s . ',' . $v);
    }
}
