<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 11:38:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript {

    /**
     *
     * @var CJavascript_Statement[]
     */
    protected static $statements = array();
    protected static $deferredStatements = array();

    public static function compile() {
        $script = '';

        foreach (self::$statements as $statement) {
            $script .= $statement->getStatement();
        }

        return $script;
    }

    public static function getStatements() {
        return self::$statements;
    }

    public static function getDeferredStatements() {
        return self::$deferredStatements;
    }

    public static function addStatement(CJavascript_Statement $statement) {
        self::$statements[$statement->hash()] = $statement;
    }

    public static function addDeferredStatement(CJavascript_Statement $statement) {
        self::$deferredStatements[$statement->hash()] = $statement;
    }

    public static function removeStatement(CJavascript_Statement $statement) {
        if (isset(self::$statements[$statement->hash()])) {
            unset(self::$statements[$statement->hash()]);
        }
    }

    public static function removeDeferredStatement(CJavascript_Statement $statement) {
        if (isset(self::$deferredStatements[$statement->hash()])) {
            unset(self::$deferredStatements[$statement->hash()]);
        }
    }

    public static function clearStatement() {
        self::$statements = [];
    }

    public static function clearDeferredStatement() {
        self::$deferredStatements = [];
    }

    /**
     * 
     * @param string $selector
     * @return CJavascript_Statement_JQuery
     */
    public static function jqueryStatement($selector = 'this') {
        return CJavascript_StatementFactory::createJQuery($selector);
    }

    /**
     * 
     * @param string $varName
     * @param string $varValue
     * @return CJavascript_Statement_Variable
     */
    public static function variableStatement($varName, $varValue = null) {
        return CJavascript_StatementFactory::createVariable($varName, $varValue);
    }

    /**
     * 
     * @param string $js
     * @return CJavascript_Statement_Raw
     */
    public static function rawStatement($js) {
        return CJavascript_StatementFactory::createRaw($js);
    }

    /**
     * 
     * @param string $functionName
     * @param array $functionParameter
     * @return CJavascript_Statement_Function
     */
    public static function functionStatement($functionName, $functionParameter = array()) {
        return CJavascript_StatementFactory::createFunction($functionName, $functionParameter);
    }

}
