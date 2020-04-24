<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 27, 2019, 2:49:40 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_File {

    public static function createConnector($engineName, $options) {
        $className = 'CManager_File_Connector_' . $engineName;
        $configClassName = 'CManager_File_Config_' . $engineName;
        $config = new $configClassName($options);
        $connector = new $className($config);
        return $connector;
    }

}
