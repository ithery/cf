<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework;

use Throwable;
use PHPUnit\Framework\Exception\Warning;
use PHPUnit\Framework\Exception\AssertionFailedError;

/**
 * @deprecated Use the `TestHook` interfaces instead
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
interface TestListener {
    /**
     * An error occurred.
     *
     * @deprecated Use `AfterTestErrorHook::executeAfterTestError` instead
     *
     * @param mixed $time
     */
    public function addError(Test $test, \Exception $t, $time);

    /**
     * A warning occurred.
     *
     * @deprecated Use `AfterTestWarningHook::executeAfterTestWarning` instead
     *
     * @param mixed $time
     */
    public function addWarning(Test $test, Warning $e, $time);

    /**
     * A failure occurred.
     *
     * @deprecated Use `AfterTestFailureHook::executeAfterTestFailure` instead
     *
     * @param mixed $time
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time);

    /**
     * Incomplete test.
     *
     * @deprecated Use `AfterIncompleteTestHook::executeAfterIncompleteTest` instead
     *
     * @param mixed $time
     */
    public function addIncompleteTest(Test $test, \Exception $t, $time);

    /**
     * Risky test.
     *
     * @deprecated Use `AfterRiskyTestHook::executeAfterRiskyTest` instead
     *
     * @param mixed $time
     */
    public function addRiskyTest(Test $test, \Exception $t, $time);

    /**
     * Skipped test.
     *
     * @deprecated Use `AfterSkippedTestHook::executeAfterSkippedTest` instead
     *
     * @param mixed $time
     */
    public function addSkippedTest(Test $test, \Exception $t, $time);

    /**
     * A test suite started.
     */
    public function startTestSuite(TestSuite $suite);

    /**
     * A test suite ended.
     */
    public function endTestSuite(TestSuite $suite);

    /**
     * A test started.
     *
     * @deprecated Use `BeforeTestHook::executeBeforeTest` instead
     */
    public function startTest(Test $test);

    /**
     * A test ended.
     *
     * @deprecated Use `AfterTestHook::executeAfterTest` instead
     *
     * @param mixed $time
     */
    public function endTest(Test $test, $time);
}
