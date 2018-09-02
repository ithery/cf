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
    protected static $deferredStack = -1;

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

        if (!isset(self::$deferredStatements[self::$deferredStack])) {
            self::$deferredStatements[self::$deferredStack] = array();
        }

        return self::$deferredStatements[self::$deferredStack];
    }

    public static function addStatement(CJavascript_Statement $statement) {
        if(self::$deferredStack>=0) {
            return self::addDeferredStatement($statement);
        }
        self::$statements[$statement->hash()] = $statement;
    }

    public static function addDeferredStatement(CJavascript_Statement $statement) {
        if (!isset(self::$deferredStatements[self::$deferredStack])) {
            self::$deferredStatements[self::$deferredStack] = array();
        }
        self::$deferredStatements[self::$deferredStack][$statement->hash()] = $statement;
    }

    public static function removeStatement(CJavascript_Statement $statement) {
        if (isset(self::$statements[$statement->hash()])) {
            unset(self::$statements[$statement->hash()]);
        }
    }

    public static function removeDeferredStatement(CJavascript_Statement $statement, $allStack = true) {
        if (!isset(self::$deferredStatements[self::$deferredStack])) {
            self::$deferredStatements[self::$deferredStack] = array();
        }
        if ($allStack) {
            for ($i = 0; $i <= self::$deferredStack; $i++) {
                if (isset(self::$deferredStatements[$i][$statement->hash()])) {
                    unset(self::$deferredStatements[$i][$statement->hash()]);
                }
            }
        } else {
            if (isset(self::$deferredStatements[self::$deferredStack][$statement->hash()])) {
                unset(self::$deferredStatements[self::$deferredStack][$statement->hash()]);
            }
        }
    }

    public static function clearStatement() {
        self::$statements = array();
    }

    public static function clearDeferredStatement() {
        if (!isset(self::$deferredStatements[self::$deferredStack])) {
            self::$deferredStatements[self::$deferredStack] = array();
        }
        self::$deferredStatements[self::$deferredStack] = array();
    }

    public static function popDeferredStack() {
        $statements = self::getDeferredStatements();
        unset(self::$deferredStatements[self::$deferredStack--]);
        return $statements;
    }

    public static function pushDeferredStack() {
        self::$deferredStatements[++self::$deferredStack] = array();
        return self::getDeferredStatements();
    }

    public static function clearDeferredStack() {
        self::$deferredStatements = [];
    }

    public static function getDeferredStack() {
        return self::$deferredStatements;
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
