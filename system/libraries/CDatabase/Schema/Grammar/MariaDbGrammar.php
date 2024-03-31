<?php

class CDatabase_Schema_Grammar_MariaDbGrammar extends CDatabase_Schema_Grammar_MySqlGrammar {
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
        if (version_compare($connection->getServerVersion(), '10.5.2', '<')) {
            $schemaBuilder = $connection->getSchemaBuilder();
            /** @var CDatabase_Schema_Builder_MariaDbBuilder */
            $column = c::collect($schemaBuilder->getColumnListing($blueprint->getTable()))
                ->firstWhere('name', $command->from);

            $columnTypeMap = [
                'bigint' => 'bigInteger',
                'int' => 'integer',
                'mediumint' => 'mediumInteger',
                'smallint' => 'smallInteger',
                'tinyint' => 'tinyInteger',

            ];

            $modifiers = $this->addModifiers($column['type'], $blueprint, new CDatabase_Schema_ColumnDefinition([
                'change' => true,
                'type' => carr::get($columnTypeMap, $column['type_name'], $column['type_name']),
                'nullable' => $column['nullable'],
                'default' => $column['default'] && str_starts_with(strtolower($column['default']), 'current_timestamp')
                    ? new CDatabase_Query_Expression($column['default'])
                    : $column['default'],
                'autoIncrement' => $column['auto_increment'],
                'collation' => $column['collation'],
                'comment' => $column['comment'],
            ]));

            return sprintf(
                'alter table %s change %s %s %s',
                $this->wrapTable($blueprint),
                $this->wrap($command->from),
                $this->wrap($command->to),
                $modifiers
            );
        }

        return parent::compileRenameColumn($blueprint, $command, $connection);
    }

    /**
     * Create the column definition for a uuid type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeUuid(CBase_Fluent $column) {
        return 'uuid';
    }

    /**
     * Create the column definition for a spatial Geometry type.
     *
     * @param \CBase_Fluent $column
     *
     * @return string
     */
    protected function typeGeometry(CBase_Fluent $column) {
        $subtype = $column->subtype ? strtolower($column->subtype) : null;

        if (!in_array($subtype, ['point', 'linestring', 'polygon', 'geometrycollection', 'multipoint', 'multilinestring', 'multipolygon'])) {
            $subtype = null;
        }

        return sprintf(
            '%s%s',
            $subtype ?? 'geometry',
            $column->srid ? ' ref_system_id=' . $column->srid : ''
        );
    }
}
