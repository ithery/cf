<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * An abstraction class for a foreign key constraint.
 *
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 12:35:33 PM
 */
class CDatabase_Schema_ForeignKeyConstraint extends CDatabase_AbstractAsset implements CDatabase_Schema_Constraint {
    /**
     * Instance of the referencing table the foreign key constraint is associated with.
     *
     * @var CDatabase_Schema_Table
     */
    protected $_localTable;

    /**
     * Asset identifier instances of the referencing table column names the foreign key constraint is associated with.
     * array($columnName => Identifier)
     *
     * @var CDatabase_Schema_Identifier[]
     */
    protected $_localColumnNames;

    /**
     * Table or asset identifier instance of the referenced table name the foreign key constraint is associated with.
     *
     * @var CDatabase_Schema_Table|Identifier
     */
    protected $_foreignTableName;

    /**
     * Asset identifier instances of the referenced table column names the foreign key constraint is associated with.
     * array($columnName => Identifier)
     *
     * @var CDatabase_Schema_Identifier[]
     */
    protected $_foreignColumnNames;

    /**
     * @var array options associated with the foreign key constraint
     */
    protected $_options;

    /**
     * Initializes the foreign key constraint.
     *
     * @param array                         $localColumnNames   names of the referencing table columns
     * @param CDatabase_Schema_Table|string $foreignTableName   referenced table
     * @param array                         $foreignColumnNames names of the referenced table columns
     * @param string|null                   $name               name of the foreign key constraint
     * @param array                         $options            options associated with the foreign key constraint
     */
    public function __construct(array $localColumnNames, $foreignTableName, array $foreignColumnNames, $name = null, array $options = []) {
        $this->_setName($name);
        $identifierConstructorCallback = function ($column) {
            return new CDatabase_Schema_Identifier($column);
        };
        $this->_localColumnNames = $localColumnNames ? array_combine($localColumnNames, array_map($identifierConstructorCallback, $localColumnNames)) : [];

        if ($foreignTableName instanceof CDatabase_Schema_Table) {
            $this->_foreignTableName = $foreignTableName;
        } else {
            $this->_foreignTableName = new CDatabase_Schema_Identifier($foreignTableName);
        }

        $this->_foreignColumnNames = $foreignColumnNames ? array_combine($foreignColumnNames, array_map($identifierConstructorCallback, $foreignColumnNames)) : [];
        $this->_options = $options;
    }

    /**
     * Returns the name of the referencing table
     * the foreign key constraint is associated with.
     *
     * @return string
     */
    public function getLocalTableName() {
        return $this->_localTable->getName();
    }

    /**
     * Sets the Table instance of the referencing table
     * the foreign key constraint is associated with.
     *
     * @param CDatabase_Schema_Table $table instance of the referencing table
     *
     * @return void
     */
    public function setLocalTable(CDatabase_Schema_Table $table) {
        $this->_localTable = $table;
    }

    /**
     * @return CDatabase_Schema_Table
     */
    public function getLocalTable() {
        return $this->_localTable;
    }

    /**
     * Returns the names of the referencing table columns
     * the foreign key constraint is associated with.
     *
     * @return array
     */
    public function getLocalColumns() {
        return array_keys($this->_localColumnNames);
    }

    /**
     * Returns the quoted representation of the referencing table column names
     * the foreign key constraint is associated with.
     *
     * But only if they were defined with one or the referencing table column name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param CDatabase_Platform $platform the platform to use for quotation
     *
     * @return array
     */
    public function getQuotedLocalColumns(CDatabase_Platform $platform) {
        $columns = [];

        foreach ($this->_localColumnNames as $column) {
            $columns[] = $column->getQuotedName($platform);
        }

        return $columns;
    }

    /**
     * Returns unquoted representation of local table column names for comparison with other FK
     *
     * @return array
     */
    public function getUnquotedLocalColumns() {
        return array_map([$this, 'trimQuotes'], $this->getLocalColumns());
    }

    /**
     * Returns unquoted representation of foreign table column names for comparison with other FK
     *
     * @return array
     */
    public function getUnquotedForeignColumns() {
        return array_map([$this, 'trimQuotes'], $this->getForeignColumns());
    }

