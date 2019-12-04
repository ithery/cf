<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CElement_Component_DataTable_Exporter_AbstractEngine implements CElement_Component_DataTable_Exporter_EngineInterface {

    /**
     *
     * @var CElement_Component_DataTable
     */
    protected $dataTable;
    /**
     *
     * @var string 
     */
    protected $filename;

    public function __construct($dataTable) {
        $this->dataTable = $dataTable;
    }

    /**
     * 
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename) {
        $this->filename = $filename;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getFilename() {
        return $this->filename;
    }

}
