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
     * @return string
     */
    public function compileCreateDatabase($name, $connection) {
        return sprintf('create database %s', $this->wrapValue($name));
    }

    /**
     * Compile a drop database if exists command.
     *
     * @param string $name
     *
     * @return string
     */
    public function compileDropDatabaseIfExists($name) {
        return sprintf('drop database if exists %s', $this->wrapValue($name));
    }

    /**
     * Compile the query to determine the schemas.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileSchemas() {
        throw new RuntimeException('This database driver does not support retrieving schemas.');
    }

    /**
     * Compile the query to determine if the given table exists.
     *
     * @param null|string $schema
     * @param string      $table
     *
     * @return null|string
     */
    public function compileTableExists($schema, $table) {
        // empty implementation
    }

    /**
     * Compile the query to determine the tables.
     *
     * @param null|string|string[] $schema
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileTables($schema) {
        throw new RuntimeException('This database driver does not support retrieving tables.');
    }

    /**
     * Compile the query to determine the views.
     *
     * @param null|string|string[] $schema
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileViews($schema) {
        throw new RuntimeException('This database driver does not support retrieving views.');
    }

    /**
     * Compile the query to determine the user-defined types.
     *
     * @param null|string|string[] $schema
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileTypes($schema) {
        throw new RuntimeException('This database driver does not support retrieving user-defined types.');
    }

    /**
     * Compile the query to determine the columns.
     *
     * @param null|string $schema
     * @param string      $table
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileColumns($schema, $table) {
        throw new RuntimeException('This database driver does not support retrieving columns.');
    }

    /**
     * Compile the query to determine the indexes.
     *
     * @param null|string $schema
     * @param string      $table
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileIndexes($schema, $table) {
        throw new RuntimeException('This database driver does not support retrieving indexes.');
    }

    /**
     * Compile a vector index key command.
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function compileVectorIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        throw new RuntimeException('The database driver in use does not support vector indexes.');
    }

    /**
     * Compile the query to determine the foreign keys.
     *
     * @param null|string $schema
     * @param string      $table
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileForeignKeys($schema, $table) {
        throw new RuntimeException('This database driver does not support retrieving foreign keys.');
    }

    /**
     * Compile a rename column command.
     *
     * @return list<string>|string
     */
    public function compileRenameColumn(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'alter table %s rename column %s to %s',
            $this->wrapTable($blueprint),
            $this->wrap($command->from),
            $this->wrap($command->to)
        );
    }

    /**
     * Compile a change column command into a series of SQL statements.
     *
     * @throws \RuntimeException
     *
     * @return list<string>|string
     */
    public function compileChange(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        throw new RuntimeException('This database driver does not support modifying columns.');
    }

    /**
     * Compile a fulltext index key command.
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
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileDropFullText(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        throw new RuntimeException('This database driver does not support fulltext index removal.');
    }

    /**
     * Compile a foreign key command.
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
     * Compile a drop foreign key command.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compileDropForeign(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        throw new RuntimeException('This database driver does not support dropping foreign keys.');
    }

    /**
     * Compile the blueprint's added column definitions.
     *
     * @return array
     */
    protected function getColumns(CDatabase_Schema_Blueprint $blueprint) {
        $columns = [];

        foreach ($blueprint->getAddedColumns() as $column) {
            $columns[] = $this->getColumn($blueprint, $column);
        }

        return $columns;
    }

    /**
     * Compile the column definition.
     *
     * @param \CDatabase_Schema_ColumnDefinition $column
     *
     * @return string
     */
    protected function getColumn(CDatabase_Schema_Blueprint $blueprint, $column) {
        // Each of the column types has their own compiler functions, which are tasked
        // with turning the column definition into its SQL format for this platform
        // used by the connection. The column's modifiers are compiled and added.
        $sql = $this->wrap($column) . ' ' . $this->getType($column);

        return $this->addModifiers($sql, $blueprint, $column);
    }

    /**
     * Get the SQL for the column data type.
     *
     * @return string
     */
    protected function getType(CBase_Fluent $column) {
        return $this->{'type' . ucfirst($column->type)}($column);
    }

    /**
     * Create the column definition for a generated, computed column type.
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function typeComputed(CBase_Fluent $column) {
        throw new RuntimeException('This database driver does not support the computed type.');
    }

    /**
     * Create the column definition for a vector type.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function typeVector(CBase_Fluent $column) {
        throw new RuntimeException('This database driver does not support the vector type.');
    }

    /**
     * Create the column definition for a tsvector type.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function typeTsvector(CBase_Fluent $column) {
        throw new RuntimeException('This database driver does not support the tsvector type.');
    }

    /**
     * Create the column definition for a raw column type.
     *
     * @return string
     */
    protected function typeRaw(CBase_Fluent $column) {
        return $column->offsetGet('definition');
    }

    /**
     * Add the column modifiers to the definition.
     *
     * @param string $sql
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
     * Get the command with a given name if it exists on the blueprint.
     *
     * @param string $name
     *
     * @return null|\CBase_Fluent
     */
    protected function getCommandByName(CDatabase_Schema_Blueprint $blueprint, $name) {
        $commands = $this->getCommandsByName($blueprint, $name);

        if (count($commands) > 0) {
            return array_first($commands);
        }
    }

    /**
     * Get all of the commands with a given name.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getCommandsByName(CDatabase_Schema_Blueprint $blueprint, $name) {
        return array_filter($blueprint->getCommands(), function ($value) use ($name) {
            return $value->name == $name;
        });
    }

    /**
     * Determine if a command with a given name exists on the blueprint.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function hasCommand(CDatabase_Schema_Blueprint $blueprint, $name) {
        foreach ($blueprint->getCommands() as $command) {
            if ($command->name === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a prefix to an array of values.
     *
     * @param string        $prefix
     * @param array<string> $values
     *
     * @return array<string>
     */
    public function prefixArray($prefix, array $values) {
        return array_map(function ($value) use ($prefix) {
            return $prefix . ' ' . $value;
        }, $values);
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param null|string $prefix
     * @param mixed       $table
     *
     * @return string
     */
    public function wrapTable($table, $prefix = null) {
        return parent::wrapTable(
            $table instanceof CDatabase_Schema_Blueprint ? $table->getTable() : $table,
            $prefix
        );
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param \CBase_Fluent|\CDatabase_Query_Expression|string $value
     * @param bool                                             $prefixAlias
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
        if ($value instanceof CDatabase_Query_Expression) {
            return $this->getValue($value);
        }

        if ($value instanceof UnitEnum) {
            return "'" . str_replace("'", "''", c::enumValue($value)) . "'";
        }

        return is_bool($value)
            ? "'" . (int) $value . "'"
            : "'" . str_replace("'", "''", $value) . "'";
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
     * Create an empty Doctrine DBAL TableDiff from the Blueprint.
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
