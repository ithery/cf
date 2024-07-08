<?php

class CReport_Builder_JrXmlEnum {
    public static function getLineStyleEnum(string $lineStyle) {
        $enumMap = [
            CReport::LINE_STYLE_DASHED => 'Dashed',
            CReport::LINE_STYLE_DOTTED => 'Dotted',
            CReport::LINE_STYLE_DOUBLE => 'Double',
            CReport::LINE_STYLE_SOLID => 'Solid',

        ];

        return carr::get($enumMap, $lineStyle);
    }
}
