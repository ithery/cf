<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Compares scalar or NULL values for equality.
 */
class CComparator_Engine_ScalarComparator extends CComparator_AbstractEngine
{
    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     *
     * @since  Method available since Release 3.6.0
     */
    public function accepts($expected, $actual)
    {
        return ((\is_scalar($expected) xor null === $expected) &&
               (\is_scalar($actual) xor null === $actual))
               // allow comparison between strings and objects featuring __toString()
               || (\is_string($expected) && \is_object($actual) && \method_exists($actual, '__toString'))
               || (\is_object($expected) && \method_exists($expected, '__toString') && \is_string($actual));
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
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        $expectedToCompare = $expected;
        $actualToCompare   = $actual;
        // always compare as strings to avoid strange behaviour
        // otherwise 0 == 'Foobar'
        if (\is_string($expected) || \is_string($actual)) {
            $expectedToCompare = (string) $expectedToCompare;
            $actualToCompare   = (string) $actualToCompare;
            if ($ignoreCase) {
                $expectedToCompare = \strtolower($expectedToCompare);
                $actualToCompare   = \strtolower($actualToCompare);
            }
        }
        if ($expectedToCompare !== $actualToCompare && \is_string($expected) && \is_string($actual)) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                $this->exporter->export($expected),
                $this->exporter->export($actual),
                false,
                'Failed asserting that two strings are equal.'
            );
        }
        if ($expectedToCompare != $actualToCompare) {
            throw new CComparator_Exception_ComparisonFailureException(
                $expected,
                $actual,
                // no diff is required
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