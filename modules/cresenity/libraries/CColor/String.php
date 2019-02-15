<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 3:16:57 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CColor_String {

    use CColor_Trait_OptionsTrait;

    protected $string;

    public function __construct($string, $options = array()) {
        $this->string = $string;
        $this->options = array();
    }

    private function toInt32() {
        $hash = md5($this->string);
        return unpack("L", substr($hash, 0, 4));
    }

    /**
     * 
     * @return CColor_Format_Type_Hex
     */
    public function getHex() {
        $hsv = $this->getHsv();
        return $hsv->toHex();
    }

    /**
     * 
     * @return \CColor_Format_Type_Hsv
     */
    public function getHsv() {
        $number = $this->toInt32();
        $h = $number % 256;
        $s = $number % 100;
        $v = $number % 100;
        return new CColor_Format_Type_Hsv(compact('h', 's', 'v'));
    }

}
