<?php

class CExporter_TaskQueue_CloseSheet extends CQueue_AbstractTask {
    use CExporter_Trait_ProxyFailures;

    /**
     * @var object
     */
    private $sheetExport;

    /**
     * @var CExporter_File_TemporaryFile
     */
    private $temporaryFile;

    /**
     * @var string
     */
    private $writerType;

    /**
     * @var int
     */
    private $sheetIndex;

    /**
     * @param object                       $sheetExport
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string                       $writerType
     * @param int                          $sheetIndex
     */
    public function __construct($sheetExport, CExporter_File_TemporaryFile $temporaryFile, $writerType, $sheetIndex) {
        $this->sheetExport = $sheetExport;
        $this->temporaryFile = $temporaryFile;
        $this->writerType = $writerType;
        $this->sheetIndex = $sheetIndex;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function execute() {
        if (!$this->temporaryFile->exists()) {
            return;
        }
        CDaemon::log('Start Close Sheet');
        CDaemon::log('File:' . $this->temporaryFile->getLocalPath());
        CDaemon::log('Memory Usage:' . memory_get_usage());
        $writer = CExporter::writer();
        $writer = $writer->reopen(
            $this->temporaryFile,
            $this->writerType
        );

        $sheet = $writer->getSheetByIndex($this->sheetIndex);

        if ($this->sheetExport instanceof CExporter_Concern_WithEvents) {
            $sheet->registerListeners($this->sheetExport->registerEvents());
        }
        CDaemon::log('close sheet');
        CDaemon::log('Memory Usage:' . memory_get_usage());
        $sheet->close($this->sheetExport);
        CDaemon::log('end close sheet');
        $writer->write(
            $this->sheetExport,
            $this->temporaryFile,
            $this->writerType
        );
    }
}
