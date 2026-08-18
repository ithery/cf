<?php

class CDatabase_Query_Processor_PostgresProcessor extends CDatabase_Query_Processor {
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

        $connection->recordsHaveBeenModified();

        $result = $connection->selectFromWriteConnection($sql, $values)[0];

        $sequence = $sequence ?: 'id';

        $id = is_object($result) ? $result->{$sequence} : $result[$sequence];

        return is_numeric($id) ? (int) $id : $id;
    }

    /**
     * Process the results of a types query.
     *
     * @param array $results
     *
     * @return array
     */
    public function processTypes($results) {
        return array_map(function ($result) {
            $result = (object) $result;
            $typeMap = [
                'b' => 'base',
                'c' => 'composite',
                'd' => 'domain',
                'e' => 'enum',
                'p' => 'pseudo',
                'r' => 'range',
                'm' => 'multirange',
            ];
            $categoryMap = [
                'a' => 'array',
                'b' => 'boolean',
                'c' => 'composite',
                'd' => 'date_time',
                'e' => 'enum',
                'g' => 'geometric',
                'i' => 'network_address',
                'n' => 'numeric',
                'p' => 'pseudo',
                'r' => 'range',
                's' => 'string',
                't' => 'timespan',
                'u' => 'user_defined',
                'v' => 'bit_string',
                'x' => 'unknown',
                'z' => 'internal_use',

            ];

            return [
                'name' => $result->name,
                'schema' => $result->schema,
                'implicit' => (bool) $result->implicit,
                'type' => carr::get($typeMap, strtolower($result->type)),
                'category' => carr::get($categoryMap, strtolower($result->category)),
            ];
        }, $results);
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

            $autoincrement = $result->default !== null && str_starts_with($result->default, 'nextval(');

            return [
                'name' => $result->name,
                'type_name' => $result->type_name,
                'type' => $result->type,
                'collation' => $result->collation,
                'nullable' => (bool) $result->nullable,
                'default' => $result->generated ? null : $result->default,
                'auto_increment' => $autoincrement,
                'comment' => $result->comment,
                'generation' => $result->generated ? [
                    'type' => $result->generated == 's' ? 'stored' : null,
                    'expression' => $result->default,
                ] : null,
            ];
        }, $results);
    }
}
