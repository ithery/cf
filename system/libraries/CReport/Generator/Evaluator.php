<?php

class CReport_Generator_Evaluator {
    protected $generator;

    public function __construct(CReport_Generator $generator) {
        $this->generator = $generator;
    }

    public function getExpression($expression) {
        preg_match_all("/P{(\w+)}/", $expression, $matchesP);
        if ($matchesP) {
            foreach ($matchesP[1] as $matchP) {
                $expression = str_ireplace(['$P{' . $matchP . '}', '"'], [$this->generator->getDictionary()->getParameterValue($matchP), ''], $expression);
            }
        }

        preg_match_all("/V{(\w+)}/", $expression, $matchesV);
        if ($matchesV) {
            foreach ($matchesV[1] as $matchV) {
                $expression = str_ireplace(['$V{' . $matchV . '}', '"'], [$this->generator->getDictionary()->getVariableValue($matchV), ''], $expression);
            }
        }

        if ($this->generator->getCurrentRow() != null) {
            // preg_match_all('/F{[^}]*}/', $expression, $matchesF);
            preg_match_all("/F{(\w+)}/", $expression, $matchesF);
            if ($matchesF) {
                //var_dump($matchesF);
                foreach ($matchesF[1] as $matchF) {
                    $expression = str_ireplace(['$F{' . $matchF . '}', '"'], [$this->generator->getFieldValue($matchF), ''], $expression);
                }
            }
        }

        // Regex pattern to capture the entire expression
        $ternaryPattern = '/(.*?)\?(.*?)\:(.*)/';

        if (preg_match($ternaryPattern, $expression, $matchesTernary) > 0) {
            // Extract the components

            $condition = trim($matchesTernary[1]);
            $valueIfTrue = trim($matchesTernary[2]);
            $valueIfFalse = trim($matchesTernary[3]);
            $expression = $this->evaluateCondition($condition) ? $valueIfTrue : $valueIfFalse;
            // $mathValue = eval('return (' . $out['target'] . ');');

             // error_reporting(5);
        }

        return $expression;
    }

    public function evaluateExpression(string $expression, CReport_Jasper_Report_DataRow $row = null) {
        $printExpressionResult = true;
        if ($expression != '') {
            $expression = $this->getExpression($expression, $row);

            //echo    'if('.$printWhenExpression.'){$print_expression_result=true;}';
            eval('if(' . $expression . '){$printExpressionResult=true;}');
        }

        return $printExpressionResult;
    }

    public function evaluateCondition($condition) {
        // Handle basic condition evaluation
        // Handle basic condition evaluation
        if (preg_match('/(.*?)==(.*?)$/', $condition, $condMatches)) {
            $leftOperand = trim(trim($condMatches[1], "\n\r\t\b\0\"' "));
            $rightOperand = trim(trim($condMatches[2], "\n\r\t\b\0\"' "));

            return $leftOperand == $rightOperand;
        }
        // Add more condition checks as needed (e.g., !=, <, >, etc.)

        return false;
    }
}
