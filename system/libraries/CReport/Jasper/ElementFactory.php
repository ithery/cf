<?php

class CReport_Jasper_ElementFactory {
    public static $classMap = [
        'title' => CReport_Jasper_Element_Title::class,
        'band' => CReport_Jasper_Element_Band::class,
        'image' => CReport_Jasper_Element_Image::class,
        'staticText' => CReport_Jasper_Element_StaticText::class,
    ];

    public static $ignoredElement = [
        'defaultFont',
        'reportElement',
        'imageExpression',
        'textElement',
        'text',
    ];

    public static function getClassName($obj) {
        return carr::get(self::$classMap, $obj, null);
    }

    public static function isIgnore($obj) {
        return in_array($obj, self::$ignoredElement);
    }
}
