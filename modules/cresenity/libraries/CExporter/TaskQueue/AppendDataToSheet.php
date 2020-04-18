<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_TaskQueue_AppendDataToSheet extends CQueue_AbstractTask {

    use CExporter_Trait_ProxyFailures;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var string
     */
    public $temporaryFile;

    /**
     * @var string
     */
    public $writerType;

    /**
     * @var int
     */
    public $sheetIndex;

    /**
     * @var object
     */
    public $sheetExport;

    /**
     * @param object        $sheetExport
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string        $writerType
     * @param int           $sheetIndex
     * @param array         $data
     */
    public function __construct($sheetExport, CExporter_File_TemporaryFile $temporaryFile, $writerType, $sheetIndex, array $data) {
        $this->sheetExport = $sheetExport;
        $this->data = $data;
        $this->temporaryFile = $temporaryFile;
        $this->writerType = $writerType;
        $this->sheetIndex = $sheetIndex;
    }

    /**
     * Get the middleware the job should be dispatched through.
     *
     * @return array
     */
    public function middleware() {
        return (method_exists($this->sheetExport, 'middleware')) ? $this->sheetExport->middleware() : [];
    }

    /**
     * @param CExporter_Writer $writer
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function execute( ) {
        $writer = CExporter::writer();
        $writer = $writer->reopen($this->temporaryFile, $this->writerType);

        $sheet = $writer->getSheetByIndex($this->sheetIndex);

        $sheet->appendRows($this->data, $this->sheetExport);

        $writer->write($this->sheetExport, $this->temporaryFile, $this->writerType);
    }

}
