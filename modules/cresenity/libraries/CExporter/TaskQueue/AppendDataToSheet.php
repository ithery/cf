<?php

class CExporter_TaskQueue_AppendDataToSheet extends CQueue_AbstractTask {
    use CExporter_Trait_ProxyFailures;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var CExporter_File_TemporaryFile
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
     * @param object                       $sheetExport
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string                       $writerType
     * @param int                          $sheetIndex
     * @param array                        $data
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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function execute() {
        $writer = CExporter::writer();
        $writer = $writer->reopen($this->temporaryFile, $this->writerType);

        $sheet = $writer->getSheetByIndex($this->sheetIndex);
        CDaemon::log('append row');
        //CDaemon::log(json_encode($this->data));
        $sheet->appendRows($this->data, $this->sheetExport);
        CDaemon::log('end append row');

        CDaemon::log('write excel');
        $writer->write($this->sheetExport, $this->temporaryFile, $this->writerType);
        CDaemon::log('end write excel');
        CDaemon::log('Memory Usage:' . memory_get_usage());
    }
}
