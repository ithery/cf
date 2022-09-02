<?php

class CColor_Format_Rgba extends CColor_FormatAbstract {
    use CColor_Trait_AlphaTrait, CColor_Trait_RgbTrait;

    /**
     * @var \CColor_Format_Rgb
     */
    protected $background;

    /**
     * @param string $code
     *
     * @return bool|string
     */
    protected function validate($code) {
        $color = str_replace(['rgba', '(', ')', ' '], '', CColor_DefinedColor::find($code, 1));
        if (substr_count($color, ',') === 2) {
            $color = "{$color},1.0";
        }
        $color = $this->fixPrecision($color);
        if (preg_match($this->validationRules(), $color, $matches)) {
            if ($matches[1] > 255 || $matches[2] > 255 || $matches[3] > 255 || $matches[4] > 1) {
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
        $colors = explode(',', $color);
        list($this->red, $this->green, $this->blue) = array_map('intval', $colors);
        $this->alpha = (double) $colors[3];
        $this->background = $this->defaultBackground();

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
     * @throws \CColor_Exception_InvalidColorException
     *
     * @return \CColor_Format_Rgb
     */
    public function toRgb() {
        list($red, $green, $blue) = array_map(function ($attribute) {
            $value = (1 - $this->alpha()) * $this->background->{$attribute}() + $this->alpha() * $this->{$attribute}();

            return floor($value);
        }, ['red', 'green', 'blue']);

        return new CColor_Format_Rgb(implode(',', [$red, $green, $blue]));
    }

    /**
     * @return \CColor_Format_Rgba
     */
    public function toRgba() {
        return $this;
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
        return $this->toRgb()->toHex()->toHexa()->alpha($this->alpha());
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
     * @return string
     */
    public function __toString() {
        return 'rgba(' . implode(',', $this->values()) . ')';
    }

    /**
     * @param \CColor_Format_Rgb $rgb
     *
     * @return $this
     */
    public function background(CColor_Format_Rgb $rgb) {
        $this->background = $rgb;

        return $this;
    }

    /**
     * @return \CColor_Format_Rgb
     */
    protected function defaultBackground() {
        return new CColor_Format_Rgb('255,255,255');
    }
}
