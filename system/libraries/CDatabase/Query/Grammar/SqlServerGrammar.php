<?php

class CDatabase_Query_Grammar_SqlServerGrammar extends CDatabase_Query_Grammar {
    /**
     * All of the available clause operators.
     *
     * @var string[]
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '!<', '!>', '<>', '!=',
        'like', 'not like', 'ilike',
        '&', '&=', '|', '|=', '^', '^=',
    ];

    /**
     * The components that make up a select clause.
     *
     * @var string[]
     */
    protected $selectComponents = [
        'aggregate',
        'columns',
        'from',
        'indexHint',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'offset',
        'limit',
        'lock',
    ];

    /**
     * Compile a select query into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return string
     */
    public function compileSelect(CDatabase_Query_Builder $query) {
        // An order by clause is required for SQL Server offset to function...
        if ($query->offset && empty($query->orders)) {
            $query->orders[] = ['sql' => '(SELECT 0)'];
        }

        return parent::compileSelect($query);
    }

    /**
     * Compile the "select *" portion of the query.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $columns
     *
     * @return null|string
     */
    protected function compileColumns(CDatabase_Query_Builder $query, $columns) {
        if (!is_null($query->aggregate)) {
            return;
        }

        $select = $query->distinct ? 'select distinct ' : 'select ';

        // If there is a limit on the query, but not an offset, we will add the top
        // clause to the query, which serves as a "limit" type clause within the
        // SQL Server system similar to the limit keywords available in MySQL.
        if (is_numeric($query->limit) && $query->limit > 0 && $query->offset <= 0) {
            $select .= 'top ' . ((int) $query->limit) . ' ';
        }

        return $select . $this->columnize($columns);
    }

    /**
     * Compile the "from" portion of the query.
     *
     * @param \CDatabase_Query_Builder $query
     * @param string                   $table
     *
     * @return string
     */
    protected function compileFrom(CDatabase_Query_Builder $query, $table) {
        $from = parent::compileFrom($query, $table);

        if (is_string($query->lock)) {
            return $from . ' ' . $query->lock;
        }

        if (!is_null($query->lock)) {
            return $from . ' with(rowlock,' . ($query->lock ? 'updlock,' : '') . 'holdlock)';
        }

        return $from;
    }

    /**
     * Compile the index hints for the query.
     *
     * @param \CDatabase_Query_Builder   $query
     * @param \CDatabase_Query_IndexHint $indexHint
     *
     * @return string
     */
    protected function compileIndexHint(CDatabase_Query_Builder $query, $indexHint) {
        return $indexHint->type === 'force'
                    ? "with (index({$indexHint->index}))"
                    : '';
    }

    /**
     * @inheritdoc
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $where
     *
     * @return string
     */
    protected function whereBitwise(CDatabase_Query_Builder $query, $where) {
        $value = $this->parameter($where['value']);

        $operator = str_replace('?', '??', $where['operator']);

        return '(' . $this->wrap($where['column']) . ' ' . $operator . ' ' . $value . ') != 0';
    }

    /**
     * Compile a "where date" clause.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $where
     *
     * @return string
     */
    protected function whereDate(CDatabase_Query_Builder $query, $where) {
        $value = $this->parameter($where['value']);

        return 'cast(' . $this->wrap($where['column']) . ' as date) ' . $where['operator'] . ' ' . $value;
    }

    /**
     * Compile a "where time" clause.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $where
     *
     * @return string
     */
    protected function whereTime(CDatabase_Query_Builder $query, $where) {
        $value = $this->parameter($where['value']);

        return 'cast(' . $this->wrap($where['column']) . ' as time) ' . $where['operator'] . ' ' . $value;
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

        return $value . ' in (select [value] from openjson(' . $field . $path . '))';
    }

    /**
     * Prepare the binding for a "JSON contains" statement.
     *
     * @param mixed $binding
     *
     * @return string
     */
    public function prepareBindingForJsonContains($binding) {
        return is_bool($binding) ? json_encode($binding) : $binding;
    }

