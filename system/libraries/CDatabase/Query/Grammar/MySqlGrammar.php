<?php

class CDatabase_Query_Grammar_MySqlGrammar extends CDatabase_Query_Grammar {
    /**
     * The grammar specific operators.
     *
     * @var string[]
     */
    protected $operators = ['sounds like'];

    /**
     * Add a "where null" clause to the query.
     *
     * @param CDatabase_Query_Builder $query
     * @param array                   $where
     *
     * @return string
     */
    protected function whereNull(CDatabase_Query_Builder $query, $where) {
        if ($this->isJsonSelector($where['column'])) {
            list($field, $path) = $this->wrapJsonFieldAndPath($where['column']);

            return '(json_extract(' . $field . $path . ') is null OR json_type(json_extract(' . $field . $path . ')) = \'NULL\')';
        }

        return parent::whereNull($query, $where);
    }

    /**
     * Add a "where not null" clause to the query.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $where
     *
     * @return string
     */
    protected function whereNotNull(CDatabase_Query_Builder $query, $where) {
        if ($this->isJsonSelector($where['column'])) {
            list($field, $path) = $this->wrapJsonFieldAndPath($where['column']);

            return '(json_extract(' . $field . $path . ') is not null AND json_type(json_extract(' . $field . $path . ')) != \'NULL\')';
        }

        return parent::whereNotNull($query, $where);
    }

    /**
     * Compile a "where fulltext" clause.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $where
     *
     * @return string
     */
    public function whereFullText(CDatabase_Query_Builder $query, $where) {
        $columns = $this->columnize($where['columns']);

        $value = $this->parameter($where['value']);

        $mode = (isset($where['options']['mode']) ? $where['options']['mode'] : []) === 'boolean'
            ? ' in boolean mode'
            : ' in natural language mode';

        $expanded = (isset($where['options']['expanded']) ? $where['options']['expanded'] : []) && (isset($where['options']['mode']) ? $where['options']['mode'] : []) !== 'boolean'
            ? ' with query expansion'
            : '';

        return "match ({$columns}) against (" . $value . "{$mode}{$expanded})";
    }

    /**
     * Compile the index hints for the query.
     *
     * @param CDatabase_Query_Builder   $query
     * @param CDatabase_Query_IndexHint $indexHint
     *
     * @return string
     */
    protected function compileIndexHint(CDatabase_Query_Builder $query, $indexHint) {
        if ($indexHint->type == 'hint') {
            return "use index ({$indexHint->index})";
        }
        if ($indexHint->type == 'force') {
            return "force index ({$indexHint->index})";
        }

        return "ignore index ({$indexHint->index})";
    }

    /**
     * Compile a group limit clause.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return string
     */
    protected function compileGroupLimit(CDatabase_Query_Builder $query) {
        return $this->useLegacyGroupLimit($query)
            ? $this->compileLegacyGroupLimit($query)
            : parent::compileGroupLimit($query);
    }

    /**
     * Determine whether to use a legacy group limit clause for MySQL < 8.0.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return bool
     */
    public function useLegacyGroupLimit(CDatabase_Query_Builder $query) {
        $version = $query->getConnection()->getServerVersion();
        $connection = $query->getConnection();
        /** @var CDatabase_Connection_Pdo_MySqlConnection $connection */
        return !$connection->isMaria() && version_compare($version, '8.0.11') < 0;
    }

