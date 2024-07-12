<?php
class CReport_Generator_Expression_Parser {
    private $lexer;

    private $currentToken;

    public function __construct($lexer) {
        $this->lexer = $lexer;
        $this->currentToken = $this->lexer->getNextToken();
    }

    private function eat($tokenType) {
        if ($this->currentToken['type'] === $tokenType) {
            $this->currentToken = $this->lexer->getNextToken();
        } else {
            throw new Exception('Syntax error: Unexpected token ' . $this->currentToken['value']);
        }
    }

    public function parse() {
        $left = $this->expression();
        if ($this->currentToken !== null && $this->currentToken['type'] === CReport_Generator_Expression_Lexer::TOKEN_OPERATOR) {
            $operator = $this->currentToken['value'];
            $this->eat(CReport_Generator_Expression_Lexer::TOKEN_OPERATOR);
            $right = $this->expression();

            return $this->evaluate($left, $operator, $right);
        }

        return $left;
    }

    private function expression() {
        $result = $this->term();

        while ($this->currentToken !== null && in_array($this->currentToken['value'], ['+', '-'])) {
            $operator = $this->currentToken['value'];
            $this->eat(CReport_Generator_Expression_Lexer::TOKEN_OPERATOR);
            $right = $this->term();
            $result = $this->evaluate($result, $operator, $right);
        }

        return $result;
    }

    private function term() {
        $result = $this->factor();

        while ($this->currentToken !== null && in_array($this->currentToken['value'], ['*', '/'])) {
            $operator = $this->currentToken['value'];
            $this->eat(CReport_Generator_Expression_Lexer::TOKEN_OPERATOR);
            $right = $this->factor();
            $result = $this->evaluate($result, $operator, $right);
        }

        return $result;
    }

    private function factor() {
        $token = $this->currentToken;
        if ($token['type'] === CReport_Generator_Expression_Lexer::TOKEN_STRING) {
            $this->eat(CReport_Generator_Expression_Lexer::TOKEN_STRING);

            return $token['value'];
        } elseif ($token['type'] === CReport_Generator_Expression_Lexer::TOKEN_NUMBER) {
            $this->eat(CReport_Generator_Expression_Lexer::TOKEN_NUMBER);

            return intval($token['value']);
        } else {
            throw new CReport_Generator_Exception_ExpressionException('Syntax error: Unexpected token ' . $token['value']);
        }
    }

    private function evaluate($left, $operator, $right) {
        switch ($operator) {
            case '==':
                return $left == $right;
            case '!=':
                return $left != $right;
            case '<>':
                return $left != $right;
            case '+':
                return is_numeric($left) && is_numeric($right) ? $left + $right : $left . $right;
            case '-':
                return $left - $right;
            case '*':
                return $left * $right;
            case '/':
                return $left / $right;
            default:
                throw new Exception('Unsupported operator: ' . $operator);
        }
    }
}
