<?php
class CConsole_Result {
    private $result = null;

    private $idx = -1;

    const LINE_TYPE_ERROR = 'error';

    const LINE_TYPE_WARNING = 'warning';

    const LINE_TYPE_INFO = 'info';

    const LINE_TYPE_HINT = 'hint';

    const LINE_TYPE_DEFAULT = 'default';

    const DEFAULT_WIDTH = 36;

    protected $widthLabel = self::DEFAULT_WIDTH;

    protected $widthValue = self::DEFAULT_WIDTH;

    const TABLE_STYLES = 'default|borderless|compact|symfony-style-guide|box|box-double';

    const DEFAULT_STYLE = 'box-double';

    const DEFAULT_PATH = '';

    protected $styleTable = self::DEFAULT_STYLE;

    public function __construct() {
        $this->reset();
    }

    public function reset() {
        $this->result = c::collect();
        $this->idx = -1;
    }

    public function addError($label, $value) {
        $this->add($label, $value, true, self::LINE_TYPE_ERROR);
    }

    public function addWarning($label, $value) {
        $this->add($label, $value, true, self::LINE_TYPE_WARNING);
    }

    public function addInfo($label, $value) {
        $this->add($label, $value, true, self::LINE_TYPE_INFO);
    }

    public function addHint($value) {
        $this->add('*** HINT', $value, true, self::LINE_TYPE_HINT);
    }

    public function addErrorAndHint($label, $errorMessage, $hintMessage) {
        $this->addError($label, $errorMessage);
        $this->addHint($hintMessage);
    }

    public function addWarningAndHint($label, $warningMessage, $hintMessage) {
        $this->addWarning($label, $warningMessage);
        $this->addHint($hintMessage);
    }

    public function addInfoAndHint($label, $infoMessage, $hintMessage) {
        $this->addInfo($label, $infoMessage);
        $this->addHint($hintMessage);
    }

    public function add($label, $value, $forceLine = false, $lineType = self::LINE_TYPE_DEFAULT) {
        $this->result->push(
            [
                'label' => $label,
                'value' => $value,
                'isLine' => $forceLine,
                'lineType' => $lineType
            ]
        );
        $this->idx++;
    }

    public static function isMessageLine($lineType) {
        return ($lineType === self::LINE_TYPE_ERROR)
            | ($lineType === self::LINE_TYPE_WARNING)
            | ($lineType === self::LINE_TYPE_INFO);
    }

    public static function isHintLine($lineType) {
        return ($lineType === self::LINE_TYPE_HINT);
    }

    public function toArray() {
        return $this->result->toArray();
    }

    public function printToConsole(CConsole_Command $console, array $headers) {
        $rows = $this->toArray();
        $rowsTable = [];
        $rowsLine = [];
        foreach ($rows as $key => $row) {
            $label = carr::get($row, 'label', '');
            $value = carr::get($row, 'value', '');
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $isLine = carr::get($row, 'isLine', false);
            $lineType = carr::get($row, 'lineType', static::LINE_TYPE_DEFAULT);

            if (strlen($value) > $this->widthValue
                || $isLine
                || $lineType === static::LINE_TYPE_ERROR
                || $lineType === static::LINE_TYPE_WARNING
            ) {
                $rowsLine[] = $row;
            } else {
                $row['label'] = $this->formatCell($label, $this->widthLabel);
                $row['value'] = $this->formatCell($value, $this->widthValue);
                $rowsTable[] = [$row['label'], $row['value']];
            }
        }
        /*
         * table style:
         * 'default'
         * 'borderless'
         * 'compact'
         * 'symfony-style-guide'
         * 'box'
         * 'box-double'
         */
        $console->table($headers, $rowsTable, $this->styleTable);
        foreach ($rowsLine as $key => $line) {
            $label = carr::get($line, 'label', '');
            $value = carr::get($line, 'value', '');
            $lineType = carr::get($line, 'lineType', static::LINE_TYPE_DEFAULT);
            if ($label != '') {
                $console->info($label . ':');
            }
            if ($lineType === static::LINE_TYPE_ERROR) {
                $console->error($value);
            } elseif ($lineType === static::LINE_TYPE_WARNING) {
                $console->warn($value);
            } else {
                $console->line($value);
            }
        }
    }

    private function formatCell($string, $width) {
        $retVal = '';
        if (strlen($string) > $width) {
            $retVal = cstr::limit($string, $width, '');
        } elseif (strlen($string) < $width) {
            $retVal = str_pad($string, $width);
        } else {
            $retVal = $string;
        }
        return $retVal;
    }
}
