<?php

/**
 * Compares numerical values for equality.
 */
class CComparator_Engine_NumericComparator extends CComparator_Engine_ScalarComparator {
    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual) {
        // all numerical values, but not if one of them is a double
        // or both of them are strings
        return \is_numeric($expected) && \is_numeric($actual)
                && !(\is_float($expected) || \is_float($actual))
                && !(\is_string($expected) && \is_string($actual));
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
        if (\is_infinite($actual) && \is_infinite($expected)) {
            return;
        }
        if ((\is_infinite($actual) xor \is_infinite($expected))
            || (\is_nan($actual) || \is_nan($expected))
            || \abs($actual - $expected) > $delta
        ) {
            throw new CComparator_Exception_ComparisonFailureException(
                $expected,
                $actual,
                '',
                '',
                false,
                \sprintf(
                    'Failed asserting that %s matches expected %s.',
                    $this->exporter->export($actual),
                    $this->exporter->export($expected)
                )
            );
        }
    }
}
