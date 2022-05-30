<?php

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CExporter_Import_HeadingRowExtractor {
    /**
     * @const int
     */
    const DEFAULT_HEADING_ROW = 1;

    /**
     * @param CExporter_Concern_WithHeadingRow|mixed $importable
     *
     * @return int
     */
    public static function headingRow($importable) {
        return method_exists($importable, 'headingRow') ? $importable->headingRow() : self::DEFAULT_HEADING_ROW;
    }

    /**
     * @param CExporter_Concern_WithHeadingRow|mixed $importable
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
     * @param Worksheet                              $worksheet
     * @param CExporter_Concern_WithHeadingRow|mixed $importable
     *
     * @return array
     */
    public static function extract(Worksheet $worksheet, $importable) {
        if (!$importable instanceof CExporter_Concern_WithHeadingRow) {
            return [];
        }

        $headingRowNumber = self::headingRow($importable);
        $rows = iterator_to_array($worksheet->getRowIterator($headingRowNumber, $headingRowNumber));
        $headingRow = c::head($rows);

        return CExporter_Import_HeadingRowFormatter::format((new CExporter_Row($headingRow))->toArray(null, false, false));
    }
}
