<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 3:41:22 PM
 */

/**
 * Represents the change of a column.
 */
class CDatabase_Schema_Column_Diff {
    /**
     * @var string
     */
    public $oldColumnName;

    /**
     * @var Column
     */
    public $column;

    /**
     * @var array
     */
    public $changedProperties = [];

    /**
     * @var Column
     */
    public $fromColumn;

    /**
     * @param string                  $oldColumnName
     * @param CDatabase_Schema_Column $column
     * @param string[]                $changedProperties
     * @param CDatabase_Schema_Column $fromColumn
     */
    public function __construct($oldColumnName, CDatabase_Schema_Column $column, array $changedProperties = [], Column $fromColumn = null) {
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