    /**
     * Compile a "JSON contains key" statement into SQL.
     *
     * @param string $column
     *
     * @return string
     */
    protected function compileJsonContainsKey($column) {
        $segments = explode('->', $column);

        $lastSegment = array_pop($segments);

        if (preg_match('/\[([0-9]+)\]$/', $lastSegment, $matches)) {
            $segments[] = cstr::beforeLast($lastSegment, $matches[0]);

            $key = $matches[1];
        } else {
            $key = "'" . str_replace("'", "''", $lastSegment) . "'";
        }

        list($field, $path) = $this->wrapJsonFieldAndPath(implode('->', $segments));

        return $key . ' in (select [key] from openjson(' . $field . $path . '))';
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

        return '(select count(*) from openjson(' . $field . $path . ')) ' . $operator . ' ' . $value;
    }

    /**
     * Compile a "JSON value cast" statement into SQL.
     *
     * @param string $value
     *
     * @return string
     */
    public function compileJsonValueCast($value) {
        return 'json_query(' . $value . ')';
    }

    /**
     * Compile a single having clause.
     *
     * @param array $having
     *
     * @return string
     */
    protected function compileHaving(array $having) {
        if ($having['type'] === 'Bitwise') {
            return $this->compileHavingBitwise($having);
        }

        return parent::compileHaving($having);
    }

    /**
     * Compile a having clause involving a bitwise operator.
     *
     * @param array $having
     *
     * @return string
     */
    protected function compileHavingBitwise($having) {
        $column = $this->wrap($having['column']);

        $parameter = $this->parameter($having['value']);

        return '(' . $column . ' ' . $having['operator'] . ' ' . $parameter . ') != 0';
    }

    /**
     * Move the order bindings to be after the "select" statement to account for an order by subquery.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return array
     */
    protected function sortBindingsForSubqueryOrderBy($query) {
        return carr::sort($query->bindings, function ($bindings, $key) {
            return array_search($key, ['select', 'order', 'from', 'join', 'where', 'groupBy', 'having', 'union', 'unionOrder']);
        });
    }

    /**
     * Compile the limit / offset row constraint for a query.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return string
     */
    protected function compileRowConstraint($query) {
        $start = (int) $query->offset + 1;

        if ($query->limit > 0) {
            $finish = (int) $query->offset + (int) $query->limit;

            return "between {$start} and {$finish}";
        }

        return ">= {$start}";
    }

    /**
     * Compile a delete statement without joins into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     * @param string                   $table
     * @param string                   $where
     *
     * @return string
     */
    protected function compileDeleteWithoutJoins(CDatabase_Query_Builder $query, $table, $where) {
        $sql = parent::compileDeleteWithoutJoins($query, $table, $where);

        return !is_null($query->limit) && $query->limit > 0 && $query->offset <= 0
                        ? cstr::replaceFirst('delete', 'delete top (' . $query->limit . ')', $sql)
                        : $sql;
    }

    /**
     * Compile the random statement into SQL.
     *
     * @param string|int $seed
     *
     * @return string
     */
    public function compileRandom($seed) {
        return 'NEWID()';
    }

    /**
     * Compile the "limit" portions of the query.
     *
     * @param \CDatabase_Query_Builder $query
     * @param int                      $limit
     *
     * @return string
     */
    protected function compileLimit(CDatabase_Query_Builder $query, $limit) {
        $limit = (int) $limit;

        if ($limit && $query->offset > 0) {
            return "fetch next {$limit} rows only";
        }

        return '';
    }

    /**
     * Compile the "offset" portions of the query.
     *
     * @param \CDatabase_Query_Builder $query
     * @param int                      $offset
     *
     * @return string
     */
    protected function compileOffset(CDatabase_Query_Builder $query, $offset) {
        $offset = (int) $offset;

        if ($offset) {
            return "offset {$offset} rows";
        }

        return '';
    }

    /**
     * Compile the lock into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     * @param bool|string              $value
     *
     * @return string
     */
    protected function compileLock(CDatabase_Query_Builder $query, $value) {
        return '';
    }

