<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 3, 2018, 2:05:07 AM
 */
class CJavascript_Statement_IfStatement extends CJavascript_Statement {
    protected $operand1 = '';

    protected $operand2 = '';

    protected $operator = '==';

    protected $bodyStatements = [];

    public function __construct($operand1, $operator, $operand2) {
        $this->operand1 = $operand1;
        $this->operator = $operator;
        $this->operand2 = $operand2;
    }

    public function addStatement($statement) {
        $this->bodyStatement[] = $statement;

        return $this;
    }

    public function getStatement() {
        if ($this->operand1 instanceof CJavascript_Statement) {
            $this->operand1 = trim($this->operand1->getStatement(), ';');
        }
        $str = 'if (' . $this->operand1 . ' ' . $this->operator . ' ' . $this->operand2 . ') {';
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
