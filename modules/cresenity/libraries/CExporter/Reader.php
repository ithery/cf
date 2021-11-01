<?php

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class CExporter_Reader {
    use CExporter_Trait_DelegatedMacroableTrait;
    use CExporter_Trait_HasEventBusTrait;
    /**
     * @var CExporter_Reader
     */
    protected static $instance;

    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var object[]
     */
    protected $sheetImports = [];

    /**
     * @var CExporter_File_TemporaryFile
     */
    protected $currentFile;

    /**
     * @var CExporter_File_TemporaryFileFactory
     */
    protected $temporaryFileFactory;

    /**
     * @var CExporter_Transaction_TransactionHandler
     */
    protected $transaction;

    /**
     * @var IReader
     */
    protected $reader;

    private function __construct() {
        $this->setDefaultValueBinder();
        $this->transaction = CExporter::transactionManager()->driver();

        $this->temporaryFileFactory = CExporter_File_TemporaryFileFactory::instance();
    }

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CExporter_Reader();
        }

        return static::$instance;
    }

    public function __sleep() {
        return ['spreadsheet', 'sheetImports', 'currentFile', 'temporaryFileFactory', 'reader'];
    }

    public function __wakeup() {
        $this->transaction = CExporter::transactionManager()->driver();
    }

    /**
     * @param object                    $import
     * @param string|CHTTP_UploadedFile $filePath
     * @param null|string               $readerType
     * @param null|string               $disk
     *
     * @throws CExporter_Exception_NoTypeDetectedException
     * @throws \CStorage_Exception_FileNotFoundException
     * @throws Exception
     *
     * @return \CQueue_PendingDispatch|$this
     */
    public function read($import, $filePath, string $readerType = null, string $disk = null) {
        $this->reader = $this->getReader($import, $filePath, $readerType, $disk);

        if ($import instanceof CExporter_Concern_WithChunkReading) {
            return (new CExporter_ChunkReader())->read($import, $this, $this->currentFile);
        }

        try {
            $this->loadSpreadsheet($import, $this->reader);

            ($this->transaction)(function () use ($import) {
                foreach ($this->sheetImports as $index => $sheetImport) {
                    if ($sheet = $this->getSheet($import, $sheetImport, $index)) {
                        $sheet->import($sheetImport, $sheet->getStartRow($sheetImport));
                        $sheet->disconnect();
                    }
                }
            });

            $this->afterImport($import);
        } catch (Throwable $e) {
            $this->raise(new CExporter_Event_ImportFailed($e));

            throw $e;
        } catch (Exception $e) {
            $this->raise(new CExporter_Event_ImportFailed($e));

            throw $e;
        }

        return $this;
    }

    /**
     * @param object                    $import
     * @param string|CHTTP_UploadedFile $filePath
     * @param string                    $readerType
     * @param null|string               $disk
     *
     * @throws CStorage_Exception_FileNotFoundException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws CExporter_Exception_NoTypeDetectedException
     * @throws Exceptions\SheetNotFoundException
     *
     * @return array
     */
    public function toArray($import, $filePath, $readerType, strng $disk = null) {
        $this->reader = $this->getReader($import, $filePath, $readerType, $disk);

        $this->loadSpreadsheet($import);

        $sheets = [];
        foreach ($this->sheetImports as $index => $sheetImport) {
            $calculatesFormulas = $sheetImport instanceof CExporter_Concern_WithCalculatedFormulas;
            if ($sheet = $this->getSheet($import, $sheetImport, $index)) {
                $sheets[$index] = $sheet->toArray($sheetImport, $sheet->getStartRow($sheetImport), null, $calculatesFormulas);
                $sheet->disconnect();
            }
        }

        $this->afterImport($import);

        return $sheets;
    }

    /**
     * @param object                    $import
     * @param string|CHTTP_UploadedFile $filePath
     * @param string                    $readerType
     * @param null|string               $disk
     *
     * @throws \CStorage_Exception_FileNotFoundException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws CExporter_Exception_NoTypeDetectedException
     * @throws Exceptions\SheetNotFoundException
     *
     * @return CCollection
     */
    public function toCollection($import, $filePath, $readerType, $disk = null) {
        $this->reader = $this->getReader($import, $filePath, $readerType, $disk);
        $this->loadSpreadsheet($import);

        $sheets = new CCollection();
        foreach ($this->sheetImports as $index => $sheetImport) {
            $calculatesFormulas = $sheetImport instanceof CExporter_Concern_WithCalculatedFormulas;
            if ($sheet = $this->getSheet($import, $sheetImport, $index)) {
                $sheets->put($index, $sheet->toCollection($sheetImport, $sheet->getStartRow($sheetImport), null, $calculatesFormulas));
                $sheet->disconnect();
            }
        }

        $this->afterImport($import);

        return $sheets;
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
        Cell::setValueBinder(CExporter::defaultValueBinder());

        return $this;
    }

    /**
     * @param object $import
     */
    public function loadSpreadsheet($import) {
        $this->sheetImports = $this->buildSheetImports($import);

        $this->readSpreadsheet();

        // When no multiple sheets, use the main import object
        // for each loaded sheet in the spreadsheet
        if (!$import instanceof CExporter_Concern_WithMultipleSheets) {
            $this->sheetImports = array_fill(0, $this->spreadsheet->getSheetCount(), $import);
        }

        $this->beforeImport($import);
    }

    public function readSpreadsheet() {
        $this->spreadsheet = $this->reader->load(
            $this->currentFile->getLocalPath()
        );
    }

    /**
     * @param object $import
     */
    public function beforeImport($import) {
        $this->raise(new CExporter_Event_BeforeImport($this, $import));
    }

    /**
     * @param object $import
     */
    public function afterImport($import) {
        $this->raise(new CExporter_Event_AfterImport($this, $import));

        $this->garbageCollect();
    }

    /**
     * @return IReader
     */
    public function getPhpSpreadsheetReader() {
        return $this->reader;
    }

    /**
     * @param object $import
     *
     * @return array
     */
    public function getWorksheets($import) {
        // Csv doesn't have worksheets.
        if (!method_exists($this->reader, 'listWorksheetNames')) {
            return ['Worksheet' => $import];
        }

        $worksheets = [];
        $worksheetNames = $this->reader->listWorksheetNames($this->currentFile->getLocalPath());
        if ($import instanceof CExporter_Concern_WithMultipleSheets) {
            $sheetImports = $import->sheets();

            // Load specific sheets.
            if (method_exists($this->reader, 'setLoadSheetsOnly')) {
                $this->reader->setLoadSheetsOnly(array_keys($sheetImports));
            }

            foreach ($sheetImports as $index => $sheetImport) {
                // Translate index to name.
                if (is_numeric($index)) {
                    $index = $worksheetNames[$index] ?? $index;
                }

                // Specify with worksheet name should have which import.
                $worksheets[$index] = $sheetImport;
            }
        } else {
            // Each worksheet the same import class.
            foreach ($worksheetNames as $name) {
                $worksheets[$name] = $import;
            }
        }

        return $worksheets;
    }

    /**
     * @return array
     */
    public function getTotalRows() {
        $info = $this->reader->listWorksheetInfo($this->currentFile->getLocalPath());

        $totalRows = [];
        foreach ($info as $sheet) {
            $totalRows[$sheet['worksheetName']] = $sheet['totalRows'];
        }

        return $totalRows;
    }

    /**
     * @param $import
     * @param $sheetImport
     * @param $index
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws SheetNotFoundException
     *
     * @return null|CExporter_Sheet
     */
    protected function getSheet($import, $sheetImport, $index) {
        try {
            return CExporter_Sheet::make($this->spreadsheet, $index);
        } catch (CExporter_Exception_SheetNotFoundException $e) {
            if ($import instanceof CExporter_Concern_SkipsUnknownSheets) {
                $import->onUnknownSheet($index);

                return null;
            }

            if ($sheetImport instanceof CExporter_Concern_SkipsUnknownSheets) {
                $sheetImport->onUnknownSheet($index);

                return null;
            }

            throw $e;
        }
    }

    /**
     * @param object $import
     *
     * @return array
     */
    private function buildSheetImports($import) {
        $sheetImports = [];
        if ($import instanceof CExporter_Concern_WithMultipleSheets) {
            $sheetImports = $import->sheets();

            // When only sheet names are given and the reader has
            // an option to load only the selected sheets.
            if (method_exists($this->reader, 'setLoadSheetsOnly')
                && count(array_filter(array_keys($sheetImports), 'is_numeric')) === 0
            ) {
                $this->reader->setLoadSheetsOnly(array_keys($sheetImports));
            }
        }

        return $sheetImports;
    }

    /**
     * @param object              $import
     * @param string|UploadedFile $filePath
     * @param null|string         $readerType
     * @param string              $disk
     *
     * @throws \CStorage_Exception_FileNotFoundException
     * @throws \CExporter_Exception_NoTypeDetectedException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws InvalidArgumentException
     *
     * @return IReader
     */
    private function getReader($import, $filePath, $readerType = null, $disk = null) {
        $shouldQueue = $import instanceof CQueue_ShouldQueueInterface;
        if ($shouldQueue && !$import instanceof CExporter_Concern_WithChunkReading) {
            throw new InvalidArgumentException('ShouldQueue is only supported in combination with WithChunkReading.');
        }

        if ($import instanceof CExporter_Concern_WithEvents) {
            $this->registerListeners($import->registerEvents());
        }

        if ($import instanceof CExporter_Concern_WithCustomValueBinder) {
            Cell::setValueBinder($import);
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $temporaryFile = $shouldQueue ? $this->temporaryFileFactory->make($fileExtension) : $this->temporaryFileFactory->makeLocal(null, $fileExtension);
        $this->currentFile = $temporaryFile->copyFrom(
            $filePath,
            $disk
        );

        return CExporter_ReaderFactory::make(
            $import,
            $this->currentFile,
            $readerType
        );
    }

    /**
     * Garbage collect.
     */
    private function garbageCollect() {
        $this->setDefaultValueBinder();

        // Force garbage collecting
        unset($this->sheetImports, $this->spreadsheet);

        $this->currentFile->delete();
    }
}
