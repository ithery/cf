<?php

class CDatabase_Analysis_Explainer_ExplainerColumn {
    public $field;

    public $type;

    public $null;

    public $key;

    public $default;

    public $extra;

    private $table;

    public function __construct($table, $sql_col) {
        $this->table = $table;
        $this->field = $sql_col['Field'];
        $this->type = $sql_col['Type'];
        $this->null = $sql_col['Null'];
        $this->key = $sql_col['Key'];
        $this->default = $sql_col['Default'];
        $this->extra = $sql_col['Extra'];
    }

    public function containsId() {
        return preg_match('/' . $this->table . '_id/', $this->field);
    }

    public function isNull() {
        return trim($this->null) == 'YES';
    }
}
