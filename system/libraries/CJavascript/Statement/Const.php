<?php

defined('SYSPATH') or die('No direct access allowed.');

class CJavascript_Statement_Const extends CJavascript_Statement {
    protected $constName = '';

    protected $constValue = '';

    public function __construct($constName, $constValue = null) {
        $this->constName = $constName;
        $this->constValue = $constValue;
    }

    public function getStatement() {
        return 'const ' . $this->constName . ' = ' . CJavascript_Helper_Javascript::prepValue($this->constValue) . ';';
    }
}
