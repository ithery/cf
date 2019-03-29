<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 30, 2019, 5:30:04 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CResources_LoaderAbstract implements CResources_LoaderInterface {

    public function delete() {
        $fullPath = $this->getBasePath();
        if (@unlink($fullPath)) {
            return true;
        } else {
            return false;
        }
    }

    public function getSize() {
        $filePath = $this->getPath();
        $file = new CFile();
        return $file->size($filePath);
    }

}
