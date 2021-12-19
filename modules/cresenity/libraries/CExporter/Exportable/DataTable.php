<?php
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CExporter_Exportable_DataTable extends CExporter_Exportable implements CExporter_Concern_ShouldAutoSize, CExporter_Concern_FromCollection, CExporter_Concern_WithHeadings, CExporter_Concern_WithMapping, CExporter_Concern_WithColumnFormatting {
    protected $table;

    protected $columnFormats;

    public function __construct(CElement_Component_DataTable $table) {
        $this->table = $table;
        $this->columnFormats = [];
    }

    public function collection() {
        $this->table->setAjax(false);

        return $this->table->getCollection();
    }

    public function map($data) {
        $columns = $this->table->getColumns();
        $newRow = [];
        $columnIntIndex = 0;
        $detectedDataType = null;
        $currencyTransforms = [
            'format_currency',
            'formatCurrency',
            'thousand_separator',
        ];
        foreach ($columns as $column) {
            $value = carr::get($data, $column->getFieldname());
            foreach ($column->transforms as $trans) {
                if (!in_array($trans->getFunction(), $currencyTransforms)) {
                    $value = $trans->execute($value);
                } else {
                    $detectedDataType = 'currency';
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
            if ($value[4] == '-' && $value[7] == '-') {
                if (strlen($value) >= 19) {
                    return 'datetime';
                } else {
                    return 'date';
                }
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
        if ($dataType == 'date') {
            return 'yyyy-mm-dd';
        }
        if ($dataType == 'datetime') {
            return 'yyyy-mm-dd hh:mm:ss';
        }

        if ($dataType == 'currency') {
            return NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
        }
        if ($dataType == 'int' || $dataType == 'number') {
            return NumberFormat::FORMAT_NUMBER;
        }
        if ($dataType == 'string') {
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
}
