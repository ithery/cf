<?php

class CReport_Builder_JrXmlToPhpEnum {
    const BOOL_TRUE = 'true';

    const BOOL_FALSE = 'false';

    public static function getLineStyleEnum(string $lineStyle) {
        return lcfirst($lineStyle);
    }

    public static function getSplitTypeEnum(string $splitType) {
        return lcfirst($splitType);
    }

    /**
     * @param string $horizontalAlignment
     *
     * @return string
     */
    public static function getHorizontalAlignmentEnum(string $horizontalAlignment) {
        return lcfirst($horizontalAlignment);
    }

    /**
     * @param string $verticalAlignment
     *
     * @return string
     */
    public static function getVerticalAlignmentEnum(string $verticalAlignment) {
        return lcfirst($verticalAlignment);
    }

    /**
     * @param string $textAlignment
     *
     * @return string
     */
    public static function getTextAlignmentEnum(string $textAlignment) {
        return lcfirst($textAlignment);
    }

    /**
     * @param string $scaleImage
     *
     * @return string
     */
    public static function getScaleImageEnum(string $scaleImage) {
        return lcfirst($scaleImage);
    }

    /**
     * @param string $scaleImage
     *
     * @return string
     */
    public static function getResetTypeEnum(string $resetType) {
        return lcfirst($resetType);
    }

    /**
     * @param string $scaleImage
     *
     * @return string
     */
    public static function getCalculationEnum(string $calculation) {
        return lcfirst($calculation);
    }

    public static function getBoolEnum($bool) {
        return $bool == self::BOOL_TRUE ? true : false;
    }

    /**
     * @param string      $javaDataType
     * @param null|string $default
     *
     * @return string
     */
    public static function getPhpDataTypeEnum(string $javaDataType, $default = 'mixed') {
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
}
