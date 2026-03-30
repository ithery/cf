<?php

defined('SYSPATH') or die('No direct access allowed.');

class CJavascript_Statement_Function extends CJavascript_Statement {
    protected $bodyStatements = [];

    protected $functionName = '';

    protected $parameters = [];

    public function __construct($functionName = '', $functionParameters = []) {
        $this->functionName = $functionName;
        $this->parameters = $functionParameters;
    }

    public function addStatement($statement) {
        $this->bodyStatements[] = $statement;
    }

    public function getStatement() {
        $implodedParameters = implode(',', $this->parameters);
        $str = 'function ' . $this->functionName . '(' . $implodedParameters . ') {';
        foreach ($this->bodyStatements as $statement) {
            if ($statement instanceof CJavascript_Statement) {
                $statement = $statement->getStatement();
            }
            $str .= $statement;
        }
        $str .= '}';

        return $str;
    }
}
