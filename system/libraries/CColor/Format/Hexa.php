<?php

class CColor_Format_Hexa extends CColor_FormatAbstract {
    use CColor_Trait_AlphaTrait, CColor_Trait_RgbTrait;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate($code) {
        $color = str_replace('#', '', CColor_DefinedColor::find($code));

        return preg_match('/^[a-f0-9]{6}([a-f0-9]{2})?$/i', $color) ? $color : false;
    }

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize($color) {
        list($this->red, $this->green, $this->blue, $this->alpha) = array_merge(str_split($color, 2), ['ff']);
        $this->alpha = $this->alphaHexToFloat($this->alpha ?? 'ff');

        return $this->values();
    }

    /**
     * @return array
     */
    public function values() {
        return [
            $this->red(),
            $this->green(),
            $this->blue(),
            $this->alpha()
        ];
    }

    /**
     * @return \CColor_Format_Hex
     */
    public function toHex() {
        return new CColor_Format_Hex(implode([$this->red(), $this->green(), $this->blue()]));
    }

    /**
     * @return \CColor_Format_Hexa
     */
    public function toHexa() {
        return $this;
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsl
     */
    public function toHsl() {
        return $this->toRgb()->toHsl();
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsla
     */
    public function toHsla() {
        return $this->toHsl()->toHsla()->alpha($this->alpha());
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Hsv
     */
    public function toHsv() {
        return $this->toRgb()->toHsv();
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Rgb
     */
    public function toRgb() {
        $rgb = implode(',', array_map('hexdec', [$this->red(), $this->green(), $this->blue()]));

        return new CColor_Format_Rgb($rgb);
    }

    /**
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Rgba
     */
    public function toRgba() {
        return $this->toRgb()->toRgba()->alpha($this->alpha());
    }

    /**
     * @return string
     */
    public function __toString() {
        list($r, $g, $b, $a) = $this->values();

        return '#' . implode('', [$r, $g, $b, $this->alphaFloatToHex($a)]);
    }
}
