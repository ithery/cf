<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;

class CExporter_WriterFactory {

    use CExporter_Trait_MapsCsvSettingsTrait;

    /**
     * @param string      $writerType
     * @param Spreadsheet $spreadsheet
     * @param object      $export
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return IWriter
     */
    public static function make( $writerType, Spreadsheet $spreadsheet, $export) {
        $writer = IOFactory::createWriter($spreadsheet, $writerType);

        if (static::includesCharts($export)) {
            $writer->setIncludeCharts(true);
        }

        if ($writer instanceof Html && $export instanceof CExporter_Concern_WithMultipleSheets) {
            $writer->writeAllSheets();
        }

        if ($writer instanceof Csv) {
            static::applyCsvSettings(CF::config('exporter.exports.csv', []));

            if ($export instanceof CExporter_Concern_WithCustomCsvSettings) {
                static::applyCsvSettings($export->getCsvSettings());
            }

            $writer->setDelimiter(static::$delimiter);
            $writer->setEnclosure(static::$enclosure);
            $writer->setLineEnding(static::$lineEnding);
            $writer->setUseBOM(static::$useBom);
            $writer->setIncludeSeparatorLine(static::$includeSeparatorLine);
            $writer->setExcelCompatibility(static::$excelCompatibility);
        }

        // Calculation settings
        $writer->setPreCalculateFormulas(
                $export instanceof CExporter_Concern_WithPreCalculateFormulas ? true : CExporter::config()->get('exports.pre_calculate_formulas', false)
        );

        return $writer;
    }

    /**
     * @param $export
     *
     * @return bool
     */
    private static function includesCharts($export) {
        if ($export instanceof CExporter_Concern_WithCharts) {
            return true;
        }

        if ($export instanceof CExporter_Concern_WithMultipleSheets) {
            foreach ($export->sheets() as $sheet) {
                if ($sheet instanceof CExporter_Concern_WithCharts) {
                    return true;
                }
            }
        }

        return false;
    }

}
