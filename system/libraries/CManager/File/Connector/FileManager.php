<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 28, 2019, 1:50:58 AM
 */
class CManager_File_Connector_FileManager extends CManager_File_ConnectorAbstract {
    protected static $defaultMethodControllerMapping = [
        'Crop' => CManager_File_Connector_FileManager_Controller_CropController::class,
        'CropImage' => CManager_File_Connector_FileManager_Controller_CropImageController::class,
        'CropNewImage' => CManager_File_Connector_FileManager_Controller_CropNewImageController::class,
        'Delete' => CManager_File_Connector_FileManager_Controller_DeleteController::class,
        'Item' => CManager_File_Connector_FileManager_Controller_ItemController::class,
        //'Folder' => CManager_File_Connector_FileManager_Controller_FolderController::class,
    ];

    public function run($method = null) {
        $controllerClass = $this->getControllerByMethod($method);

        if ($controllerClass == null || !class_exists($controllerClass)) {
            //throw error method not available
            throw new Exception('Controller class for ' . $method . ' not available');
        }


        //$controllerClass = 'CManager_File_Connector_FileManager_Controller_' . $controllerName . 'Controller';
        $controller = new $controllerClass($this);
        $controller->execute();
    }

    protected function getControllerByMethod($method) {
        $method = ucfirst($method);
        $availableMethod = array_keys(static::$defaultMethodControllerMapping);

        //check method is valid
        $defaultControllerClass = carr::get(static::$defaultMethodControllerMapping, $method);
        if (!in_array($method, $availableMethod)) {
            //throw error method not available
            $defaultControllerClass = 'CManager_File_Connector_FileManager_Controller_' . $method . 'Controller';
        }

        if (!class_exists($defaultControllerClass)) {
            throw new Exception('Method ' . $method . ' not available');
        }
        //get class name from config, otherwise default

        $controllerClass = $this->config->getConfig('controller.' . $method, $defaultControllerClass);
        return $controllerClass;
    }
}
