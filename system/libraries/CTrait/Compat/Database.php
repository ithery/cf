<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 22, 2019, 2:10:37 PM
 */

// @codingStandardsIgnoreStart
trait CTrait_Compat_Database {
    /**
     * @return string
     *
     * @deprecated since version 1.2
     */
    public function driver_name() {
        return $this->driverName();
    }

    /**
     * @deprecated
     *
     * @param string $str
     *
     * @return string
     */
    public function escape_like($str) {
        return $this->escapeLike($str);
    }

    /**
     * @deprecated since version 1.2
     *
     * @return bool
     */
    public function in_transaction() {
        return $this->inTransaction();
    }

    /**
     * Returns the last query run.
     *
     * @deprecated
     *
     * @return string SQL
     */
    public function last_query() {
        return $this->lastQuery();
    }

    /**
     * Escapes a string for a query.
     *
     * @param   string  string to escape
     * @param mixed $str
     *
     * @return string
     */
    public function escape_str($str) {
        return $this->escapeStr($str);
    }

    /**
     * Escapes a table name for a query.
     *
     * @param   string  string to escape
     * @param mixed $table
     *
     * @return string
     */
    public function escape_table($table) {
        return $this->escapeTable($table);
    }

    /**
     * Escapes a column name for a query.
     *
     * @param   string  string to escape
     * @param mixed $table
     *
     * @return string
     */
    public function escape_column($table) {
        return $this->escapeColumn($table);
    }

    /**
     * See if a table exists in the database.
     *
     * @param   string   table name
     * @param   bool  True to attach table prefix
     * @param mixed $table_name
     * @param mixed $prefix
     *
     * @return bool
     */
    public function table_exists($table_name, $prefix = true) {
        return $this->tableExists($table_name, $prefix);
    }

    /**
     * Lists all the tables in the current database.
     *
     * @return array
     */
    public function list_tables() {
        return $this->listTables();
    }

    /**
     * Get the field data for a database table, along with the field's attributes.
     *
     * @param   string  table name
     * @param mixed $table
     *
     * @return array
     *
     * @deprecated
     */
    public function list_fields($table = '') {
        return $this->listFields($table);
    }

