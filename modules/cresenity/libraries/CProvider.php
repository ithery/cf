<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 24, 2019, 1:11:06 AM
 */
class CProvider {
    /**
     * @return \CProvider_Data_Sql
     */
    public static function createSqlDataProvider() {
        return new CProvider_Data_Sql();
    }

    /**
     * @return \CProvider_Data_Array
     */
    public static function createArrayDataProvider() {
        return new CProvider_Data_Array();
    }

    /**
     * @return \CProvider_Data_Elastic
     */
    public static function createElasticDataProvider() {
        return new CProvider_Data_Elastic();
    }

    /**
     * @return \CProvider_Data_Model
     */
    public static function createModelDataProvider() {
        return new CProvider_Data_Model();
    }

    /**
     * @return \CProvider_Data_Null
     */
    public static function createNullDataProvider() {
        return new CProvider_Data_Null();
    }
}
