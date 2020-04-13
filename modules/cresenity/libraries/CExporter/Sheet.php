<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PhpOffice\PhpSpreadsheet\Cell\Cell as SpreadsheetCell;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CExporter_Sheet {

    use CExporter_Trait_DelegatedMacroableTrait,
        CExporter_Trait_HasEventBusTrait;

    /**
     * @var int
     */
    protected $chunkSize;

    /**
     * @var CExporter_File_TemporaryFileFactory
     */
    protected $temporaryFileFactory;

    /**
     * @var object
     */
    protected $exportable;

    /**
     * @var Worksheet
     */
    private $worksheet;

    /**
     * @param Worksheet $worksheet
     */
    public function __construct(Worksheet $worksheet) {
        $this->worksheet = $worksheet;
        $this->chunkSize = CExporter::config()->get('exports.chunk_size', 100);
        $this->temporaryFileFactory = CExporter_File_TemporaryFileFactory::instance();
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param string|int  $index
     *
     * @return Sheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws SheetNotFoundException
     */
    public static function make(Spreadsheet $spreadsheet, $index) {
        if (is_numeric($index)) {
            return self::byIndex($spreadsheet, $index);
        }

        return self::byName($spreadsheet, $index);
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param int         $index
     *
     * @return CExporter_Sheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws SheetNotFoundException
     */
    public static function byIndex(Spreadsheet $spreadsheet, $index) {
        if (!isset($spreadsheet->getAllSheets()[$index])) {
            throw CExporter_Exception_SheetNotFoundException::byIndex($index, $spreadsheet->getSheetCount());
        }

        return new static($spreadsheet->getSheet($index));
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param string      $name
     *
     * @return CExporter_Sheet
     *
     * @throws SheetNotFoundException
     */
    public static function byName(Spreadsheet $spreadsheet, $name) {
        if (!$spreadsheet->sheetNameExists($name)) {
            throw CExporter_Exception_SheetNotFoundException::byName($name);
        }

        return new static($spreadsheet->getSheetByName($name));
    }

    /**
     * @param object $sheetExport
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function open($sheetExport) {
        $this->exportable = $sheetExport;

        if ($sheetExport instanceof CExporter_Concern_WithCustomValueBinder) {
            SpreadsheetCell::setValueBinder($sheetExport);
        }

        if ($sheetExport instanceof CExporter_Concern_WithEvents) {
            $this->registerListeners($sheetExport->registerEvents());
        }

        $this->raise(new CExporter_Event_BeforeSheet($this, $this->exportable));

        if ($sheetExport instanceof CExporter_Concern_WithTitle) {
            $this->worksheet->setTitle($sheetExport->title());
        }

        if (($sheetExport instanceof CExporter_Concern_FromQuery || $sheetExport instanceof CExporter_Concern_FromCollection || $sheetExport instanceof CExporter_Concern_FromArray) && $sheetExport instanceof CExporter_Concern_FromView && $sheetExport instanceof CExporter_Concern_FromSql) {
            throw CExporter_Exception_ConcernConflictException::queryOrCollectionAndView();
        }

        if (!$sheetExport instanceof CExporter_Concern_FromView && $sheetExport instanceof CExporter_Concern_WithHeadings) {
            if ($sheetExport instanceof CExporter_Concern_WithCustomStartCell) {
                $startCell = $sheetExport->startCell();
            }

            $this->append(
                    CExporter_Helper_ArrayHelper::ensureMultipleRows($sheetExport->headings()), isset($startCell) ? $startCell : null, $this->hasStrictNullComparison($sheetExport)
            );
        }

        if ($sheetExport instanceof CExporter_Concern_WithCharts) {
            $this->addCharts($sheetExport->charts());
        }

        if ($sheetExport instanceof CExporter_Concern_WithDrawings) {
            $this->addDrawings($sheetExport->drawings());
        }
    }

    /**
     * @param object $sheetExport
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function export($sheetExport) {
        $this->open($sheetExport);

        if ($sheetExport instanceof CExporter_Concern_FromView) {
            $this->fromView($sheetExport);
        } else {
            if ($sheetExport instanceof CExporter_Concern_FromQuery) {
                $this->fromQuery($sheetExport, $this->worksheet);
            }

            if ($sheetExport instanceof CExporter_Concern_FromSql) {
                $this->fromSql($sheetExport, $this->worksheet);
            }

            if ($sheetExport instanceof CExporter_Concern_FromCollection) {
                $this->fromCollection($sheetExport);
            }

            if ($sheetExport instanceof CExporter_Concern_FromArray) {
                $this->fromArray($sheetExport);
            }

            if ($sheetExport instanceof CExporter_Concern_FromIterator) {
                $this->fromIterator($sheetExport);
            }

            if ($sheetExport instanceof CExporter_Concern_FromGenerator) {
                $this->fromGenerator($sheetExport);
            }
        }

        $this->close($sheetExport);
    }

    /**
     * @param object $import
     * @param int    $startRow
     */
    public function import($import, $startRow = 1) {
        if ($import instanceof CExporter_Concern_WithEvents) {
            $this->registerListeners($import->registerEvents());
        }

        $this->raise(new CExporter_Event_BeforeSheet($this, $this->exportable));

        if ($import instanceof CExporter_Concern_WithProgressBar && !$import instanceof CExporter_Concern_WithChunkReading) {
            $import->getConsoleOutput()->progressStart($this->worksheet->getHighestRow());
        }

        $calculatesFormulas = $import instanceof CExporter_Concern_WithCalculatedFormulas;

        if ($import instanceof CExporter_Concern_WithMappedCells) {
            app(MappedReader::class)->map($import, $this->worksheet);
        } else {
            if ($import instanceof CExporter_Concern_ToModel) {
                app(ModelImporter::class)->import($this->worksheet, $import, $startRow);
            }

            if ($import instanceof CExporter_Concern_ToCollection) {
                $import->collection($this->toCollection($import, $startRow, null, $calculatesFormulas));
            }

            if ($import instanceof CExporter_Concern_ToArray) {
                $import->array($this->toArray($import, $startRow, null, $calculatesFormulas));
            }
        }

        if ($import instanceof CExporter_Concern_OnEachRow) {
            $headingRow = HeadingRowExtractor::extract($this->worksheet, $import);

            foreach ($this->worksheet->getRowIterator()->resetStart(isset($startRow) ? $startRow : 1) as $row) {
                $sheetRow = new Row($row, $headingRow);

                if ($import instanceof CExporter_Concern_WithValidation) {
                    $toValidate = [$sheetRow->getIndex() => $sheetRow->toArray(null, $import instanceof CExporter_Concern_WithCalculatedFormulas)];

                    try {
                        CExporter_Validator_RowValidator::instance()->validate($toValidate, $import);
                        $import->onRow($sheetRow);
                    } catch (CExporter_Exception_RowSkippedException $e) {
                        
                    }
                } else {
                    $import->onRow($sheetRow);
                }

                if ($import instanceof CExporter_Concern_WithProgressBar) {
                    $import->getConsoleOutput()->progressAdvance();
                }
            }
        }

        $this->raise(new CExporter_Event_AfterSheet($this, $this->exportable));

        if ($import instanceof CExporter_Concern_WithProgressBar && !$import instanceof CExporter_Concern_WithChunkReading) {
            $import->getConsoleOutput()->progressFinish();
        }
    }

    /**
     * @param object   $import
     * @param int|null $startRow
     * @param null     $nullValue
     * @param bool     $calculateFormulas
     * @param bool     $formatData
     *
     * @return array
     */
    public function toArray($import, $startRow = null, $nullValue = null, $calculateFormulas = false, $formatData = false) {
        $endRow = CExporter_Import_EndRowFinder::find($import, $startRow);
        $headingRow = CExporter_Import_HeadingRowExtractor::extract($this->worksheet, $import);

        $rows = [];
        foreach ($this->worksheet->getRowIterator($startRow, $endRow) as $row) {
            $row = (new CExporter_Excel_Row($row, $headingRow))->toArray($nullValue, $calculateFormulas, $formatData);

            if ($import instanceof CExporter_Concern_WithMapping) {
                $row = $import->map($row);
            }

            $rows[] = $row;

            if ($import instanceof CExporter_Concern_WithProgressBar) {
                $import->getConsoleOutput()->progressAdvance();
            }
        }

        return $rows;
    }

    /**
     * @param object   $import
     * @param int|null $startRow
     * @param null     $nullValue
     * @param bool     $calculateFormulas
     * @param bool     $formatData
     *
     * @return Collection
     */
    public function toCollection($import, $startRow = null, $nullValue = null, $calculateFormulas = false, $formatData = false) {
        return c::collect(array_map(function (array $row) {
                            return c::collect($row);
                        }, $this->toArray($import, $startRow, $nullValue, $calculateFormulas, $formatData)));
    }

    /**
     * @param object $sheetExport
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function close($sheetExport) {
        $this->exportable = $sheetExport;

        if ($sheetExport instanceof CExporter_Concern_WithColumnFormatting) {
            foreach ($sheetExport->columnFormats() as $column => $format) {
                $this->formatColumn($column, $format);
            }
        }

        if ($sheetExport instanceof CExporter_Concern_ShouldAutoSize) {
            $this->autoSize();
        }

        $this->raise(new CExporter_Event_AfterSheet($this, $this->exportable));
    }

    /**
     * @param FromView $sheetExport
     * @param int|null $sheetIndex
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function fromView(CExporter_Concern_FromView $sheetExport, $sheetIndex = null) {
        $temporaryFile = $this->temporaryFileFactory->makeLocal();
        $temporaryFile->put($sheetExport->view()->render());

        $spreadsheet = $this->worksheet->getParent();

        /** @var Html $reader */
        $reader = IOFactory::createReader('Html');

        // If no sheetIndex given, insert content into the last sheet
        $reader->setSheetIndex($sheetIndex ?: $spreadsheet->getSheetCount() - 1);
        $reader->loadIntoExisting($temporaryFile->getLocalPath(), $spreadsheet);

        $temporaryFile->delete();
    }

    /**
     * @param CExporter_Concern_FromQuery $sheetExport
     * @param Worksheet $worksheet
     */
    public function fromQuery(CExporter_Concern_FromQuery $sheetExport, Worksheet $worksheet) {
        $sheetExport->query()->chunk($this->getChunkSize($sheetExport), function ($chunk) use ($sheetExport, $worksheet) {
            $this->appendRows($chunk, $sheetExport);
        });
    }

    /**
     * @param CExporter_Concern_FromQuery $sheetExport
     * @param Worksheet $worksheet
     */
    public function fromSql(CExporter_Concern_FromSql $sheetExport, Worksheet $worksheet) {

        CExporter_Helper_SqlHelper::chunkSqlResult($sheetExport->sql(), $this->getChunkSize($sheetExport), function ($chunk) use ($sheetExport, $worksheet) {
            $this->appendRows($chunk, $sheetExport);
        });
    }

    /**
     * @param FromCollection $sheetExport
     */
    public function fromCollection(CExporter_Concern_FromCollection $sheetExport) {
        $this->appendRows($sheetExport->collection()->all(), $sheetExport);
    }

    /**
     * @param CExporter_Concern_FromArray $sheetExport
     */
    public function fromArray(CExporter_Concern_FromArray $sheetExport) {
        $this->appendRows($sheetExport->getArray(), $sheetExport);
    }

    /**
     * @param FromIterator $sheetExport
     */
    public function fromIterator(CExporter_Concern_FromIterator $sheetExport) {
        $this->appendRows($sheetExport->iterator(), $sheetExport);
    }

    /**
     * @param FromGenerator $sheetExport
     */
    public function fromGenerator(CExporter_Concern_FromGenerator $sheetExport) {
        $this->appendRows($sheetExport->generator(), $sheetExport);
    }

    /**
     * @param array       $rows
     * @param string|null $startCell
     * @param bool        $strictNullComparison
     */
    public function append(array $rows, $startCell = null, $strictNullComparison = false) {
        if (!$startCell) {
            $startCell = 'A1';
        }

        if ($this->hasRows()) {
            $startCell = CExporter_Helper_CellHelper::getColumnFromCoordinate($startCell) . ($this->worksheet->getHighestRow() + 1);
        }
        
        $this->worksheet->fromArray($rows, null, $startCell, $strictNullComparison);
    }

    public function autoSize() {
        foreach ($this->buildColumnRange('A', $this->worksheet->getHighestDataColumn()) as $col) {
            $this->worksheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * @param string $column
     * @param string $format
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function formatColumn($column, $format) {
        $this->worksheet
                ->getStyle($column . '1:' . $column . $this->worksheet->getHighestRow())
                ->getNumberFormat()
                ->setFormatCode($format);
    }

    /**
     * @param int $chunkSize
     *
     * @return Sheet
     */
    public function chunkSize($chunkSize) {
        $this->chunkSize = $chunkSize;

        return $this;
    }

    /**
     * @return Worksheet
     */
    public function getDelegate() {
        return $this->worksheet;
    }

    /**
     * @param Chart|Chart[] $charts
     */
    public function addCharts($charts) {
        $charts = \is_array($charts) ? $charts : [$charts];

        foreach ($charts as $chart) {
            $this->worksheet->addChart($chart);
        }
    }

    /**
     * @param BaseDrawing|BaseDrawing[] $drawings
     */
    public function addDrawings($drawings) {
        $drawings = \is_array($drawings) ? $drawings : [$drawings];

        foreach ($drawings as $drawing) {
            $drawing->setWorksheet($this->worksheet);
        }
    }

    /**
     * @param string $concern
     *
     * @return string
     */
    public function hasConcern($concern) {
        return $this->exportable instanceof $concern;
    }

    /**
     * @param iterable $rows
     * @param object   $sheetExport
     */
    public function appendRows($rows, $sheetExport) {
        
        $rows = (new CCollection($rows))->flatMap(function ($row) use ($sheetExport) {
                    if ($sheetExport instanceof CExporter_Concern_WithMapping) {
                        $row = $sheetExport->map($row);
                    }

                    return CExporter_Helper_ArrayHelper::ensureMultipleRows(
                                    static::mapArraybleRow($row)
                    );
                })->toArray();
                
        $this->append(
                $rows, $sheetExport instanceof CExporter_Concern_WithCustomStartCell ? $sheetExport->startCell() : null, $this->hasStrictNullComparison($sheetExport)
        );
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public static function mapArraybleRow($row) {
        // When dealing with eloquent models, we'll skip the relations
        // as we won't be able to display them anyway.
        if (method_exists($row, 'attributesToArray')) {
            return $row->attributesToArray();
        }

        if ($row instanceof CInterface_Arrayable) {
            return $row->toArray();
        }

        // Convert StdObjects to arrays
        if (is_object($row)) {
            return json_decode(json_encode($row), true);
        }

        return $row;
    }

    /**
     * @param $sheetImport
     *
     * @return int
     */
    public function getStartRow($sheetImport) {
        return CExporter_Import_HeadingRowExtractor::determineStartRow($sheetImport);
    }

    /**
     * Disconnect the sheet.
     */
    public function disconnect() {
        $this->worksheet->disconnectCells();
        unset($this->worksheet);
    }

    /**
     * @param string $lower
     * @param string $upper
     *
     * @return \Generator
     */
    protected function buildColumnRange($lower, $upper) {
        $upper++;
        for ($i = $lower; $i !== $upper; $i++) {
            yield $i;
        }
    }

    /**
     * @return bool
     */
    private function hasRows() {
        $startCell = 'A1';
        if ($this->exportable instanceof CExporter_Concern_WithCustomStartCell) {
            $startCell = $this->exportable->startCell();
        }

        return $this->worksheet->cellExists($startCell);
    }

    /**
     * @param object $sheetExport
     *
     * @return bool
     */
    private function hasStrictNullComparison($sheetExport) {
        return $sheetExport instanceof CExporter_Concern_WithStrictNullComparison;
    }

    /**
     * @param object|WithCustomChunkSize $export
     *
     * @return int
     */
    private function getChunkSize($export) {
        if ($export instanceof CExporter_Concern_WithCustomChunkSize) {
            return $export->chunkSize();
        }

        return $this->chunkSize;
    }

}
