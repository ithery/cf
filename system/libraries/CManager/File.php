<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 27, 2019, 2:49:40 PM
 */
class CManager_File {
    /**
     * @param string $engineName
     * @param array  $options
     *
     * @return CManager_File_ConnectorAbstract
     *
     * @see CManager_File_Connector_FileManager
     * @see CManager_File_Config_FileManager
     */
    public static function createConnector($engineName, $options) {
        $className = 'CManager_File_Connector_' . $engineName;
        $configClassName = 'CManager_File_Config_' . $engineName;
        $config = new $configClassName($options);
        $connector = new $className($config);

        return $connector;
    }
}
