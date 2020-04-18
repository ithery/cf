<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder as PhpSpreadsheetDefaultValueBinder;

class CExporter_DefaultValueBinder extends PhpSpreadsheetDefaultValueBinder {

    /**
     * @param Cell $cell Cell to bind value to
     * @param mixed $value Value to bind in cell
     *
     * @return bool
     */
    public function bindValue(Cell $cell, $value) {
        if (is_array($value)) {
            $value = \json_encode($value);
        }

        return parent::bindValue($cell, $value);
    }

}
