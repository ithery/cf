<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Exportable_DataTable extends CExporter_Exportable implements CExporter_Concern_FromCollection,CExporter_Concern_WithHeadingRow,CExporter_Concern_WithMapping {

    protected $table;

    public function __construct(CElement_Component_DataTable $table) {
        $this->table = $table;
    }

    public function collection() {
        
    }

}
