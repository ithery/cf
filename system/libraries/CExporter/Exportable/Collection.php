<?php

class CExporter_Exportable_Collection extends CExporter_Exportable implements CExporter_Concern_FromCollection {
    protected $collection;

    public function __construct(CCollection $collection) {
        $this->collection = $collection;
    }

    public function collection() {
        return $this->collection;
    }
}
