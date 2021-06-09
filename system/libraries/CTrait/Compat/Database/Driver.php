<?php

// @codingStandardsIgnoreStart
trait CTrait_Compat_Database_Driver {
    /**
     * Determines if the string has an arithmetic operator in it.
     *
     * @param string $str string to check
     *
     * @return bool
     *
     * @deprecated use hasOperator
     */
    public function has_operator($str) {
        return $this->hasOperator($str);
    }

    /**
     * Fetches SQL type information about a field, in a generic format.
     *
     * @param string $str field datatype
     *
     * @return array
     *
     * @deprecated use sqlType
     */
    protected function sql_type($str) {
        return $this->sqlType($str);
    }

    /**
     * Creates a hash for an SQL query string. Replaces newlines with spaces,
     * trims, and hashes.
     *
     * @param string $sql SQL query
     *
     * @return string
     *
     * @deprecated use queryHash
     */
    protected function query_hash($sql) {
        return $this->queryHash($sql);
    }

    /**
     * Clears the internal query cache.
     *
     * @param string $sql SQL query
     *
     * @deprecated use clearCache
     */
    public function clear_cache($sql = null) {
        return $this->clearCache($sql);
    }
}
