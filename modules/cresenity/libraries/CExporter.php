<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter {

    use CExporter_Trait_RegistersCustomConcernsTrait;

    /**
     * {@inheritdoc}
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
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @return TemporaryFile
     */
    protected static function export($export, $fileName, $writerType = null) {
        $writerType = CExporter_FileTypeDetector::detectStrict($fileName, $writerType);

        return static::writer()->export($export, $writerType);
    }

    public static function config() {
        return CExporter_Config::instance();
    }

    /**
     * 
     * @return CExporter_Writer
     */
    protected static function writer() {
        return CExporter_Writer::instance();
    }

    protected static function storage() {
        return CExporter_Storage::instance();
    }

}
