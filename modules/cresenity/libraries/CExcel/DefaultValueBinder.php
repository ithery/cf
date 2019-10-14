<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 1, 2019, 5:23:33 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder as PhpSpreadsheetDefaultValueBinder;

class CExcel_DefaultValueBinder extends PhpSpreadsheetDefaultValueBinder {

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
