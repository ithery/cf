<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 9:26:43 PM
 */
class CJavascript_Statement_Function extends CJavascript_Statement {
    protected $bodyStatements = [];

    protected $functionName = '';

    protected $parameters = [];

    public function __construct($functionName = '', $functionParameters = []) {
        $this->functionName = $functionName;
        $this->parameters = $functionParameters;
    }

    public function addStatement($statement) {
        $this->bodyStatement[] = $statement;
    }

    public function getStatement() {
        $implodedParameters = implode(',', $this->parameters);
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
