<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_TaskQueue_QueueExport extends CQueue_AbstractTask {

    /**
     * @var object
     */
    public $export;

    /**
     * @var string
     */
    private $writerType;

    /**
     * @var TemporaryFile
     */
    private $temporaryFile;

    /**
     * @param object        $export
     * @param TemporaryFile $temporaryFile
     * @param string        $writerType
     */
    public function __construct($export, CExporter_File_TemporaryFile $temporaryFile, $writerType) {
        $this->export = $export;
        $this->writerType = $writerType;
        $this->temporaryFile = $temporaryFile;
        
    }

    /**
     * Get the middleware the job should be dispatched through.
     *
     * @return array
     */
    public function middleware() {
        return (method_exists($this->export, 'middleware')) ? $this->export->middleware() : [];
    }

    /**
     * @param Writer $writer
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function execute() {
        
        
        $writer = CExporter::writer();
        $writer->open($this->export);

        $sheetExports = [$this->export];
        if ($this->export instanceof CExporter_Concern_WithMultipleSheets) {
            $sheetExports = $this->export->sheets();
        }

        // Pre-create the worksheets
        foreach ($sheetExports as $sheetIndex => $sheetExport) {
            $sheet = $writer->addNewSheet($sheetIndex);
            $sheet->open($sheetExport);
        }

        CDaemon::log('Try to writing temporary '.$this->writerType.' to '.$this->temporaryFile->getLocalPath());

        // Write to temp file with empty sheets.
        $writer->write($sheetExport, $this->temporaryFile, $this->writerType);
    }

    public function chain($chain) {
        c::collect($chain)->each(function ($job) {
            $this->chained[] = serialize($job);
        });

        return $this;
    }

}
