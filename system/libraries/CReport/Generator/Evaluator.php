<?php

class CReport_Generator_Evaluator {
    protected $generator;

    public function __construct(CReport_Generator $generator) {
        $this->generator = $generator;
    }

    public function evaluatePrintWhenExpression(string $expression = null, string $evaluationTime = CREPORT::EVALUATION_TIME_REPORT) {
        $originalExpression = $expression;
        if ($expression != '') {
            $expression = $this->getExpression($expression, $evaluationTime);

            if (!is_bool($expression)) {
                throw new Exception('error on evaluate expression ' . $originalExpression);
            }

            return $expression;

            //echo    'if('.$printWhenExpression.'){$print_expression_result=true;}';
            //eval('if(' . $expression . '){$printExpressionResult=true;}');
        }

        return true;
    }

    public function getExpression($expression, string $evaluationTime = CReport::EVALUATION_TIME_NOW, CReport_Builder_Row $row = null) {
        $originalExpression = $expression;

        if (!$this->generator->isProcessingHook()) {
            $this->generator->setProcessingHook(true);
            preg_match_all("/H{(\w+)}/", $expression, $matchesH);
            if ($matchesH) {
                foreach ($matchesH[1] as $matchH) {
                    if (class_exists($matchH)) {
                        $hookClass = new $matchH($this->generator);
                        if ($hookClass instanceof CReport_Hook_HookInterface) {
                            $expression = $hookClass->getValue();
                        }
                    }
                }
            }
            $this->generator->setProcessingHook(false);
        }
        preg_match_all("/P{(\w+)}/", $expression, $matchesP);
        if ($matchesP) {
            foreach ($matchesP[1] as $matchP) {
                $expression = str_ireplace(['$P{' . $matchP . '}'], [$this->generator->getParameterValue($matchP)], $expression);
            }
        }

        preg_match_all("/V{(\w+)}/", $expression, $matchesV);
        $evaluateNow = true;
        if ($matchesV) {
            foreach ($matchesV[1] as $matchV) {
                if ($evaluationTime == CReport::EVALUATION_TIME_NOW && $matchV == 'PAGE_COUNT') {
                    $evaluateNow = false;

                    continue;
                }
                $variableValue = $this->generator->getVariableValue($matchV);
                if (is_string($variableValue)) {
                    $variableValue = '"' . $variableValue . '"';
                }
                // if ($originalExpression == '$V{groupNumber} == 1') {
                //     cdbg::dd($originalExpression, $expression, $variableValue);
                // }
                $expression = str_ireplace(['$V{' . $matchV . '}'], [$variableValue], $expression);
            }
        }
        if ($row == null) {
            $row = $this->generator->getCurrentRow();
        }

        if ($row != null) {
            // preg_match_all('/F{[^}]*}/', $expression, $matchesF);
            preg_match_all('/F{(.+?)}/', $expression, $matchesF);
            if ($matchesF) {
                foreach ($matchesF[1] as $matchF) {
                    $fieldValue = carr::get($row, $matchF);
                    if (is_string($fieldValue)) {
                        $fieldValue = '"' . $fieldValue . '"';
                    }

                    $expression = str_ireplace(['$F{' . $matchF . '}'], [$fieldValue], $expression);
                }
            }
        }
        $result = $expression;
        if ($evaluateNow) {
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
            $expressionEvaluator = new CReport_Generator_Expression($expression);
            $result = $expressionEvaluator->evaluate();
        }

        return $result;
    }

    public function evaluateExpression(string $expression, string $evaluationTime = CReport::EVALUATION_TIME_NOW) {
        $printExpressionResult = true;
        if ($expression != '') {
            $expression = $this->getExpression($expression, $evaluationTime);

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
