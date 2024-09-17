<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 3:16:48 AM
 */
use League\Flysystem\UnableToRetrieveMetadata;
use CManager_File_Connector_FileManager_FM as FM;

class CManager_File_Connector_FileManager_Controller_ItemController extends CManager_File_Connector_FileManager_AbstractController {
    /**
     * Get list of folders as json to populate treeview.
     *
     * @return mixed
     */
    public function execute() {
        $fm = $this->fm();
        $data = [];

        try {
            $data = [
                'items' => array_map(function ($item) {
                    return $item->fill()->attributes;
                }, array_merge($fm->path()->folders(), $fm->path()->files())),
                'display' => $fm->getDisplayMode(),
                'working_dir' => $fm->path()->path('working_dir'),
            ];
        } catch (UnableToRetrieveMetadata $e) {
            return c::abort(404);
        }

        return c::response()->json($data);
    }
}
