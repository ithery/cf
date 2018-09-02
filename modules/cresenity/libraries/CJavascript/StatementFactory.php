<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 11:02:36 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_StatementFactory {

    public static function createJQuery($selector = 'this') {
        return new CJavascript_Statement_JQuery($selector);
    }

    public static function createVariable($varName, $varValue = null) {
        return new CJavascript_Statement_Variable($varName, $varValue);
    }

    /**
     * 
     * @param string $js
     * @return CJavascript_Statement_Raw
     */
    public static function createRaw($js) {
        return CJavascript_Statement_Raw($js);
    }

    /**
     * 
     * @param string $functionName
     * @param array $functionParameter
     * @return CJavascript_Statement_Function
     */
    public static function createFunction($functionName, $functionParameter = array()) {
        return CJavascript_Statement_Function($functionName, $functionParameter);
    }

}
