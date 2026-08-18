<?php
class CDatabase_Query_Grammar_SqliteGrammar extends CDatabase_Query_Grammar {
    /**
     * All of the available clause operators.
     *
     * @var string[]
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'ilike',
        '&', '|', '<<', '>>',
    ];

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
        return 'select * from (' . $sql . ')';
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
        return $this->dateBasedWhere('%Y-%m-%d', $query, $where);
    }

    /**
     * Compile a "where day" clause.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $where
     *
     * @return string
     */
    protected function whereDay(CDatabase_Query_Builder $query, $where) {
        return $this->dateBasedWhere('%d', $query, $where);
    }

    /**
     * Compile a "where month" clause.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $where
     *
     * @return string
     */
    protected function whereMonth(CDatabase_Query_Builder $query, $where) {
        return $this->dateBasedWhere('%m', $query, $where);
    }

    /**
     * Compile a "where year" clause.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $where
     *
     * @return string
     */
    protected function whereYear(CDatabase_Query_Builder $query, $where) {
        return $this->dateBasedWhere('%Y', $query, $where);
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
        return $this->dateBasedWhere('%H:%M:%S', $query, $where);
    }

    /**
     * Compile a date based where clause.
     *
     * @param string                   $type
     * @param \CDatabase_Query_Builder $query
     * @param array                    $where
     *
     * @return string
     */
    protected function dateBasedWhere($type, CDatabase_Query_Builder $query, $where) {
        $value = $this->parameter($where['value']);

        return "strftime('{$type}', {$this->wrap($where['column'])}) {$where['operator']} cast({$value} as text)";
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

        return 'json_array_length(' . $field . $path . ') ' . $operator . ' ' . $value;
    }

    /**
     * Compile an update statement into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $values
     *
     * @return string
     */
    public function compileUpdate(CDatabase_Query_Builder $query, array $values) {
        if (isset($query->joins) || isset($query->limit)) {
            return $this->compileUpdateWithJoinsOrLimit($query, $values);
        }

        return parent::compileUpdate($query, $values);
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
        return cstr::replaceFirst('insert', 'insert or ignore', $this->compileInsert($query, $values));
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
        $jsonGroups = $this->groupJsonColumnsForUpdate($values);

        return c::collect($values)->reject(function ($value, $key) {
            return $this->isJsonSelector($key);
        })->merge($jsonGroups)->map(function ($value, $key) use ($jsonGroups) {
            $column = c::last(explode('.', $key));

            $value = isset($jsonGroups[$key]) ? $this->compileJsonPatch($column, $value) : $this->parameter($value);

            return $this->wrap($column) . ' = ' . $value;
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
        $sql = $this->compileInsert($query, $values);

        $sql .= ' on conflict (' . $this->columnize($uniqueBy) . ') do update set ';

        $columns = c::collect($update)->map(function ($value, $key) {
            return is_numeric($key)
                ? $this->wrap($value) . ' = ' . $this->wrapValue('excluded') . '.' . $this->wrap($value)
                : $this->wrap($key) . ' = ' . $this->parameter($value);
        })->implode(', ');

        return $sql . $columns;
    }

    /**
     * Group the nested JSON columns.
     *
     * @param array $values
     *
     * @return array
     */
    protected function groupJsonColumnsForUpdate(array $values) {
        $groups = [];

        foreach ($values as $key => $value) {
            if ($this->isJsonSelector($key)) {
                carr::set($groups, str_replace('->', '.', cstr::after($key, '.')), $value);
            }
        }

        return $groups;
    }

    /**
     * Compile a "JSON" patch statement into SQL.
     *
     * @param string $column
     * @param mixed  $value
     *
     * @return string
     */
    protected function compileJsonPatch($column, $value) {
        return "json_patch(ifnull({$this->wrap($column)}, json('{}')), json({$this->parameter($value)}))";
    }

    /**
     * Compile an update statement with joins or limit into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     * @param array                    $values
     *
     * @return string
     */
    protected function compileUpdateWithJoinsOrLimit(CDatabase_Query_Builder $query, array $values) {
        $table = $this->wrapTable($query->from);

        $columns = $this->compileUpdateColumns($query, $values);

        $alias = c::last(preg_split('/\s+as\s+/i', $query->from));

        $selectSql = $this->compileSelect($query->select($alias . '.rowid'));

        return "update {$table} set {$columns} where {$this->wrap('rowid')} in ({$selectSql})";
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
        $groups = $this->groupJsonColumnsForUpdate($values);

        $values = c::collect($values)->reject(function ($value, $key) {
            return $this->isJsonSelector($key);
        })->merge($groups)->map(function ($value) {
            return is_array($value) ? json_encode($value) : $value;
        })->all();

        $cleanBindings = carr::except($bindings, 'select');

        return array_values(
            array_merge($values, carr::flatten($cleanBindings))
        );
    }

    /**
     * Compile a delete statement into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return string
     */
    public function compileDelete(CDatabase_Query_Builder $query) {
        if (isset($query->joins) || isset($query->limit)) {
            return $this->compileDeleteWithJoinsOrLimit($query);
        }

        return parent::compileDelete($query);
    }

    /**
     * Compile a delete statement with joins or limit into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return string
     */
    protected function compileDeleteWithJoinsOrLimit(CDatabase_Query_Builder $query) {
        $table = $this->wrapTable($query->from);

        $alias = c::last(preg_split('/\s+as\s+/i', $query->from));

        $selectSql = $this->compileSelect($query->select($alias . '.rowid'));

        return "delete from {$table} where {$this->wrap('rowid')} in ({$selectSql})";
    }

    /**
     * Compile a truncate table statement into SQL.
     *
     * @param \CDatabase_Query_Builder $query
     *
     * @return array
     */
    public function compileTruncate(CDatabase_Query_Builder $query) {
        return [
            'delete from sqlite_sequence where name = ?' => [$query->from],
            'delete from ' . $this->wrapTable($query->from) => [],
        ];
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

        return 'json_extract(' . $field . $path . ')';
    }
}
