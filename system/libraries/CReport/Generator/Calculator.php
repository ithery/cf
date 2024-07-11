<?php

class CReport_Generator_Calculator {
    protected $generator;

    public function __construct(CReport_Generator $generator) {
        $this->generator = $generator;
    }

    public function calculateMathExpression($expression) {
        // Remove any unwanted characters (for security)
        $expression = preg_replace('/[^0-9\.\+\-\*\/\s]/', '', $expression);

        // Split the expression into tokens (numbers and operators)
        $tokens = preg_split('/\s*([\+\-\*\/])\s*/', $expression, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // Initialize the result with the first number
        $result = array_shift($tokens);

        // Ensure the initial result is a float
        $result = floatval($result);

        // Iterate through the tokens and perform the calculations
        while (!empty($tokens)) {
            $operator = array_shift($tokens);
            $number = array_shift($tokens);

            // Ensure the number is a float
            $number = floatval($number);

            // Perform the calculation based on the operator
            switch ($operator) {
                case '+':
                    $result += $number;

                    break;
                case '-':
                    $result -= $number;

                    break;
                case '*':
                    $result *= $number;

                    break;
                case '/':
                    if ($number == 0) {
                        throw new Exception('Division by zero');
                    }
                    $result /= $number;

                    break;
            }
        }

        return $result;
    }

    /**
     * @param string $calculation
     *
     * @return CReport_Generator_CalculationAbstract
     */
    public function createCalculationCalculator($calculation) {
        $calculationMap = [
            CReport::CALCULATION_NOTHING => CReport_Generator_Calculation_NothingCalculation::class,
            CReport::CALCULATION_SYSTEM => CReport_Generator_Calculation_SystemCalculation::class,
            CReport::CALCULATION_SUM => CReport_Generator_Calculation_SumCalculation::class,
        ];
        $className = carr::get($calculationMap, $calculation);
        if ($className == null) {
            throw new Exception('Calculation class for ' . $calculation . ' is not found');
        }

        return new $className($this->generator);
    }

    public function variablesCalculation() {
        $variables = $this->generator->getDictionary()->getVariables();
        foreach ($variables as $variable) {
            $this->variableCalculation($variable, $this->generator->getCurrentRow());
        }
    }

    public function variableCalculation(CReport_Builder_Dictionary_Variable $variable, CReport_Builder_Row $row) {
        $orginalExpression = $variable->getVariableExpression();
        $expression = $this->generator->getExpression($orginalExpression);
        $expression = $this->calculateMathExpression($expression);
        // $htmlData = array_key_exists('htmlData', $this->arrayVariable) ? $this->arrayVariable['htmlData']['class'] : '';
        // if (preg_match('/(\d+)(?:\s*)([\+\-\*\/])(?:\s*)/', $out['target'], $matchesMath) > 0 && $htmlData != 'HTMLDATA') {
        //     // error_reporting(0);
        //     $mathValue = $this->calculateMathExpression($out['target']);
        //     // $mathValue = eval('return (' . $out['target'] . ');');

        //     // error_reporting(5);
        // }

        $originalValue = $variable->getValue();
        $newValue = $expression;
        // $resetType = $variable->getResetType();
        $calculation = $variable->getCalculation();
        $value = $originalValue;
        $calculationCalculator = $this->createCalculationCalculator($calculation);
        $value = $calculationCalculator->calculate($value, $newValue);

        // if ($resetType == 'Page') {
        //     if ($this->pageChanged == 'true') {
        //         $value = $newValue;
        //     }
        // }
        // $this->arrayVariable[$k]['lastValue'] = $newValue;
        // if ($resetType == 'Group') {
        //     $group = $this->getGroupCollection()->find($out['resetGroup']);
        //     if ($group && $group->isResetVariable()) {
        //         $value = 0;
        //         $group->unsetResetVariable();
        //     }
        // }
        $variable->setValue($value);
    }
}
