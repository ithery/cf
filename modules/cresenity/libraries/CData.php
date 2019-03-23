<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 12:54:24 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CData {

    /**
     * 
     * @return \CData_Provider_Sql
     */
    public static function createSqlProvider() {
        return new CData_Provider_Sql();
    }

    /**
     * 
     * @return \CData_Provider_Array
     */
    public static function createArrayProvider() {
        return new CData_Provider_Array();
    }

    /**
     * 
     * @return \CData_Provider_Elastic
     */
    public static function createElasticProvider() {
        return new CData_Provider_Elastic();
    }

    /**
     * 
     * @return \CData_Provider_Model
     */
    public static function createModelProvider() {
        return new CData_Provider_Model();
    }

    /**
     * 
     * @return \CData_Provider_Null
     */
    public static function createNullProvider() {
        return new CData_Provider_Null();
    }

}
