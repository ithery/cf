<?php

defined('SYSPATH') or die('No direct access allowed.');


/**
 * Represents the change of a column.
 */
class CDatabase_Schema_Column_Diff {
    /**
     * @var string
     */
    public $oldColumnName;

    /**
     * @var CDatabase_Schema_Column
     */
    public $column;

    /**
     * @var array
     */
    public $changedProperties = [];

    /**
     * @var CDatabase_Schema_Column
     */
    public $fromColumn;

    /**
     * @param string                  $oldColumnName
     * @param CDatabase_Schema_Column $column
     * @param string[]                $changedProperties
     * @param CDatabase_Schema_Column $fromColumn
     */
    public function __construct($oldColumnName, CDatabase_Schema_Column $column, array $changedProperties = [], CDatabase_Schema_Column $fromColumn = null) {
        $this->oldColumnName = $oldColumnName;
        $this->column = $column;
        $this->changedProperties = $changedProperties;
        $this->fromColumn = $fromColumn;
    }

    /**
     * @param string $propertyName
     *
     * @return bool
     */
    public function hasChanged($propertyName) {
        return in_array($propertyName, $this->changedProperties);
    }

    /**
     * @return Identifier
     */
    public function getOldColumnName() {
        $quote = $this->fromColumn && $this->fromColumn->isQuoted();

        return new CDatabase_Schema_Identifier($this->oldColumnName, $quote);
    }
}
