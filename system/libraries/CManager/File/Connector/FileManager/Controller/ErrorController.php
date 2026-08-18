<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_File_Connector_FileManager_Controller_ErrorController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Server/config warnings (missing PHP extensions, bad mime config, ...) shown
     * in the .fm-alerts banner -- not action-result errors, so errCode stays 0.
     *
     * @return \CHTTP_JsonResponse
     */
    public function execute() {
        $fm = $this->fm();
        $arrErrors = [];
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            array_push($arrErrors, c::trans('element/filemanager.message-extension_not_found'));
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

        return $this->successResponse(['messages' => $arrErrors]);
    }
}
