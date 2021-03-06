<?php

class CExporter_Event_AfterImport extends CExporter_Event {
    /**
     * @var CExporter_Reader
     */
    public $reader;

    /**
     * @var object
     */
    private $importable;

    /**
     * @param Reader $reader
     * @param object $importable
     */
    public function __construct(CExporter_Reader $reader, $importable) {
        $this->reader = $reader;
        $this->importable = $importable;
    }

    /**
     * @return CExporter_Reader
     */
    public function getReader() {
        return $this->reader;
    }

    /**
     * @return object
     */
    public function getConcernable() {
        return $this->importable;
    }

    /**
     * @return mixed
     */
    public function getDelegate() {
        return $this->reader;
    }
}
