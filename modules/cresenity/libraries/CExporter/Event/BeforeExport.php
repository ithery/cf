<?php

class CExporter_Event_BeforeExport extends CExporter_Event {
    /**
     * @var Writer
     */
    public $writer;

    /**
     * @var object
     */
    private $exportable;

    /**
     * @param CExporter_Writer $writer
     * @param object           $exportable
     */
    public function __construct(CExporter_Writer $writer, $exportable) {
        $this->writer = $writer;
        $this->exportable = $exportable;
    }

    /**
     * @return CExporter_Writer
     */
    public function getWriter() {
        return $this->writer;
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
        return $this->writer;
    }
}
