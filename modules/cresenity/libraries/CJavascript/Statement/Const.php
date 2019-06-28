<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 12:39:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 10:50:54 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
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
