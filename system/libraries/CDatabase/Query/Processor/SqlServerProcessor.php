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
     * Process the results of a column listing query.
     *
     * @param array $results
     *
     * @return array
     */
    public function processColumnListing($results) {
        return array_map(function ($result) {
            return ((object) $result)->name;
        }, $results);
    }
}
