<?php

class CExporter_Event_AfterExport {
    /**
     * @var string
     */
    public $filePath;

    /**
     * @var bool
     */
    public $isSuccess;

    /**
     * @param string $filePath
     * @param mixed  $isSuccess
     */
    public function __construct($filePath, $isSuccess) {
        $this->filePath = $filePath;
        $this->isSuccess = $isSuccess;
    }
}
