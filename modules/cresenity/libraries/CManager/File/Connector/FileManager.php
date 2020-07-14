<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 28, 2019, 1:50:58 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_File_Connector_FileManager extends CManager_File_ConnectorAbstract {

    public function run($method = null) {
        $controllerName = ucfirst($method);
        $controllerClass = 'CManager_File_Connector_FileManager_Controller_' . $controllerName . 'Controller';
        $controller = new $controllerClass($this);
        $controller->execute();
    }


}
