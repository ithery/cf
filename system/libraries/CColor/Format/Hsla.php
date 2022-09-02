<?php

class CColor_Format_Hsla extends CColor_FormatAbstract {
    use CColor_Trait_AlphaTrait, CColor_Trait_HslTrait;

    /**
     * @param string $code
     *
     * @return false|string
     */
    protected function validate($code) {
        list($class, $index) = property_exists($this, 'lightness') ? ['hsl', 2] : ['hsv', 3];
        $color = str_replace(["{$class}a", '(', ')', ' ', '%'], '', CColor_DefinedColor::find($code, $index));
        if (substr_count($color, ',') === 2) {
            $color = "{$color},1.0";
        }
        $color = $this->fixPrecision($color);
        if (preg_match($this->validationRules(), $color, $matches)) {
            if ($matches[1] > 360 || $matches[2] > 100 || $matches[3] > 100 || $matches[4] > 1) {
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
        list($this->hue, $this->saturation, $this->lightness, $this->alpha) = explode(',', $color);
        $this->alpha = (double) $this->alpha;

        return $this->values();
    }

    /**
     * @return array
     */
    public function values() {
        return array_merge($this->getValues(), [$this->alpha()]);
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsl
     */
    public function toHsl() {
        return $this->toRgba()->toHsl();
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Rgba
     */
    public function toRgba() {
        return $this->convertToRgb()->toRgba()->alpha($this->alpha());
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Rgb
     */
    public function toRgb() {
        return $this->toRgba()->toRgb();
    }

    /**
     * @return \CColor_Format_Hsla
     */
    public function toHsla() {
        return $this;
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsv
     */
    public function toHsv() {
        return $this->toRgba()->toHsv();
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hex
     */
    public function toHex() {
        return $this->toRgba()->toHex();
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hexa
     */
    public function toHexa() {
        return $this->toHex()->toHexa()->alpha($this->alpha());
    }

    /**
     * @return string
     */
    public function __toString() {
        return 'hsla(' . implode(',', $this->values()) . ')';
    }
}
