<?php

/**
 * Defines how an output builder should take a generated
 * diff array and return a string representation of that diff.
 */
interface CComparator_Differ_OutputInterface {
    public function getDiff(array $diff);
}
