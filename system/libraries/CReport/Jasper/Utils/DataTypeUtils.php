<?php

class CReport_Jasper_Utils_DataTypeUtils {
    private static $javaToPHPTypeMap = [
        'java.lang.String' => 'string',
        'java.lang.Integer' => 'int',
        'java.lang.Long' => 'int',
        'java.lang.Short' => 'int',
        'java.lang.Double' => 'float',
        'java.lang.Float' => 'float',
        'java.lang.Boolean' => 'bool',
        'java.util.Date' => 'DateTime', // Assuming you're using DateTime for date handling in PHP
        'java.sql.Timestamp' => 'DateTime',
        'java.sql.Time' => 'DateTime',
    ];

    private static $phpToJavaTypeMap = [
        'string' => 'java.lang.String',
        'int' => 'java.lang.Integer',
        'float' => 'java.lang.Double', // Assuming using Double for float, can be adjusted
        'bool' => 'java.lang.Boolean',
        'DateTime' => 'java.util.Date',
        // Add other mappings as needed
    ];

    /**
     * @param string      $javaDataType
     * @param null|string $default
     *
     * @return string
     */
    public function getPhpDataType(string $javaDataType, $default = 'mixed') {
        return carr::get(self::$javaToPHPTypeMap, $javaDataType, $default);
    }

    /**
     * @param string      $phpDataType
     * @param null|string $default
     *
     * @return string
     */
    public function getJavaDataType(string $phpDataType, $default = 'java.lang.Object') {
        return carr::get(self::$phpToJavaTypeMap, $phpDataType, $default);
    }
}
