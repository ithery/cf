<?php

class CDatabase_Query_Processor {
    /**
     * Process the results of a "select" query.
     *
     * @param CDatabase_Query_Builder $query
     * @param array                   $results
     *
     * @return array
     */
    public function processSelect(CDatabase_Query_Builder $query, $results) {
        return $results;
    }

    /**
     * Process an  "insert get ID" query.
     *
     * @param CDatabase_Query_Builder $query
     * @param string                  $sql
     * @param array                   $values
     * @param string                  $sequence
     *
     * @return int
     */
    public function processInsertGetId(CDatabase_Query_Builder $query, $sql, $values, $sequence = null) {
        $resultInsert = $query->getConnection()->query($sql, $values);

        $id = $resultInsert->insert_id($sequence);

        return is_numeric($id) ? (int) $id : $id;
    }

    /**
     * Process the results of a column listing query.
     *
     * @param array $results
     *
     * @return array
     */
    public function processColumnListing($results) {
        return $results;
    }
}
