<?php

class CExporter_Event_AfterSheet extends CExporter_Event {
    /**
     * @var Sheet
     */
    public $sheet;

    /**
     * @var object
     */
    private $exportable;

    /**
     * @param CExporter_Sheet $sheet
     * @param object          $exportable
     */
    public function __construct(CExporter_Sheet $sheet, $exportable) {
        $this->sheet = $sheet;
        $this->exportable = $exportable;
    }

    /**
     * @return CExporter_Sheet
     */
    public function getSheet() {
        return $this->sheet;
    }

    /**
     * @return object
     */
    public function getConcernable() {
        return $this->exportable;
    }

    /**
     * @return mixed
     */
    public function getDelegate() {
        return $this->sheet;
    }
}
