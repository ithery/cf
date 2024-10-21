<?php

class CDatabase_Schema_Grammar_SqlServerGrammar extends CDatabase_Schema_Grammar {
    /**
     * If this Grammar supports schema changes wrapped in a transaction.
     *
     * @var bool
     */
    protected $transactions = true;

    /**
     * The possible column modifiers.
     *
     * @var string[]
     */
    protected $modifiers = ['Collate', 'Nullable', 'Default', 'Persisted', 'Increment'];

    /**
     * The columns available as serials.
     *
     * @var string[]
     */
    protected $serials = ['tinyInteger', 'smallInteger', 'mediumInteger', 'integer', 'bigInteger'];

    /**
     * The commands to be executed outside of create or alter command.
     *
     * @var string[]
     */
    protected $fluentCommands = ['Default'];

    /**
     * Compile a create database command.
     *
     * @param string                $name
     * @param \CDatabase_Connection $connection
     *
     * @return string
     */
    public function compileCreateDatabase($name, $connection) {
        return sprintf(
            'create database %s',
            $this->wrapValue($name),
        );
    }

    /**
     * Compile a drop database if exists command.
     *
     * @param string $name
     *
     * @return string
     */
    public function compileDropDatabaseIfExists($name) {
        return sprintf(
            'drop database if exists %s',
            $this->wrapValue($name)
        );
    }

    /**
     * Compile the query to determine if a table exists.
     *
     * @return string
     */
    public function compileTableExists() {
        return "select * from sys.sysobjects where id = object_id(?) and xtype in ('U', 'V')";
    }

    /**
     * Compile the query to determine the list of columns.
     *
     * @param string $table
     *
     * @return string
     */
    public function compileColumnListing($table) {
        return "select name from sys.columns where object_id = object_id('$table')";
    }

    /**
     * Compile a create table command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileCreate(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $columns = implode(', ', $this->getColumns($blueprint));

        return 'create table ' . $this->wrapTable($blueprint) . " ($columns)";
    }

    /**
     * Compile a column addition table command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileAdd(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'alter table %s add %s',
            $this->wrapTable($blueprint),
            implode(', ', $this->getColumns($blueprint))
        );
    }

    /**
     * Compile a rename column command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     * @param \CDatabase_Connection       $connection
     *
     * @return array|string
     */
    public function compileRenameColumn(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command, CDatabase_Connection $connection) {
        return $connection->usingNativeSchemaOperations()
            ? sprintf(
                "sp_rename '%s', %s, 'COLUMN'",
                $this->wrap($blueprint->getTable() . '.' . $command->from),
                $this->wrap($command->to)
            )
            : parent::compileRenameColumn($blueprint, $command, $connection);
    }

    /**
     * Compile a change column command into a series of SQL statements.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     * @param \CDatabase_Connection       $connection
     *
     * @throws \RuntimeException
     *
     * @return array|string
     */
    public function compileChange(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command, CDatabase_Connection $connection) {
        if (!$connection->usingNativeSchemaOperations()) {
            return parent::compileChange($blueprint, $command, $connection);
        }

        $changes = [$this->compileDropDefaultConstraint($blueprint, $command)];

        foreach ($blueprint->getChangedColumns() as $column) {
            $sql = sprintf(
                'alter table %s alter column %s %s',
                $this->wrapTable($blueprint),
                $this->wrap($column),
                $this->getType($column)
            );

            foreach ($this->modifiers as $modifier) {
                if (method_exists($this, $method = "modify{$modifier}")) {
                    $sql .= $this->{$method}($blueprint, $column);
                }
            }

            $changes[] = $sql;
        }

        return $changes;
    }

    /**
     * Compile a primary key command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compilePrimary(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'alter table %s add constraint %s primary key (%s)',
            $this->wrapTable($blueprint),
            $this->wrap($command->index),
            $this->columnize($command->columns)
        );
    }

    /**
     * Compile a unique key command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileUnique(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'create unique index %s on %s (%s)',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $this->columnize($command->columns)
        );
    }

    /**
     * Compile a plain index key command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'create index %s on %s (%s)',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $this->columnize($command->columns)
        );
    }

    /**
     * Compile a spatial index key command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileSpatialIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'create spatial index %s on %s (%s)',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $this->columnize($command->columns)
        );
    }

    /**
     * Compile a default command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return null|string
     */
    public function compileDefault(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        if ($command->column->change && !is_null($command->column->default)) {
            return sprintf(
                'alter table %s add default %s for %s',
                $this->wrapTable($blueprint),
                $this->getDefaultValue($command->column->default),
                $this->wrap($command->column)
            );
        }
    }

