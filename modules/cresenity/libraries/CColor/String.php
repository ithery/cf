<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:16:57 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CColor_String {

    use CColor_Trait_OptionsTrait,
        CColor_Trait_ColorInfoTrait;

    protected $string;
    protected $int32;

    public function __construct($string, $options = array()) {
        $this->string = $string;
        $this->int32 = hexdec(substr(md5($this->string), 0, 8));
        $this->options = array();
    }

    private function pickHue() {

        return $this->getModulo(array(0, 255));
    }

    private function pickSaturation($h) {
        if ($this->getOption('hue') === 'monochrome') {
            return 0;
        }
        if ($this->getOption('luminosity') === 'random') {
            return $this->getModulo(array(0, 100));
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
        return $this->getModulo($range);
    }

    /**
     * 
     * @return CColor_Format_Type_Hex
     */
    public function toHex() {
        $hsv = $this->toHsv();
        return $hsv->toHex();
    }

    /**
     * 
     * @return \CColor_Format_Type_Hsv
     */
    public function toHsv() {
        $h = $this->pickHue();
        $s = $this->pickSaturation($h);
        $v = $this->pickBrightness($h, $s);

        return new CColor_Format_Type_Hsv(compact('h', 's', 'v'));
    }

    public function getModulo($range) {
        if($range[1] - $range[0] == 0) {
            return 0;
        }
        return $range[0] + ($this->int32 % ($range[1] - $range[0]));
    }

}
