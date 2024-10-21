<?php

class CDatabase_Schema_Grammar_OdbcGrammar extends CDatabase_Schema_Grammar {
    /**
     * The keyword identifier wrapper format.
     *
     * @var string
     */
    protected $wrapper = '%s';

    /**
     * The possible column modifiers.
     *
     * @var array
     */
    protected $modifiers = ['Unsigned', 'Nullable', 'Default', 'Increment'];

    /**
     * Compile the query to determine if a table exists.
     *
     * @return string
     */
    public function compileTableExists() {
        return 'select * from information_schema.tables where table_schema = ? and table_name = ?';
    }

    /**
     * Compile a create table command.
     *
     * @return string
     */
    public function compileCreate(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $columns = implode(', ', $this->getColumns($blueprint));

        return 'create table ' . $this->wrapTable($blueprint) . " ($columns)";
    }

    /**
     * Compile a create table command.
     *
     * @return string
     */
    public function compileAdd(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $table = $this->wrapTable($blueprint);

        $columns = $this->prefixArray('add', $this->getColumns($blueprint));

        return 'alter table ' . $table . ' ' . implode(', ', $columns);
    }

    /**
     * Compile a primary key command.
     *
     * @return string
     */
    public function compilePrimary(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $command->name(null);

        return $this->compileKey($blueprint, $command, 'primary key');
    }

    /**
     * Compile an index creation command.
     *
     * @param string $type
     *
     * @return string
     */
    protected function compileKey(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command, $type) {
        $columns = $this->columnize($command->columns);

        $table = $this->wrapTable($blueprint);

        return "alter table {$table} add {$type} {$command->index}($columns)";
    }

    /**
     * Compile a unique key command.
     *
     * @return string
     */
    public function compileUnique(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return $this->compileKey($blueprint, $command, 'unique');
    }

    /**
     * Compile a plain index key command.
     *
     * @return string
     */
    public function compileIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return $this->compileKey($blueprint, $command, 'index');
    }

    /**
     * Compile a drop table command.
     *
     * @return string
     */
    public function compileDrop(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return 'drop table ' . $this->wrapTable($blueprint);
    }

    /**
     * Compile a drop table (if exists) command.
     *
     * @return string
     */
    public function compileDropIfExists(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return 'drop table if exists ' . $this->wrapTable($blueprint);
    }

    /**
     * Compile a drop column command.
     *
     * @return string
     */
    public function compileDropColumn(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $columns = $this->prefixArray('drop', $this->wrapArray($command->columns));

        $table = $this->wrapTable($blueprint);

        return 'alter table ' . $table . ' ' . implode(', ', $columns);
    }

    /**
     * Compile a drop primary key command.
     *
     * @return string
     */
    public function compileDropPrimary(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return 'alter table ' . $this->wrapTable($blueprint) . ' drop primary key';
    }

    /**
     * Compile a drop unique key command.
     *
     * @return string
     */
    public function compileDropUnique(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $table = $this->wrapTable($blueprint);

        return "alter table {$table} drop index {$command->index}";
    }

    /**
     * Compile a drop index command.
     *
     * @return string
     */
    public function compileDropIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $table = $this->wrapTable($blueprint);

        return "alter table {$table} drop index {$command->index}";
    }

    /**
     * Compile a drop foreign key command.
     *
     * @return string
     */
    public function compileDropForeign(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $table = $this->wrapTable($blueprint);

        return "alter table {$table} drop foreign key {$command->index}";
    }

    /**
     * Compile a rename table command.
     *
     * @return string
     */
    public function compileRename(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $from = $this->wrapTable($blueprint);

        return "rename table {$from} to " . $this->wrapTable($command->to);
    }

    /**
     * Create the column definition for a string type.
     *
     * @return string
     */
    protected function typeString(CBase_Fluent $column) {
        return "varchar({$column->length})";
    }

    /**
     * Create the column definition for a text type.
     *
     * @return string
     */
    protected function typeText(CBase_Fluent $column) {
        return 'text';
    }

    /**
     * Create the column definition for a integer type.
     *
     * @return string
     */
    protected function typeInteger(CBase_Fluent $column) {
        return 'int';
    }

    /**
     * Create the column definition for a float type.
     *
     * @return string
     */
    protected function typeFloat(CBase_Fluent $column) {
        return "float({$column->total}, {$column->places})";
    }

    /**
     * Create the column definition for a decimal type.
     *
     * @return string
     */
    protected function typeDecimal(CBase_Fluent $column) {
        return "decimal({$column->total}, {$column->places})";
    }

    /**
     * Create the column definition for a boolean type.
     *
     * @return string
     */
    protected function typeBoolean(CBase_Fluent $column) {
        return 'tinyint';
    }

    /**
     * Create the column definition for a enum type.
     *
     * @return string
     */
    protected function typeEnum(CBase_Fluent $column) {
        return "enum('" . implode("', '", $column->allowed) . "')";
    }

    /**
     * Create the column definition for a date type.
     *
     * @return string
     */
    protected function typeDate(CBase_Fluent $column) {
        return 'date';
    }

    /**
     * Create the column definition for a date-time type.
     *
     * @return string
     */
    protected function typeDateTime(CBase_Fluent $column) {
        return 'datetime';
    }

    /**
     * Create the column definition for a time type.
     *
     * @return string
     */
    protected function typeTime(CBase_Fluent $column) {
        return 'time';
    }

    /**
     * Create the column definition for a timestamp type.
     *
     * @return string
     */
    protected function typeTimestamp(CBase_Fluent $column) {
        return 'timestamp default 0';
    }

    /**
     * Create the column definition for a binary type.
     *
     * @return string
     */
    protected function typeBinary(CBase_Fluent $column) {
        return 'blob';
    }

    /**
     * Get the SQL for an unsigned column modifier.
     *
     * @return null|string
     */
    protected function modifyUnsigned(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        if ($column->type == 'integer' and $column->unsigned) {
            return ' unsigned';
        }
    }

    /**
     * Get the SQL for a nullable column modifier.
     *
     * @return null|string
     */
    protected function modifyNullable(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        return $column->nullable ? ' null' : ' not null';
    }

    /**
     * Get the SQL for a default column modifier.
     *
     * @return null|string
     */
    protected function modifyDefault(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        if (!is_null($column->default)) {
            return " default '" . $this->getDefaultValue($column->default) . "'";
        }
    }

    /**
     * Get the SQL for an auto-increment column modifier.
     *
     * @return null|string
     */
    protected function modifyIncrement(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        if ($column->type == 'integer' and $column->autoIncrement) {
            return ' auto_increment primary key';
        }
    }
}
