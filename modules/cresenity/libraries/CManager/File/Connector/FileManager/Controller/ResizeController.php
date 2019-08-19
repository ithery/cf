<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 12, 2019, 12:38:55 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CManager_File_Connector_FileManager_FM as FM;
use Intervention\Image\ImageManager;

class CManager_File_Connector_FileManager_Controller_ResizeController extends CManager_File_Connector_FileManager_Controller_BaseController {

    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = new FM();
        $app = CApp::instance();

        $ratio = 1.0;
        $image = $fm->input('img');
        $imageManager = new ImageManager();
        $original_image = $imageManager->make($fm->path()->setName($image)->path('absolute'));
        $original_width = $original_image->width();
        $original_height = $original_image->height();
        $scaled = false;
        // FIXME size should be configurable
        if ($original_width > 600) {
            $ratio = 600 / $original_width;
            $width = $original_width * $ratio;
            $height = $original_height * $ratio;
            $scaled = true;
        } else {
            $width = $original_width;
            $height = $original_height;
        }
        if ($height > 400) {
            $ratio = 400 / $original_height;
            $width = $original_width * $ratio;
            $height = $original_height * $ratio;
            $scaled = true;
        }

        $app->addTemplate()->setTemplate('CElement/Component/FileManager/Resizer')
                ->setVar('fm', $fm)
                ->setVar('img', $fm->path()->pretty($image))
                ->setVar('height', number_format($height, 0))
                ->setVar('width', $width)
                ->setVar('original_height', $original_height)
                ->setVar('original_width', $original_width)
                ->setVar('scaled', $scaled)
                ->setVar('ratio', $ratio);
        echo $app->render();
    }

}
