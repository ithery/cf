<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 12:18:31 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Table Diff.
 */
class CDatabase_Schema_Table_Diff {

    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string|boolean
     */
    public $newName = false;

    /**
     * All added fields.
     *
     * @var CDatabase_Schema_Column[]
     */
    public $addedColumns;

    /**
     * All changed fields.
     *
     * @var CDatabase_Schema_Column_Diff[]
     */
    public $changedColumns = [];

    /**
     * All removed fields.
     *
     * @var CDatabase_Schema_Column[]
     */
    public $removedColumns = [];

    /**
     * Columns that are only renamed from key to column instance name.
     *
     * @var CDatabase_Schema_Column[]
     */
    public $renamedColumns = [];

    /**
     * All added indexes.
     *
     * @var CDatabase_SchemaIndex[]
     */
    public $addedIndexes = [];

    /**
     * All changed indexes.
     *
     * @var CDatabase_Schema_Index[]
     */
    public $changedIndexes = [];

    /**
     * All removed indexes
     *
     * @var CDatabase_Schema_Index[]
     */
    public $removedIndexes = [];

    /**
     * Indexes that are only renamed but are identical otherwise.
     *
     * @var CDatabase_Schema_Index[]
     */
    public $renamedIndexes = [];

    /**
     * All added foreign key definitions
     *
     * @var CDatabase_Schema_ForeignKeyConstraint[]
     */
    public $addedForeignKeys = [];

    /**
     * All changed foreign keys
     *
     * @var CDatabase_Schema_ForeignKeyConstraint[]
     */
    public $changedForeignKeys = [];

    /**
     * All removed foreign keys
     *
     * @var CDatabase_Schema_ForeignKeyConstraint[]|string[]
     */
    public $removedForeignKeys = [];

    /**
     * @var CDatabase_Schema_Table|null
     */
    public $fromTable;

    /**
     * Constructs an TableDiff object.
     *
     * @param string                        $tableName
     * @param CDatabase_Schema_Column[]     $addedColumns
     * @param CDatabase_Schema_ColumnDiff[] $changedColumns
     * @param CDatabase_Schema_Column[]     $removedColumns
     * @param CDatabase_Schema_Index[]      $addedIndexes
     * @param CDatabase_Schema_Index[]      $changedIndexes
     * @param CDatabase_Schema_Index[]      $removedIndexes
     * @param CDatabase_Schema_Table|null   $fromTable
     */
    public function __construct($tableName, $addedColumns = [], $changedColumns = [], $removedColumns = [], $addedIndexes = [], $changedIndexes = [], $removedIndexes = [], CDatabase_Schema_Table $fromTable = null) {
        $this->name = $tableName;
        $this->addedColumns = $addedColumns;
        $this->changedColumns = $changedColumns;
        $this->removedColumns = $removedColumns;
        $this->addedIndexes = $addedIndexes;
        $this->changedIndexes = $changedIndexes;
        $this->removedIndexes = $removedIndexes;
        $this->fromTable = $fromTable;
    }

    /**
     * @param CDatabase_Platform $platform The platform to use for retrieving this table diff's name.
     *
     * @return CDatabase_Schema_Identifier
     */
    public function getName(CDatabase_Platform $platform) {
        return new CDatabase_Schema_Identifier(
                $this->fromTable instanceof CDatabase_Schema_Table ? $this->fromTable->getQuotedName($platform) : $this->name
        );
    }

    /**
     * @return CDatabase_Schema_Identifier|string|bool
     */
    public function getNewName() {
        return $this->newName ? new CDatabase_Schema_Identifier($this->newName) : $this->newName;
    }

}
