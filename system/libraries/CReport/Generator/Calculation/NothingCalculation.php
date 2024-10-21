<?php

class CReport_Generator_Calculation_NothingCalculation extends CReport_Generator_CalculationAbstract {
    public function calculate($value, $newValue) {
        return $newValue;
    }
}