    /**
     * Wrap a union subquery in parentheses.
     *
     * @param string $sql
     *
     * @return string
     */
    protected function wrapUnion($sql) {
        return 'select * from (' . $sql . ') as ' . $this->wrapTable('temp_table');
    }

    /**
     * Compile an exists statement into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return string
     */
    public function compileExists(CDatabase_Query_Builder $query) {
        $existsQuery = clone $query;

        $existsQuery->columns = [];

        return $this->compileSelect($existsQuery->selectRaw('1 [exists]')->limit(1));
    }

    /**
     * Compile an update statement with joins into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     * @param string                   $table
     * @param string                   $columns
     * @param string                   $where
     *
     * @return string
     */
    protected function compileUpdateWithJoins(CDatabase_Query_Builder $query, $table, $columns, $where) {
        $alias = c::last(explode(' as ', $table));

        $joins = $this->compileJoins($query, $query->joins);

        return "update {$alias} set {$columns} from {$table} {$joins} {$where}";
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
        $columns = $this->columnize(array_keys(reset($values)));

        $sql = 'merge ' . $this->wrapTable($query->from) . ' ';

        $parameters = c::collect($values)->map(function ($record) {
            return '(' . $this->parameterize($record) . ')';
        })->implode(', ');

        $sql .= 'using (values ' . $parameters . ') ' . $this->wrapTable('laravel_source') . ' (' . $columns . ') ';

        $on = c::collect($uniqueBy)->map(function ($column) use ($query) {
            return $this->wrap('laravel_source.' . $column) . ' = ' . $this->wrap($query->from . '.' . $column);
        })->implode(' and ');

        $sql .= 'on ' . $on . ' ';

        if ($update) {
            $update = c::collect($update)->map(function ($value, $key) {
                return is_numeric($key)
                    ? $this->wrap($value) . ' = ' . $this->wrap('laravel_source.' . $value)
                    : $this->wrap($key) . ' = ' . $this->parameter($value);
            })->implode(', ');

            $sql .= 'when matched then update set ' . $update . ' ';
        }

        $sql .= 'when not matched then insert (' . $columns . ') values (' . $columns . ');';

        return $sql;
    }

    /**
     * Prepare the bindings for an update statement.
     *
     * @param array $bindings
     * @param array $values
     *
     * @return array
     */
    public function prepareBindingsForUpdate(array $bindings, array $values) {
        $cleanBindings = carr::except($bindings, 'select');

        return array_values(
            array_merge($values, carr::flatten($cleanBindings))
        );
    }

    /**
     * Compile the SQL statement to define a savepoint.
     *
     * @param string $name
     *
     * @return string
     */
    public function compileSavepoint($name) {
        return 'SAVE TRANSACTION ' . $name;
    }

    /**
     * Compile the SQL statement to execute a savepoint rollback.
     *
     * @param string $name
     *
     * @return string
     */
    public function compileSavepointRollBack($name) {
        return 'ROLLBACK TRANSACTION ' . $name;
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat() {
        return 'Y-m-d H:i:s.v';
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param string $value
     *
     * @return string
     */
    protected function wrapValue($value) {
        return $value === '*' ? $value : '[' . str_replace(']', ']]', $value) . ']';
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

        return 'json_value(' . $field . $path . ')';
    }

    /**
     * Wrap the given JSON boolean value.
     *
     * @param string $value
     *
     * @return string
     */
    protected function wrapJsonBooleanValue($value) {
        return "'" . $value . "'";
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param \CDatabase_Query_Expression|string $table
     *
     * @return string
     */
    public function wrapTable($table) {
        if (!$this->isExpression($table)) {
            return $this->wrapTableValuedFunction(parent::wrapTable($table));
        }

        return $this->getValue($table);
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param string $table
     *
     * @return string
     */
    protected function wrapTableValuedFunction($table) {
        if (preg_match('/^(.+?)(\(.*?\))]$/', $table, $matches) === 1) {
            $table = $matches[1] . ']' . $matches[2];
        }

        return $table;
    }
}
