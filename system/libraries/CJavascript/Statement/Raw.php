<?php

defined('SYSPATH') or die('No direct access allowed.');

class CJavascript_Statement_Raw extends CJavascript_Statement {
    protected $raw = '';

    public function __construct($raw = ';') {
        $this->raw = $raw;
    }

    public function setStatement($statement) {
        $this->raw = $statement;
    }

    public function getStatement() {
        return trim($this->raw, ';') . ';';
    }
}
