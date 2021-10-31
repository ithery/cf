<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

class CExporter_ReaderFactory {

    use CExporter_Trait_MapsCsvSettingsTrait;

    /**
     * @param object        $import
     * @param CExporter_File_TemporaryFile $file
     * @param string        $readerType
     *
     * @throws Exception
     * @return IReader
     */
    public static function make($import, CExporter_File_TemporaryFile $file, $readerType = null) {
        $reader = IOFactory::createReader(
                        $readerType ?: static::identify($file)
        );

        if (method_exists($reader, 'setReadDataOnly')) {
            $reader->setReadDataOnly(config('excel.imports.read_only', true));
        }

        if ($reader instanceof Csv) {
            static::applyCsvSettings(config('excel.imports.csv', []));

            if ($import instanceof CExporter_Concern_WithCustomCsvSettings) {
                static::applyCsvSettings($import->getCsvSettings());
            }

            $reader->setDelimiter(static::$delimiter);
            $reader->setEnclosure(static::$enclosure);
            $reader->setEscapeCharacter(static::$escapeCharacter);
            $reader->setContiguous(static::$contiguous);
            $reader->setInputEncoding(static::$inputEncoding);
        }

        return $reader;
    }

    /**
     * @param CExporter_File_TemporaryFile $temporaryFile
     *
     * @throws CExporter_Exception_NoTypeDetectedException
     * @return string
     */
    private static function identify(CExporter_File_TemporaryFile $temporaryFile) {
        try {
            return IOFactory::identify($temporaryFile->getLocalPath());
        } catch (Exception $e) {
            throw new CExporter_Exception_NoTypeDetectedException(null, null, $e);
        }
    }

}
