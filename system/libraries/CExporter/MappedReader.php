<?php

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CExporter_MappedReader {
    /**
     * @param CExporter_Concern_WithMappedCells $import
     * @param Worksheet                         $worksheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public static function map(CExporter_Concern_WithMappedCells $import, Worksheet $worksheet) {
        $mapped = [];
        foreach ($import->mapping() as $name => $coordinate) {
            $cell = CExporter_Cell::make($worksheet, $coordinate);

            $mapped[$name] = $cell->getValue(
                null,
                $import instanceof CExporter_Concern_WithCalculatedFormulas,
                $import instanceof CExporter_Concern_WithFormatData
            );
        }

        if ($import instanceof CExporter_Concern_ToModel) {
            $model = $import->model($mapped);

            if ($model) {
                $model->saveOrFail();
            }
        }

        if ($import instanceof CExporter_Concern_ToCollection) {
            $import->collection(new CCollection($mapped));
        }

        if ($import instanceof CExporter_Concern_ToArray) {
            $import->toArray($mapped);
        }
    }
}
