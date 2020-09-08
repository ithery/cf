<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Exportable_DataTableTemp extends CExporter_Exportable implements CExporter_Concern_FromCollection, CExporter_Concern_WithHeadings, CExporter_Concern_WithMapping {

    protected $file;

    public function __construct($file) {
        $this->file = $file;
    }

    protected function table() {
        $data = CAjax::getData($this->file);
        
        $table = unserialize(carr::get($data, 'data.table'));
        return new CExporter_Exportable_DataTable($table);
    }

    public function collection() {

        return $this->table()->collection();
    }

    public function map($data) {
        return $this->table()->map($data);
    }

    public function headings() {
        return $this->table()->headings();
    }

}