    /**
     * Compile a drop table command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDrop(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return 'drop table ' . $this->wrapTable($blueprint);
    }

    /**
     * Compile a drop table (if exists) command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDropIfExists(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            'if exists (select * from sys.sysobjects where id = object_id(%s, \'U\')) drop table %s',
            "'" . str_replace("'", "''", $this->getTablePrefix() . $blueprint->getTable()) . "'",
            $this->wrapTable($blueprint)
        );
    }

    /**
     * Compile the SQL needed to drop all tables.
     *
     * @return string
     */
    public function compileDropAllTables() {
        return "EXEC sp_msforeachtable 'DROP TABLE ?'";
    }

    /**
     * Compile a drop column command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDropColumn(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $columns = $this->wrapArray($command->columns);

        $dropExistingConstraintsSql = $this->compileDropDefaultConstraint($blueprint, $command) . ';';

        return $dropExistingConstraintsSql . 'alter table ' . $this->wrapTable($blueprint) . ' drop column ' . implode(', ', $columns);
    }

    /**
     * Compile a drop default constraint command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDropDefaultConstraint(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $columns = $command->name === 'change'
            ? "'" . c::collect($blueprint->getChangedColumns())->pluck('name')->implode("','") . "'"
            : "'" . implode("','", $command->columns) . "'";

        $tableName = $this->getTablePrefix() . $blueprint->getTable();

        $sql = "DECLARE @sql NVARCHAR(MAX) = '';";
        $sql .= "SELECT @sql += 'ALTER TABLE [dbo].[{$tableName}] DROP CONSTRAINT ' + OBJECT_NAME([default_object_id]) + ';' ";
        $sql .= 'FROM sys.columns ';
        $sql .= "WHERE [object_id] = OBJECT_ID('[dbo].[{$tableName}]') AND [name] in ({$columns}) AND [default_object_id] <> 0;";
        $sql .= 'EXEC(@sql)';

        return $sql;
    }

    /**
     * Compile a drop primary key command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDropPrimary(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $index = $this->wrap($command->index);

        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
    }

    /**
     * Compile a drop unique key command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDropUnique(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $index = $this->wrap($command->index);

        return "drop index {$index} on {$this->wrapTable($blueprint)}";
    }

    /**
     * Compile a drop index command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDropIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $index = $this->wrap($command->index);

        return "drop index {$index} on {$this->wrapTable($blueprint)}";
    }

    /**
     * Compile a drop spatial index command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDropSpatialIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return $this->compileDropIndex($blueprint, $command);
    }

    /**
     * Compile a drop foreign key command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileDropForeign(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $index = $this->wrap($command->index);

        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
    }

    /**
     * Compile a rename table command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileRename(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        $from = $this->wrapTable($blueprint);

        return "sp_rename {$from}, " . $this->wrapTable($command->to);
    }

    /**
     * Compile a rename index command.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     *
     * @return string
     */
    public function compileRenameIndex(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command) {
        return sprintf(
            "sp_rename N'%s', %s, N'INDEX'",
            $this->wrap($blueprint->getTable() . '.' . $command->from),
            $this->wrap($command->to)
        );
    }

    /**
     * Compile the command to enable foreign key constraints.
     *
     * @return string
     */
    public function compileEnableForeignKeyConstraints() {
        return 'EXEC sp_msforeachtable @command1="print \'?\'", @command2="ALTER TABLE ? WITH CHECK CHECK CONSTRAINT all";';
    }

    /**
     * Compile the command to disable foreign key constraints.
     *
     * @return string
     */
    public function compileDisableForeignKeyConstraints() {
        return 'EXEC sp_msforeachtable "ALTER TABLE ? NOCHECK CONSTRAINT all";';
    }

    /**
     * Compile the command to drop all foreign keys.
     *
     * @return string
     */
    public function compileDropAllForeignKeys() {
        return "DECLARE @sql NVARCHAR(MAX) = N'';
            SELECT @sql += 'ALTER TABLE '
                + QUOTENAME(OBJECT_SCHEMA_NAME(parent_object_id)) + '.' + + QUOTENAME(OBJECT_NAME(parent_object_id))
                + ' DROP CONSTRAINT ' + QUOTENAME(name) + ';'
            FROM sys.foreign_keys;

            EXEC sp_executesql @sql;";
    }

