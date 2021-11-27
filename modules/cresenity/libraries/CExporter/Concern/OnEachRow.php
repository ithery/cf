<?php

interface CExporter_Concern_OnEachRow {
    /**
     * @param CExporter_Row $row
     */
    public function onRow(CExporter_Row $row);
}
