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
        'DoMove' => CManager_File_Connector_FileManager_Controller_DoMoveController::class,
        'Download' => CManager_File_Connector_FileManager_Controller_DownloadController::class,
        'Error' => CManager_File_Connector_FileManager_Controller_ErrorController::class,
        'Folder' => CManager_File_Connector_FileManager_Controller_FolderController::class,
        'Index' => CManager_File_Connector_FileManager_Controller_IndexController::class,
        'Item' => CManager_File_Connector_FileManager_Controller_ItemController::class,
        'Move' => CManager_File_Connector_FileManager_Controller_MoveController::class,
        'NewFolder' => CManager_File_Connector_FileManager_Controller_NewFolderController::class,
        'Rename' => CManager_File_Connector_FileManager_Controller_RenameController::class,
        'Resize' => CManager_File_Connector_FileManager_Controller_ResizeController::class,
        'ResizeImage' => CManager_File_Connector_FileManager_Controller_ResizeImageController::class,
        'Upload' => CManager_File_Connector_FileManager_Controller_UploadController::class,
    ];

    public function run($method = null) {
        $controllerClass = $this->getControllerByMethod($method);

        if ($controllerClass == null || !class_exists($controllerClass)) {
            //throw error method not available
            throw new Exception('Controller class for ' . $method . ' not available');
        }


        //$controllerClass = 'CManager_File_Connector_FileManager_Controller_' . $controllerName . 'Controller';
        $controller = new $controllerClass($this);
        return $controller->execute();
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
        //get class name from config, otherwise default

        $controllerClass = $this->config->getConfig('controller.' . $method, $defaultControllerClass);
        return $controllerClass;
    }
}
