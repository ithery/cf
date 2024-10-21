<?php

class CReport_Generator_Expression {
    protected $expression;

    public function __construct($expression) {
        $this->expression = $expression;
    }

    private function removeQuotes($input) {
        $output = '';
        $quoteType = ''; // Menyimpan tipe quote saat ini (', " atau kosong jika di luar quote)
        $escaped = false; // Menyimpan status karakter escape

        $length = strlen($input);
        for ($i = 0; $i < $length; $i++) {
            $char = $input[$i];
            if ($quoteType === '') {
                if ($char === '"' || $char === "'") {
                    $quoteType = $char;
                } else {
                    $output .= $char;
                }
            } else {
                if ($char === '\\' && !$escaped) {
                    $escaped = true;
                } elseif ($char === $quoteType && !$escaped) {
                    $quoteType = '';
                } else {
                    $output .= $char;
                    $escaped = false;
                }
            }
        }

        return $output;
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
        $expression = $this->expression;
        if ($expression == '') {
            return '';
        }
        $expression = str_replace('\\n', PHP_EOL, $expression);
        if (!$this->hasOperator()) {
            if (is_string($expression)) {
                $expression = $this->removeQuotes($expression);
            }

            return $expression;
        }
        // CF::log('info', 'evaluate expression ' . $expression);
        $parser = new CReport_Generator_Expression_Parser($expression);

        try {
            $result = $parser->parse();
        } catch (CReport_Generator_Exception_ExpressionException $e) {
            $result = $expression;
        }
        // CF::log('info', 'result evaluate expression ' . $expression . ' : ' . $result);

        return $result;
    }
}
