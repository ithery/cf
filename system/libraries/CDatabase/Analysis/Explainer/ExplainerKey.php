<?php

class CDatabase_Analysis_Explainer_ExplainerKey {
    public $keyName;

    public $colName;

    public function __construct($sqlKeyRow) {
        $this->keyName = $sqlKeyRow['Key_name'];
        $this->colName = $sqlKeyRow['Column_name'];
    }

    public function isPrimary() {
        return $this->keyName == 'PRIMARY';
    }
}
