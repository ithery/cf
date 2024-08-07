<?php

/**
 * Compares \SplObjectStorage instances for equality.
 */
class CComparator_Engine_SplObjectStorageComparator extends CComparator_AbstractEngine {
    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual) {
        return $expected instanceof \SplObjectStorage && $actual instanceof \SplObjectStorage;
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
        foreach ($actual as $object) {
            if (!$expected->contains($object)) {
                throw new CComparator_Exception_ComparisonFailureException(
                    $expected,
                    $actual,
                    $this->exporter->export($expected),
                    $this->exporter->export($actual),
                    false,
                    'Failed asserting that two objects are equal.'
                );
            }
        }
        foreach ($expected as $object) {
            if (!$actual->contains($object)) {
                throw new CComparator_Exception_ComparisonFailureException(
                    $expected,
                    $actual,
                    $this->exporter->export($expected),
                    $this->exporter->export($actual),
                    false,
                    'Failed asserting that two objects are equal.'
                );
            }
        }
    }
}
