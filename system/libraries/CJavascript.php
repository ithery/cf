<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 11:38:45 PM
 */
class CJavascript {
    /**
     * @var CJavascript_Statement[]
     */
    protected static $statements = [];

    /**
     * @var array
     */
    protected static $deferredStatements = [];

    /**
     * @var int
     */
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

    /**
     * @return CJavascript_Statement[]
     */
    public static function getDeferredStatements() {
        if (!isset(self::$deferredStatements[self::$deferredStack])) {
            self::$deferredStatements[self::$deferredStack] = [];
        }

        return self::$deferredStatements[self::$deferredStack];
    }

    /**
     * @param CJavascript_Statement $statement
     *
     * @return void
     */
    public static function addStatement(CJavascript_Statement $statement) {
        if (self::$deferredStack >= 0) {
            return self::addDeferredStatement($statement);
        }
        self::$statements[$statement->hash()] = $statement;
    }

    public static function addDeferredStatement(CJavascript_Statement $statement) {
        if (!isset(self::$deferredStatements[self::$deferredStack])) {
            self::$deferredStatements[self::$deferredStack] = [];
        }
        self::$deferredStatements[self::$deferredStack][$statement->hash()] = $statement;
    }

    public static function removeStatement(CJavascript_Statement $statement) {
        if (isset(self::$statements[$statement->hash()])) {
            unset(self::$statements[$statement->hash()]);
        }
    }

    /**
     * @param CJavascript_Statement $statement
     * @param bool                  $allStack
     *
     * @return void
     */
    public static function removeDeferredStatement(CJavascript_Statement $statement, $allStack = true) {
        if (!isset(self::$deferredStatements[self::$deferredStack])) {
            self::$deferredStatements[self::$deferredStack] = [];
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

    /**
     * @return void
     */
    public static function clearStatement() {
        self::$statements = [];
    }

    /**
     * @return void
     */
    public static function clearDeferredStatement() {
        if (!isset(self::$deferredStatements[self::$deferredStack])) {
            self::$deferredStatements[self::$deferredStack] = [];
        }
        self::$deferredStatements[self::$deferredStack] = [];
    }

    public static function popDeferredStack() {
        $statements = self::getDeferredStatements();
        unset(self::$deferredStatements[self::$deferredStack--]);

        return $statements;
    }

    public static function pushDeferredStack() {
        self::$deferredStatements[++self::$deferredStack] = [];

        return self::getDeferredStatements();
    }

    public static function clearDeferredStack() {
        self::$deferredStatements = [];
    }

    public static function getDeferredStack() {
        return self::$deferredStatements;
    }

    /**
     * @param string $selector
     *
     * @return CJavascript_Statement_JQuery
     */
    public static function jqueryStatement($selector = 'this') {
        return CJavascript_StatementFactory::createJQuery($selector);
    }

    /**
     * @param string $varName
     * @param string $varValue
     *
     * @return CJavascript_Statement_Variable
     */
    public static function variableStatement($varName, $varValue = null) {
        return CJavascript_StatementFactory::createVariable($varName, $varValue);
    }

    /**
     * @param string $js
     *
     * @return CJavascript_Statement_Raw
     */
    public static function rawStatement($js) {
        return CJavascript_StatementFactory::createRaw($js);
    }

    /**
     * @param string $functionName
     * @param array  $functionParameter
     *
     * @return CJavascript_Statement_Function
     */
    public static function functionStatement($functionName, $functionParameter = []) {
        return CJavascript_StatementFactory::createFunction($functionName, $functionParameter);
    }

    /**
     * @param mixed $operand1
     * @param mixed $operator
     * @param mixed $operand2
     *
     * @return CJavascript_Statement_IfStatement
     */
    public static function ifStatement($operand1, $operator, $operand2) {
        return CJavascript_StatementFactory::createIf($operand1, $operator, $operand2);
    }

    public static function cresJs() {
        return CJavascript_CresJs::instance();
    }
}
