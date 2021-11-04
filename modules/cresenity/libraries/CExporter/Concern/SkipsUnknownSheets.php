<?php

interface CExporter_Concern_SkipsUnknownSheets {
    /**
     * @param string|int $sheetName
     */
    public function onUnknownSheet($sheetName);
}
