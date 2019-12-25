<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Compares DateTimeInterface instances for equality.
 */
class CComparator_Engine_DateTimeComparator extends CComparator_Engine_ObjectComparator {

    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual) {
        return ($expected instanceof \DateTime || $expected instanceof \DateTimeInterface) &&
                ($actual instanceof \DateTime || $actual instanceof \DateTimeInterface);
    }

    /**
     * Asserts that two values are equal.
     *
     * @param mixed $expected     First value to compare
     * @param mixed $actual       Second value to compare
     * @param float $delta        Allowed numerical distance between two values to consider them equal
     * @param bool  $canonicalize Arrays are sorted before comparison when set to true
     * @param bool  $ignoreCase   Case is ignored when set to true
     * @param array $processed    List of already processed elements (used to prevent infinite recursion)
     *
     * @throws \Exception
     * @throws ComparisonFailure
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false, array &$processed = []) {
        /** @var \DateTimeInterface $expected */
        /** @var \DateTimeInterface $actual */
        $absDelta = \abs($delta);
        $delta = new \DateInterval(\sprintf('PT%dS', $absDelta));
        $delta->f = $absDelta - \floor($absDelta);

        $actualClone = (clone $actual);

        $actualClone->setTimezone(new \DateTimeZone('UTC'));
        $expectedLower = (clone $expected);

        $expectedLower->setTimezone(new \DateTimeZone('UTC'))
                ->sub($delta);
        $expectedUpper = (clone $expected);

        $expectedUpper->setTimezone(new \DateTimeZone('UTC'))
                ->add($delta);
        if ($actualClone < $expectedLower || $actualClone > $expectedUpper) {
            throw new CComparator_Exception_ComparisonFailureException(
            $expected, $actual, $this->dateTimeToString($expected), $this->dateTimeToString($actual), false, 'Failed asserting that two DateTime objects are equal.'
            );
        }
    }

    /**
     * Returns an ISO 8601 formatted string representation of a datetime or
     * 'Invalid DateTimeInterface object' if the provided DateTimeInterface was not properly
     * initialized.
     */
    private function dateTimeToString(\DateTimeInterface $datetime) {
        $string = $datetime->format('Y-m-d\TH:i:s.uO');
        return $string ?: 'Invalid DateTimeInterface object';
    }

}
