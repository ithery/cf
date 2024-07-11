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

    public static function getSplitTypeEnum(string $splitType) {
        $enumMap = [
            CReport::SPLIT_TYPE_IMMEDIATE => 'Immediate',
            CReport::SPLIT_TYPE_PREVENT => 'Prevent',
            CReport::SPLIT_TYPE_STRETCH => 'Stretch',

        ];

        return carr::get($enumMap, $splitType);
    }

    /**
     * @param string $horizontalAlignment
     *
     * @return string
     */
    public static function getHorizontalAlignmentEnum(string $horizontalAlignment) {
        return ucfirst($horizontalAlignment);
    }

    /**
     * @param string $verticalAlignment
     *
     * @return string
     */
    public static function getVerticalAlignmentEnum(string $verticalAlignment) {
        return ucfirst($verticalAlignment);
    }

    /**
     * @param string $textAlignment
     *
     * @return string
     */
    public static function getTextAlignmentEnum(string $textAlignment) {
        return ucfirst($textAlignment);
    }

    /**
     * @param string $scaleImage
     *
     * @return string
     */
    public static function getScaleImageEnum(string $scaleImage) {
        return ucfirst($scaleImage);
    }

    public static function getBoolEnum($bool) {
        if (is_bool($bool)) {
            return $bool ? self::BOOL_TRUE : self::BOOL_FALSE;
        }
        if (is_string($bool)) {
            return $bool == self::BOOL_TRUE ? self::BOOL_TRUE : self::BOOL_FALSE;
        }

        return (bool) $bool ? self::BOOL_TRUE : self::BOOL_FALSE;
    }

    /**
     * @param string      $javaDataType
     * @param null|string $default
     *
     * @return string
     */
    public function getPhpDataTypeEnum(string $javaDataType, $default = 'mixed') {
        $javaToPHPTypeMap = [
            'java.lang.Object' => CReport::DATA_TYPE_MIXED,
            'java.lang.String' => CReport::DATA_TYPE_STRING,
            'java.lang.Integer' => CReport::DATA_TYPE_INT,
            'java.lang.Long' => CReport::DATA_TYPE_INT,
            'java.lang.Short' => CReport::DATA_TYPE_INT,
            'java.lang.Double' => CReport::DATA_TYPE_FLOAT,
            'java.lang.Float' => CReport::DATA_TYPE_FLOAT,
            'java.lang.Boolean' => CReport::DATA_TYPE_FLOAT,
            'java.util.Date' => CReport::DATA_TYPE_DATETIME,
            'java.sql.Timestamp' => CReport::DATA_TYPE_DATETIME,
            'java.sql.Time' => CReport::DATA_TYPE_DATETIME,
        ];

        return carr::get($javaToPHPTypeMap, $javaDataType, $default);
    }

    /**
     * @param string      $phpDataType
     * @param null|string $default
     *
     * @return string
     */
    public function getJavaDataTypeEnum(string $phpDataType, $default = 'java.lang.Object') {
        $phpToJavaTypeMap = [
            CReport::DATA_TYPE_STRING => 'java.lang.String',
            CReport::DATA_TYPE_INT => 'java.lang.Integer',
            CReport::DATA_TYPE_FLOAT => 'java.lang.Double', // Assuming using Double for float, can be adjusted
            CReport::DATA_TYPE_BOOL => 'java.lang.Boolean',
            CReport::DATA_TYPE_DATETIME => 'java.util.Date',
            CReport::DATA_TYPE_MIXED => 'java.lang.Object',
            // Add other mappings as needed
        ];

        return carr::get($phpToJavaTypeMap, $phpDataType, $default);
    }
}
