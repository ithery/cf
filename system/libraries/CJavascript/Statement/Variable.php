<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 10:50:54 PM
 */
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
