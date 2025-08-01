<?php
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Cell\DataType;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CExporter_Exportable_DataTable extends CExporter_Exportable implements CExporter_Concern_ShouldAutoSize, CExporter_Concern_FromDataTable, CExporter_Concern_WithHeadings, CExporter_Concern_WithMapping, CExporter_Concern_WithColumnFormatting {
    use CExporter_Exportable_Trait_UseColorTrait;
    /**
     * @var CElement_Component_DataTable
     */
    protected $table;

    protected $columnFormats;

    /**
     * @var string|CColor_FormatAbstract
     */
    protected $headingColor = null;

    /**
     * @var string|CColor_FormatAbstract
     */
    protected $headingBackgroundColor = null;

    /**
     * Possible values:
     * none, dashDot, dashDotDot, dashed, dotted, double, hair,
     * medium, mediumDashDot, mediumDashDotDot, slantDashDot
     * thick, thin.
     *
     * @var string
     */
    protected $borderStyle = Border::BORDER_NONE;

    /**
     * @var string|CColor_FormatAbstract
     */
    protected $borderColor = null;

    public function __construct(CElement_Component_DataTable $table) {
        $this->table = $table;
        $this->columnFormats = [];
    }

    public function setHeadingColor($color) {
        $this->headingColor = $color;

        return $this;
    }

    public function setHeadingBackgroundColor($color) {
        $this->headingBackgroundColor = $color;

        return $this;
    }

    /**
     * @return CElement_Component_DataTable
     */
    public function dataTable() {
        $this->table->setAjax(false);
        //d

        return $this->table;
    }

    public function map($data) {
        $columns = $this->table->getColumns();
        $newRow = [];
        $columnIntIndex = 0;
        $detectedDataType = null;
        $transformMaps = [
            'format_currency' => CExporter::DATA_TYPE_CURRENCY,
            'formatCurrency' => 'currency',
            'thousand_separator' => 'float',
            'formatDatetime' => 'datetime',
            'formatDate' => 'date',

        ];

        foreach ($columns as $column) {
            $value = carr::get($data, $column->getFieldname());
            foreach ($column->getTransforms() as $trans) {
                $tempDataType = null;
                if (is_string($trans)) {
                    $tempDataType = carr::get($transformMaps, $trans);
                }

                if ($tempDataType === null) {
                    $value = c::manager()->transform()->call($trans, $value, $data);
                } else {
                    $detectedDataType = $tempDataType;

                    break;
                }
            }
            if (strlen($column->format) > 0) {
                $tempValue = $column->format;
                foreach ($data as $k2 => $v2) {
                    if (strpos($tempValue, '{' . $k2 . '}') !== false) {
                        $tempValue = str_replace('{' . $k2 . '}', $v2, $tempValue);
                    }
                    $value = $tempValue;
                }
            }
            //if have callback
            $exportCallback = $column->determineExportCallback();
            if ($exportCallback != null) {
                $value = CFunction::factory($exportCallback)
                    ->addArg($data)
                    ->addArg($value)
                    ->setRequire($column->determineExportCallbackRequire())
                    ->execute();
                if ($value instanceof CRenderable) {
                    $value = $value->html();
                }
                if (is_array($value) && isset($value['html'], $value['js'])) {
                    $value = $value['html'];
                }
            }

            if (($this->table->cellCallbackFunc) != null) {
                $value = CFunction::factory($this->table->cellCallbackFunc)
                    ->addArg($this)
                    ->addArg($column->getFieldname())
                    ->addArg($data)
                    ->addArg($value)
                    ->setRequire($this->table->requires)
                    ->execute();
                if ($value instanceof CRenderable) {
                    $value = $value->html();
                }
                if (is_array($value) && isset($value['html'], $value['js'])) {
                    $value = $value['html'];
                }
            }
            if (is_string($value)) {
                $value = strip_tags($value);
            }
            $dataType = $column->getDataType();
            if ($dataType === null) {
                $dataType = static::detectDataTypeFromValue($value, $detectedDataType);
            }
            $columnIndex = Coordinate::stringFromColumnIndex($columnIntIndex + 1);
            $this->columnFormats[$columnIndex] = static::dataTypeToColumnFormat($dataType);

            $newRow[$column->getFieldname()] = static::convertToDataType($value, $dataType);
            $columnIntIndex++;
        }

        return $newRow;
    }

    public static function detectDataTypeFromValue($value, $detectedDataType = null) {
        if ($detectedDataType != null) {
            if (in_array($detectedDataType, ['number', 'integer', 'float', 'double', 'currency']) && !is_numeric($value)) {
                $detectedDataType = null;
            }
        }
        if ($detectedDataType !== null) {
            return $detectedDataType;
        }
        if ($value instanceof DateTimeInterface) {
            return 'datetime';
        }
        if (is_string($value) && strlen($value) >= 10) {
            if (preg_match('#^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$#', $value)) {
                return 'date';
            }
            if (preg_match('#(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})#', $value)) {
                return 'datetime';
            }
        }
        $nativeType = gettype($value);
        if ($nativeType !== 'string') {
            if (in_array($nativeType, ['integer', 'float', 'double'])) {
                return $nativeType;
            }
        }

        return null;
    }

    public static function convertToDataType($value, $dataType) {
        if ($dataType == 'currency') {
            return (double) $value;
        }
        if ($dataType == 'datetime') {
            if (!($value instanceof DateTimeInterface)) {
                $value = CCarbon::parse($value);
            }

            return Date::dateTimeToExcel($value);
        }

        if ($dataType == 'string') {
            return (string) $value;
        }

        return $value;
    }

    public static function dataTypeToColumnFormat($dataType) {
        if ($dataType == CExporter::DATA_TYPE_DATE) {
            return NumberFormat::FORMAT_DATE_YYYYMMDD;
        }
        if ($dataType == CExporter::DATA_TYPE_DATETIME) {
            return 'yyyy-mm-dd hh:mm:ss';
        }

        if ($dataType == CExporter::DATA_TYPE_CURRENCY) {
            return NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
        }
        if ($dataType == CExporter::DATA_TYPE_INTEGER || $dataType == CExporter::DATA_TYPE_NUMBER) {
            return NumberFormat::FORMAT_NUMBER;
        }
        if ($dataType == CExporter::DATA_TYPE_STRING) {
            return DataType::TYPE_STRING;
        }

        return null;
    }

    public function columnFormats() {
        return $this->columnFormats;
    }

    public function headings() {
        $columns = $this->table->getColumns();
        $heading = [];
        foreach ($columns as $column) {
            $heading[] = $column->determineExportLabel();
        }

        return $heading;
    }

    public function handleAfterSheet(CExporter_Event_AfterSheet $event) {
        $worksheet = $event->sheet->getDelegate();
        $columnString = $worksheet->getHighestColumn();
        $lastColumn = Coordinate::columnIndexFromString($columnString);

        // last column as letter value (e.g., D)
        $lastColumnStr = Coordinate::stringFromColumnIndex($lastColumn);

        // set up a style array for cell formatting
        $styleTextCenter = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ];
        if ($this->headingBackgroundColor) {
            $worksheet->getStyle('A1:' . $lastColumnStr . '1')->applyFromArray(
                [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => $this->toSpreadsheetColor($this->headingBackgroundColor)]
                    ],
                ],
            );
        }
        if ($this->headingColor) {
            $worksheet->getStyle('A1:' . $lastColumnStr . '1')->applyFromArray(
                [
                    'font' => [
                        'color' => ['argb' => $this->toSpreadsheetColor($this->headingColor)],
                    ],

                ],
            );
        }
        $reportHeaders = $this->table->getReportHeaders();
        $headersCount = count($reportHeaders);
        if ($headersCount > 0) {
            // at row 1, insert 2 rows
            $worksheet->insertNewRowBefore(1, $headersCount);
            for ($i = 1; $i <= $headersCount; $i++) {
                // merge cells for full-width
                $worksheet->mergeCells(sprintf('A%d:%s%d', $i, $lastColumnStr, $i));

                // assign cell values
                $worksheet->setCellValue(sprintf('A%d', $i), $reportHeaders[$i - 1]);
            }
        }
        //$worksheet->getStyle(sprintf('A%d:A%d', $i, $headersCount))->applyFromArray($styleTextCenter);

        $lastRow = $worksheet->getHighestRow();

        $event->sheet->autoSize();

        $footerFields = $this->table->getFooterFields();
        $currentRow = $lastRow;
        foreach ($footerFields as $footerField) {
            $currentRow = $currentRow + 1;
            $label = $footerField->getLabel();
            $value = $footerField->getValue();
            $dataType = static::detectDataTypeFromValue($value);
            $worksheet->setCellValue('A' . $currentRow, $label);
            $worksheet->mergeCells(sprintf('A%d:%s%d', $currentRow, $lastColumnStr, $currentRow));
            if ($dataType) {
                $worksheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($lastColumn) . $currentRow, $value, $dataType);
            } else {
                $worksheet->setCellValue(Coordinate::stringFromColumnIndex($lastColumn) . $currentRow, $value, $dataType);
            }
        }

        if ($this->borderColor) {
            $worksheet->getStyle(
                'A1:'
                . $worksheet->getHighestColumn()
                . $worksheet->getHighestRow()
            )->applyFromArray([
                'borders' => [
                    'outline' => [
                        'color' => ['argb' => $this->toSpreadsheetColor($this->borderColor)],
                    ]
                ]
            ]);
        }
        if ($this->borderStyle) {
            $worksheet->getStyle(
                'A1:'
                . $worksheet->getHighestColumn()
                . $worksheet->getHighestRow()
            )->applyFromArray([
                'borders' => [
                    'outline' => [
                        'style' => $this->borderStyle,
                    ]
                ]
            ]);
        }
        parent::handleAfterSheet($event);
    }
}
