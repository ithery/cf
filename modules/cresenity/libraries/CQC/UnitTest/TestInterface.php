<?php

/**
 * Description of TestInterface
 *
 * @author Hery
 */

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
interface CQC_UnitTest_TestInterface extends Countable {

    /**
     * Runs a test and collects its result in a TestResult instance.
     * @return CQC_UnitTest_TestResult
     */
    public function run(CQC_UnitTest_TestResult $result = null);
}
