<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Exportable_Collection extends CExporter_Exportable implements CExporter_Concern_FromCollection {

    protected $collection;

    public function __construct(CCollection $collection) {
        $this->collection = $collection;
    }

    public function collection() {
        return $this->collection;
    }

}
