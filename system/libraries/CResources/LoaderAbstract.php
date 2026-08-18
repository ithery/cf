<?php

defined('SYSPATH') or die('No direct access allowed.');

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
