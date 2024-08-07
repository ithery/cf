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

use function is_dir;
use function substr;
use ReflectionClass;
use function is_file;
use ReflectionException;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Exception\Exception;
use SebastianBergmann\FileIterator\Facade as FileIteratorFacade;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
abstract class BaseTestRunner {
    /**
     * @var int
     */
    const STATUS_UNKNOWN = -1;

    /**
     * @var int
     */
    const STATUS_PASSED = 0;

    /**
     * @var int
     */
    const STATUS_SKIPPED = 1;

    /**
     * @var int
     */
    const STATUS_INCOMPLETE = 2;

    /**
     * @var int
     */
    const STATUS_FAILURE = 3;

    /**
     * @var int
     */
    const STATUS_ERROR = 4;

    /**
     * @var int
     */
    const STATUS_RISKY = 5;

    /**
     * @var int
     */
    const STATUS_WARNING = 6;

    /**
     * @var string
     */
    const SUITE_METHODNAME = 'suite';

    /**
     * Returns the loader to be used.
     */
    public function getLoader() {
        return new StandardTestSuiteLoader();
    }

    /**
     * Returns the Test corresponding to the given suite.
     * This is a template method, subclasses override
     * the runFailed() and clearStatus() methods.
     *
     * @param string|string[] $suffixes
     * @param mixed           $suiteClassFile
     *
     * @throws Exception
     */
    public function getTest($suiteClassFile, $suffixes = '') {
        if (is_dir($suiteClassFile)) {
            /** @var string[] $files */
            $files = (new FileIteratorFacade())->getFilesAsArray(
                $suiteClassFile,
                $suffixes
            );

            $suite = new TestSuite($suiteClassFile);
            $suite->addTestFiles($files);

            return $suite;
        }

        if (is_file($suiteClassFile) && substr($suiteClassFile, -5, 5) === '.phpt') {
            $suite = new TestSuite();
            $suite->addTestFile($suiteClassFile);

            return $suite;
        }

        try {
            $testClass = $this->loadSuiteClass(
                $suiteClassFile
            );
        } catch (\PHPUnit\Exception $e) {
            /** @var \Throwable $e */
            $this->runFailed($e->getMessage());

            return null;
        }

        try {
            $suiteMethod = $testClass->getMethod(self::SUITE_METHODNAME);

            if (!$suiteMethod->isStatic()) {
                $this->runFailed(
                    'suite() method must be static.'
                );

                return null;
            }

            $test = $suiteMethod->invoke(null, $testClass->getName());
        } catch (ReflectionException $e) {
            $test = new TestSuite($testClass);
        }

        $this->clearStatus();

        return $test;
    }

    /**
     * Returns the loaded ReflectionClass for a suite name.
     *
     * @param mixed $suiteClassFile
     */
    protected function loadSuiteClass($suiteClassFile) {
        return $this->getLoader()->load($suiteClassFile);
    }

    /**
     * Clears the status message.
     */
    protected function clearStatus() {
    }

    /**
     * Override to define how to handle a failed loading of
     * a test suite.
     *
     * @param mixed $message
     */
    abstract protected function runFailed($message);
}
