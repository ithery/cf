<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElastic_DataType {

    protected static $numericTypes = array(
        'long' => 'Long',
        'integer' => 'Integer',
        'short' => 'Short',
        'byte' => 'Byte',
        'double' => 'Double',
        'float' => 'Float',
        'half_float' => 'Half Float',
        'scaled_float' => 'Scaled Float'
    );
    protected static $stringTypes = array(
        'text' => 'Text',
        'keyword' => 'Keyword',
    );
    protected static $dateTypes = array(
        'date' => 'Date',
    );
    protected static $booleanTypes = array(
        'boolean' => 'Boolean',
    );
    protected static $binaryTypes = array(
        'binary' => 'Binary',
    );
    protected static $rangeTypes = array(
        'integer_range' => 'Integer Range',
        'float_range' => 'Float Range',
        'long_range' => 'Long Range',
        'double_range' => 'Double Range',
        'date_range' => 'Date Range',
    );

    public static function getList() {
        $list = self::$stringTypes + self::$numericTypes + self::$dateTypes + self::$binaryTypes + self::$rangeTypes;
        return $list;
    }

}
