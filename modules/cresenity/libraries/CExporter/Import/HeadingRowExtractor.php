<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CExporter_Import_HeadingRowExtractor {

    /**
     * @const int
     */
    const DEFAULT_HEADING_ROW = 1;

    /**
     * @param WithHeadingRow|mixed $importable
     *
     * @return int
     */
    public static function headingRow($importable) {
        return method_exists($importable, 'headingRow') ? $importable->headingRow() : self::DEFAULT_HEADING_ROW;
    }

    /**
     * @param WithHeadingRow|mixed $importable
     *
     * @return int
     */
    public static function determineStartRow($importable) {
        if ($importable instanceof CExporter_Concern_WithStartRow) {
            return $importable->startRow();
        }

        // The start row is the row after the heading row if we have one!
        return $importable instanceof CExporter_Concern_WithHeadingRow ? self::headingRow($importable) + 1 : self::DEFAULT_HEADING_ROW;
    }

    /**
     * @param Worksheet            $worksheet
     * @param WithHeadingRow|mixed $importable
     *
     * @return array
     */
    public static function extract(Worksheet $worksheet, $importable) {
        if (!$importable instanceof WithHeadingRow) {
            return [];
        }

        $headingRowNumber = self::headingRow($importable);
        $rows = iterator_to_array($worksheet->getRowIterator($headingRowNumber, $headingRowNumber));
        $headingRow = head($rows);

        return CExporter_Import_HeadingRowFormatter::format((new CExporter_Excel_Row($headingRow))->toArray(null, false, false));
    }

}
