<?php

class CReport_Jasper_ElementFactory {
    public static $classMap = [
        'title' => CReport_Jasper_Element_Title::class,
        'band' => CReport_Jasper_Element_Band::class,
        'image' => CReport_Jasper_Element_Image::class,
        'staticText' => CReport_Jasper_Element_StaticText::class,
        'pageHeader' => CReport_Jasper_Element_PageHeader::class,
        'frame' => CReport_Jasper_Element_Frame::class,
        'columnHeader' => CReport_Jasper_Element_ColumnHeader::class,
        'detail' => CReport_Jasper_Element_Detail::class,
        'textField' => CReport_Jasper_Element_TextField::class,
        'groupFooter' => CReport_Jasper_Element_GroupFooter::class,
        'groupHeader' => CReport_Jasper_Element_GroupHeader::class,

    ];

    public static $ignoredElement = [
        'defaultFont',
        'reportElement',
        'imageExpression',
        'textElement',
        'text',
        'box',
        'textFieldExpression',
        'pattern',
        'parameter',
        'variable',
        'variableExpression',
        'initialValueExpression',
        'group',

    ];

    public static function getClassName($obj) {
        return carr::get(self::$classMap, $obj, null);
    }

    public static function isIgnore($obj) {
        return in_array($obj, self::$ignoredElement);
    }
}
