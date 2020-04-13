<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_TaskQueue_CloseSheet extends CQueue_AbstractTask {

    use CExporter_Trait_ProxyFailures;

    /**
     * @var object
     */
    private $sheetExport;

    /**
     * @var string
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
     * @param object        $sheetExport
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string        $writerType
     * @param int           $sheetIndex
     */
    public function __construct($sheetExport, CExporter_File_TemporaryFile $temporaryFile, $writerType, $sheetIndex) {
        $this->sheetExport = $sheetExport;
        $this->temporaryFile = $temporaryFile;
        $this->writerType = $writerType;
        $this->sheetIndex = $sheetIndex;
    }

    /**
     * @param Writer $writer
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function execute() {
        $writer = CExporter::writer();
        $writer = $writer->reopen(
                $this->temporaryFile, $this->writerType
        );

        $sheet = $writer->getSheetByIndex($this->sheetIndex);

        if ($this->sheetExport instanceof CExporter_Concern_WithEvents) {
            $sheet->registerListeners($this->sheetExport->registerEvents());
        }

        $sheet->close($this->sheetExport);

        $writer->write(
                $this->sheetExport, $this->temporaryFile, $this->writerType
        );
    }

}