    /**
     * Selects the or like(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        like value to match with field
     * @param mixed $field
     * @param mixed $match
     *
     * @return Database_Core this Database object
     *
     * @deprecated
     */
    public function orregex($field, $match = '') {
        $fields = is_array($field) ? $field : [$field => $match];

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== false) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->regex($field, $match, 'OR ', count($this->where));
        }

        return $this;
    }

    /**
     * Selects the not regex(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        regex value to match with field
     * @param mixed $field
     * @param mixed $match
     *
     * @return Database_Core this Database object
     *
     * @deprecated
     */
    public function notregex($field, $match = '') {
        $fields = is_array($field) ? $field : [$field => $match];

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== false) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->notregex($field, $match, 'AND ', count($this->where));
        }

        return $this;
    }

    /**
     * Selects the or not regex(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        regex value to match with field
     * @param mixed $field
     * @param mixed $match
     *
     * @return Database_Core this Database object
     *
     * @deprecated
     */
    public function ornotregex($field, $match = '') {
        $fields = is_array($field) ? $field : [$field => $match];

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== false) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->notregex($field, $match, 'OR ', count($this->where));
        }

        return $this;
    }

    /**
     * Chooses the column to group by in a select query.
     *
     * @param   string  column name to group by
     * @param mixed $by
     *
     * @return Database_Core this Database object
     *
     * @deprecated
     */
    public function groupby($by) {
        if (!is_array($by)) {
            $by = explode(',', (string) $by);
        }

        foreach ($by as $val) {
            $val = trim($val);

            if ($val != '') {
                // Add the table prefix if we are using table.column names
                if (strpos($val, '.')) {
                    $val = $this->config['table_prefix'] . $val;
                }

                $this->groupby[] = $this->driver->escape_column($val);
            }
        }

        return $this;
    }

    /**
     * Selects the having(s) for a database query.
     *
     * @param   string|array  key name or array of key => value pairs
     * @param   string        value to match with key
     * @param   bool       disable quoting of WHERE clause
     * @param mixed $key
     * @param mixed $value
     * @param mixed $quote
     *
     * @return Database_Core this Database object
     *
     * @deprecated
     */
    public function having($key, $value = '', $quote = true) {
        $this->having[] = $this->driver->where($key, $value, 'AND', count($this->having), true);
        return $this;
    }

    /**
     * Selects the or having(s) for a database query.
     *
     * @param   string|array  key name or array of key => value pairs
     * @param   string        value to match with key
     * @param   bool       disable quoting of WHERE clause
     * @param mixed $key
     * @param mixed $value
     * @param mixed $quote
     *
     * @return Database_Core this Database object
     *
     * @deprecated
     */
    public function orhaving($key, $value = '', $quote = true) {
        $this->having[] = $this->driver->where($key, $value, 'OR', count($this->having), true);
        return $this;
    }

    /**
     * Chooses which column(s) to order the select query by.
     *
     * @param   string|array  column(s) to order on, can be an array, single column, or comma seperated list of columns
     * @param   string        direction of the order
     * @param mixed      $orderby
     * @param null|mixed $direction
     *
     * @return Database_Core this Database object
     *
     * @deprecated
     */
    public function orderby($orderby, $direction = null) {
        if (!is_array($orderby)) {
            $orderby = [$orderby => $direction];
        }

        foreach ($orderby as $column => $direction) {
            $direction = strtoupper(trim($direction));

            // Add a direction if the provided one isn't valid
            if (!in_array($direction, ['ASC', 'DESC', 'RAND()', 'RANDOM()', 'NULL'])) {
                $direction = 'ASC';
            }

            // Add the table prefix if a table.column was passed
            if (strpos($column, '.')) {
                $column = $this->config['table_prefix'] . $column;
            }

            $this->orderby[] = $this->driver->escape_column($column) . ' ' . $direction;
        }

        return $this;
    }

    /**
     * Selects the limit section of a query.
     *
     * @param   int  number of rows to limit result to
     * @param   int  offset in result to start returning rows from
     * @param mixed      $limit
     * @param null|mixed $offset
     *
     * @return CDatabase this Database object
     *
     * @deprecated
     */
    public function limit($limit, $offset = null) {
        $this->limit = (int) $limit;

        if ($offset !== null or !is_int($this->offset)) {
            $this->offset($offset);
        }

        return $this;
    }

    /**
     * Sets the offset portion of a query.
     *
     * @param   int  offset value
     * @param mixed $value
     *
     * @return Database_Core this Database object
     *
     * @deprecated 1.1
     */
    public function offset($value) {
        $this->offset = (int) $value;

        return $this;
    }

    /**
     * Count query records.
     *
     * @param string $table table name
     * @param array  $where where clause
     *
     * @return int
     *
     * @deprecated 1.1
     */
    public function count_records($table = false, $where = null) {
        if (count($this->from) < 1) {
            if ($table == false) {
                throw new CDatabase_Exception('You must set a database table for your query');
            }
            $this->from($table);
        }

        if ($where !== null) {
            $this->where($where);
        }

        $query = $this->select('COUNT(*) AS ' . $this->escape_column('records_found'))->get()->result(true);

        return (int) $query->current()->records_found;
    }

    /**
     * Resets all private select variables.
     *
     * @return void
     *
     * @deprecated 1.1
     */
    protected function reset_select() {
        $this->select = [];
        $this->from = [];
        $this->join = [];
        $this->where = [];
        $this->orderby = [];
        $this->groupby = [];
        $this->having = [];
        $this->distinct = false;
        $this->limit = false;
        $this->offset = false;
    }

    /**
     * Resets all private insert and update variables.
     *
     * @return void
     *
     * @deprecated 1.1
     */
    protected function reset_write() {
        $this->set = [];
        $this->from = [];
        $this->where = [];
    }

    /**
     * Compiles the select statement based on the other functions called and runs the query.
     *
     * @param string      $table  table name
     * @param null|string $limit  limit clause
     * @param null|string $offset offset clause
     *
     * @return CDatabase_Result
     *
     * @deprecated 1.1
     */
    public function get($table = '', $limit = null, $offset = null) {
        if ($table != '') {
            $this->from($table);
        }

        if (!is_null($limit)) {
            $this->limit($limit, $offset);
        }

        $sql = $this->driver->compile_select(get_object_vars($this));

        $this->reset_select();

        $result = $this->query($sql);

        $this->last_query = $sql;

        return $result;
    }

    /**
     * Compiles the select statement based on the other functions called and runs the query.
     *
     * @param string $table  table name
     * @param array  $where  where clause
     * @param string $limit  limit clause
     * @param string $offset offset clause
     *
     * @return CDatabase this Database object
     *
     * @deprecated 1.1
     */
    public function getwhere($table = '', $where = null, $limit = null, $offset = null) {
        if ($table != '') {
            $this->from($table);
        }

        if (!is_null($where)) {
            $this->where($where);
        }

        if (!is_null($limit)) {
            $this->limit($limit, $offset);
        }

        $sql = $this->driver->compile_select(get_object_vars($this));

        $this->reset_select();

        $result = $this->query($sql);

        return $result;
    }

    /**
     * Returns table prefix of current configuration.
     *
     * @return string
     *
     * @deprecated 1.1
     */
    public function table_prefix() {
        return $this->config['table_prefix'];
    }

    /**
     * Clears the query cache.
     *
     * @param   string|true  clear cache by SQL statement or TRUE for last query
     * @param null|mixed $sql
     *
     * @return CDatabase this Database object
     *
     * @deprecated 1.1
     */
    public function clear_cache($sql = null) {
        if ($sql === true) {
            $this->driver->clear_cache($this->last_query);
        } elseif (is_string($sql)) {
            $this->driver->clear_cache($sql);
        } else {
            $this->driver->clear_cache();
        }

        return $this;
    }

    /**
     * Pushes existing query space onto the query stack.  Use push
     * and pop to prevent queries from clashing before they are
     * executed
     *
     * @return CDatabase This Databaes object
     *
     * @deprecated 1.1
     */
    public function push() {
        array_push($this->query_history, [
            $this->select,
            $this->from,
            $this->join,
            $this->where,
            $this->orderby,
            $this->order,
            $this->groupby,
            $this->having,
            $this->distinct,
            $this->limit,
            $this->offset
        ]);

        $this->reset_select();

        return $this;
    }

    /**
     * Pops from query stack into the current query space.
     *
     * @return CDatabase This Databaes object
     *
     * @deprecated 1.1
     */
    public function pop() {
        if (count($this->query_history) == 0) {
            // No history
            return $this;
        }

        list(
                $this->select,
                $this->from,
                $this->join,
                $this->where,
                $this->orderby,
                $this->order,
                $this->groupby,
                $this->having,
                $this->distinct,
                $this->limit,
                $this->offset
                ) = array_pop($this->query_history);

        return $this;
    }

    /**
     * Get the field data for a database table, along with the field's attributes.
     *
     * @param string $table table name
     *
     * @return array
     *
     * @deprecated
     */
    public function field_data($table = '') {
        return $this->fieldData($table);
    }

    /**
     * Count the number of records in the last query, without LIMIT or OFFSET applied.
     *
     * @return int
     *
     * @deprecated 1.1
     */
    public function count_last_query() {
        return $this->countLastQuery();
    }
}
// @codingStandardsIgnoreEnd