    /**
     * Compile a group limit clause for MySQL < 8.0.
     *
     * Derived from https://softonsofa.com/tweaking-eloquent-relations-how-to-get-n-related-models-per-parent/.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return string
     */
    protected function compileLegacyGroupLimit(CDatabase_Query_Builder $query) {
        $limit = (int) $query->groupLimit['value'];
        $offset = $query->offset;

        if (isset($offset)) {
            $offset = (int) $offset;
            $limit += $offset;

            $query->offset = null;
        }

        $column = c::last(explode('.', $query->groupLimit['column']));
        $column = $this->wrap($column);

        $partition = ', @cf_row := if(@cf_group = ' . $column . ', @cf_row + 1, 1) as `cf_row`';
        $partition .= ', @cf_group := ' . $column;

        $orders = (array) $query->orders;

        array_unshift($orders, [
            'column' => $query->groupLimit['column'],
            'direction' => 'asc',
        ]);

        $query->orders = $orders;

        $components = $this->compileComponents($query);

        $sql = $this->concatenate($components);

        $from = '(select @cf_row := 0, @cf_group := 0) as `cf_vars`, (' . $sql . ') as `cf_table`';

        $sql = 'select `cf_table`.*' . $partition . ' from ' . $from . ' having `cf_row` <= ' . $limit;

        if (isset($offset)) {
            $sql .= ' and `cf_row` > ' . $offset;
        }

        return $sql . ' order by `cf_row`';
    }

    /**
     * Compile an insert ignore statement into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $values
     *
     * @return string
     */
    public function compileInsertOrIgnore(CDatabase_Query_Builder $query, array $values) {
        return cstr::replaceFirst('insert', 'insert ignore', $this->compileInsert($query, $values));
    }

    /**
     * Compile a "JSON contains" statement into SQL.
     *
     * @param string $column
     * @param string $value
     *
     * @return string
     */
    protected function compileJsonContains($column, $value) {
        list($field, $path) = $this->wrapJsonFieldAndPath($column);

        return 'json_contains(' . $field . ', ' . $value . $path . ')';
    }

    /**
     * Compile a "JSON length" statement into SQL.
     *
     * @param string $column
     * @param string $operator
     * @param string $value
     *
     * @return string
     */
    protected function compileJsonLength($column, $operator, $value) {
        list($field, $path) = $this->wrapJsonFieldAndPath($column);

        return 'json_length(' . $field . $path . ') ' . $operator . ' ' . $value;
    }

    /**
     * Compile the random statement into SQL.
     *
     * @param string $seed
     *
     * @return string
     */
    public function compileRandom($seed) {
        return 'RAND(' . $seed . ')';
    }

    /**
     * Compile the lock into SQL.
     *
     * @param CDatabase_Query_Builder $query
     * @param bool|string             $value
     *
     * @return string
     */
    protected function compileLock(CDatabase_Query_Builder $query, $value) {
        if (!is_string($value)) {
            return $value ? 'for update' : 'lock in share mode';
        }

        return $value;
    }

    /**
     * Compile an insert statement into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $values
     *
     * @return string
     */
    public function compileInsert(CDatabase_Query_Builder $query, array $values) {
        if (empty($values)) {
            $values = [[]];
        }

        return parent::compileInsert($query, $values);
    }

    /**
     * Compile the columns for an update statement.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $values
     *
     * @return string
     */
    protected function compileUpdateColumns(CDatabase_Query_Builder $query, array $values) {
        return c::collect($values)->map(function ($value, $key) {
            if ($this->isJsonSelector($key)) {
                return $this->compileJsonUpdateColumn($key, $value);
            }

            return $this->wrap($key) . ' = ' . $this->parameter($value);
        })->implode(', ');
    }

    /**
     * Compile an "upsert" statement into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $values
     * @param array                    $uniqueBy
     * @param array                    $update
     *
     * @return string
     */
    public function compileUpsert(CDatabase_Query_Builder $query, array $values, array $uniqueBy, array $update) {
        $sql = $this->compileInsert($query, $values) . ' on duplicate key update ';

        $columns = c::collect($update)->map(function ($value, $key) {
            return is_numeric($key)
                ? $this->wrap($value) . ' = values(' . $this->wrap($value) . ')'
                : $this->wrap($key) . ' = ' . $this->parameter($value);
        })->implode(', ');

        return $sql . $columns;
    }

