<?php

class CDatabase_Query_Processor_Sqlsrv extends CDatabase_Query_Processor_Mysql {
    /**
     * Process an "insert get ID" query.
     *
     * @param \CDatabase_Query_Builder $query
     * @param string                   $sql
     * @param array                    $values
     * @param string|null              $sequence
     *
     * @return int
     */
    public function processInsertGetId(CDatabase_Query_Builder $query, $sql, $values, $sequence = null) {
        $db = $query->getConnection();

        $r = $db->insert($sql, $values);

        //$id = $connection->getValue('SELECT CAST(COALESCE(SCOPE_IDENTITY(), @@IDENTITY) AS int) AS insertid');
        $id = $r->insertId();

        return is_numeric($id) ? (int) $id : $id;
    }

    // /**
    //  * Process an "insert get ID" query for ODBC.
    //  *
    //  * @param \CD $connection
    //  *
    //  * @return int
    //  *
    //  * @throws \Exception
    //  */
    // protected function processInsertGetIdForOdbc(Connection $connection) {
    //     $result = $connection->selectFromWriteConnection(
    //         'SELECT CAST(COALESCE(SCOPE_IDENTITY(), @@IDENTITY) AS int) AS insertid'
    //     );

    //     if (!$result) {
    //         throw new Exception('Unable to retrieve lastInsertID for ODBC.');
    //     }

    //     $row = $result[0];

    //     return is_object($row) ? $row->insertid : $row['insertid'];
    // }

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
