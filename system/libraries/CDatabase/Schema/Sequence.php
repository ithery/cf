<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Sequence structure.
 */
class CDatabase_Schema_Sequence extends CDatabase_AbstractAsset {
    /**
     * @var int
     */
    protected $allocationSize = 1;

    /**
     * @var int
     */
    protected $initialValue = 1;

    /**
     * @var null|int
     */
    protected $cache = null;

    /**
     * @param string   $name
     * @param int      $allocationSize
     * @param int      $initialValue
     * @param null|int $cache
     */
    public function __construct($name, $allocationSize = 1, $initialValue = 1, $cache = null) {
        $this->setName($name);
        $this->allocationSize = is_numeric($allocationSize) ? $allocationSize : 1;
        $this->initialValue = is_numeric($initialValue) ? $initialValue : 1;
        $this->cache = $cache;
    }

    /**
     * @return int
     */
    public function getAllocationSize() {
        return $this->allocationSize;
    }

    /**
     * @return int
     */
    public function getInitialValue() {
        return $this->initialValue;
    }

    /**
     * @return null|int
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * @param int $allocationSize
     *
     * @return CDatabase_Schema_Sequence
     */
    public function setAllocationSize($allocationSize) {
        $this->allocationSize = is_numeric($allocationSize) ? $allocationSize : 1;

        return $this;
    }

    /**
     * @param int $initialValue
     *
     * @return CDatabase_Schema_Sequence
     */
    public function setInitialValue($initialValue) {
        $this->initialValue = is_numeric($initialValue) ? $initialValue : 1;

        return $this;
    }

    /**
     * @param int $cache
     *
     * @return CDatabase_Schema_Sequence
     */
    public function setCache($cache) {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Checks if this sequence is an autoincrement sequence for a given table.
     *
     * This is used inside the comparator to not report sequences as missing,
     * when the "from" schema implicitly creates the sequences.
     *
     * @param CDatabase_Schema_Table $table
     *
     * @return bool
     */
    public function isAutoIncrementsFor(CDatabase_Schema_Table $table) {
        if (!$table->hasPrimaryKey()) {
            return false;
        }

        $pkColumns = $table->getPrimaryKey()->getColumns();

        if (count($pkColumns) != 1) {
            return false;
        }

        $column = $table->getColumn($pkColumns[0]);

        if (!$column->getAutoincrement()) {
            return false;
        }

        $sequenceName = $this->getShortestName($table->getNamespaceName());
        $tableName = $table->getShortestName($table->getNamespaceName());
        $tableSequenceName = sprintf('%s_%s_seq', $tableName, $column->getShortestName($table->getNamespaceName()));

        return $tableSequenceName === $sequenceName;
    }

    /**
     * @param CDatabase_Schema_Visitor_Interface $visitor
     *
     * @return void
     */
    public function visit(CDatabase_Schema_Visitor_Interface $visitor) {
        $visitor->acceptSequence($this);
    }
}
