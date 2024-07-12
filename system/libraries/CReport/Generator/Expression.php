<?php

class CReport_Generator_Expression {
    protected $expression;

    public function __construct($expression) {
        $this->expression = $expression;
    }

    public function hasOperator() {
        // Regex untuk mencocokkan operator matematika atau perbandingan
        $regex = '/[+\-*\/<>!=]=?/';

        // Menggunakan preg_match untuk mencari operator dalam ekspresi
        if (preg_match($regex, $this->expression)) {
            return true;
        }

        return false;
    }

    public function evaluate() {
        if ($this->expression == '') {
            return '';
        }
        if (!$this->hasOperator()) {
            return $this->expression;
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
