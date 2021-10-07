<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 2:32:44 AM
 */
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_ErrorController extends CManager_File_Connector_FileManager_AbstractController {
    public function execute() {
        $fm = $this->fm();
        $arrErrors = [];
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            array_push($arrErrors, c::trans('filemanager.message-extension_not_found'));
        }
        if (!extension_loaded('exif')) {
            array_push($arrErrors, 'EXIF extension not found.');
        }
        if (!extension_loaded('fileinfo')) {
            array_push($arrErrors, 'Fileinfo extension not found.');
        }
        $mine_config_key = 'folder_categories.'
                . $fm->currentFmType()
                . '.valid_mime';
        if (!is_array($fm->config($mine_config_key))) {
            array_push($arrErrors, 'Config : ' . $mine_config_key . ' is not a valid array.');
        }
        return c::response()->json($arrErrors);
    }
}
