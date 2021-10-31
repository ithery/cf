<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Import_EndRowFinder {

    /**
     * @param object|WithLimit $import
     * @param int              $startRow
     *
     * @return int|null
     */
    public static function find($import, $startRow = null) {
        if (!$import instanceof CExporter_Concern_WithLimit) {
            return null;
        }

        // When no start row given,
        // use the first row as start row.
        $startRow = $startRow ?: 1;

        // Subtract 1 row from the start row, so a limit
        // of 1 row, will have the same start and end row.
        return ($startRow - 1) + $import->limit();
    }

}
