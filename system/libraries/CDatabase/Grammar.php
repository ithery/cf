<?php

abstract class CDatabase_Grammar {
    /**
     * The connection used for escaping values.
     *
     * @var \CDatabase_Connection
     */
    protected $connection;

    /**
     * The grammar table prefix.
     *
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * Wrap an array of values.
     *
     * @return array
     */
    public function wrapArray(array $values) {
        return array_map([$this, 'wrap'], $values);
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param CDatabase_Contract_Query_ExpressionInterface|string $table
     * @param null|string                                         $prefix
     *
     * @return string
     */
    public function wrapTable($table, $prefix = null) {
        if ($this->isExpression($table)) {
            return $this->getValue($table);
        }

        $prefix ??= $this->connection->getTablePrefix();

        // If the table being wrapped has an alias we'll need to separate the pieces
        // so we can prefix the table and then wrap each of the segments on their
        // own and then join these both back together using the "as" connector.
        if (stripos($table, ' as ') !== false) {
            return $this->wrapAliasedTable($table, $prefix);
        }

        // If the table being wrapped has a custom schema name specified, we need to
        // prefix the last segment as the table name then wrap each segment alone
        // and eventually join them both back together using the dot connector.
        if (str_contains($table, '.')) {
            $table = substr_replace($table, '.' . $prefix, strrpos($table, '.'), 1);

            return (new CCollection(explode('.', $table)))
                ->map($this->wrapValue(...))
                ->implode('.');
        }

        return $this->wrapValue($prefix . $table);
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param CDatabase_Contract_Query_ExpressionInterface|string $value
     * @param bool                                                $prefixAlias
     *
     * @return string
     */
    public function wrap($value, $prefixAlias = false) {
        if ($this->isExpression($value)) {
            return $this->getValue($value);
        }

        // If the value being wrapped has a column alias we will need to separate out
        // the pieces so we can wrap each of the segments of the expression on it
        // own, and then joins them both back together with the "as" connector.
        if (strpos(strtolower($value), ' as ') !== false) {
            return $this->wrapAliasedValue($value, $prefixAlias);
        }

        // If the given value is a JSON selector we will wrap it differently than a
        // traditional value. We will need to split this path and wrap each part
        // wrapped, etc. Otherwise, we will simply wrap the value as a string.
        if ($this->isJsonSelector($value)) {
            return $this->wrapJsonSelector($value);
        }

        return $this->wrapSegments(explode('.', $value));
    }

    /**
     * Wrap a value that has an alias.
     *
     * @param string $value
     * @param bool   $prefixAlias
     *
     * @return string
     */
    protected function wrapAliasedValue($value, $prefixAlias = false) {
        $segments = preg_split('/\s+as\s+/i', $value);

        // If we are wrapping a table we need to prefix the alias with the table prefix
        // as well in order to generate proper syntax. If this is a column of course
        // no prefix is necessary. The condition will be true when from wrapTable.
        if ($prefixAlias) {
            $segments[1] = $this->tablePrefix . $segments[1];
        }

        return $this->wrap($segments[0]) . ' as ' . $this->wrapValue($segments[1]);
    }

    /**
     * Wrap a table that has an alias.
     *
     * @param string      $value
     * @param null|string $prefix
     *
     * @return string
     */
    protected function wrapAliasedTable($value, $prefix = null) {
        $segments = preg_split('/\s+as\s+/i', $value);

        $prefix ??= $this->connection->getTablePrefix();

        return $this->wrapTable($segments[0], $prefix) . ' as ' . $this->wrapValue($prefix . $segments[1]);
    }

    /**
     * Wrap the given value segments.
     *
     * @param array $segments
     *
     * @return string
     */
    protected function wrapSegments($segments) {
        $collection = c::collect($segments);

        return $collection->map(function ($segment, $key) use ($segments) {
            return $key == 0 && count($segments) > 1
                ? $this->wrapTable($segment)
                : $this->wrapValue($segment);
        })->implode('.');
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param string $value
     *
     * @return string
     */
    protected function wrapValue($value) {
        if ($value !== '*') {
            return '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }

    /**
     * Wrap the given JSON selector.
     *
     * @param string $value
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function wrapJsonSelector($value) {
        throw new RuntimeException('This database engine does not support JSON operations.');
    }

    /**
     * Determine if the given string is a JSON selector.
     *
     * @param string $value
     *
     * @return bool
     */
    protected function isJsonSelector($value) {
        return str_contains($value, '->');
    }

    /**
     * Convert an array of column names into a delimited string.
     *
     * @return string
     */
    public function columnize(array $columns) {
        return implode(', ', array_map([$this, 'wrap'], $columns));
    }

    /**
     * Create query parameter place-holders for an array.
     *
     * @return string
     */
    public function parameterize(array $values) {
        return implode(', ', array_map([$this, 'parameter'], $values));
    }

    /**
     * Get the appropriate query parameter place-holder for a value.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function parameter($value) {
        return $this->isExpression($value) ? $this->getValue($value) : '?';
    }

    /**
     * Quote the given string literal.
     *
     * @param string|array $value
     *
     * @return string
     */
    public function quoteString($value) {
        if (is_array($value)) {
            return implode(', ', array_map([$this, __FUNCTION__], $value));
        }

        return "'${value}'";
    }

    /**
     * Escapes a value for safe SQL embedding.
     *
     * @param null|string|float|int|bool $value
     * @param bool                       $binary
     *
     * @return string
     */
    public function escape($value, $binary = false) {
        if (is_null($this->connection)) {
            throw new RuntimeException("The database driver's grammar implementation does not support escaping values.");
        }

        return $this->connection->escape($value, $binary);
    }

    /**
     * Determine if the given value is a raw expression.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isExpression($value) {
        return $value instanceof CDatabase_Contract_Query_ExpressionInterface;
    }

    /**
     * Get the value of a raw expression.
     *
     * @param CDatabase_Contract_Query_ExpressionInterface $expression
     *
     * @return string
     */
    public function getValue($expression) {
        if ($this->isExpression($expression)) {
            return $this->getValue($expression->getValue($this));
        }

        return $expression;
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat() {
        return 'Y-m-d H:i:s';
    }

    /**
     * Get the grammar's table prefix.
     *
     * @return string
     */
    public function getTablePrefix() {
        return $this->tablePrefix;
    }

    /**
     * Set the grammar's table prefix.
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function setTablePrefix($prefix) {
        $this->tablePrefix = $prefix;

        return $this;
    }

    /**
     * Set the grammar's database connection.
     *
     * @param \CDatabase_Connection $connection
     *
     * @return $this
     */
    public function setConnection($connection) {
        $this->connection = $connection;

        return $this;
    }
}
