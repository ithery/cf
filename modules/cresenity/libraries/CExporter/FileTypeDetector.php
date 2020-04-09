<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 1, 2019, 3:14:05 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CExporter_FileTypeDetector {

    /**
     * @param             $filePath
     * @param string|null $type
     *
     * @throws NoTypeDetectedException
     * @return string|null
     */
    public static function detect($filePath, $type = null) {
        if (null !== $type) {
            return $type;
        }
        if (!$filePath instanceof UploadedFile) {
            $pathInfo = pathinfo($filePath);
            $extension = isset($pathInfo['extension']) ? $pathInfo['extension'] : '';
        } else {
            $extension = $filePath->getClientOriginalExtension();
        }
        if (null === $type && trim($extension) === '') {
            throw new CExporter_Exception_NoTypeDetectedException();
        }
        $data = array(
            'xlsx' => CExporter_Constant::XLSX,
            'xlsm' => CExporter_Constant::XLSX,
            'xltx' => CExporter_Constant::XLSX,
            'xltm' => CExporter_Constant::XLSX,
            'xls' => CExporter_Constant::XLS,
            'xlt' => CExporter_Constant::XLS,
            'ods' => CExporter_Constant::ODS,
            'ots' => CExporter_Constant::ODS,
            'slk' => CExporter_Constant::SLK,
            'xml' => CExporter_Constant::XML,
            'gnumeric' => CExporter_Constant::GNUMERIC,
            'htm' => CExporter_Constant::HTML,
            'html' => CExporter_Constant::HTML,
            'csv' => CExporter_Constant::CSV,
            'tsv' => CExporter_Constant::TSV,
            'pdf' => CExporter_Constant::DOMPDF,
        );
        return carr::get($data, strtolower($extension));
    }

    /**
     * @param string      $filePath
     * @param string|null $type
     *
     * @throws CExporter_Exception_NoTypeDetectedException
     * @return string
     */
    public static function detectStrict($filePath, $type = null) {
        $type = static::detect($filePath, $type);
        if (!$type) {
            throw new CExporter_Exception_NoTypeDetectedException();
        }
        return $type;
    }

}