    /**
     * Compile the command to drop all views.
     *
     * @return string
     */
    public function compileDropAllViews() {
        return "DECLARE @sql NVARCHAR(MAX) = N'';
            SELECT @sql += 'DROP VIEW ' + QUOTENAME(OBJECT_SCHEMA_NAME(object_id)) + '.' + QUOTENAME(name) + ';'
            FROM sys.views;

            EXEC sp_executesql @sql;";
    }

    /**
     * Compile the SQL needed to retrieve all table names.
     *
     * @return string
     */
    public function compileGetAllTables() {
        return "select name, type from sys.tables where type = 'U'";
    }

    /**
     * Compile the SQL needed to retrieve all view names.
     *
     * @return string
     */
    public function compileGetAllViews() {
        return "select name, type from sys.objects where type = 'V'";
    }

    /**
     * Create the column definition for a char type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeChar(CBase_Fluent $column) {
        return "nchar({$column->length})";
    }

    /**
     * Create the column definition for a string type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeString(CBase_Fluent $column) {
        return "nvarchar({$column->length})";
    }

    /**
     * Create the column definition for a tiny text type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeTinyText(CBase_Fluent $column) {
        return 'nvarchar(255)';
    }

    /**
     * Create the column definition for a text type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeText(CBase_Fluent $column) {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for a medium text type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeMediumText(CBase_Fluent $column) {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for a long text type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeLongText(CBase_Fluent $column) {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for an integer type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeInteger(CBase_Fluent $column) {
        return 'int';
    }

    /**
     * Create the column definition for a big integer type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeBigInteger(CBase_Fluent $column) {
        return 'bigint';
    }

    /**
     * Create the column definition for a medium integer type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeMediumInteger(CBase_Fluent $column) {
        return 'int';
    }

    /**
     * Create the column definition for a tiny integer type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeTinyInteger(CBase_Fluent $column) {
        return 'tinyint';
    }

    /**
     * Create the column definition for a small integer type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeSmallInteger(CBase_Fluent $column) {
        return 'smallint';
    }

    /**
     * Create the column definition for a float type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeFloat(CBase_Fluent $column) {
        return 'float';
    }

    /**
     * Create the column definition for a double type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeDouble(CBase_Fluent $column) {
        return 'float';
    }

    /**
     * Create the column definition for a decimal type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeDecimal(CBase_Fluent $column) {
        return "decimal({$column->total}, {$column->places})";
    }

    /**
     * Create the column definition for a boolean type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeBoolean(CBase_Fluent $column) {
        return 'bit';
    }

    /**
     * Create the column definition for an enumeration type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeEnum(CBase_Fluent $column) {
        return sprintf(
            'nvarchar(255) check ("%s" in (%s))',
            $column->name,
            $this->quoteString($column->allowed)
        );
    }

    /**
     * Create the column definition for a json type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeJson(CBase_Fluent $column) {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for a jsonb type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeJsonb(CBase_Fluent $column) {
        return 'nvarchar(max)';
    }

    /**
     * Create the column definition for a date type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeDate(CBase_Fluent $column) {
        return 'date';
    }

    /**
     * Create the column definition for a date-time type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeDateTime(CBase_Fluent $column) {
        return $this->typeTimestamp($column);
    }

    /**
     * Create the column definition for a date-time (with time zone) type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeDateTimeTz(CBase_Fluent $column) {
        return $this->typeTimestampTz($column);
    }

    /**
     * Create the column definition for a time type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeTime(CBase_Fluent $column) {
        return $column->precision ? "time($column->precision)" : 'time';
    }

    /**
     * Create the column definition for a time (with time zone) type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeTimeTz(CBase_Fluent $column) {
        return $this->typeTime($column);
    }

    /**
     * Create the column definition for a timestamp type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeTimestamp(CBase_Fluent $column) {
        if ($column->useCurrent) {
            $column->default(new CDatabase_Query_Expression('CURRENT_TIMESTAMP'));
        }

        return $column->precision ? "datetime2($column->precision)" : 'datetime';
    }

    /**
     * Create the column definition for a timestamp (with time zone) type.
     *
     * @link https://docs.microsoft.com/en-us/sql/t-sql/data-types/datetimeoffset-transact-sql?view=sql-server-ver15
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeTimestampTz(CBase_Fluent $column) {
        if ($column->useCurrent) {
            $column->default(new CDatabase_Query_Expression('CURRENT_TIMESTAMP'));
        }

        return $column->precision ? "datetimeoffset($column->precision)" : 'datetimeoffset';
    }

    /**
     * Create the column definition for a year type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeYear(CBase_Fluent $column) {
        return $this->typeInteger($column);
    }

    /**
     * Create the column definition for a binary type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeBinary(CBase_Fluent $column) {
        return 'varbinary(max)';
    }

    /**
     * Create the column definition for a uuid type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeUuid(CBase_Fluent $column) {
        return 'uniqueidentifier';
    }

    /**
     * Create the column definition for an IP address type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeIpAddress(CBase_Fluent $column) {
        return 'nvarchar(45)';
    }

    /**
     * Create the column definition for a MAC address type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeMacAddress(CBase_Fluent $column) {
        return 'nvarchar(17)';
    }

    /**
     * Create the column definition for a spatial Geometry type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    public function typeGeometry(CBase_Fluent $column) {
        return 'geography';
    }

    /**
     * Create the column definition for a spatial Point type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    public function typePoint(CBase_Fluent $column) {
        return 'geography';
    }

    /**
     * Create the column definition for a spatial LineString type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    public function typeLineString(CBase_Fluent $column) {
        return 'geography';
    }

    /**
     * Create the column definition for a spatial Polygon type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    public function typePolygon(CBase_Fluent $column) {
        return 'geography';
    }

    /**
     * Create the column definition for a spatial GeometryCollection type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    public function typeGeometryCollection(CBase_Fluent $column) {
        return 'geography';
    }

    /**
     * Create the column definition for a spatial MultiPoint type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    public function typeMultiPoint(CBase_Fluent $column) {
        return 'geography';
    }

    /**
     * Create the column definition for a spatial MultiLineString type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    public function typeMultiLineString(CBase_Fluent $column) {
        return 'geography';
    }

    /**
     * Create the column definition for a spatial MultiPolygon type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    public function typeMultiPolygon(CBase_Fluent $column) {
        return 'geography';
    }

    /**
     * Create the column definition for a generated, computed column type.
     *
     * @param \CBase_Fluent $column
     *
     * @return null|string
     */
    protected function typeComputed(CBase_Fluent $column) {
        return "as ({$column->expression})";
    }

