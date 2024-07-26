<?php

class CReport_Builder_ElementFactory {
    public static $classMap = [
        'title' => CReport_Builder_Element_Title::class,
        'image' => CReport_Builder_Element_Image::class,
        'staticText' => CReport_Builder_Element_StaticText::class,
        'pageHeader' => CReport_Builder_Element_PageHeader::class,
        'pageFooter' => CReport_Builder_Element_PageFooter::class,
        'frame' => CReport_Builder_Element_Frame::class,
        'columnHeader' => CReport_Builder_Element_ColumnHeader::class,
        'detail' => CReport_Builder_Element_Detail::class,
        'textField' => CReport_Builder_Element_TextField::class,
        'group' => CReport_Builder_Element_Group::class,
        'groupFooter' => CReport_Builder_Element_GroupFooter::class,
        'groupHeader' => CReport_Builder_Element_GroupHeader::class,
        'summary' => CReport_Builder_Element_Summary::class,
        'variable' => CReport_Builder_Element_Variable::class,
        'queryString' => CReport_Builder_Element_QueryString::class,
        'line' => CReport_Builder_Element_Line::class,
        'rectangle' => CReport_Builder_Element_Rectangle::class,
        'style' => CReport_Builder_Element_Style::class,
        'styles' => CReport_Builder_Element_Style::class,

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
        'variableExpression',
        'initialValueExpression',
        'band',
        'groupExpression',
    ];

    public static function getClassName($obj) {
        return carr::get(self::$classMap, $obj, null);
    }

    public static function isIgnore($obj) {
        return in_array($obj, self::$ignoredElement);
    }

    public static function createElementFromXml($tag, SimpleXMLElement $xml) {
        $className = carr::get(self::$classMap, $tag);
        if ($className == null) {
            throw new Exception('Class for ' . $tag . ' is not found in builder');
        }

        return $className::fromXml($xml);
    }
}
