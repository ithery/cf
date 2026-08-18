<?php

class CDatabase_Schema_Grammar_RenameColumn {
    /**
     * Compile a rename column command.
     *
     * @param \CDatabase_Schema_Grammar   $grammar
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     * @param \CDatabase_Connection       $connection
     *
     * @return array
     */
    public static function compile(CDatabase_Schema_Grammar $grammar, CDatabase_Schema_Blueprint $blueprint, CBase_Fluent $command, CDatabase_Connection $connection) {
        $schema = $connection->getSchemaManager();
        $databasePlatform = $schema->getDatabasePlatform();
        $databasePlatform->registerDoctrineTypeMapping('enum', 'string');

        $column = $connection->getColumn(
            $grammar->getTablePrefix() . $blueprint->getTable(),
            $command->from
        );

        return (array) $databasePlatform->getAlterTableSQL(static::getRenamedDiff(
            $grammar,
            $blueprint,
            $command,
            $column,
            $schema
        ));
    }

    /**
     * Get a new column instance with the new column name.
     *
     * @param \CDatabase_Schema_Grammar   $grammar
     * @param \CDatabase_Schema_Blueprint $blueprint
     * @param \CBase_Fluent               $command
     * @param \CDatabase_Schema_Column    $column
     * @param \CDatabase_Schema_Manager   $schema
     *
     * @return \CDatabase_Schema_Table_Diff
     */
    protected static function getRenamedDiff(
        CDatabase_Schema_Grammar $grammar,
        CDatabase_Schema_Blueprint $blueprint,
        CBase_Fluent $command,
        CDatabase_Schema_Column $column,
        CDatabase_Schema_Manager $schema
    ) {
        return static::setRenamedColumns(
            $grammar->getDoctrineTableDiff($blueprint, $schema),
            $command,
            $column
        );
    }

    /**
     * Set the renamed columns on the table diff.
     *
     * @param \CDatabase_Schema_Table_Diff $tableDiff
     * @param \CBase_Fluent                $command
     * @param \CDatabase_Schema_Column     $column
     *
     * @return \CDatabase_Schema_Table_Diff
     */
    protected static function setRenamedColumns(
        CDatabase_Schema_Table_Diff $tableDiff,
        CBase_Fluent $command,
        CDatabase_Schema_Column $column
    ) {
        $tableDiff->renamedColumns = [
            $command->from => new CDatabase_Schema_Column($command->to, $column->getType(), self::getWritableColumnOptions($column)),
        ];

        return $tableDiff;
    }

    /**
     * Get the writable column options.
     *
     * @param \CDatabase_Schema_Column $column
     *
     * @return array
     */
    private static function getWritableColumnOptions(CDatabase_Schema_Column $column) {
        return array_filter($column->toArray(), function ($name) use ($column) {
            return method_exists($column, 'set' . $name);
        }, ARRAY_FILTER_USE_KEY);
    }
}
