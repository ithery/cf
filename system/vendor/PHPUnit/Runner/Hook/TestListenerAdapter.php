<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Hook;

use PHPUnit\Framework\Exception\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Exception\Warning;
use PHPUnit\Util\Test as TestUtil;
use Throwable;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestListenerAdapter implements TestListener
{
    /**
     * @var TestHook[]
     */
    private $hooks = [];

    /**
     * @var bool
     */
    private $lastTestWasNotSuccessful;

    public function add(TestHook $hook)
    {
        $this->hooks[] = $hook;
    }

    public function startTest(Test $test)
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof BeforeTestHook) {
                $hook->executeBeforeTest(TestUtil::describeAsString($test));
            }
        }

        $this->lastTestWasNotSuccessful = false;
    }

    public function addError(Test $test, \Exception $t, $time)
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof AfterTestErrorHook) {
                $hook->executeAfterTestError(TestUtil::describeAsString($test), $t->getMessage(), $time);
            }
        }

        $this->lastTestWasNotSuccessful = true;
    }

    public function addWarning(Test $test, Warning $e, $time)
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof AfterTestWarningHook) {
                $hook->executeAfterTestWarning(TestUtil::describeAsString($test), $e->getMessage(), $time);
            }
        }

        $this->lastTestWasNotSuccessful = true;
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof AfterTestFailureHook) {
                $hook->executeAfterTestFailure(TestUtil::describeAsString($test), $e->getMessage(), $time);
            }
        }

        $this->lastTestWasNotSuccessful = true;
    }

    public function addIncompleteTest(Test $test, \Exception $t, $time)
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof AfterIncompleteTestHook) {
                $hook->executeAfterIncompleteTest(TestUtil::describeAsString($test), $t->getMessage(), $time);
            }
        }

        $this->lastTestWasNotSuccessful = true;
    }

    public function addRiskyTest(Test $test, \Exception $t, $time)
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof AfterRiskyTestHook) {
                $hook->executeAfterRiskyTest(TestUtil::describeAsString($test), $t->getMessage(), $time);
            }
        }

        $this->lastTestWasNotSuccessful = true;
    }

    public function addSkippedTest(Test $test, \Exception $t, $time)
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof AfterSkippedTestHook) {
                $hook->executeAfterSkippedTest(TestUtil::describeAsString($test), $t->getMessage(), $time);
            }
        }

        $this->lastTestWasNotSuccessful = true;
    }

    public function endTest(Test $test, $time)
    {
        if (!$this->lastTestWasNotSuccessful) {
            foreach ($this->hooks as $hook) {
                if ($hook instanceof AfterSuccessfulTestHook) {
                    $hook->executeAfterSuccessfulTest(TestUtil::describeAsString($test), $time);
                }
            }
        }

        foreach ($this->hooks as $hook) {
            if ($hook instanceof AfterTestHook) {
                $hook->executeAfterTest(TestUtil::describeAsString($test), $time);
            }
        }
    }

    public function startTestSuite(TestSuite $suite)
    {
    }

    public function endTestSuite(TestSuite $suite)
    {
    }
}
