<?php

abstract class CReport_Generator_CalculationAbstract {
    protected $generator;

    public function __construct(CReport_Generator $generator) {
        $this->generator = $generator;
    }

    abstract public function calculate($value, $newValue);
}
