<?php

/**
 * Description of HasPrintableTestCaseNameInterface
 *
 * @author Hery
 */

/**
 * @internal
 */
interface CTesting_PhpUnit_HasPrintableTestCaseNameInterface {
    /**
     * Returns the test case name that should be used by the printer.
     */
    public function getPrintableTestCaseName(): string;
}
