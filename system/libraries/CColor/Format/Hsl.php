<?php

class CColor_Format_Hsl extends CColor_FormatAbstract {
    use CColor_Trait_HslTrait;

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize($color) {
        return list($this->hue, $this->saturation, $this->lightness) = explode(',', $color);
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
        return $this->toHex()->toHexa();
    }

    /**
     * @return \CColor_Format_Hsl
     */
    public function toHsl() {
        return $this;
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsla
     */
    public function toHsla() {
        return new CColor_Format_Hsla(implode(',', array_merge($this->values(), [1.0])));
    }

    /**
     * Source: https://en.wikipedia.org/wiki/HSL_and_HSV#Interconversion.
     *
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsv
     */
    public function toHsv() {
        list($h, $s, $l) = $this->valuesInUnitInterval();
        $v = $s * min($l, 1 - $l) + $l;
        $s = $v ? 2 * (1 - $l / $v) : 0;
        $code = implode(',', [round($h * 360), round($s * 100), round($v * 100)]);

        return new CColor_Format_Hsv($code);
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Rgb
     */
    public function toRgb() {
        return $this->convertToRgb();
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
        return 'hsl(' . implode(',', $this->values()) . ')';
    }
}