    /**
     * {@inheritdoc}
     *
     * @see getLocalColumns
     */
    public function getColumns() {
        return $this->getLocalColumns();
    }

    /**
     * Returns the quoted representation of the referencing table column names
     * the foreign key constraint is associated with.
     *
     * But only if they were defined with one or the referencing table column name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param CDatabase_Platform $platform the platform to use for quotation
     *
     * @see getQuotedLocalColumns
     *
     * @return array
     */
    public function getQuotedColumns(CDatabase_Platform $platform) {
        return $this->getQuotedLocalColumns($platform);
    }

    /**
     * Returns the name of the referenced table
     * the foreign key constraint is associated with.
     *
     * @return string
     */
    public function getForeignTableName() {
        return $this->_foreignTableName->getName();
    }

    /**
     * Returns the non-schema qualified foreign table name.
     *
     * @return string
     */
    public function getUnqualifiedForeignTableName() {
        $parts = explode('.', $this->_foreignTableName->getName());

        return strtolower(end($parts));
    }

    /**
     * Returns the quoted representation of the referenced table name
     * the foreign key constraint is associated with.
     *
     * But only if it was defined with one or the referenced table name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param CDatabase_Platform $platform the platform to use for quotation
     *
     * @return string
     */
    public function getQuotedForeignTableName(CDatabase_Platform $platform) {
        return $this->_foreignTableName->getQuotedName($platform);
    }

    /**
     * Returns the names of the referenced table columns
     * the foreign key constraint is associated with.
     *
     * @return array
     */
    public function getForeignColumns() {
        return array_keys($this->_foreignColumnNames);
    }

    /**
     * Returns the quoted representation of the referenced table column names
     * the foreign key constraint is associated with.
     *
     * But only if they were defined with one or the referenced table column name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param CDatabase_Platform $platform the platform to use for quotation
     *
     * @return array
     */
    public function getQuotedForeignColumns(CDatabase_Platform $platform) {
        $columns = [];

        foreach ($this->_foreignColumnNames as $column) {
            $columns[] = $column->getQuotedName($platform);
        }

        return $columns;
    }

    /**
     * Returns whether or not a given option
     * is associated with the foreign key constraint.
     *
     * @param string $name name of the option to check
     *
     * @return bool
     */
    public function hasOption($name) {
        return isset($this->_options[$name]);
    }

    /**
     * Returns an option associated with the foreign key constraint.
     *
     * @param string $name name of the option the foreign key constraint is associated with
     *
     * @return mixed
     */
    public function getOption($name) {
        return $this->_options[$name];
    }

    /**
     * Returns the options associated with the foreign key constraint.
     *
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * Returns the referential action for UPDATE operations
     * on the referenced table the foreign key constraint is associated with.
     *
     * @return string|null
     */
    public function onUpdate() {
        return $this->onEvent('onUpdate');
    }

    /**
     * Returns the referential action for DELETE operations
     * on the referenced table the foreign key constraint is associated with.
     *
     * @return string|null
     */
    public function onDelete() {
        return $this->onEvent('onDelete');
    }

    /**
     * Returns the referential action for a given database operation
     * on the referenced table the foreign key constraint is associated with.
     *
     * @param string $event name of the database operation/event to return the referential action for
     *
     * @return string|null
     */
    private function onEvent($event) {
        if (isset($this->_options[$event])) {
            $onEvent = strtoupper($this->_options[$event]);

            if (!in_array($onEvent, ['NO ACTION', 'RESTRICT'])) {
                return $onEvent;
            }
        }

        return false;
    }

    /**
     * Checks whether this foreign key constraint intersects the given index columns.
     *
     * Returns `true` if at least one of this foreign key's local columns
     * matches one of the given index's columns, `false` otherwise.
     *
     * @param CDatabase_Schema_Index $index the index to be checked against
     *
     * @return bool
     */
    public function intersectsIndexColumns(CDatabase_Schema_Index $index) {
        foreach ($index->getColumns() as $indexColumn) {
            foreach ($this->_localColumnNames as $localColumn) {
                if (strtolower($indexColumn) === strtolower($localColumn->getName())) {
                    return true;
                }
            }
        }

        return false;
    }
}
