<?php

defined('SYSPATH') or die('No direct access allowed.');

use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_UploadController extends CManager_File_Connector_FileManager_AbstractController {
    public function execute() {
        $fm = $this->fm();

        $uploadedFiles = CHTTP::request()->file('upload');
        $errorBag = [];
        $newFilename = null;
        foreach (is_array($uploadedFiles) ? $uploadedFiles : [$uploadedFiles] as $file) {
            try {
                $newFilename = $fm->path()->upload($file);
            } catch (\Exception $e) {
                // clog::error($e->getMessage(), [
                //     'file' => $e->getFile(),
                //     'line' => $e->getLine(),
                //     'trace' => $e->getTraceAsString()
                // ]);
                array_push($errorBag, $e->getMessage());
            }
        }
        if (is_array($uploadedFiles)) {
            if (count($errorBag) > 0) {
                return $this->errorResponse(implode("\n", $errorBag));
            }

            return $this->successResponse();
        } else { // upload via ckeditor 'Upload' tab
            if (is_null($newFilename)) {
                $response = $errorBag[0];
            } else {
                $response = "<script type='text/javascript'>
                    function getUrlParam(paramName) {
                        var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
                        var match = window.location.search.match(reParam);
                        return ( match && match.length > 1 ) ? match[1] : null;
                    }
                    var funcNum = getUrlParam('CKEditorFuncNum');
                    var par = window.parent;
                    var op = window.opener;
                    var o = (par && par.CKEDITOR) ? par : ((op && op.CKEDITOR) ? op : false);
                    if (op) window.close();
                    if (o !== false) o.CKEDITOR.tools.callFunction(funcNum, '" . $fm->path()->setName($newFilename)->url() . "');
                    </script>";
            }
        }

        return is_string($response) ? c::response($response) : c::response()->json($response);
    }
}
