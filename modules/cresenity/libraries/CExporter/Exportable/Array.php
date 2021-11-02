<?php

class CExporter_Exportable_Array extends CExporter_Exportable implements CExporter_Concern_FromArray {
    protected $array;

    /**
     * @param array $array
     */
    public function __construct(array $array) {
        $this->array = $array;
    }

    /**
     * @inherit
     */
    public function getArray() {
        return $this->array;
    }
}
