<?php

class CReport_Generator_Expression {
    protected $expression;

    public function __construct($expression) {
        $this->expression = $expression;
    }

    public function evaluate() {
        if ($this->expression == '') {
            return '';
        }
        $lexer = new CReport_Generator_Expression_Lexer($this->expression);
        $parser = new CReport_Generator_Expression_Parser($lexer);

        try {
            $result = $parser->parse();
        } catch (CReport_Generator_Exception_ExpressionException $e) {
            $result = $this->expression;
        }

        return $result;
    }
}
