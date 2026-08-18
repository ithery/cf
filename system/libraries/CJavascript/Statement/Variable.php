<?php

defined('SYSPATH') or die('No direct access allowed.');

class CJavascript_Statement_Variable extends CJavascript_Statement {
    protected $varName = '';

    protected $varValue = '';

    public function __construct($varName, $varValue = null) {
        $this->varName = $varName;
        $this->varValue = $varValue;
    }

    public function getStatement() {
        return 'var ' . $this->varName . ' = ' . CJavascript_Helper_Javascript::prepValue($this->varValue) . ';';
    }
}
