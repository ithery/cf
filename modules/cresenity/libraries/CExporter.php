<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter {

    use CExporter_Trait_RegistersCustomConcernsTrait;

    const ACTION_STORE = 'store';
    const ACTION_DOWNLOAD = 'download';
    const XLSX = 'Xlsx';
    const CSV = 'Csv';
    const TSV = 'Csv';
    const ODS = 'Ods';
    const XLS = 'Xls';
    const SLK = 'Slk';
    const XML = 'Xml';
    const GNUMERIC = 'Gnumeric';
    const HTML = 'Html';
    const MPDF = 'Mpdf';
    const DOMPDF = 'Dompdf';
    const TCPDF = 'Tcpdf';

    /**
     * @param object      $export
     * @param string      $filePath
     * @param string|null $disk
     * @param string      $writerType
     * @param mixed       $diskOptions
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return bool
     */
    public static function store($export, $filePath, $options = []) {
        $diskName = carr::get($options, 'diskName');
        $writerType = carr::get($options, 'writerType');
        $queued = carr::get($options, 'queued', false);
        $diskOptions = carr::get($options, 'diskOptions', []);

        $export = CExporter_ExportableDetector::toExportable($export);



        if ($queued) {
            return static::queue($export, $filePath, $diskName, $writerType, $diskOptions);
        }

        $temporaryFile = static::export($export, $filePath, $writerType);


        $exported = static::storage()->disk($diskName, $diskOptions)->copy(
                $temporaryFile, $filePath
        );

        $temporaryFile->delete();

        return $exported;
    }

    /**
     * @param object      $export
     * @param string|null $fileName
     * @param string      $writerType
     * @param array       $headers
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return void
     */
    public static function download($export, $fileName, $writerType = null, array $headers = []) {
        $localPath = static::export($export, $fileName, $writerType)->getLocalPath();

        cdownload::force($localPath, null, $fileName);
        unlink($localPath);
    }

    /**
     * @param object      $export
     * @param string|null $fileName
     * @param string      $writerType
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @return TemporaryFile
     */
    protected static function export($export, $fileName, $writerType = null) {

        $writerType = CExporter_FileTypeDetector::detectStrict($fileName, $writerType);
        $export = CExporter_ExportableDetector::toExportable($export);
        return static::writer()->export($export, $writerType);
    }

    public static function config() {
        return CExporter_Config::instance();
    }

    /**
     * 
     * @return CExporter_Writer
     */
    public static function writer() {
        return CExporter_Writer::instance();
    }
    
    /**
     * 
     * @return CExporter_QueuedWriter
     */
    public static function queuedWriter() {
        return CExporter_QueuedWriter::instance();
    }

    /**
     * 
     * @return CExporter_Storage
     */
    public static function storage() {
        return CExporter_Storage::instance();
    }

    public static function generateExtension($writerType = self::XLSX) {
        switch ($writerType) {
            case static::XLSX:
                return 'xlsx';
            case static::XLS:
                return 'xls';
            case static::ODS:
                return 'ods';
            case static::XLS:
                return 'xls';
            case static::SLK:
                return 'slk';
            case static::XML:
                return 'xml';
            case static::GNUMERIC:
                return 'gnumeric';
            case static::HTML:
                return 'html';
            case static::CSV:
                return 'csv';
            case static::TSV:
                return 'tsv';
            case static::MPDF:
            case static::TCPDF:
            case static::DOMPDF:
                return 'pdf';
        }
        return 'xlsx';
    }

    public static function randomFilename($writerType = self::XLSX) {
        return 'export-' . cstr::random(32) . '.' . static::generateExtension($writerType);
    }

    /**
     * @param object $export
     * @param string $writerType
     *
     * @return string
     */
    public static function raw($export, $writerType) {
        $temporaryFile = static::writer()->export($export, $writerType);

        $contents = $temporaryFile->contents();
        $temporaryFile->delete();

        return $contents;
    }

    /**
     * @param object      $export
     * @param string      $filePath
     * @param string|null $disk
     * @param string      $writerType
     * @param mixed       $diskOptions
     *
     * @return PendingDispatch
     */
    public static function queue($export, $filePath, $disk = null, $writerType = null, $diskOptions = []) {
        $writerType = CExporter_FileTypeDetector::detectStrict($filePath, $writerType);
        $export = CExporter_ExportableDetector::toExportable($export);
        return static::queuedWriter()->store(
                        $export, $filePath, $disk, $writerType, $diskOptions
        );
    }

}
