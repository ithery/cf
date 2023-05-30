<?php

class CDatabase_ResultAffecting {
    protected $affectedRows;

    protected $insertId;

    public function __construct($affectedRows, $insertId = null) {
        $this->affectedRows = $affectedRows;
        $this->insertId = $insertId;
    }
}
