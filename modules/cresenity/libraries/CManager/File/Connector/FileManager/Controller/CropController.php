<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 11:58:48 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_CropController extends CManager_File_Connector_FileManager_Controller_BaseController {

    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = new FM();
        $app = CApp::instance();
   
        $workingdir = $fm->input('working_dir');
        $app->addTemplate()->setTemplate('CElement/Component/FileManager/Cropper')->setVar('fm', $fm)
                ->setVar('working_dir', $workingdir)
                ->setVar('img', $fm->path()->pretty($fm->input('img')));
        echo $app->render();
    }

}
