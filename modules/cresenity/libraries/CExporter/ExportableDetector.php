<?php

class CExporter_ExportableDetector {
    public static function toExportable($data) {
        if ($data instanceof CExporter_Exportable) {
            return $data;
        }
        if (is_array($data)) {
            return new CExporter_Exportable_Array($data);
        }
        if ($data instanceof CCollection) {
            return new CExporter_Exportable_Collection($data);
        }
        if ($data instanceof Iterator) {
            return new CExporter_Exportable_Iterator($data);
        }
        return $data;
    }
}
