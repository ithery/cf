<?php
class CReport_Generator_Expression_Lexer {
    const TOKEN_STRING = 'STRING';

    const TOKEN_NUMBER = 'NUMBER';

    const TOKEN_OPERATOR = 'OPERATOR';

    const TOKEN_WHITESPACE = 'WHITESPACE';

    const TOKEN_UNKNOWN = 'UNKNOWN';

    private $input;

    private $position;

    private $currentChar;

    public function __construct($input) {
        $this->input = $input;
        $this->position = 0;
        $this->currentChar = $this->input[$this->position] ?? null;
    }

    public function getInput() {
        return $this->input;
    }

    private function advance() {
        $this->position++;
        $this->currentChar = $this->input[$this->position] ?? null;
    }

    private function match($pattern) {
        return preg_match($pattern, $this->currentChar);
    }

    public function getNextToken() {
        while ($this->currentChar !== null) {
            if (ctype_space($this->currentChar)) {
                $this->skipWhitespace();

                continue;
            }

            if ($this->match('/\d/')) {
                return $this->number();
            }

            if ($this->currentChar === '"' || $this->currentChar === "'") {
                return $this->string();
            }

            if (($this->currentChar === '=' && $this->input[$this->position + 1] === '=')
                || ($this->currentChar === '!' && $this->input[$this->position + 1] === '=')
                || ($this->currentChar === '<' && $this->input[$this->position + 1] === '>')
            ) {
                $operator = $this->currentChar . $this->input[$this->position + 1];
                $this->advance();
                $this->advance();

                return ['type' => self::TOKEN_OPERATOR, 'value' => $operator];
            }

            if (in_array($this->currentChar, ['+', '-', '*', '/', '<', '>'])) {
                $char = $this->currentChar;
                $this->advance();

                return ['type' => self::TOKEN_OPERATOR, 'value' => $char];
            }

            return ['type' => self::TOKEN_UNKNOWN, 'value' => $this->currentChar];
        }

        return null;
    }

    private function skipWhitespace() {
        while ($this->currentChar !== null && ctype_space($this->currentChar)) {
            $this->advance();
        }
    }

    private function number() {
        $result = '';
        while ($this->currentChar !== null && $this->match('/\d/')) {
            $result .= $this->currentChar;
            $this->advance();
        }

        return ['type' => self::TOKEN_NUMBER, 'value' => $result];
    }

    private function string() {
        $quoteType = $this->currentChar;
        $result = '';
        $this->advance();
        while ($this->currentChar !== null && $this->currentChar !== $quoteType) {
            $result .= $this->currentChar;
            $this->advance();
        }
        $this->advance(); // Skip closing quote

        return ['type' => self::TOKEN_STRING, 'value' => $result];
    }
}
