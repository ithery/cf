<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Helper\Dimension as CssDimension;

class ColumnDimension extends Dimension {
    /**
     * Column index.
     *
     * @var string
     */
    private $columnIndex;

    /**
     * Column width.
     *
     * When this is set to a negative value, the column width should be ignored by IWriter
     *
     * @var float
     */
    private $width = -1;

    /**
     * Auto size?
     *
     * @var bool
     */
    private $autoSize = false;

    /**
     * Create a new ColumnDimension.
     *
     * @param string $pIndex Character column index
     */
    public function __construct($pIndex = 'A') {
        // Initialise values
        $this->columnIndex = $pIndex;

        // set dimension as unformatted by default
        parent::__construct(0);
    }

    /**
     * Get ColumnIndex.
     *
     * @return string
     */
    public function getColumnIndex() {
        return $this->columnIndex;
    }

    /**
     * Set ColumnIndex.
     *
     * @param string $pValue
     *
     * @return ColumnDimension
     */
    public function setColumnIndex($pValue) {
        $this->columnIndex = $pValue;

        return $this;
    }

    /**
     * Get column index as numeric.
     *
     * @return int
     */
    public function getColumnNumeric() {
        return Coordinate::columnIndexFromString($this->columnIndex);
    }

    /**
     * Set column index as numeric.
     *
     * @param int $index
     */
    public function setColumnNumeric($index) {
        $this->columnIndex = Coordinate::stringFromColumnIndex($index);

        return $this;
    }

    /**
     * Get Width.
     *
     * Each unit of column width is equal to the width of one character in the default font size.
     * By default, this will be the return value; but this method also accepts a unit of measure argument and will
     *     return the value converted to the specified UoM using an approximation method.
     *
     * @param null|string $unitOfMeasure
     *
     * @return float
     */
    public function getWidth($unitOfMeasure = null) {
        return ($unitOfMeasure === null || $this->width < 0)
            ? $this->width
            : (new CssDimension((string) $this->width))->toUnit($unitOfMeasure);
    }

    /**
     * Set Width.
     *
     * Each unit of column width is equal to the width of one character in the default font size.
     * By default, this will be the unit of measure for the passed value; but this method accepts a unit of measure
     *    argument, and will convert the value from the specified UoM using an approximation method.
     *
     * @param mixed       $width
     * @param null|string $unitOfMeasure
     *
     * @return $this
     */
    public function setWidth($width, $unitOfMeasure = null) {
        $this->width = ($unitOfMeasure === null || $width < 0)
            ? $width
            : (new CssDimension("{$width}{$unitOfMeasure}"))->width();

        return $this;
    }

    /**
     * Get Auto Size.
     *
     * @return bool
     */
    public function getAutoSize() {
        return $this->autoSize;
    }

    /**
     * Set Auto Size.
     *
     * @param bool $autosizeEnabled
     *
     * @return $this
     */
    public function setAutoSize($autosizeEnabled) {
        $this->autoSize = $autosizeEnabled;

        return $this;
    }
}
