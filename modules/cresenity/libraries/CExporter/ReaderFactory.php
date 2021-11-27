<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class CExporter_ReaderFactory {
    use CExporter_Trait_MapsCsvSettingsTrait;

    /**
     * @param object                       $import
     * @param CExporter_File_TemporaryFile $file
     * @param string                       $readerType
     *
     * @throws Exception
     *
     * @return IReader
     */
    public static function make($import, CExporter_File_TemporaryFile $file, $readerType = null) {
        $reader = IOFactory::createReader(
            $readerType ?: static::identify($file)
        );

        if (method_exists($reader, 'setReadDataOnly')) {
            $reader->setReadDataOnly(CExporter::config()->get('imports.read_only', true));
        }

        if ($reader instanceof Csv) {
            static::applyCsvSettings(CExporter::config()->get('imports.csv', []));

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
     *
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
