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
            'xlsx' => CExporter::XLSX,
            'xlsm' => CExporter::XLSX,
            'xltx' => CExporter::XLSX,
            'xltm' => CExporter::XLSX,
            'xls' => CExporter::XLS,
            'xlt' => CExporter::XLS,
            'ods' => CExporter::ODS,
            'ots' => CExporter::ODS,
            'slk' => CExporter::SLK,
            'xml' => CExporter::XML,
            'gnumeric' => CExporter::GNUMERIC,
            'htm' => CExporter::HTML,
            'html' => CExporter::HTML,
            'csv' => CExporter::CSV,
            'tsv' => CExporter::TSV,
            'pdf' => CExporter::DOMPDF,
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