    /**
     * Get the SQL for a collation column modifier.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $column
     *
     * @return null|string
     */
    protected function modifyCollate(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        if (!is_null($column->collation)) {
            return ' collate ' . $column->collation;
        }
    }

    /**
     * Get the SQL for a nullable column modifier.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $column
     *
     * @return null|string
     */
    protected function modifyNullable(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        if ($column->type !== 'computed') {
            return $column->nullable ? ' null' : ' not null';
        }
    }

    /**
     * Get the SQL for a default column modifier.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $column
     *
     * @return null|string
     */
    protected function modifyDefault(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        if (!$column->change && !is_null($column->default)) {
            return ' default ' . $this->getDefaultValue($column->default);
        }
    }

    /**
     * Get the SQL for an auto-increment column modifier.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $column
     *
     * @return null|string
     */
    protected function modifyIncrement(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        if (!$column->change && in_array($column->type, $this->serials) && $column->autoIncrement) {
            return ' identity primary key';
        }
    }

    /**
     * Get the SQL for a generated stored column modifier.
     *
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $column
     *
     * @return null|string
     */
    protected function modifyPersisted(CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $column) {
        if ($column->change) {
            if ($column->type === 'computed') {
                return $column->persisted ? ' add persisted' : ' drop persisted';
            }

            return null;
        }

        if ($column->persisted) {
            return ' persisted';
        }
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param \CDatabase_Schema_Blueprint|\Illuminate\Contracts\Database\Query\Expression|string $table
     *
     * @return string
     */
    public function wrapTable($table) {
        if ($table instanceof CDatabase_Schema_Blueprint && $table->temporary) {
            $this->setTablePrefix('#');
        }

        return parent::wrapTable($table);
    }

    /**
     * Quote the given string literal.
     *
     * @param string|array $value
     *
     * @return string
     */
    public function quoteString($value) {
        if (is_array($value)) {
            return implode(', ', array_map([$this, __FUNCTION__], $value));
        }

        return "N'$value'";
    }
}
