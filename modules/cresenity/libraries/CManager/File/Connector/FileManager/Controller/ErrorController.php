<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 2:32:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_ErrorController extends CManager_File_Connector_FileManager_Controller_BaseController {

    public function execute() {
        $fm = new FM();
        $arr_errors = [];
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            array_push($arr_errors, trans('laravel-filemanager::lfm.message-extension_not_found'));
        }
        if (!extension_loaded('exif')) {
            array_push($arr_errors, 'EXIF extension not found.');
        }
        if (!extension_loaded('fileinfo')) {
            array_push($arr_errors, 'Fileinfo extension not found.');
        }
        $mine_config_key = 'folder_categories.'
                . $fm->currentFmType()
                . '.valid_mime';
        if (!is_array($fm->config($mine_config_key))) {
            array_push($arr_errors, 'Config : ' . $mine_config_key . ' is not a valid array.');
        }
        echo json_encode($arr_errors);
    }

}
