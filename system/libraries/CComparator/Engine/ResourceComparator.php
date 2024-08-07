<?php

/**
 * Compares resources for equality.
 */
class CComparator_Engine_ResourceComparator extends CComparator_AbstractEngine {
    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual) {
        return \is_resource($expected) && \is_resource($actual);
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
     * @throws CComparator_Exception_ComparisonFailureException
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false) {
        if ($actual != $expected) {
            throw new CComparator_Exception_ComparisonFailureException(
                $expected,
                $actual,
                $this->exporter->export($expected),
                $this->exporter->export($actual)
            );
        }
    }
}
