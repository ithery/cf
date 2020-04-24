<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 5:51:41 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_UploadController extends CManager_File_Connector_FileManager_AbstractController {

    public function execute() {
        $fm = $this->fm();

        $uploaded_files = CHTTP::request()->file('upload');
        $error_bag = [];
        $new_filename = null;
        foreach (is_array($uploaded_files) ? $uploaded_files : [$uploaded_files] as $file) {
            try {
                $new_filename = $fm->path()->upload($file);
            } catch (\Exception $e) {
//                clog::error($e->getMessage(), [
//                    'file' => $e->getFile(),
//                    'line' => $e->getLine(),
//                    'trace' => $e->getTraceAsString()
//                ]);
                array_push($error_bag, $e->getMessage());
            }
        }
        if (is_array($uploaded_files)) {
            $response = count($error_bag) > 0 ? $error_bag : parent::$successResponse;
        } else { // upload via ckeditor 'Upload' tab
            if (is_null($new_filename)) {
                $response = $error_bag[0];
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
  if (o !== false) o.CKEDITOR.tools.callFunction(funcNum, '" . $fm->path()->setName($new_filename)->url() . "');
</script>";
            }
        }
        echo (is_string($response) ? $response : json_encode($response));
    }

}
