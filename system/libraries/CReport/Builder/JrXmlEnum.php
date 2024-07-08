<?php

class CReport_Builder_JrXmlEnum {
    const BOOL_TRUE = 'true';

    const BOOL_FALSE = 'false';

    public static function getLineStyleEnum(string $lineStyle) {
        $enumMap = [
            CReport::LINE_STYLE_DASHED => 'Dashed',
            CReport::LINE_STYLE_DOTTED => 'Dotted',
            CReport::LINE_STYLE_DOUBLE => 'Double',
            CReport::LINE_STYLE_SOLID => 'Solid',

        ];

        return carr::get($enumMap, $lineStyle);
    }

    public static function getBoolEnum(mixed $bool) {
        if (is_bool($bool)) {
            return $bool ? self::BOOL_TRUE : self::BOOL_FALSE;
        }
        if (is_string($bool)) {
            return $bool == self::BOOL_TRUE ? self::BOOL_TRUE : self::BOOL_FALSE;
        }

        return (bool) $bool ? self::BOOL_TRUE : self::BOOL_FALSE;
    }
}
