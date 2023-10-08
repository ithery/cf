<?php

abstract class CDatabase_Schema_Grammar extends CDatabase_Grammar {
    use CDatabase_Trait_CompileJsonPathTrait;
    /**
     * The possible column modifiers.
     *
     * @var string[]
     */
    protected $modifiers = [];

    /**
     * If this Grammar supports schema changes wrapped in a transaction.
     *
     * @var bool
     */
    protected $transactions = false;

    /**
     * The commands to be executed outside of create or alter command.
     *
     * @var array
     */
    protected $fluentCommands = [];

    /**
     * Compile a create database command.
     *
     * @param string     $name
     * @param \CDatabase $connection
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function compileCreateDatabase($name, $connection) {
        throw new LogicException('This database driver does not support creating databases.');
    }

    /**
     * Compile a drop database if exists command.
     *
     * @param string $name
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function compileDropDatabaseIfExists($name) {
        throw new LogicException('This database driver does not support dropping databases.');
    }

    /**
     * Compile a rename column command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     * @param \CDatabase_Connection       $connection
     *
     * @return array
     */
    public function compileRenameColumn(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command, CDatabase_Connection $connection) {
        return CDatabase_Schema_Grammar_RenameColumn::compile($this, $blueprint, $command, $connection);
    }

    /**
     * Compile a change column command into a series of SQL statements.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     * @param \CDatabase                  $connection
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function compileChange(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command, CDatabase_Connection $connection) {
        return CDatabase_Schema_Grammar_ChangeColumn::compile($this, $blueprint, $command, $connection);
    }

    /**
     * Compile a fulltext index key command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileFulltext(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        throw new RuntimeException('This database driver does not support fulltext index creation.');
    }

    /**
     * Compile a drop fulltext index command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDropFulltext(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        throw new RuntimeException('This database driver does not support fulltext index creation.');
    }

    /**
     * Compile a foreign key command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileForeign(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        // We need to prepare several of the elements of the foreign key definition
        // before we can create the SQL, such as wrapping the tables and convert
        // an array of columns to comma-delimited strings for the SQL queries.
        $sql = sprintf(
            'alter table %s add constraint %s ',
            $this->wrapTable($blueprint),
            $this->wrap($command->index)
        );

        // Once we have the initial portion of the SQL statement we will add on the
        // key name, table name, and referenced columns. These will complete the
        // main portion of the SQL statement and this SQL will almost be done.
        $sql .= sprintf(
            'foreign key (%s) references %s (%s)',
            $this->columnize($command->columns),
            $this->wrapTable($command->on),
            $this->columnize((array) $command->references)
        );

        // Once we have the basic foreign key creation statement constructed we can
        // build out the syntax for what should happen on an update or delete of
        // the affected columns, which will get something like "cascade", etc.
        if (!is_null($command->onDelete)) {
            $sql .= " on delete {$command->onDelete}";
        }

        if (!is_null($command->onUpdate)) {
            $sql .= " on update {$command->onUpdate}";
        }

        return $sql;
    }

    /**
     * Compile the blueprint's column definitions.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     *
     * @return array
     */
    protected function getColumns(CDatabase_Schema_Blueprint $blueprint) {
        $columns = [];

        foreach ($blueprint->getAddedColumns() as $column) {
            // Each of the column types have their own compiler functions which are tasked
            // with turning the column definition into its SQL format for this platform
            // used by the connection. The column's modifiers are compiled and added.
            $sql = $this->wrap($column) . ' ' . $this->getType($column);

            $columns[] = $this->addModifiers($sql, $blueprint, $column);
        }

        return $columns;
    }

