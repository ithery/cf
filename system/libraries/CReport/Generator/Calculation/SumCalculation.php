<?php

class CReport_Generator_Calculation_SumCalculation extends CReport_Generator_CalculationAbstract {
    public function calculate($value, $newValue) {
        return $value + $newValue;
    }
}
