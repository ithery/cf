<?php

interface CComparator_Differ_LongestCommonSubsequenceCalculatorInterface {
    /**
     * Calculates the longest common subsequence of two arrays.
     */
    public function calculate(array $from, array $to);
}
