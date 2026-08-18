<?php

class CDatabase_Query_Processor_SqlServerProcessor extends CDatabase_Query_Processor {
    /**
     * Process an "insert get ID" query.
     *
     * @param \CDatabase_Query_Builder $query
     * @param string                   $sql
     * @param array                    $values
     * @param null|string              $sequence
     *
     * @return int
     */
    public function processInsertGetId(CDatabase_Query_Builder $query, $sql, $values, $sequence = null) {
        $connection = $query->getConnection();

        $connection->insert($sql, $values);

        if ($connection->getConfig('odbc') === true) {
            $id = $this->processInsertGetIdForOdbc($connection);
        } else {
            $id = $connection->getPdo()->lastInsertId();
        }

        return is_numeric($id) ? (int) $id : $id;
    }

    /**
     * Process an "insert get ID" query for ODBC.
     *
     * @param \CDatabase_Connection $connection
     *
     * @throws \Exception
     *
     * @return int
     */
    protected function processInsertGetIdForOdbc(CDatabase_Connection $connection) {
        $result = $connection->selectFromWriteConnection(
            'SELECT CAST(COALESCE(SCOPE_IDENTITY(), @@IDENTITY) AS int) AS insertid'
        );

        if (!$result) {
            throw new Exception('Unable to retrieve lastInsertID for ODBC.');
        }

        $row = $result[0];

        return is_object($row) ? $row->insertid : $row['insertid'];
    }

    /**
     * Process the results of a columns query.
     *
     * @param array $results
     *
     * @return array
     */
    public function processColumns($results) {
        return array_map(function ($result) {
            $result = (object) $result;

            $typeName = $result->type_name;
            $type = $typeName;
            if (in_array($result->type_name, ['binary', 'varbinary', 'char', 'varchar', 'nchar', 'nvarchar'])) {
                $type = $result->length == -1 ? $typeName . '(max)' : $typeName . "($result->length)";
            } elseif (in_array($result->type_name, ['decimal', 'numeric'])) {
                $type = $typeName . "($result->precision,$result->places)";
            } elseif (in_array($result->type_name, ['float', 'datetime2', 'datetimeoffset', 'time'])) {
                $type = $typeName . "($result->precision)";
            }

            return [
                'name' => $result->name,
                'type_name' => $result->type_name,
                'type' => $type,
                'collation' => $result->collation,
                'nullable' => (bool) $result->nullable,
                'default' => $result->default,
                'auto_increment' => (bool) $result->autoincrement,
                'comment' => $result->comment,
                'generation' => $result->expression ? [
                    'type' => $result->persisted ? 'stored' : 'virtual',
                    'expression' => $result->expression,
                ] : null,
            ];
        }, $results);
    }

    /**
     * Process the results of an indexes query.
     *
     * @param array $results
     *
     * @return array
     */
    public function processIndexes($results) {
        return array_map(function ($result) {
            $result = (object) $result;

            return [
                'name' => strtolower($result->name),
                'columns' => explode(',', $result->columns),
                'type' => strtolower($result->type),
                'unique' => (bool) $result->unique,
                'primary' => (bool) $result->primary,
            ];
        }, $results);
    }

    /**
     * Process the results of a foreign keys query.
     *
     * @param array $results
     *
     * @return array
     */
    public function processForeignKeys($results) {
        return array_map(function ($result) {
            $result = (object) $result;

            return [
                'name' => $result->name,
                'columns' => explode(',', $result->columns),
                'foreign_schema' => $result->foreign_schema,
                'foreign_table' => $result->foreign_table,
                'foreign_columns' => explode(',', $result->foreign_columns),
                'on_update' => strtolower(str_replace('_', ' ', $result->on_update)),
                'on_delete' => strtolower(str_replace('_', ' ', $result->on_delete)),
            ];
        }, $results);
    }
}
