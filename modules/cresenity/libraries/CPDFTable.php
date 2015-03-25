<?php

defined('SYSPATH') OR die('No direct access allowed.');


require_once dirname(__FILE__) . "/Lib/pdftable/pdftable.inc.php";

class CPDFTable extends PDFTable {

    function CPDFTable($orientation = 'P', $unit = 'mm', $format = 'A4') {
        parent::PDFTable($orientation, $unit, $format);
        $this->SetMargins(10, 15, 10, 15);
        $this->SetAuthor('Cresenity Tech');
        $this->_makePageSize();
        $this->isNotYetSetFont = true;
        $this->headerTable = $this->footerTable = '';
    }

    protected function _tableWrite(&$table) {
        /*
          if ($this->CurOrientation == 'P' && $table['w']>$this->width+5){
          $this->CurOrientation = 'L';
          }
         */

        if ($this->CurPageSize[0] < $table['w'] + $this->lMargin + $this->rMargin) {
            $this->CurPageSize[0] = $table['w'] + $this->lMargin + $this->rMargin;
        }
        if ($this->PageNo() <= 0) {

            $this->AddPage($this->CurOrientation, $this->CurPageSize);
        };

        parent::_tableWrite($table);
    }

}