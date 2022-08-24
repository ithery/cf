<?php

class CColor_Format_Hsv extends CColor_FormatAbstract {
    use CColor_Trait_HsTrait;

    /**
     * @var int
     */
    protected $value;

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize($color) {
        return list($this->hue, $this->saturation, $this->value) = explode(',', $color);
    }

    /**
     * @return array
     */
    public function values() {
        return $this->getValues();
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hex
     */
    public function toHex() {
        return $this->toRgb()->toHex();
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hexa
     */
    public function toHexa() {
        return $this->toRgb()->toHex()->toHexa();
    }

    /**
     * Source: https://en.wikipedia.org/wiki/HSL_and_HSV#Interconversion.
     *
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsl
     */
    public function toHsl() {
        list($h, $s, $v) = $this->valuesInUnitInterval();
        $l = $v * (1 - $s / 2);
        $m = min($l, 1 - $l);
        $s = $l && $l < 1 ? ($v - $l) / $m : 0;
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
     * @return \CColor_Format_Hsv
     */
    public function toHsv() {
        return $this;
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Rgb
     */
    public function toRgb() {
        list($h, $s, $v) = $this->valuesInUnitInterval();
        $i = floor($h * 6);
        $f = $h * 6 - $i;
        $p = $v * (1 - $s);
        $q = $v * (1 - $f * $s);
        $t = $v * (1 - (1 - $f) * $s);
        switch ($i % 6) {
            case 0:
                list($r, $g, $b) = [$v, $t, $p];

                break;
            case 1:
                list($r, $g, $b) = [$q, $v, $p];

                break;
            case 2:
                list($r, $g, $b) = [$p, $v, $t];

                break;
            case 3:
                list($r, $g, $b) = [$p, $q, $v];

                break;
            case 4:
                list($r, $g, $b) = [$t, $p, $v];

                break;
            case 5:
                list($r, $g, $b) = [$v, $p, $q];

                break;
        }
        $code = implode(',', [round($r * 255), round($g * 255), round($b * 255)]);

        return new CColor_Format_Rgb($code);
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Rgba
     */
    public function toRgba() {
        return $this->toRgb()->toRgba();
    }

    /**
     * @return string
     */
    public function __toString() {
        return 'hsv(' . implode(',', $this->values()) . ')';
    }

    /**
     * @param int|string $value
     *
     * @return int|$this
     */
    public function value($value = null) {
        if (is_numeric($value)) {
            $this->value = $value >= 0 && $value <= 100 ? $value : $this->value;

            return $this;
        }

        return (int) $this->value;
    }
}
