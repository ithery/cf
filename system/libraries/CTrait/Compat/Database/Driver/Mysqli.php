<?php

// @codingStandardsIgnoreStart
trait CTrait_Compat_Database_Driver_Mysqli {
    public function compile_select($database) {
        $sql = ($database['distinct'] == true) ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= (count($database['select']) > 0) ? implode(', ', $database['select']) : '*';

        if (count($database['from']) > 0) {
            // Escape the tables
            $froms = [];
            foreach ($database['from'] as $from) {
                $froms[] = $this->escapeColumn($from);
            }
            $sql .= "\nFROM (";
            $sql .= implode(', ', $froms) . ')';
        }

        if (count($database['join']) > 0) {
            foreach ($database['join'] as $join) {
                $sql .= "\n" . $join['type'] . 'JOIN ' . implode(', ', $join['tables']) . ' ON ' . $join['conditions'];
            }
        }

        if (count($database['where']) > 0) {
            $sql .= "\nWHERE ";
        }

        $sql .= implode("\n", $database['where']);

        if (count($database['groupby']) > 0) {
            $sql .= "\nGROUP BY ";
            $sql .= implode(', ', $database['groupby']);
        }

        if (count($database['having']) > 0) {
            $sql .= "\nHAVING ";
            $sql .= implode("\n", $database['having']);
        }

        if (count($database['orderby']) > 0) {
            $sql .= "\nORDER BY ";
            $sql .= implode(', ', $database['orderby']);
        }

        if (is_numeric($database['limit'])) {
            $sql .= "\n";
            $sql .= $this->limit($database['limit'], $database['offset']);
        }

        return $sql;
    }

    /**
     * @param mixed $charset
     *
     * @deprecated use setCharset
     */
    public function set_charset($charset) {
        return $this->setCharset($charset);
    }

    /**
     * @deprecated use listTables
     */
    public function list_tables() {
        return $this->listTables();
    }

    /**
     * @param string $table
     *
     * @deprecated use escapeTable
     */
    public function escape_table($table) {
        return $this->escapeTable($table);
    }

    /**
     * @param string $table
     *
     * @deprecated use fieldData
     */
    public function field_data($table) {
        return $this->fieldData($table);
    }

    /**
     * @param string $table
     *
     * @deprecated use listFields
     */
    public function list_fields($table) {
        return $this->listFields($table);
    }

    /**
     * @param string $str
     *
     * @deprecated use escapeStr
     */
    public function escape_str($str) {
        return $this->escapeStr($str);
    }

    /**
     * @param string $column
     *
     * @deprecated use escapeColumn
     */
    public function escape_column($column) {
        return $this->escapeColumn($column);
    }

    /**
     * @deprecated use showError
     */
    public function show_error() {
        return $this->showError();
    }
}
