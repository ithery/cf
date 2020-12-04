<?php

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @internal
 */
final class CTesting_PhpUnit_Printer implements \PHPUnit\TextUI\ResultPrinter {

    /**
     * Holds an instance of the style.
     *
     * Style is a class we use to interact with output.
     *
     * @var Style
     */
    private $style;

    /**
     * Holds the duration time of the test suite.
     *
     * @var Timer
     */
    private $timer;

    /**
     * Holds the state of the test
     * suite. The number of tests, etc.
     *
     * @var State
     */
    private $state;

    /**
     * If the test suite has failed.
     *
     * @var bool
     */
    private $failed = false;

    /**
     * Creates a new instance of the listener.
     *
     * @param ConsoleOutput $output
     *
     * @throws \ReflectionException
     */
    public function __construct(\Symfony\Component\Console\Output\ConsoleOutputInterface $output = null, $verbose = false, $colors = 'always') {
        $this->timer = Timer::start();

        $decorated = $colors === 'always' || $colors === 'auto';

        $output = $output != null ? $output : new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, $decorated);

        ConfigureIO::of(new ArgvInput(), $output);

        $this->style = new Style($output);
        $dummyTest = new CTesting_PhpUnit_DummyTestCase();

        $this->state = State::from($dummyTest);
    }

    /**
     * {@inheritdoc}
     */
    public function addError(Test $testCase, Throwable $throwable, $time) {
        $this->failed = true;

        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(CTesting_PhpUnit_TestResult::fromTestCase($testCase, CTesting_PhpUnit_TestResult::FAIL, $throwable));
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning(Test $testCase, Warning $warning, $time) {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(CTesting_PhpUnit_TestResult::fromTestCase($testCase, CTesting_PhpUnit_TestResult::WARN, $warning));
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(Test $testCase, AssertionFailedError $error, $time) {
        $this->failed = true;

        $testCase = $this->testCaseFromTest($testCase);

        $reflector = new ReflectionObject($error);

        if ($reflector->hasProperty('message')) {
            $message = trim((string) preg_replace("/\r|\n/", "\n  ", $error->getMessage()));
            $property = $reflector->getProperty('message');
            $property->setAccessible(true);
            $property->setValue($error, $message);
        }

        $this->state->add(CTesting_PhpUnit_TestResult::fromTestCase($testCase, CTesting_PhpUnit_TestResult::FAIL, $error));
    }

    /**
     * {@inheritdoc}
     */
    public function addIncompleteTest(Test $testCase, Throwable $throwable, $time) {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(CTesting_PhpUnit_TestResult::fromTestCase($testCase, CTesting_PhpUnit_TestResult::INCOMPLETE, $throwable));
    }

    /**
     * {@inheritdoc}
     */
    public function addRiskyTest(Test $testCase, Throwable $throwable, $time) {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(CTesting_PhpUnit_TestResult::fromTestCase($testCase, CTesting_PhpUnit_TestResult::RISKY, $throwable));
    }

    /**
     * {@inheritdoc}
     */
    public function addSkippedTest(Test $testCase, Throwable $throwable, $time) {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(CTesting_PhpUnit_TestResult::fromTestCase($testCase, CTesting_PhpUnit_TestResult::SKIPPED, $throwable));
    }

    /**
     * {@inheritdoc}
     */
    public function startTestSuite(TestSuite $suite) {
        if ($this->state->suiteTotalTests === null) {
            $this->state->suiteTotalTests = $suite->count();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endTestSuite(TestSuite $suite) {
        // ..
    }

    /**
     * {@inheritdoc}
     */
    public function startTest(Test $testCase) {
        $testCase = $this->testCaseFromTest($testCase);

        // Let's check first if the testCase is over.
        if ($this->state->testCaseHasChanged($testCase)) {
            $this->style->writeCurrentTestCaseSummary($this->state);

            $this->state->moveTo($testCase);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endTest(Test $testCase, $time) {
        $testCase = $this->testCaseFromTest($testCase);

        if (!$this->state->existsInTestCase($testCase)) {
            $this->state->add(CTesting_PhpUnit_TestResult::fromTestCase($testCase, CTesting_PhpUnit_TestResult::PASS));
        }

        if ($testCase instanceof TestCase && $testCase->getTestResultObject() instanceof \PHPUnit\Framework\TestResult && !$testCase->getTestResultObject()->isStrictAboutOutputDuringTests() && !$testCase->hasExpectationOnOutput()) {
            $this->style->write($testCase->getActualOutput());
        }
    }

    /**
     * Intentionally left blank as we output things on events of the listener.
     */
    public function write($content) {
        // ..
    }

    /**
     * Returns a test case from the given test.
     *
     * Note: This printer is do not work with normal Test classes - only
     * with Test Case classes. Please report an issue if you think
     * this should work any other way.
     */
    private function testCaseFromTest(Test $test) {
        if (!$test instanceof TestCase) {
            throw new CTesting_Exception_ShouldNotHappen();
        }

        return $test;
    }

    /**
     * Intentionally left blank as we output things on events of the listener.
     */
    public function printResult(\PHPUnit\Framework\TestResult $result) {
        if ($result->count() === 0) {
            $this->style->writeWarning('No tests executed!');
        }

        $this->style->writeCurrentTestCaseSummary($this->state);

        if ($this->failed) {
            $onFailure = $this->state->suiteTotalTests !== $this->state->testSuiteTestsCount();
            $this->style->writeErrorsSummary($this->state, $onFailure);
        }

        $this->style->writeRecap($this->state, $this->timer);
    }

}
