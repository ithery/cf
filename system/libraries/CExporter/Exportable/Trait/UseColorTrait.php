<?php

trait CExporter_Exportable_Trait_UseColorTrait {
    use CColor_Trait_AlphaTrait;
    public function toSpreadsheetColor($color) {
        if (!($color instanceof CColor_FormatAbstract)) {
            $color = CColor::create($color);
        }
        list($r, $g, $b, $a) = $color->toHexa()->values();

        return implode('', [$this->alphaFloatToHex($a),$r, $g, $b]);
    }
}
