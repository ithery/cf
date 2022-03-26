<?php

class CExporter_Exportable_Iterator extends CExporter_Exportable implements CExporter_Concern_FromIterator {
    protected $iterator;

    public function __construct(Iterator $iterator) {
        $this->iterator = $iterator;
    }

    public function iterator() {
        return $this->iterator;
    }
}
