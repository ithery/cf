<?php

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CExporter_Import_ModelImporter {
    /**
     * @var CExporter_Import_ModelManager
     */
    private $manager;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->manager = CExporter::modelManager();
    }

    /**
     * @param Worksheet $worksheet
     * @param ToModel   $import
     * @param null|int  $startRow
     */
    public function import(Worksheet $worksheet, CExporter_Concern_ToModel $import, int $startRow = 1) {
        $headingRow = CExporter_Import_HeadingRowExtractor::extract($worksheet, $import);
        $batchSize = $import instanceof CExporter_Concern_WithBatchInserts ? $import->batchSize() : 1;
        $endRow = CExporter_Import_EndRowFinder::find($import, $startRow);
        $withCalcFormulas = $import instanceof CExporter_Concern_WithCalculatedFormulas;

        $i = 0;
        foreach ($worksheet->getRowIterator($startRow, $endRow) as $spreadSheetRow) {
            $i++;

            $row = new CExporter_Row($spreadSheetRow, $headingRow);
            $rowArray = $row->toArray(null, $withCalcFormulas);

            if ($import instanceof CExporter_Concern_WithMapping) {
                $rowArray = $import->map($rowArray);
            }

            $this->manager->add(
                $row->getIndex(),
                $rowArray
            );

            // Flush each batch.
            if (($i % $batchSize) === 0) {
                $this->manager->flush($import, $batchSize > 1);
                $i = 0;

                if ($import instanceof CExporter_Concern_WithProgressBar) {
                    $import->getConsoleOutput()->progressAdvance($batchSize);
                }
            }
        }

        // Flush left-overs.
        $this->manager->flush($import, $batchSize > 1);
    }
}
