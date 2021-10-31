<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

class CExporter_TaskQueue_ReadChunk extends CQueue_AbstractTask {

    use CExporter_Trait_HasEventBusTrait;

    /**
     * @var int
     */
    public $timeout;

    /**
     * @var int
     */
    public $tries;

    /**
     * @var WithChunkReading
     */
    private $import;

    /**
     * @var IReader
     */
    private $reader;

    /**
     * @var TemporaryFile
     */
    private $temporaryFile;

    /**
     * @var string
     */
    private $sheetName;

    /**
     * @var object
     */
    private $sheetImport;

    /**
     * @var int
     */
    private $startRow;

    /**
     * @var int
     */
    private $chunkSize;

    /**
     * @param  WithChunkReading  $import
     * @param  IReader  $reader
     * @param  TemporaryFile  $temporaryFile
     * @param  string  $sheetName
     * @param  object  $sheetImport
     * @param  int  $startRow
     * @param  int  $chunkSize
     */
    public function __construct(CExporter_Concern_WithChunkReading $import, IReader $reader, CExporter_File_TemporaryFile $temporaryFile, $sheetName, $sheetImport, $startRow, $chunkSize) {
        $this->import = $import;
        $this->reader = $reader;
        $this->temporaryFile = $temporaryFile;
        $this->sheetName = $sheetName;
        $this->sheetImport = $sheetImport;
        $this->startRow = $startRow;
        $this->chunkSize = $chunkSize;
        $this->timeout = isset($import->timeout) ? $import->timeout : null;
        $this->tries = isset($import->tries) ? $import->tries : null;
    }

    /**
     * @param  CExporter_Transaction_TransactionHandler  $transaction
     *
     * @throws CExporter_Exception_SheetNotFoundException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function execute(CExporter_Transaction_TransactionHandler $transaction) {
        if ($this->sheetImport instanceof CExporter_Concern_WithCustomValueBinder) {
            Cell::setValueBinder($this->sheetImport);
        }

        $headingRow = CExporter_Import_HeadingRowExtractor::headingRow($this->sheetImport);

        $filter = new CExporter_Filter_ChunkReadFilter(
                $headingRow, $this->startRow, $this->chunkSize, $this->sheetName
        );

        $this->reader->setReadFilter($filter);
        $this->reader->setReadDataOnly(true);
        $this->reader->setReadEmptyCells(false);

        $spreadsheet = $this->reader->load(
                $this->temporaryFile->sync()->getLocalPath()
        );

        $sheet = CExporter_Sheet::byName(
                        $spreadsheet, $this->sheetName
        );

        if ($sheet->getHighestRow() < $this->startRow) {
            $sheet->disconnect();

            return;
        }

        $transaction(function () use ($sheet) {
            $sheet->import(
                    $this->sheetImport, $this->startRow
            );

            $sheet->disconnect();
        });
    }

    /**
     * @param  Throwable  $e
     */
    public function failed(Throwable $e) {
        if ($this->import instanceof CExporter_Concern_WithEvents) {
            $this->registerListeners($this->import->registerEvents());
            $this->raise(new CExporter_Event_ImportFailed($e));

            if (method_exists($this->import, 'failed')) {
                $this->import->failed($e);
            }
        }
    }

}
