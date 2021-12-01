<?php

class CExporter_Event_AfterExport {
    /**
     * @var string
     */
    public $filePath;

    /**
     * @param string $filePath
     */
    public function __construct($filePath) {
        $this->filePath = $filePath;
    }
}
