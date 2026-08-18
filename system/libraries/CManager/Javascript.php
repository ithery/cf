<?php

defined('SYSPATH') or die('No direct access allowed.');

class CManager_Javascript {
    public static function jQuery() {
        return CJavascript::jqueryStatement();
    }

    public static function raw($js) {
        return CJavascript::rawStatement($js);
    }

    /**
     * @param mixed $operand1
     * @param mixed $operator
     * @param mixed $operand2
     *
     * @return CJavascript_Statement_IfStatement
     */
    public static function ifStatement($operand1, $operator, $operand2) {
        return CJavascript::ifStatement($operand1, $operator, $operand2);
    }
}
