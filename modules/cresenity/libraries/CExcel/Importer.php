<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 1, 2019, 3:11:09 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CExcel_Importer {

    /**
     * {@inheritdoc}
     */
    public function import($import, $filePath, $disk = null, $readerType = null) {
        $readerType = FileTypeDetector::detect($filePath, $readerType);
        $response = $this->reader->read($import, $filePath, $readerType, $disk);
        if ($response instanceof PendingDispatch) {
            return $response;
        }
        return $this;
    }

}
