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
        $result = $this->expression();
        if ($this->currentToken !== null && $this->currentToken['type'] === CReport_Generator_Expression_Lexer::TOKEN_OPERATOR) {
            $operator = $this->currentToken['value'];
            $this->eat(CReport_Generator_Expression_Lexer::TOKEN_OPERATOR);
            $right = $this->expression();

            return $this->evaluate($result, $operator, $right);
        }

        return $result;
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
        if ($this->currentToken['type'] === CReport_Generator_Expression_Lexer::TOKEN_PAREN && $this->currentToken['value'] === '(') {
            $this->eat(CReport_Generator_Expression_Lexer::TOKEN_PAREN);
            $result = $this->expression();
            $this->eat(CReport_Generator_Expression_Lexer::TOKEN_PAREN); // Eat closing ')'

            return $result;
        } else {
            return $this->primary();
        }
    }

    private function primary() {
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
            case '<=':
                return $left <= $right;
            case '>=':
                return $left >= $right;
            case '<>':
                return $left != $right;
            case '+':
                return is_numeric($left) && is_numeric($right) ? $left + $right : $left . $right;
            case '-':
                if (!is_numeric($left) && !is_numeric($right)) {
                    throw new Exception('left and right must numeric for operator -, got left:' . $left . ', right:.' . $right);
                }

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
