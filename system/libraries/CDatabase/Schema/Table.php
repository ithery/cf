<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDatabase_Schema_Table extends CDatabase_AbstractAsset {
    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var CDatabase_Schema_Column[]
     */
    protected $columns = [];

    /**
     * @var CDatabase_Schema_Index[]
     */
    protected $indexes = [];

    /**
     * @var string
     */
    protected $primaryKeyName = false;

    /**
     * @var CDatabase_Schema_ForeignKeyConstraint[]
     */
    protected $fkConstraints = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var null|CDatabase_Schema_SchemaConfig
     */
    protected $schemaConfig = null;

    /**
     * @var CDatabase_Schema_Index[]
     */
    private $implicitIndexes = [];

    /**
     * @param string                                  $tableName
     * @param CDatabase_Schema_Column[]               $columns
     * @param CDatabase_Schema_Index[]                $indexes
     * @param CDatabase_Schema_ForeignKeyConstraint[] $fkConstraints
     * @param int                                     $idGeneratorType
     * @param array                                   $options
     *
     * @throws CDatabase_Exception
     */
    public function __construct($tableName, array $columns = [], array $indexes = [], array $fkConstraints = [], $idGeneratorType = 0, array $options = []) {
        if (strlen($tableName) == 0) {
            throw CDatabase_Exception::invalidTableName($tableName);
        }

        $this->setName($tableName);

        foreach ($columns as $column) {
            $this->protectedAddColumn($column);
        }

        foreach ($indexes as $idx) {
            $this->protectedAddIndex($idx);
        }

        foreach ($fkConstraints as $constraint) {
            $this->protectedAddForeignKeyConstraint($constraint);
        }

        $this->options = $options;
    }

    /**
     * @param CDatabase_Schema_Config $schemaConfig
     *
     * @return void
     */
    public function setSchemaConfig(CDatabase_Schema_Config $schemaConfig) {
        $this->schemaConfig = $schemaConfig;
    }

    /**
     * @return int
     */
    protected function getMaxIdentifierLength() {
        if ($this->schemaConfig instanceof CDatabase_Schema_Config) {
            return $this->schemaConfig->getMaxIdentifierLength();
        }

        return 63;
    }

    /**
     * Sets the Primary Key.
     *
     * @param array       $columns
     * @param string|bool $indexName
     *
     * @return self
     */
    public function setPrimaryKey(array $columns, $indexName = false) {
        $this->protectedAddIndex($this->protectedCreateIndex($columns, $indexName ?: 'primary', true, true));

        foreach ($columns as $columnName) {
            $column = $this->getColumn($columnName);
            $column->setNotnull(true);
        }

        return $this;
    }

    /**
     * @param array       $columnNames
     * @param null|string $indexName
     * @param array       $flags
     * @param array       $options
     *
     * @return self
     */
    public function addIndex(array $columnNames, $indexName = null, array $flags = [], array $options = []) {
        if ($indexName == null) {
            $indexName = $this->generateIdentifierName(
                array_merge([$this->getName()], $columnNames),
                'idx',
                $this->getMaxIdentifierLength()
            );
        }

        return $this->protectedAddIndex($this->protectedCreateIndex($columnNames, $indexName, false, false, $flags, $options));
    }

    /**
     * Drops the primary key from this table.
     *
     * @return void
     */
    public function dropPrimaryKey() {
        $this->dropIndex($this->primaryKeyName);
        $this->primaryKeyName = false;
    }

    /**
     * Drops an index from this table.
     *
     * @param string $indexName the index name
     *
     * @throws CDatabase_Exception_SchemaException if the index does not exist
     *
     * @return void
     */
    public function dropIndex($indexName) {
        $indexName = $this->normalizeIdentifier($indexName);
        if (!$this->hasIndex($indexName)) {
            throw CDatabase_Schema_Exception::indexDoesNotExist($indexName, $this->name);
        }
        unset($this->indexes[$indexName]);
    }

    /**
     * @param array       $columnNames
     * @param null|string $indexName
     * @param array       $options
     *
     * @return self
     */
    public function addUniqueIndex(array $columnNames, $indexName = null, array $options = []) {
        if ($indexName === null) {
            $indexName = $this->generateIdentifierName(
                array_merge([$this->getName()], $columnNames),
                'uniq',
                $this->getMaxIdentifierLength()
            );
        }

        return $this->protectedAddIndex($this->protectedCreateIndex($columnNames, $indexName, true, false, [], $options));
    }

    /**
     * Renames an index.
     *
     * @param string      $oldIndexName the name of the index to rename from
     * @param null|string $newIndexName The name of the index to rename to.
     *                                  If null is given, the index name will be auto-generated.
     *
     * @throws CDatabase_Exception_SchemaException if no index exists for the given current name
     *                                             or if an index with the given new name already exists on this table
     *
     * @return self this table instance
     */
    public function renameIndex($oldIndexName, $newIndexName = null) {
        $oldIndexName = $this->normalizeIdentifier($oldIndexName);
        $normalizedNewIndexName = $this->normalizeIdentifier($newIndexName);

        if ($oldIndexName === $normalizedNewIndexName) {
            return $this;
        }

        if (!$this->hasIndex($oldIndexName)) {
            throw CDatabase_Exception_SchemaException::indexDoesNotExist($oldIndexName, $this->name);
        }

        if ($this->hasIndex($normalizedNewIndexName)) {
            throw CDatabase_Exception_SchemaException::indexAlreadyExists($normalizedNewIndexName, $this->name);
        }

        $oldIndex = $this->indexes[$oldIndexName];

        if ($oldIndex->isPrimary()) {
            $this->dropPrimaryKey();

            return $this->setPrimaryKey($oldIndex->getColumns(), $newIndexName);
        }

        unset($this->indexes[$oldIndexName]);

        if ($oldIndex->isUnique()) {
            return $this->addUniqueIndex($oldIndex->getColumns(), $newIndexName, $oldIndex->getOptions());
        }

        return $this->addIndex($oldIndex->getColumns(), $newIndexName, $oldIndex->getFlags(), $oldIndex->getOptions());
    }

    /**
     * Checks if an index begins in the order of the given columns.
     *
     * @param array $columnsNames
     *
     * @return bool
     */
    public function columnsAreIndexed(array $columnsNames) {
        foreach ($this->getIndexes() as $index) {
            /* @var $index Index */
            if ($index->spansColumns($columnsNames)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array  $columnNames
     * @param string $indexName
     * @param bool   $isUnique
     * @param bool   $isPrimary
     * @param array  $flags
     * @param array  $options
     *
     * @throws CDatabase_Exception_SchemaException
     *
     * @return CDatabase_Schema_Index
     */
    private function protectedCreateIndex(array $columnNames, $indexName, $isUnique, $isPrimary, array $flags = [], array $options = []) {
        if (preg_match('(([^a-zA-Z0-9_]+))', $this->normalizeIdentifier($indexName))) {
            throw CDatabase_Schema_Exception::indexNameInvalid($indexName);
        }

        foreach ($columnNames as $columnName => $indexColOptions) {
            if (is_numeric($columnName) && is_string($indexColOptions)) {
                $columnName = $indexColOptions;
            }

            if (!$this->hasColumn($columnName)) {
                throw CDatabase_Schema_Exception::columnDoesNotExist($columnName, $this->name);
            }
        }

        return new CDatabase_Schema_Index($indexName, $columnNames, $isUnique, $isPrimary, $flags, $options);
    }

    /**
     * @param string $columnName
     * @param string $typeName
     * @param array  $options
     *
     * @return Column
     */
    public function addColumn($columnName, $typeName, array $options = []) {
        $column = new CDatabase_Schema_Column($columnName, CDatabase_Type::getType($typeName), $options);

        $this->protectedAddColumn($column);

        return $column;
    }

    /**
     * @param CDatabase_Schema_Column $column
     *
     * @throws CDatabase_Exception_SchemaException
     *
     * @return void
     */
    protected function protectedAddColumn(CDatabase_Schema_Column $column) {
        $columnName = $column->getName();
        $columnName = $this->normalizeIdentifier($columnName);

        if (isset($this->columns[$columnName])) {
            throw CDatabase_Schema_Exception::columnAlreadyExists($this->getName(), $columnName);
        }

        $this->columns[$columnName] = $column;
    }

    /**
     * Renames a Column.
     *
     * @param string $oldColumnName
     * @param string $newColumnName
     *
     * @deprecated
     *
     * @throws CDatabase_Exception
     */
    public function renameColumn($oldColumnName, $newColumnName) {
        throw new CDatabase_Exception('Table#renameColumn() was removed, because it drops and recreates '
        . 'the column instead. There is no fix available, because a schema diff cannot reliably detect if a '
        . 'column was renamed or one column was created and another one dropped.');
    }

    /**
     * Change Column Details.
     *
     * @param string $columnName
     * @param array  $options
     *
     * @return self
     */
    public function changeColumn($columnName, array $options) {
        $column = $this->getColumn($columnName);
        $column->setOptions($options);

        return $this;
    }

    /**
     * Drops a Column from the Table.
     *
     * @param string $columnName
     *
     * @return self
     */
    public function dropColumn($columnName) {
        $columnName = $this->normalizeIdentifier($columnName);
        unset($this->columns[$columnName]);

        return $this;
    }

    /**
     * Adds a foreign key constraint.
     *
     * Name is inferred from the local columns.
     *
     * @param Table|string $foreignTable       Table schema instance or table name
     * @param array        $localColumnNames
     * @param array        $foreignColumnNames
     * @param array        $options
     * @param null|string  $constraintName
     *
     * @return self
     */
    public function addForeignKeyConstraint($foreignTable, array $localColumnNames, array $foreignColumnNames, array $options = [], $constraintName = null) {
        $constraintName = $constraintName ?: $this->generateIdentifierName(array_merge((array) $this->getName(), $localColumnNames), 'fk', $this->getMaxIdentifierLength());

        return $this->addNamedForeignKeyConstraint($constraintName, $foreignTable, $localColumnNames, $foreignColumnNames, $options);
    }

    /**
     * Adds a foreign key constraint.
     *
     * Name is to be generated by the database itself.
     *
     * @param CDatabase_Schema_Table|string $foreignTable       Table schema instance or table name
     * @param array                         $localColumnNames
     * @param array                         $foreignColumnNames
     * @param array                         $options
     *
     * @deprecated Use {@link addForeignKeyConstraint}
     *
     * @return self
     */
    public function addUnnamedForeignKeyConstraint($foreignTable, array $localColumnNames, array $foreignColumnNames, array $options = []) {
        return $this->addForeignKeyConstraint($foreignTable, $localColumnNames, $foreignColumnNames, $options);
    }

    /**
     * Adds a foreign key constraint with a given name.
     *
     * @param string                        $name
     * @param CDatabase_Schema_Table|string $foreignTable       Table schema instance or table name
     * @param array                         $localColumnNames
     * @param array                         $foreignColumnNames
     * @param array                         $options
     *
     * @throws CDatabase_Exception_SchemaException
     *
     * @return self
     *
     * @deprecated Use {@link addForeignKeyConstraint}
     */
    public function addNamedForeignKeyConstraint($name, $foreignTable, array $localColumnNames, array $foreignColumnNames, array $options = []) {
        if ($foreignTable instanceof CDatabase_Schema_Table) {
            foreach ($foreignColumnNames as $columnName) {
                if (!$foreignTable->hasColumn($columnName)) {
                    throw CDatabase_Schema_Exception::columnDoesNotExist($columnName, $foreignTable->getName());
                }
            }
        }

        foreach ($localColumnNames as $columnName) {
            if (!$this->hasColumn($columnName)) {
                throw CDatabase_Schema_Exception::columnDoesNotExist($columnName, $this->name);
            }
        }

        $constraint = new CDatabase_Schema_ForeignKeyConstraint(
            $localColumnNames,
            $foreignTable,
            $foreignColumnNames,
            $name,
            $options
        );
        $this->protectedAddForeignKeyConstraint($constraint);

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public function addOption($name, $value) {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Adds an index to the table.
     *
     * @param CDatabase_Schema_Index $indexCandidate
     *
     * @throws CDatabase_Exception_SchemaException
     *
     * @return self
     */
    protected function protectedAddIndex(CDatabase_Schema_Index $indexCandidate) {
        $indexName = $indexCandidate->getName();
        $indexName = $this->normalizeIdentifier($indexName);
        $replacedImplicitIndexes = [];

        foreach ($this->implicitIndexes as $name => $implicitIndex) {
            if ($implicitIndex->isFullfilledBy($indexCandidate) && isset($this->indexes[$name])) {
                $replacedImplicitIndexes[] = $name;
            }
        }

        if ((isset($this->indexes[$indexName]) && !in_array($indexName, $replacedImplicitIndexes, true))
            || ($this->primaryKeyName != false && $indexCandidate->isPrimary())
        ) {
            throw CDatabase_Exception_SchemaException::indexAlreadyExists($indexName, $this->name);
        }

        foreach ($replacedImplicitIndexes as $name) {
            unset($this->indexes[$name], $this->implicitIndexes[$name]);
        }

        if ($indexCandidate->isPrimary()) {
            $this->primaryKeyName = $indexName;
        }

        $this->indexes[$indexName] = $indexCandidate;

        return $this;
    }

    /**
     * @param CDatabase_Schema_ForeignKeyConstraint $constraint
     *
     * @return void
     */
    protected function protectedAddForeignKeyConstraint(CDatabase_Schema_ForeignKeyConstraint $constraint) {
        $constraint->setLocalTable($this);

        if (strlen($constraint->getName())) {
            $name = $constraint->getName();
        } else {
            $name = $this->generateIdentifierName(
                array_merge((array) $this->getName(), $constraint->getLocalColumns()),
                'fk',
                $this->getMaxIdentifierLength()
            );
        }
        $name = $this->normalizeIdentifier($name);

        $this->fkConstraints[$name] = $constraint;

        // add an explicit index on the foreign key columns. If there is already an index that fulfils this requirements drop the request.
        // In the case of __construct calling this method during hydration from schema-details all the explicitly added indexes
        // lead to duplicates. This creates computation overhead in this case, however no duplicate indexes are ever added (based on columns).
        $indexName = $this->generateIdentifierName(
            array_merge([$this->getName()], $constraint->getColumns()),
            'idx',
            $this->getMaxIdentifierLength()
        );
        $indexCandidate = $this->protectedCreateIndex($constraint->getColumns(), $indexName, false, false);

        foreach ($this->indexes as $existingIndex) {
            if ($indexCandidate->isFullfilledBy($existingIndex)) {
                return;
            }
        }

        $this->protectedAddIndex($indexCandidate);
        $this->implicitIndexes[$this->normalizeIdentifier($indexName)] = $indexCandidate;
    }

    /**
     * Returns whether this table has a foreign key constraint with the given name.
     *
     * @param string $constraintName
     *
     * @return bool
     */
    public function hasForeignKey($constraintName) {
        $constraintName = $this->normalizeIdentifier($constraintName);

        return isset($this->fkConstraints[$constraintName]);
    }

    /**
     * Returns the foreign key constraint with the given name.
     *
     * @param string $constraintName the constraint name
     *
     * @throws CDatabase_Exception_SchemaException if the foreign key does not exist
     *
     * @return ForeignKeyConstraint
     */
    public function getForeignKey($constraintName) {
        $constraintName = $this->normalizeIdentifier($constraintName);
        if (!$this->hasForeignKey($constraintName)) {
            throw CDatabase_Exception_SchemaException::foreignKeyDoesNotExist($constraintName, $this->name);
        }

        return $this->fkConstraints[$constraintName];
    }

    /**
     * Removes the foreign key constraint with the given name.
     *
     * @param string $constraintName the constraint name
     *
     * @throws CDatabase_Exception_SchemaException
     *
     * @return void
     */
    public function removeForeignKey($constraintName) {
        $constraintName = $this->normalizeIdentifier($constraintName);
        if (!$this->hasForeignKey($constraintName)) {
            throw CDatabase_Exception_SchemaException::foreignKeyDoesNotExist($constraintName, $this->name);
        }

        unset($this->fkConstraints[$constraintName]);
    }

    /**
     * Returns ordered list of columns (primary keys are first, then foreign keys, then the rest).
     *
     * @return CDatabase_Schema_Column[]
     */
    public function getColumns() {
        $primaryKeyColumns = [];
        if ($this->hasPrimaryKey()) {
            $primaryKeyColumns = $this->filterColumns($this->getPrimaryKey()->getColumns());
        }

        return array_merge($primaryKeyColumns, $this->getForeignKeyColumns(), $this->columns);
    }

    /**
     * Returns foreign key columns.
     *
     * @return CDatabase_Schema_Column[]
     */
    private function getForeignKeyColumns() {
        $foreignKeyColumns = [];
        foreach ($this->getForeignKeys() as $foreignKey) {
            /* @var $foreignKey ForeignKeyConstraint */
            $foreignKeyColumns = array_merge($foreignKeyColumns, $foreignKey->getColumns());
        }

        return $this->filterColumns($foreignKeyColumns);
    }

    /**
     * Returns only columns that have specified names.
     *
     * @param array $columnNames
     *
     * @return CDatabase_Schema_Column[]
     */
    private function filterColumns(array $columnNames) {
        return array_filter($this->columns, function ($columnName) use ($columnNames) {
            return in_array($columnName, $columnNames, true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Returns whether this table has a Column with the given name.
     *
     * @param string $columnName the column name
     *
     * @return bool
     */
    public function hasColumn($columnName) {
        $columnName = $this->normalizeIdentifier($columnName);

        return isset($this->columns[$columnName]);
    }

    /**
     * Returns the Column with the given name.
     *
     * @param string $columnName the column name
     *
     * @throws CDatabase_Exception_SchemaException if the column does not exist
     *
     * @return CDatabase_Schema_Column
     */
    public function getColumn($columnName) {
        $columnName = $this->normalizeIdentifier($columnName);
        if (!$this->hasColumn($columnName)) {
            throw CDatabase_Exception_SchemaException::columnDoesNotExist($columnName, $this->name);
        }

        return $this->columns[$columnName];
    }

    /**
     * Returns the primary key.
     *
     * @return null|CDatabase_Schema_Index the primary key, or null if this Table has no primary key
     */
    public function getPrimaryKey() {
        if (!$this->hasPrimaryKey()) {
            return null;
        }

        return $this->getIndex($this->primaryKeyName);
    }

    /**
     * Returns the primary key columns.
     *
     * @throws CDatabase_Exception
     *
     * @return array
     */
    public function getPrimaryKeyColumns() {
        if (!$this->hasPrimaryKey()) {
            throw new CDatabase_Exception('Table ' . $this->getName() . ' has no primary key.');
        }

        return $this->getPrimaryKey()->getColumns();
    }

    /**
     * Returns whether this table has a primary key.
     *
     * @return bool
     */
    public function hasPrimaryKey() {
        return $this->primaryKeyName && $this->hasIndex($this->primaryKeyName);
    }

    /**
     * Returns whether this table has an Index with the given name.
     *
     * @param string $indexName the index name
     *
     * @return bool
     */
    public function hasIndex($indexName) {
        $indexName = $this->normalizeIdentifier($indexName);

        return isset($this->indexes[$indexName]);
    }

    /**
     * Returns the Index with the given name.
     *
     * @param string $indexName the index name
     *
     * @throws CDatabase_Exception_SchemaException if the index does not exist
     *
     * @return CDatabase_Schema_Index
     */
    public function getIndex($indexName) {
        $indexName = $this->normalizeIdentifier($indexName);
        if (!$this->hasIndex($indexName)) {
            throw CDatabase_Exception_SchemaException::indexDoesNotExist($indexName, $this->name);
        }

        return $this->indexes[$indexName];
    }

    /**
     * @return CDatabase_Schema_Index[]
     */
    public function getIndexes() {
        return $this->indexes;
    }

    /**
     * Returns the foreign key constraints.
     *
     * @return CDatabase_Schema_ForeignKeyConstraint[]
     */
    public function getForeignKeys() {
        return $this->fkConstraints;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name) {
        return isset($this->options[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getOption($name) {
        return $this->options[$name];
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @param CDatabase_Schema_Visitor_Interface $visitor
     *
     * @return void
     */
    public function visit(CDatabase_Schema_Visitor_Interface $visitor) {
        $visitor->acceptTable($this);

        foreach ($this->getColumns() as $column) {
            $visitor->acceptColumn($this, $column);
        }

        foreach ($this->getIndexes() as $index) {
            $visitor->acceptIndex($this, $index);
        }

        foreach ($this->getForeignKeys() as $constraint) {
            $visitor->acceptForeignKey($this, $constraint);
        }
    }

    /**
     * Clone of a Table triggers a deep clone of all affected assets.
     *
     * @return void
     */
    public function __clone() {
        foreach ($this->columns as $k => $column) {
            $this->columns[$k] = clone $column;
        }
        foreach ($this->indexes as $k => $index) {
            $this->indexes[$k] = clone $index;
        }
        foreach ($this->fkConstraints as $k => $fk) {
            $this->fkConstraints[$k] = clone $fk;
            $this->fkConstraints[$k]->setLocalTable($this);
        }
    }

    /**
     * Normalizes a given identifier.
     *
     * Trims quotes and lowercases the given identifier.
     *
     * @param string $identifier the identifier to normalize
     *
     * @return string the normalized identifier
     */
    private function normalizeIdentifier($identifier) {
        return $this->trimQuotes(strtolower($identifier));
    }
}
