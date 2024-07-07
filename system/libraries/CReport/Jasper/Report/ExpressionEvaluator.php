<?php

class CReport_Jasper_Report_ExpressionEvaluator {
    protected $parameters;

    protected $variables;

    public function __construct(CReport_Jasper_Report_ParameterCollection $parameters, CReport_Jasper_Report_VariableCollection $variables) {
        $this->parameters = $parameters;
        $this->variables = $variables;
    }

    public function evaluateExpression(string $text, CReport_Jasper_Report_DataRow $row = null) {
        preg_match_all("/P{(\w+)}/", $text, $matchesP);
        if ($matchesP) {
            foreach ($matchesP[1] as $macthP) {
                $text = str_ireplace(['$P{' . $macthP . '}', '"'], [$this->parameters->getValue($macthP), ''], $text);
            }
        }

        $originalText = $text;
        preg_match_all("/V{(\w+)}/", $text, $matchesV);
        if ($matchesV) {
            foreach ($matchesV[1] as $matchV) {
                $text = $this->getValOfVariable($matchV, $text, $writeHTML, $element);
            }
        }

        if ($row) {
            preg_match_all('/F{[^}]*}/', $text, $matchesF);
            if ($matchesF) {
                //var_dump($matchesF);
                foreach ($matchesF[0] as $matchF) {
                    $match = str_ireplace(['F{', '}'], '', $matchF);
                    $text = $this->getValOfField($match, $row, $text, $writeHTML);
                }
            }
        }

        // Regex pattern to capture the entire expression
        $ternaryPattern = '/(.*?)\?(.*?)\:(.*)/';

        if (preg_match($ternaryPattern, $text, $matchesTernary) > 0) {
            // Extract the components

            $condition = trim($matchesTernary[1]);
            $valueIfTrue = trim($matchesTernary[2]);
            $valueIfFalse = trim($matchesTernary[3]);
            $text = $this->evaluateCondition($condition) ? $valueIfTrue : $valueIfFalse;
            // $mathValue = eval('return (' . $out['target'] . ');');

             // error_reporting(5);
        }

        return $text;
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
