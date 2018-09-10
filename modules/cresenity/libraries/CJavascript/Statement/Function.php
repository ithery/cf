<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 9:26:43 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_Statement_Function extends CJavascript_Statement {

    protected $bodyStatements = array();
    protected $functionName = '';
    protected $parameters = array();

    public function __construct($functionName = '', $functionParameters = array()) {
        $this->functionName = $functionName;
        $this->parameters = $functionParameters;
    }

    public function addStatement($statement) {
        $this->bodyStatement[] = $statement;
    }

    public function getStatement() {
        $implodedParameters = implode(",", $this->parameters);
        $str = 'function ' . $this->functionName . '(' . $implodedParameters . ') {';
        foreach ($this->bodyStatement as $statement) {
            if ($statement instanceof CJavascript_Statement) {
                $statement = $statement->getStatement();
            }
            $str .= $statement;
        }
        $str .= '}';
        return $str;
    }

}
