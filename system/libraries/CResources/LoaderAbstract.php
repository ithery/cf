<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 30, 2019, 5:30:04 AM
 */
abstract class CResources_LoaderAbstract implements CResources_LoaderInterface {
    public function delete() {
        $fullPath = $this->getPath();
        if (@unlink($fullPath)) {
            return true;
        } else {
            return false;
        }
    }

    public function getFileSize() {
        $filePath = $this->getPath();
        $file = new CFile();

        if ($file->exists($filePath)) {
            return $file->size($filePath);
        }
        return false;
    }
}
