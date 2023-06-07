<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 12, 2019, 12:38:55 AM
 */
use Intervention\Image\ImageManager;
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_ResizeController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = $this->fm();
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

        $app->addView('cresenity.element.component.file-manager.resizer', [
            'fm' => $fm,
            'img' => $fm->path()->pretty($image),
            'height' => number_format($height, 0),
            'width' => $width,
            'original_height' => $original_height,
            'original_width' => $original_width,
            'scaled' => $scaled,
            'ratio' => $ratio,
        ]);

        return $app;
    }
}
