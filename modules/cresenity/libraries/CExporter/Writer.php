<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CExporter_Writer {

    use CExporter_Trait_DelegatedMacroableTrait;
    use CExporter_Trait_HasEventBusTrait;

    /**
     *
     * @var CExporter_Writer
     */
    protected static $instance;

    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var object
     */
    protected $exportable;

    /**
     * @var CExporter_File_TemporaryFileFactory
     */
    protected $temporaryFileFactory;

    /**
     * @param CExporter_File_TemporaryFileFactory $temporaryFileFactory
     */
    private function __construct() {
        $this->temporaryFileFactory = CExporter_File_TemporaryFileFactory::instance();

        $this->setDefaultValueBinder();
    }

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CExporter_Writer();
        }
        return static::$instance;
    }

    /**
     * @param object $export
     * @param string $writerType
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @return TemporaryFile
     */
    public function export($export, $writerType) {
        $this->open($export);

        $sheetExports = [$export];
        if ($export instanceof CExporter_Concern_WithMultipleSheets) {
            $sheetExports = $export->sheets();
        }

        foreach ($sheetExports as $sheetExport) {
            $this->addNewSheet()->export($sheetExport);
        }

        return $this->write($export, $this->temporaryFileFactory->makeLocal(), $writerType);
    }

    /**
     * @param object $export
     *
     * @return $this
     */
    public function open($export) {
        $this->exportable = $export;

        if ($export instanceof CExporter_Concern_WithEvents) {
            $this->registerListeners($export->registerEvents());
        }

        $this->exportable = $export;
        $this->spreadsheet = new Spreadsheet;
        $this->spreadsheet->disconnectWorksheets();

        if ($export instanceof CExporter_Concern_WithCustomValueBinder) {
            Cell::setValueBinder($export);
        }

        $this->raise(new CExporter_Event_BeforeExport($this, $this->exportable));

        if ($export instanceof CExporter_Concern_WithTitle) {
            $this->spreadsheet->getProperties()->setTitle($export->title());
        }

        return $this;
    }

    /**
     * @param CExporter_TemporaryFile $tempFile
     * @param string        $writerType
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @return Writer
     */
    public function reopen(CExporter_TemporaryFile $tempFile, $writerType) {
        $reader = IOFactory::createReader($writerType);
        $this->spreadsheet = $reader->load($tempFile->sync()->getLocalPath());

        return $this;
    }

    /**
     * @param object        $export
     * @param TemporaryFile $temporaryFile
     * @param string        $writerType
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return CExporter_File_TemporaryFile
     */
    public function write($export, CExporter_File_TemporaryFile $temporaryFile, $writerType) {
        $this->exportable = $export;

        $this->spreadsheet->setActiveSheetIndex(0);

        $this->raise(new CExporter_Event_BeforeWriting($this, $this->exportable));

        $writer = CExporter_WriterFactory::make(
                        $writerType, $this->spreadsheet, $export
        );

        $writer->save(
                $path = $temporaryFile->getLocalPath()
        );

        if ($temporaryFile instanceof CExporter_File_RemoteTemporaryFile) {
            $temporaryFile->updateRemote();
        }

        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);

        return $temporaryFile;
    }

    /**
     * @param int|null $sheetIndex
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @return CExporter_Sheet
     */
    public function addNewSheet($sheetIndex = null) {
        return new CExporter_Sheet($this->spreadsheet->createSheet($sheetIndex));
    }

    /**
     * @return Spreadsheet
     */
    public function getDelegate() {
        return $this->spreadsheet;
    }

    /**
     * @return $this
     */
    public function setDefaultValueBinder() {
        $defaultValueBinderClass = CExporter::config()->get('value_binder.default', CExporter_DefaultValueBinder::class);
        Cell::setValueBinder(new $defaultValueBinderClass);

        return $this;
    }

    /**
     * @param int $sheetIndex
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @return CExporter_Sheet
     */
    public function getSheetByIndex($sheetIndex) {
        return new CExporter_Sheet($this->getDelegate()->getSheet($sheetIndex));
    }

    /**
     * @param string $concern
     *
     * @return bool
     */
    public function hasConcern($concern) {
        return $this->exportable instanceof $concern;
    }

}
