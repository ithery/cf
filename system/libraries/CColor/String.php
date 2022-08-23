<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 3:16:57 AM
 */
class CColor_String {
    use CColor_Trait_OptionsTrait,
        CColor_Trait_ColorInfoTrait;

    protected $string;

    protected $int32;

    public function __construct($string, $options = []) {
        $this->string = $string;
        $this->int32 = hexdec(substr(md5($this->string), 0, 8));
        $this->options = $options;
    }

    private function pickHue() {
        return $this->getModulo([0, 255]);
    }

    private function pickSaturation($h) {
        if ($this->getOption('hue') === 'monochrome') {
            return 0;
        }
        if ($this->getOption('luminosity') === 'random') {
            return $this->getModulo([0, 100]);
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

        return $this->getModulo($range);
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

        return $this->getModulo($range);
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

    public function getModulo($range) {
        if (floor(($range[1] - $range[0])) == 0) {
            return 0;
        }

        return $range[0] + ($this->int32 % ($range[1] - $range[0]));
    }
}