    /**
     * Compile a "lateral join" clause.
     *
     * @param \CDatabase_Query_JoinLateralClause $join
     * @param string                             $expression
     *
     * @return string
     */
    public function compileJoinLateral(CDatabase_Query_JoinLateralClause $join, $expression) {
        return trim("{$join->type} join lateral {$expression} on true");
    }

    /**
     * Prepare a JSON column being updated using the JSON_SET function.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return string
     */
    protected function compileJsonUpdateColumn($key, $value) {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            $value = 'cast(? as json)';
        } else {
            $value = $this->parameter($value);
        }

        list($field, $path) = $this->wrapJsonFieldAndPath($key);

        return "{$field} = json_set({$field}{$path}, {$value})";
    }

    /**
     * Compile an update statement without joins into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     * @param string                   $table
     * @param string                   $columns
     * @param string                   $where
     *
     * @return string
     */
    protected function compileUpdateWithoutJoins(CDatabase_Query_Builder $query, $table, $columns, $where) {
        $sql = parent::compileUpdateWithoutJoins($query, $table, $columns, $where);

        if (!empty($query->orders)) {
            $sql .= ' ' . $this->compileOrders($query, $query->orders);
        }

        if (isset($query->limit)) {
            $sql .= ' ' . $this->compileLimit($query, $query->limit);
        }

        return $sql;
    }

    /**
     * Prepare the bindings for an update statement.
     *
     * Booleans, integers, and doubles are inserted into JSON updates as raw values.
     *
     * @param array $bindings
     * @param array $values
     *
     * @return array
     */
    public function prepareBindingsForUpdate(array $bindings, array $values) {
        $values = c::collect($values)->reject(function ($value, $column) {
            return $this->isJsonSelector($column) && is_bool($value);
        })->map(function ($value) {
            return is_array($value) ? json_encode($value) : $value;
        })->all();

        return parent::prepareBindingsForUpdate($bindings, $values);
    }

    /**
     * Compile a delete query that does not use joins.
     *
     * @param \CDatabase_Query_Builder $query
     * @param string                   $table
     * @param string                   $where
     *
     * @return string
     */
    protected function compileDeleteWithoutJoins(CDatabase_Query_Builder $query, $table, $where) {
        $sql = parent::compileDeleteWithoutJoins($query, $table, $where);

        // When using MySQL, delete statements may contain order by statements and limits
        // so we will compile both of those here. Once we have finished compiling this
        // we will return the completed SQL statement so it will be executed for us.
        if (!empty($query->orders)) {
            $sql .= ' ' . $this->compileOrders($query, $query->orders);
        }

        if (isset($query->limit)) {
            $sql .= ' ' . $this->compileLimit($query, $query->limit);
        }

        return $sql;
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param string $value
     *
     * @return string
     */
    protected function wrapValue($value) {
        return $value === '*' ? $value : '`' . str_replace('`', '``', $value) . '`';
    }

    /**
     * Wrap the given JSON selector.
     *
     * @param string $value
     *
     * @return string
     */
    protected function wrapJsonSelector($value) {
        list($field, $path) = $this->wrapJsonFieldAndPath($value);

        return 'json_unquote(json_extract(' . $field . $path . '))';
    }

    /**
     * Wrap the given JSON selector for boolean values.
     *
     * @param string $value
     *
     * @return string
     */
    protected function wrapJsonBooleanSelector($value) {
        list($field, $path) = $this->wrapJsonFieldAndPath($value);

        return 'json_extract(' . $field . $path . ')';
    }

    /**
     * Compile the "from" portion of the query.
     *
     * @param CDatabase_Query_Builder $query
     * @param string                  $table
     *
     * @return string
     */
    protected function compileFrom(CDatabase_Query_Builder $query, $table) {
        $from = 'from ' . $this->wrapTable($table);
        if ($query->getUseIndex()) {
            $from .= ' use index(' . $query->getUseIndex() . ')';
        }

        return $from;
    }
}