    /**
     * Get the SQL for the column data type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function getType(CBase_Fluent $column) {
        return $this->{'type' . ucfirst($column->type)}($column);
    }

    /**
     * Create the column definition for a generated, computed column type.
     *
     * @param \CBase_Fluent $column
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function typeComputed(CBase_Fluent $column) {
        throw new RuntimeException('This database driver does not support the computed type.');
    }

    /**
     * Add the column modifiers to the definition.
     *
     * @param string                      $sql
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $column
     *
     * @return string
     */
    protected function addModifiers($sql, CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        foreach ($this->modifiers as $modifier) {
            if (method_exists($this, $method = "modify{$modifier}")) {
                $sql .= $this->{$method}($blueprint, $column);
            }
        }

        return $sql;
    }

    /**
     * Get the primary key command if it exists on the blueprint.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param string                      $name
     *
     * @return null|\CBase_Fluent
     */
    protected function getCommandByName(CDatabase_Schema_Blueprint $blueprint, $name) {
        $commands = $this->getCommandsByName($blueprint, $name);

        if (count($commands) > 0) {
            return reset($commands);
        }
    }

    /**
     * Get all of the commands with a given name.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param string                      $name
     *
     * @return array
     */
    protected function getCommandsByName(CDatabase_Schema_Blueprint $blueprint, $name) {
        return array_filter($blueprint->getCommands(), function ($value) use ($name) {
            return $value->name == $name;
        });
    }

    /**
     * Add a prefix to an array of values.
     *
     * @param string $prefix
     * @param array  $values
     *
     * @return array
     */
    public function prefixArray($prefix, array $values) {
        return array_map(function ($value) use ($prefix) {
            return $prefix . ' ' . $value;
        }, $values);
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param mixed $table
     *
     * @return string
     */
    public function wrapTable($table) {
        return parent::wrapTable(
            $table instanceof CDatabase_Schema_Blueprint ? $table->getTable() : $table
        );
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param \CDatabase_Query_Expression|string $value
     * @param bool                               $prefixAlias
     *
     * @return string
     */
    public function wrap($value, $prefixAlias = false) {
        return parent::wrap(
            $value instanceof CBase_Fluent ? $value->name : $value,
            $prefixAlias
        );
    }

    /**
     * Format a value so that it can be used in "default" clauses.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function getDefaultValue($value) {
        if ($value instanceof CDatabase_Contract_Query_ExpressionInterface) {
            return $this->getValue($value);
        }

        return is_bool($value)
                    ? "'" . (int) $value . "'"
                    : "'" . (string) $value . "'";
    }

    /**
     * Create an empty Doctrine DBAL TableDiff from the Blueprint.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CDatabase_Schema_Manager   $schema
     *
     * @return \CDatabase_Schema_Table_Diff
     */
    public function getDoctrineTableDiff(CDatabase_Schema_Blueprint $blueprint, CDatabase_Schema_Manager $schema) {
        $table = $this->getTablePrefix() . $blueprint->getTable();

        return c::tap(new CDatabase_Schema_Table_Diff($table), function ($tableDiff) use ($schema, $table) {
            $tableDiff->fromTable = $schema->listTableDetails($table);
        });
    }

    /**
     * Get the fluent commands for the grammar.
     *
     * @return array
     */
    public function getFluentCommands() {
        return $this->fluentCommands;
    }

    /**
     * Check if this Grammar supports schema changes wrapped in a transaction.
     *
     * @return bool
     */
    public function supportsSchemaTransactions() {
        return $this->transactions;
    }

    /**
     * Compile the query to determine the list of tables.
     *
     * @return string
     */
    public function compileTableExists() {
        throw new LogicException('This database driver does not support check table exists.');
    }

    /**
     * Compile the command to enable foreign key constraints.
     *
     * @return string
     */
    public function compileEnableForeignKeyConstraints() {
        throw new LogicException('This database driver does not support enable foreign key constraints.');
    }

    /**
     * Compile the command to disable foreign key constraints.
     *
     * @return string
     */
    public function compileDisableForeignKeyConstraints() {
        throw new LogicException('This database driver does not support disable foreign key constraints.');
    }
}
