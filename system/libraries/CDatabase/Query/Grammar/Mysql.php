<?php

class CDatabase_Query_Grammar_Mysql extends CDatabase_Query_Grammar {

    /**
     * The components that make up a select clause.
     *
     * @var array
     */
    protected $selectComponents = [
        'aggregate',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'lock',
    ];

    /**
     * Compile a select query into SQL.
     *
     * @param  CDatabase_Query_Builder  $query
     * @return string
     */
    public function compileSelect(CDatabase_Query_Builder $query) {
        $sql = parent::compileSelect($query);

        if ($query->unions) {
            $sql = '(' . $sql . ') ' . $this->compileUnions($query);
        }

        return $sql;
    }

    /**
     * Compile a single union statement.
     *
     * @param  array  $union
     * @return string
     */
    protected function compileUnion(array $union) {
        $conjuction = $union['all'] ? ' union all ' : ' union ';

        return $conjuction . '(' . $union['query']->toSql() . ')';
    }

    /**
     * Compile the random statement into SQL.
     *
     * @param  string  $seed
     * @return string
     */
    public function compileRandom($seed) {
        return 'RAND(' . $seed . ')';
    }

    /**
     * Compile the lock into SQL.
     *
     * @param  CDatabase_Query_Builder  $query
     * @param  bool|string  $value
     * @return string
     */
    protected function compileLock(CDatabase_Query_Builder $query, $value) {
        if (!is_string($value)) {
            return $value ? 'for update' : 'lock in share mode';
        }

        return $value;
    }

    /**
     * Compile the columns for an update statement.
     *
     * @param  CDatabase_Query_Builder  $query
     * @param  array  $values
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
     * Prepares a JSON column being updated using the JSON_SET function.
     *
     * @param  string  $key
     * @param  \Illuminate\Database\Query\JsonExpression  $value
     * @return string
     */
    protected function compileJsonUpdateColumn($key, JsonExpression $value) {
        $path = explode('->', $key);

        $field = $this->wrapValue(array_shift($path));

        $accessor = "'$.\"" . implode('"."', $path) . "\"'";

        return "{$field} = json_set({$field}, {$accessor}, {$value->getValue()})";
    }

    /**
     * Prepare the bindings for an update statement.
     *
     * Booleans, integers, and doubles are inserted into JSON updates as raw values.
     *
     * @param  array  $bindings
     * @param  array  $values
     * @return array
     */
    public function prepareBindingsForUpdate(array $bindings, array $values) {
        $collection = new CCollection($values);
        $values = $collection->reject(function ($value, $column) {
                    return $this->isJsonSelector($column) &&
                            in_array(gettype($value), ['boolean', 'integer', 'double']);
                })->all();

        return parent::prepareBindingsForUpdate($bindings, $values);
    }

    /**
     * Compile a delete statement into SQL.
     *
     * @param  CDatabase_Query_Builder  $query
     * @return string
     */
    public function compileDelete(CDatabase_Query_Builder $query) {
        $table = $this->wrapTable($query->from);

        $where = is_array($query->wheres) ? $this->compileWheres($query) : '';

        return isset($query->joins) ? $this->compileDeleteWithJoins($query, $table, $where) : $this->compileDeleteWithoutJoins($query, $table, $where);
    }

    /**
     * Prepare the bindings for a delete statement.
     *
     * @param  array  $bindings
     * @return array
     */
    public function prepareBindingsForDelete(array $bindings) {
        $cleanBindings = carr::except($bindings, ['join', 'select']);

        return array_values(
                array_merge($bindings['join'], carr::flatten($cleanBindings))
        );
    }

    /**
     * Compile a delete query that does not use joins.
     *
     * @param  CDatabase_Query_Builder  $query
     * @param  string  $table
     * @param  array  $where
     * @return string
     */
    protected function compileDeleteWithoutJoins($query, $table, $where) {
        $sql = trim("delete from {$table} {$where}");

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
     * Compile a delete query that uses joins.
     *
     * @param  CDatabase_Query_Builder  $query
     * @param  string  $table
     * @param  array  $where
     * @return string
     */
    protected function compileDeleteWithJoins($query, $table, $where) {
        $joins = ' ' . $this->compileJoins($query, $query->joins);

        $alias = strpos(strtolower($table), ' as ') !== false ? explode(' as ', $table)[1] : $table;

        return trim("delete {$alias} from {$table}{$joins} {$where}");
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapValue($value) {
        if ($value === '*') {
            return $value;
        }

        // If the given value is a JSON selector we will wrap it differently than a
        // traditional value. We will need to split this path and wrap each part
        // wrapped, etc. Otherwise, we will simply wrap the value as a string.
        if ($this->isJsonSelector($value)) {
            return $this->wrapJsonSelector($value);
        }

        return '`' . str_replace('`', '``', $value) . '`';
    }

    /**
     * Wrap the given JSON selector.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonSelector($value) {
        $path = explode('->', $value);

        $field = $this->wrapValue(array_shift($path));

        return sprintf('%s->\'$.%s\'', $field, collect($path)->map(function ($part) {
                    return '"' . $part . '"';
                })->implode('.'));
    }

    /**
     * Determine if the given string is a JSON selector.
     *
     * @param  string  $value
     * @return bool
     */
    protected function isJsonSelector($value) {
        return cstr::contains($value, '->');
    }

}
