<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Compares values for type equality.
 */
class CComparator_Engine_TypeComparator extends CComparator_AbstractEngine {

    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual) {
        return true;
    }

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
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false) {
        if (\gettype($expected) != \gettype($actual)) {
            throw new CComparator_Exception_ComparisonFailureException(
            $expected, $actual,
            // we don't need a diff
            '', '', false, \sprintf(
                    '%s does not match expected type "%s".', $this->exporter->shortenedExport($actual), \gettype($expected)
            )
            );
        }
    }

}
