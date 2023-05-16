<?php

trait CImage_Chart_Trait_UseColorTrait {
    public function toRgba(CColor_FormatAbstract $color) {
        list($r,$g,$b,$a) = $color->toRgba()->values();
        $code = sprintf('%02x%02x%02x%02x', $r, $g, $b, $a*255);
        return $code;
    }

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
