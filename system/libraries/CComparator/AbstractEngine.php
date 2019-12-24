<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CComparator_AbstractEngine {

    /**
     * @var CComparator_Factory
     */
    protected $factory;

    /**
     * @var CComparator_Exporter
     */
    protected $exporter;

    public function __construct() {
        $this->exporter = CComparator::createExporter();
    }

    public function setFactory(CComparator_Factory $factory) {
        $this->factory = $factory;
    }

    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    abstract public function accepts($expected, $actual);

    /**
     * Asserts that two values are equal.
     *
     * @param mixed $expected     First value to compare
     * @param mixed $actual       Second value to compare
     * @param float $delta        Allowed numerical distance between two values to consider them equal
     * @param bool  $canonicalize Arrays are sorted before comparison when set to true
     * @param bool  $ignoreCase   Case is ignored when set to true
     *
     * @throws ComparisonFailure
     */
    abstract public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false);
}
