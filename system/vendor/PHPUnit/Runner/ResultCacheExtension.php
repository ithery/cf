<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner;

use function preg_match;
use function round;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ResultCacheExtension implements AfterIncompleteTestHook, AfterLastTestHook, AfterRiskyTestHook, AfterSkippedTestHook, AfterSuccessfulTestHook, AfterTestErrorHook, AfterTestFailureHook, AfterTestWarningHook
{
    /**
     * @var TestResultCache
     */
    private $cache;

    public function __construct(TestResultCache $cache)
    {
        $this->cache = $cache;
    }

    public function flush()
    {
        $this->cache->persist();
    }

    public function executeAfterSuccessfulTest($test, $time)
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, round($time, 3));
    }

    public function executeAfterIncompleteTest($test, $message, $time)
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_INCOMPLETE);
    }

    public function executeAfterRiskyTest($test, $message, $time)
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_RISKY);
    }

    public function executeAfterSkippedTest($test, $message, $time)
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_SKIPPED);
    }

    public function executeAfterTestError($test, $message, $time)
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_ERROR);
    }

    public function executeAfterTestFailure($test, $message, $time)
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_FAILURE);
    }

    public function executeAfterTestWarning($test, $message, $time)
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_WARNING);
    }

    public function executeAfterLastTest()
    {
        $this->flush();
    }

    /**
     * @param string $test A long description format of the current test
     *
     * @return string The test name without TestSuiteClassName:: and @dataprovider details
     */
    private function getTestName($test)
    {
        $matches = [];

        if (preg_match('/^(?<name>\S+::\S+)(?:(?<dataname> with data set (?:#\d+|"[^"]+"))\s\()?/', $test, $matches)) {
            $test = $matches['name'] . (isset($matches['dataname']) ? $matches['dataname'] : '');
        }

        return $test;
    }
}
