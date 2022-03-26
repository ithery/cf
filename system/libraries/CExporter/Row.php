<?php
use PhpOffice\PhpSpreadsheet\Worksheet\Row as SpreadsheetRow;

class CExporter_Row implements ArrayAccess {
    use CExporter_Trait_DelegatedMacroableTrait;
    /**
     * @var array
     */
    protected $headingRow = [];

    /**
     * @var \Closure
     */
    protected $preparationCallback;

    /**
     * @var SpreadsheetRow
     */
    protected $row;

    /**
     * @var null|array
     */
    protected $rowCache;

    /**
     * @param SpreadsheetRow $row
     * @param array          $headingRow
     */
    public function __construct(SpreadsheetRow $row, array $headingRow = []) {
        $this->row = $row;
        $this->headingRow = $headingRow;
    }

    /**
     * @return SpreadsheetRow
     */
    public function getDelegate() {
        return $this->row;
    }

    /**
     * @param null        $nullValue
     * @param bool        $calculateFormulas
     * @param bool        $formatData
     * @param null|string $endColumn
     *
     * @return CCollection
     */
    public function toCollection($nullValue = null, $calculateFormulas = false, $formatData = true, $endColumn = null) {
        return new CCollection($this->toArray($nullValue, $calculateFormulas, $formatData, $endColumn));
    }

    /**
     * @param null        $nullValue
     * @param bool        $calculateFormulas
     * @param bool        $formatData
     * @param null|string $endColumn
     *
     * @return array
     */
    public function toArray($nullValue = null, $calculateFormulas = false, $formatData = true, $endColumn = null) {
        if (is_array($this->rowCache)) {
            return $this->rowCache;
        }

        $cells = [];

        $i = 0;
        foreach ($this->row->getCellIterator('A', $endColumn) as $cell) {
            $value = (new CExporter_Cell($cell))->getValue($nullValue, $calculateFormulas, $formatData);

            if (isset($this->headingRow[$i])) {
                $cells[$this->headingRow[$i]] = $value;
            } else {
                $cells[] = $value;
            }

            $i++;
        }

        if (isset($this->preparationCallback)) {
            $cells = ($this->preparationCallback)($cells, $this->row->getRowIndex());
        }

        $this->rowCache = $cells;

        return $cells;
    }

    /**
     * @param mixed $calculateFormulas
     *
     * @return bool
     */
    public function isEmpty($calculateFormulas = false) {
        return count(array_filter($this->toArray(null, $calculateFormulas, false))) === 0;
    }

    /**
     * @return int
     */
    public function getIndex() {
        return $this->row->getRowIndex();
    }

    public function offsetExists($offset) {
        return isset(($this->toArray())[$offset]);
    }

    public function offsetGet($offset) {
        return ($this->toArray())[$offset];
    }

    public function offsetSet($offset, $value) {
    }

    public function offsetUnset($offset) {
    }

    /**
     * @param \Closure $preparationCallback
     *
     * @internal
     */
    public function setPreparationCallback(Closure $preparationCallback = null) {
        $this->preparationCallback = $preparationCallback;
    }
}
