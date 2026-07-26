<?php

class CElement_Component_DataTable_Exporter {
    /**
     * @var CElement_Component_DataTable
     */
    protected $dataTable;

    /**
     * @param CElement_Component_DataTable $dataTable
     *
     * @return void
     */
    public function __construct(CElement_Component_DataTable $dataTable) {
        $this->dataTable = $dataTable;
    }

    /**
     * @param string $engine
     *
     * @return CElement_Component_DataTable_Exporter_AbstractEngine
     */
    public function createEngine($engine) {
        $engineClassName = 'CElement_Component_DataTable_Engine_' . $engine . 'Engine';
        $engineClass = new $engineClassName($this->dataTable);

        return $engineClass;
    }
}
