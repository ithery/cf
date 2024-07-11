<?php

class CReport_Generator_Calculator {
    protected $generator;

    public function __construct(CReport_Generator $generator) {
        $this->generator = $generator;
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
        $varName = $variable->getName();
        $expression = $variable->getVariableExpression();
        $expression = $this->generator->getExpression($expression);

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
