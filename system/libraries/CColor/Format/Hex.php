<?php

class CColor_Format_Hex extends CColor_FormatAbstract {
    use CColor_Trait_RgbTrait;

    /**
     * @param string $code
     *
     * @return string|bool
     */
    protected function validate($code) {
        $color = str_replace('#', '', CColor_DefinedColor::find($code));
        if (strlen($color) === 3) {
            $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
        }

        return preg_match('/^[a-f0-9]{6}$/i', $color) ? $color : false;
    }

    /**
     * @param string $color
     *
     * @return array
     */
    protected function initialize($color) {
        return list($this->red, $this->green, $this->blue) = str_split($color, 2);
    }

    /**
     * @return \CColor_Format_Hex
     */
    public function toHex() {
        return $this;
    }

    /**
     * @return \CColor_Format_Hexa
     */
    public function toHexa() {
        return new CColor_Format_Hexa((string) $this . 'FF');
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
        return $this->toHsl()->toHsla();
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
        $rgb = implode(',', array_map('hexdec', $this->values()));

        return new CColor_Format_Rgb($rgb);
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
        return '#' . implode('', $this->values());
    }
}
