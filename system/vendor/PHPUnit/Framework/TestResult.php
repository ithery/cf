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

use const PHP_EOL;
use function count;
use function function_exists;
use function get_class;
use function sprintf;
use AssertionError;
use Countable;
use Error;
use PHPUnit\Util\ErrorHandler;
use PHPUnit\Util\ExcludeList;
use PHPUnit\Util\Printer;
use PHPUnit\Util\Test as TestUtil;
use ReflectionClass;
use ReflectionException;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Exception as OriginalCodeCoverageException;
use SebastianBergmann\CodeCoverage\UnintentionallyCoveredCodeException;
use SebastianBergmann\Invoker\Invoker;
use SebastianBergmann\Invoker\TimeoutException;
use SebastianBergmann\ResourceOperations\ResourceOperations;
use SebastianBergmann\Timer\Timer;
use Throwable;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestResult implements Countable
{
    /**
     * @var array
     */
    private $passed = [];

    /**
     * @var array<string>
     */
    private $passedTestClasses = [];

    /**
     * @var bool
     */
    private $currentTestSuiteFailed = false;

    /**
     * @var TestFailure[]
     */
    private $errors = [];

    /**
     * @var TestFailure[]
     */
    private $failures = [];

    /**
     * @var TestFailure[]
     */
    private $warnings = [];

    /**
     * @var TestFailure[]
     */
    private $notImplemented = [];

    /**
     * @var TestFailure[]
     */
    private $risky = [];

    /**
     * @var TestFailure[]
     */
    private $skipped = [];

    /**
     * @deprecated Use the `TestHook` interfaces instead
     *
     * @var TestListener[]
     */
    private $listeners = [];

    /**
     * @var int
     */
    private $runTests = 0;

    /**
     * @var float
     */
    private $time = 0;

    /**
     * @var TestSuite
     */
    private $topTestSuite;

    /**
     * Code Coverage information.
     *
     * @var CodeCoverage
     */
    private $codeCoverage;

    /**
     * @var bool
     */
    private $convertDeprecationsToExceptions = true;

    /**
     * @var bool
     */
    private $convertErrorsToExceptions = true;

    /**
     * @var bool
     */
    private $convertNoticesToExceptions = true;

    /**
     * @var bool
     */
    private $convertWarningsToExceptions = true;

    /**
     * @var bool
     */
    private $stop = false;

    /**
     * @var bool
     */
    private $stopOnError = false;

    /**
     * @var bool
     */
    private $stopOnFailure = false;

    /**
     * @var bool
     */
    private $stopOnWarning = false;

    /**
     * @var bool
     */
    private $beStrictAboutTestsThatDoNotTestAnything = true;

    /**
     * @var bool
     */
    private $beStrictAboutOutputDuringTests = false;

    /**
     * @var bool
     */
    private $beStrictAboutTodoAnnotatedTests = false;

    /**
     * @var bool
     */
    private $beStrictAboutResourceUsageDuringSmallTests = false;

    /**
     * @var bool
     */
    private $enforceTimeLimit = false;

    /**
     * @var bool
     */
    private $forceCoversAnnotation = false;

    /**
     * @var int
     */
    private $timeoutForSmallTests = 1;

    /**
     * @var int
     */
    private $timeoutForMediumTests = 10;

    /**
     * @var int
     */
    private $timeoutForLargeTests = 60;

    /**
     * @var bool
     */
    private $stopOnRisky = false;

    /**
     * @var bool
     */
    private $stopOnIncomplete = false;

    /**
     * @var bool
     */
    private $stopOnSkipped = false;

    /**
     * @var bool
     */
    private $lastTestFailed = false;

    /**
     * @var int
     */
    private $defaultTimeLimit = 0;

    /**
     * @var bool
     */
    private $stopOnDefect = false;

    /**
     * @var bool
     */
    private $registerMockObjectsFromTestArgumentsRecursively = false;

    /**
     * @deprecated Use the `TestHook` interfaces instead
     *
     * @codeCoverageIgnore
     *
     * Registers a TestListener.
     */
    public function addListener(TestListener $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * @deprecated Use the `TestHook` interfaces instead
     *
     * @codeCoverageIgnore
     *
     * Unregisters a TestListener.
     */
    public function removeListener(TestListener $listener)
    {
        foreach ($this->listeners as $key => $_listener) {
            if ($listener === $_listener) {
                unset($this->listeners[$key]);
            }
        }
    }

    /**
     * @deprecated Use the `TestHook` interfaces instead
     *
     * @codeCoverageIgnore
     *
     * Flushes all flushable TestListeners.
     */
    public function flushListeners()
    {
        foreach ($this->listeners as $listener) {
            if ($listener instanceof Printer) {
                $listener->flush();
            }
        }
    }

    /**
     * Adds an error to the list of errors.
     */
    public function addError(Test $test, Throwable $t, $time)
    {
        if ($t instanceof RiskyTestError) {
            $this->risky[] = new TestFailure($test, $t);
            $notifyMethod  = 'addRiskyTest';

            if ($test instanceof TestCase) {
                $test->markAsRisky();
            }

            if ($this->stopOnRisky || $this->stopOnDefect) {
                $this->stop();
            }
        } elseif ($t instanceof IncompleteTest) {
            $this->notImplemented[] = new TestFailure($test, $t);
            $notifyMethod           = 'addIncompleteTest';

            if ($this->stopOnIncomplete) {
                $this->stop();
            }
        } elseif ($t instanceof SkippedTest) {
            $this->skipped[] = new TestFailure($test, $t);
            $notifyMethod    = 'addSkippedTest';

            if ($this->stopOnSkipped) {
                $this->stop();
            }
        } else {
            $this->errors[] = new TestFailure($test, $t);
            $notifyMethod   = 'addError';

            if ($this->stopOnError || $this->stopOnFailure) {
                $this->stop();
            }
        }

        // @see https://github.com/sebastianbergmann/phpunit/issues/1953
        if ($t instanceof Error) {
            $t = new ExceptionWrapper($t);
        }

        foreach ($this->listeners as $listener) {
            $listener->{$notifyMethod}($test, $t, $time);
        }

        $this->lastTestFailed = true;
        $this->time += $time;
    }

    /**
     * Adds a warning to the list of warnings.
     * The passed in exception caused the warning.
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
        if ($this->stopOnWarning || $this->stopOnDefect) {
            $this->stop();
        }

        $this->warnings[] = new TestFailure($test, $e);

        foreach ($this->listeners as $listener) {
            $listener->addWarning($test, $e, $time);
        }

        $this->time += $time;
    }

    /**
     * Adds a failure to the list of failures.
     * The passed in exception caused the failure.
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        if ($e instanceof RiskyTestError || $e instanceof OutputError) {
            $this->risky[] = new TestFailure($test, $e);
            $notifyMethod  = 'addRiskyTest';

            if ($test instanceof TestCase) {
                $test->markAsRisky();
            }

            if ($this->stopOnRisky || $this->stopOnDefect) {
                $this->stop();
            }
        } elseif ($e instanceof IncompleteTest) {
            $this->notImplemented[] = new TestFailure($test, $e);
            $notifyMethod           = 'addIncompleteTest';

            if ($this->stopOnIncomplete) {
                $this->stop();
            }
        } elseif ($e instanceof SkippedTest) {
            $this->skipped[] = new TestFailure($test, $e);
            $notifyMethod    = 'addSkippedTest';

            if ($this->stopOnSkipped) {
                $this->stop();
            }
        } else {
            $this->failures[] = new TestFailure($test, $e);
            $notifyMethod     = 'addFailure';

            if ($this->stopOnFailure || $this->stopOnDefect) {
                $this->stop();
            }
        }

        foreach ($this->listeners as $listener) {
            $listener->{$notifyMethod}($test, $e, $time);
        }

        $this->lastTestFailed = true;
        $this->time += $time;
    }

    /**
     * Informs the result that a test suite will be started.
     */
    public function startTestSuite(TestSuite $suite)
    {
        $this->currentTestSuiteFailed = false;

        if ($this->topTestSuite === null) {
            $this->topTestSuite = $suite;
        }

        foreach ($this->listeners as $listener) {
            $listener->startTestSuite($suite);
        }
    }

    /**
     * Informs the result that a test suite was completed.
     */
    public function endTestSuite(TestSuite $suite)
    {
        if (!$this->currentTestSuiteFailed) {
            $this->passedTestClasses[] = $suite->getName();
        }

        foreach ($this->listeners as $listener) {
            $listener->endTestSuite($suite);
        }
    }

    /**
     * Informs the result that a test will be started.
     */
    public function startTest(Test $test)
    {
        $this->lastTestFailed = false;
        $this->runTests += count($test);

        foreach ($this->listeners as $listener) {
            $listener->startTest($test);
        }
    }

    /**
     * Informs the result that a test was completed.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function endTest(Test $test, $time)
    {
        foreach ($this->listeners as $listener) {
            $listener->endTest($test, $time);
        }

        if (!$this->lastTestFailed && $test instanceof TestCase) {
            $class = get_class($test);
            $key   = $class . '::' . $test->getName();

            $this->passed[$key] = [
                'result' => $test->getResult(),
                'size'   => \PHPUnit\Util\Test::getSize(
                    $class,
                    $test->getName(false)
                ),
            ];

            $this->time += $time;
        }

        if ($this->lastTestFailed && $test instanceof TestCase) {
            $this->currentTestSuiteFailed = true;
        }
    }

    /**
     * Returns true if no risky test occurred.
     */
    public function allHarmless()
    {
        return $this->riskyCount() === 0;
    }

    /**
     * Gets the number of risky tests.
     */
    public function riskyCount()
    {
        return count($this->risky);
    }

    /**
     * Returns true if no incomplete test occurred.
     */
    public function allCompletelyImplemented()
    {
        return $this->notImplementedCount() === 0;
    }

    /**
     * Gets the number of incomplete tests.
     */
    public function notImplementedCount()
    {
        return count($this->notImplemented);
    }

    /**
     * Returns an array of TestFailure objects for the risky tests.
     *
     * @return TestFailure[]
     */
    public function risky()
    {
        return $this->risky;
    }

    /**
     * Returns an array of TestFailure objects for the incomplete tests.
     *
     * @return TestFailure[]
     */
    public function notImplemented()
    {
        return $this->notImplemented;
    }

    /**
     * Returns true if no test has been skipped.
     */
    public function noneSkipped()
    {
        return $this->skippedCount() === 0;
    }

    /**
     * Gets the number of skipped tests.
     */
    public function skippedCount()
    {
        return count($this->skipped);
    }

    /**
     * Returns an array of TestFailure objects for the skipped tests.
     *
     * @return TestFailure[]
     */
    public function skipped()
    {
        return $this->skipped;
    }

    /**
     * Gets the number of detected errors.
     */
    public function errorCount()
    {
        return count($this->errors);
    }

    /**
     * Returns an array of TestFailure objects for the errors.
     *
     * @return TestFailure[]
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Gets the number of detected failures.
     */
    public function failureCount()
    {
        return count($this->failures);
    }

    /**
     * Returns an array of TestFailure objects for the failures.
     *
     * @return TestFailure[]
     */
    public function failures()
    {
        return $this->failures;
    }

    /**
     * Gets the number of detected warnings.
     */
    public function warningCount()
    {
        return count($this->warnings);
    }

    /**
     * Returns an array of TestFailure objects for the warnings.
     *
     * @return TestFailure[]
     */
    public function warnings()
    {
        return $this->warnings;
    }

    /**
     * Returns the names of the tests that have passed.
     */
    public function passed()
    {
        return $this->passed;
    }

    /**
     * Returns the names of the TestSuites that have passed.
     *
     * This enables @depends-annotations for TestClassName::class
     */
    public function passedClasses()
    {
        return $this->passedTestClasses;
    }

    /**
     * Returns the (top) test suite.
     */
    public function topTestSuite()
    {
        return $this->topTestSuite;
    }

    /**
     * Returns whether code coverage information should be collected.
     */
    public function getCollectCodeCoverageInformation()
    {
        return $this->codeCoverage !== null;
    }

    /**
     * Runs a TestCase.
     *
     * @throws CodeCoverageException
     * @throws UnintentionallyCoveredCodeException
     * @throws \SebastianBergmann\CodeCoverage\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function run(Test $test)
    {
        Assert::resetCount();

        if ($test instanceof TestCase) {
            $test->setRegisterMockObjectsFromTestArgumentsRecursively(
                $this->registerMockObjectsFromTestArgumentsRecursively
            );

            $isAnyCoverageRequired = TestUtil::requiresCodeCoverageDataCollection($test);
        }

        $error      = false;
        $failure    = false;
        $warning    = false;
        $incomplete = false;
        $risky      = false;
        $skipped    = false;

        $this->startTest($test);

        if ($this->convertDeprecationsToExceptions || $this->convertErrorsToExceptions || $this->convertNoticesToExceptions || $this->convertWarningsToExceptions) {
            $errorHandler = new ErrorHandler(
                $this->convertDeprecationsToExceptions,
                $this->convertErrorsToExceptions,
                $this->convertNoticesToExceptions,
                $this->convertWarningsToExceptions
            );

            $errorHandler->register();
        }

        $collectCodeCoverage = $this->codeCoverage !== null &&
                               !$test instanceof ErrorTestCase &&
                               !$test instanceof WarningTestCase &&
                               $isAnyCoverageRequired;

        if ($collectCodeCoverage) {
            $this->codeCoverage->start($test);
        }

        $monitorFunctions = $this->beStrictAboutResourceUsageDuringSmallTests &&
                            !$test instanceof ErrorTestCase &&
                            !$test instanceof WarningTestCase &&
                            $test->getSize() === \PHPUnit\Util\Test::SMALL &&
                            function_exists('xdebug_start_function_monitor');

        if ($monitorFunctions) {
            /* @noinspection ForgottenDebugOutputInspection */
            xdebug_start_function_monitor(ResourceOperations::getFunctions());
        }

        $timer = new Timer;
        $timer->start();

        try {
            $invoker = new Invoker;

            if (!$test instanceof ErrorTestCase &&
                !$test instanceof WarningTestCase &&
                $this->enforceTimeLimit &&
                ($this->defaultTimeLimit || $test->getSize() != \PHPUnit\Util\Test::UNKNOWN) &&
                $invoker->canInvokeWithTimeout()) {
                switch ($test->getSize()) {
                    case \PHPUnit\Util\Test::SMALL:
                        $_timeout = $this->timeoutForSmallTests;

                        break;

                    case \PHPUnit\Util\Test::MEDIUM:
                        $_timeout = $this->timeoutForMediumTests;

                        break;

                    case \PHPUnit\Util\Test::LARGE:
                        $_timeout = $this->timeoutForLargeTests;

                        break;

                    case \PHPUnit\Util\Test::UNKNOWN:
                        $_timeout = $this->defaultTimeLimit;

                        break;
                }

                $invoker->invoke([$test, 'runBare'], [], $_timeout);
            } else {
                $test->runBare();
            }
        } catch (TimeoutException $e) {
            $this->addFailure(
                $test,
                new RiskyTestError(
                    $e->getMessage()
                ),
                $_timeout
            );

            $risky = true;
        } catch (AssertionFailedError $e) {
            $failure = true;

            if ($e instanceof RiskyTestError) {
                $risky = true;
            } elseif ($e instanceof IncompleteTestError) {
                $incomplete = true;
            } elseif ($e instanceof SkippedTestError) {
                $skipped = true;
            }
        } catch (AssertionError $e) {
            $test->addToAssertionCount(1);

            $failure = true;
            $frame   = $e->getTrace()[0];

            $e = new AssertionFailedError(
                sprintf(
                    '%s in %s:%s',
                    $e->getMessage(),
                    $frame['file'],
                    $frame['line']
                )
            );
        } catch (Warning $e) {
            $warning = true;
        } catch (Exception $e) {
            $error = true;
        } catch (Throwable $e) {
            $e     = new ExceptionWrapper($e);
            $error = true;
        }

        $time = $timer->stop()->asSeconds();

        $test->addToAssertionCount(Assert::getCount());

        if ($monitorFunctions) {
            $excludeList = new ExcludeList;

            /** @noinspection ForgottenDebugOutputInspection */
            $functions = xdebug_get_monitored_functions();

            /* @noinspection ForgottenDebugOutputInspection */
            xdebug_stop_function_monitor();

            foreach ($functions as $function) {
                if (!$excludeList->isExcluded($function['filename'])) {
                    $this->addFailure(
                        $test,
                        new RiskyTestError(
                            sprintf(
                                '%s() used in %s:%s',
                                $function['function'],
                                $function['filename'],
                                $function['lineno']
                            )
                        ),
                        $time
                    );
                }
            }
        }

        if ($this->beStrictAboutTestsThatDoNotTestAnything &&
            $test->getNumAssertions() === 0) {
            $risky = true;
        }

        if ($this->forceCoversAnnotation && !$error && !$failure && !$warning && !$incomplete && !$skipped && !$risky) {
            $annotations = TestUtil::parseTestMethodAnnotations(
                get_class($test),
                $test->getName(false)
            );

            if (!isset($annotations['class']['covers']) &&
                !isset($annotations['method']['covers']) &&
                !isset($annotations['class']['coversNothing']) &&
                !isset($annotations['method']['coversNothing'])) {
                $this->addFailure(
                    $test,
                    new MissingCoversAnnotationException(
                        'This test does not have a @covers annotation but is expected to have one'
                    ),
                    $time
                );

                $risky = true;
            }
        }

        if ($collectCodeCoverage) {
            $append           = !$risky && !$incomplete && !$skipped;
            $linesToBeCovered = [];
            $linesToBeUsed    = [];

            if ($append && $test instanceof TestCase) {
                try {
                    $linesToBeCovered = \PHPUnit\Util\Test::getLinesToBeCovered(
                        get_class($test),
                        $test->getName(false)
                    );

                    $linesToBeUsed = \PHPUnit\Util\Test::getLinesToBeUsed(
                        get_class($test),
                        $test->getName(false)
                    );
                } catch (InvalidCoversTargetException $cce) {
                    $this->addWarning(
                        $test,
                        new Warning(
                            $cce->getMessage()
                        ),
                        $time
                    );
                }
            }

            try {
                $this->codeCoverage->stop(
                    $append,
                    $linesToBeCovered,
                    $linesToBeUsed
                );
            } catch (UnintentionallyCoveredCodeException $cce) {
                $this->addFailure(
                    $test,
                    new UnintentionallyCoveredCodeError(
                        'This test executed code that is not listed as code to be covered or used:' .
                        PHP_EOL . $cce->getMessage()
                    ),
                    $time
                );
            } catch (OriginalCodeCoverageException $cce) {
                $error = true;

                $e = isset($e) ? $e : $cce;
            }
        }

        if (isset($errorHandler)) {
            $errorHandler->unregister();

            unset($errorHandler);
        }

        if ($error) {
            $this->addError($test, $e, $time);
        } elseif ($failure) {
            $this->addFailure($test, $e, $time);
        } elseif ($warning) {
            $this->addWarning($test, $e, $time);
        } elseif ($this->beStrictAboutTestsThatDoNotTestAnything &&
            !$test->doesNotPerformAssertions() &&
            $test->getNumAssertions() === 0) {
            try {
                $reflected = new ReflectionClass($test);
                // @codeCoverageIgnoreStart
            } catch (ReflectionException $e) {
                throw new Exception(
                    $e->getMessage(),
                    (int) $e->getCode(),
                    $e
                );
            }
            // @codeCoverageIgnoreEnd

            $name = $test->getName(false);

            if ($name && $reflected->hasMethod($name)) {
                try {
                    $reflected = $reflected->getMethod($name);
                    // @codeCoverageIgnoreStart
                } catch (ReflectionException $e) {
                    throw new Exception(
                        $e->getMessage(),
                        (int) $e->getCode(),
                        $e
                    );
                }
                // @codeCoverageIgnoreEnd
            }

            $this->addFailure(
                $test,
                new RiskyTestError(
                    sprintf(
                        "This test did not perform any assertions\n\n%s:%d",
                        $reflected->getFileName(),
                        $reflected->getStartLine()
                    )
                ),
                $time
            );
        } elseif ($this->beStrictAboutTestsThatDoNotTestAnything &&
            $test->doesNotPerformAssertions() &&
            $test->getNumAssertions() > 0) {
            $this->addFailure(
                $test,
                new RiskyTestError(
                    sprintf(
                        'This test is annotated with "@doesNotPerformAssertions" but performed %d assertions',
                        $test->getNumAssertions()
                    )
                ),
                $time
            );
        } elseif ($this->beStrictAboutOutputDuringTests && $test->hasOutput()) {
            $this->addFailure(
                $test,
                new OutputError(
                    sprintf(
                        'This test printed output: %s',
                        $test->getActualOutput()
                    )
                ),
                $time
            );
        } elseif ($this->beStrictAboutTodoAnnotatedTests && $test instanceof TestCase) {
            $annotations = TestUtil::parseTestMethodAnnotations(
                get_class($test),
                $test->getName(false)
            );

            if (isset($annotations['method']['todo'])) {
                $this->addFailure(
                    $test,
                    new RiskyTestError(
                        'Test method is annotated with @todo'
                    ),
                    $time
                );
            }
        }

        $this->endTest($test, $time);
    }

    /**
     * Gets the number of run tests.
     */
    public function count()
    {
        return $this->runTests;
    }

    /**
     * Checks whether the test run should stop.
     */
    public function shouldStop()
    {
        return $this->stop;
    }

    /**
     * Marks that the test run should stop.
     */
    public function stop()
    {
        $this->stop = true;
    }

    /**
     * Returns the code coverage object.
     */
    public function getCodeCoverage()
    {
        return $this->codeCoverage;
    }

    /**
     * Sets the code coverage object.
     */
    public function setCodeCoverage(CodeCoverage $codeCoverage)
    {
        $this->codeCoverage = $codeCoverage;
    }

    /**
     * Enables or disables the deprecation-to-exception conversion.
     */
    public function convertDeprecationsToExceptions($flag)
    {
        $this->convertDeprecationsToExceptions = $flag;
    }

    /**
     * Returns the deprecation-to-exception conversion setting.
     */
    public function getConvertDeprecationsToExceptions()
    {
        return $this->convertDeprecationsToExceptions;
    }

    /**
     * Enables or disables the error-to-exception conversion.
     */
    public function convertErrorsToExceptions($flag)
    {
        $this->convertErrorsToExceptions = $flag;
    }

    /**
     * Returns the error-to-exception conversion setting.
     */
    public function getConvertErrorsToExceptions()
    {
        return $this->convertErrorsToExceptions;
    }

    /**
     * Enables or disables the notice-to-exception conversion.
     */
    public function convertNoticesToExceptions($flag)
    {
        $this->convertNoticesToExceptions = $flag;
    }

    /**
     * Returns the notice-to-exception conversion setting.
     */
    public function getConvertNoticesToExceptions()
    {
        return $this->convertNoticesToExceptions;
    }

    /**
     * Enables or disables the warning-to-exception conversion.
     */
    public function convertWarningsToExceptions($flag)
    {
        $this->convertWarningsToExceptions = $flag;
    }

    /**
     * Returns the warning-to-exception conversion setting.
     */
    public function getConvertWarningsToExceptions()
    {
        return $this->convertWarningsToExceptions;
    }

    /**
     * Enables or disables the stopping when an error occurs.
     */
    public function stopOnError($flag)
    {
        $this->stopOnError = $flag;
    }

    /**
     * Enables or disables the stopping when a failure occurs.
     */
    public function stopOnFailure($flag)
    {
        $this->stopOnFailure = $flag;
    }

    /**
     * Enables or disables the stopping when a warning occurs.
     */
    public function stopOnWarning($flag)
    {
        $this->stopOnWarning = $flag;
    }

    public function beStrictAboutTestsThatDoNotTestAnything($flag)
    {
        $this->beStrictAboutTestsThatDoNotTestAnything = $flag;
    }

    public function isStrictAboutTestsThatDoNotTestAnything()
    {
        return $this->beStrictAboutTestsThatDoNotTestAnything;
    }

    public function beStrictAboutOutputDuringTests($flag)
    {
        $this->beStrictAboutOutputDuringTests = $flag;
    }

    public function isStrictAboutOutputDuringTests()
    {
        return $this->beStrictAboutOutputDuringTests;
    }

    public function beStrictAboutResourceUsageDuringSmallTests($flag)
    {
        $this->beStrictAboutResourceUsageDuringSmallTests = $flag;
    }

    public function isStrictAboutResourceUsageDuringSmallTests()
    {
        return $this->beStrictAboutResourceUsageDuringSmallTests;
    }

    public function enforceTimeLimit($flag)
    {
        $this->enforceTimeLimit = $flag;
    }

    public function enforcesTimeLimit()
    {
        return $this->enforceTimeLimit;
    }

    public function beStrictAboutTodoAnnotatedTests($flag)
    {
        $this->beStrictAboutTodoAnnotatedTests = $flag;
    }

    public function isStrictAboutTodoAnnotatedTests()
    {
        return $this->beStrictAboutTodoAnnotatedTests;
    }

    public function forceCoversAnnotation()
    {
        $this->forceCoversAnnotation = true;
    }

    public function forcesCoversAnnotation()
    {
        return $this->forceCoversAnnotation;
    }

    /**
     * Enables or disables the stopping for risky tests.
     */
    public function stopOnRisky($flag)
    {
        $this->stopOnRisky = $flag;
    }

    /**
     * Enables or disables the stopping for incomplete tests.
     */
    public function stopOnIncomplete($flag)
    {
        $this->stopOnIncomplete = $flag;
    }

    /**
     * Enables or disables the stopping for skipped tests.
     */
    public function stopOnSkipped($flag)
    {
        $this->stopOnSkipped = $flag;
    }

    /**
     * Enables or disables the stopping for defects: error, failure, warning.
     */
    public function stopOnDefect($flag)
    {
        $this->stopOnDefect = $flag;
    }

    /**
     * Returns the time spent running the tests.
     */
    public function time()
    {
        return $this->time;
    }

    /**
     * Returns whether the entire test was successful or not.
     */
    public function wasSuccessful()
    {
        return $this->wasSuccessfulIgnoringWarnings() && empty($this->warnings);
    }

    public function wasSuccessfulIgnoringWarnings()
    {
        return empty($this->errors) && empty($this->failures);
    }

    public function wasSuccessfulAndNoTestIsRiskyOrSkippedOrIncomplete()
    {
        return $this->wasSuccessful() && $this->allHarmless() && $this->allCompletelyImplemented() && $this->noneSkipped();
    }

    /**
     * Sets the default timeout for tests.
     */
    public function setDefaultTimeLimit($timeout)
    {
        $this->defaultTimeLimit = $timeout;
    }

    /**
     * Sets the timeout for small tests.
     */
    public function setTimeoutForSmallTests($timeout)
    {
        $this->timeoutForSmallTests = $timeout;
    }

    /**
     * Sets the timeout for medium tests.
     */
    public function setTimeoutForMediumTests($timeout)
    {
        $this->timeoutForMediumTests = $timeout;
    }

    /**
     * Sets the timeout for large tests.
     */
    public function setTimeoutForLargeTests($timeout)
    {
        $this->timeoutForLargeTests = $timeout;
    }

    /**
     * Returns the set timeout for large tests.
     */
    public function getTimeoutForLargeTests()
    {
        return $this->timeoutForLargeTests;
    }

    public function setRegisterMockObjectsFromTestArgumentsRecursively($flag)
    {
        $this->registerMockObjectsFromTestArgumentsRecursively = $flag;
    }
}
