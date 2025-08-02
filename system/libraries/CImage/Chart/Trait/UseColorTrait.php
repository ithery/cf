<?php

trait CImage_Chart_Trait_UseColorTrait {
    /**
     * Returns a color as an RGBA code in hexadecimal format.
     *
     * @param CColor_FormatAbstract $color The color to convert
     * @return string The color as an RGBA code in hexadecimal format
     */
    public function toRgba(CColor_FormatAbstract $color) {
        list($r,$g,$b,$a) = $color->toRgba()->values();
        $code = sprintf('%02x%02x%02x%02x', $r, $g, $b, $a*255);
        return $code;
    }

    /**
     * Returns an array in the format that HighCharts expects for setting the color of elements.
     *
     * @param CColor_FormatAbstract $color The color to convert
     * @return array A HighCharts-compatible array
     */
    public function toRgbaArray(CColor_FormatAbstract $color) {
        list($r,$g,$b,$a) = $color->toRgba()->values();

        return [
            'r'=>$r,
            'g'=>$g,
            'b'=>$b,
            'alpha'=>$a * 255,
        ];
    }
}
