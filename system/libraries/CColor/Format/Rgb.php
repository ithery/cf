<?php

class CColor_Format_Rgb extends CColor_FormatAbstract {
    use CColor_Trait_RgbTrait;

    /**
     * @var bool
     */
    protected $castsInteger = true;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate($code) {
        $color = str_replace(['rgb', '(', ')', ' '], '', CColor_DefinedColor::find($code, 1));
        if (preg_match('/^(\d{1,3}),(\d{1,3}),(\d{1,3})$/', $color, $matches)) {
            if ($matches[1] > 255 || $matches[2] > 255 || $matches[3] > 255) {
                return false;
            }

            return $color;
        }

        return false;
    }

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize($color) {
        return list($this->red, $this->green, $this->blue) = explode(',', $color);
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hex
     */
    public function toHex() {
        $code = sprintf('%02x%02x%02x', $this->red(), $this->green(), $this->blue());

        return new CColor_Format_Hex($code);
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hexa
     */
    public function toHexa() {
        return $this->toHex()->toHexa();
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsl
     */
    public function toHsl() {
        list($r, $g, $b, $min, $max) = $this->getHValues();
        $l = ($max + $min) / 2;
        if ($max === $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            $h = $this->getH($max, $r, $g, $b, $d);
        }
        $code = implode(',', [round($h * 360), round($s * 100), round($l * 100)]);

        return new CColor_Format_Hsl($code);
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsla
     */
    public function toHsla() {
        return $this->toHsl()->toHsla();
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsv
     */
    public function toHsv() {
        list($r, $g, $b, $min, $max) = $this->getHValues();
        $v = $max;
        $d = $max - $min;
        $s = $max === 0 ? 0 : $d / $max;
        $h = $max === $min ? 0 : $this->getH($max, $r, $g, $b, $d);
        $code = implode(',', [round($h * 360), round($s * 100), round($v * 100)]);

        return new CColor_Format_Hsv($code);
    }

    /**
     * @return \CColor_Format_Rgb
     */
    public function toRgb() {
        return $this;
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Rgba
     */
    public function toRgba() {
        return new CColor_Format_Rgba(implode(',', array_merge($this->values(), ['1.0'])));
    }

    /**
     * @return string
     */
    public function __toString() {
        return 'rgb(' . implode(',', $this->values()) . ')';
    }

    /**
     * @param float $max
     * @param float $r
     * @param float $g
     * @param float $b
     * @param float $d
     *
     * @return float
     */
    private function getH($max, $r, $g, $b, $d) {
        switch ($max) {
            case $r:
                $h = ($g - $b) / $d + ($g < $b ? 6 : 0);

                break;
            case $g:
                $h = ($b - $r) / $d + 2;

                break;
            case $b:
                $h = ($r - $g) / $d + 4;

                break;
            default:
                $h = $max;

                break;
        }

        return $h / 6;
    }

    /**
     * @return array
     */
    private function getHValues() {
        list($r, $g, $b) = $values = array_map(function ($value) {
            return $value / 255;
        }, $this->values());
        list($min, $max) = [min($values), max($values)];

        return [$r, $g, $b, $min, $max];
    }
}
