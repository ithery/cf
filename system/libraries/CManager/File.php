<?php

defined('SYSPATH') or die('No direct access allowed.');

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
