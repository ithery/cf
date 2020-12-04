<?php



use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class CTesting_PhpUnit_State
{
    /**
     * The complete test suite number of tests.
     *
     * @var int|null
     */
    public $suiteTotalTests;

    /**
     * The complete test suite tests.
     *
     * @var array<int, CTesting_PhpUnit_TestResult>
     */
    public $suiteTests = [];

    /**
     * The current test case class.
     *
     * @var string
     */
    public $testCaseName;

    /**
     * The current test case tests.
     *
     * @var array<int, CTesting_PhpUnit_TestResult>
     */
    public $testCaseTests = [];

    /**
     * The current test case tests.
     *
     * @var array<int, CTesting_PhpUnit_TestResult>
     */
    public $toBePrintedCaseTests = [];

    /**
     * Header printed.
     *
     * @var bool
     */
    public $headerPrinted = false;

    /**
     * The state constructor.
     */
    private function __construct( $testCaseName)
    {
        $this->testCaseName = $testCaseName;
    }

    /**
     * Creates a new State starting from the given test case.
     */
    public static function from(TestCase $test)
    {
        return new self(self::getPrintableTestCaseName($test));
    }

    /**
     * Adds the given test to the State.
     */
    public function add(CTesting_PhpUnit_TestResult $test)
    {
        $this->testCaseTests[]        = $test;
        $this->toBePrintedCaseTests[] = $test;

        $this->suiteTests[] = $test;
    }

    /**
     * Gets the test case title.
     */
    public function getTestCaseTitle()
    {
        foreach ($this->testCaseTests as $test) {
            if ($test->type === CTesting_PhpUnit_TestResult::FAIL) {
                return 'FAIL';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type !== CTesting_PhpUnit_TestResult::PASS) {
                return 'WARN';
            }
        }

        return 'PASS';
    }

    /**
     * Gets the test case title color.
     */
    public function getTestCaseTitleColor()
    {
        foreach ($this->testCaseTests as $test) {
            if ($test->type === CTesting_PhpUnit_TestResult::FAIL) {
                return 'red';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type !== CTesting_PhpUnit_TestResult::PASS) {
                return 'yellow';
            }
        }

        return 'green';
    }

    /**
     * Returns the number of tests on the current test case.
     */
    public function testCaseTestsCount()
    {
        return count($this->testCaseTests);
    }

    /**
     * Returns the number of tests on the complete test suite.
     */
    public function testSuiteTestsCount()
    {
        return count($this->suiteTests);
    }

    /**
     * Checks if the given test case is different from the current one.
     */
    public function testCaseHasChanged(TestCase $testCase)
    {
        return self::getPrintableTestCaseName($testCase) !== $this->testCaseName;
    }

    /**
     * Moves the a new test case.
     */
    public function moveTo(TestCase $testCase)
    {
        $this->testCaseName = self::getPrintableTestCaseName($testCase);

        $this->testCaseTests = [];

        $this->headerPrinted = false;
    }

    /**
     * Foreach test in the test case.
     */
    public function eachTestCaseTests(callable $callback)
    {
        foreach ($this->toBePrintedCaseTests as $test) {
            $callback($test);
        }

        $this->toBePrintedCaseTests = [];
    }

    public function countTestsInTestSuiteBy( $type)
    {
        return count(array_filter($this->suiteTests, function (CTesting_PhpUnit_TestResult $testResult) use ($type) {
            return $testResult->type === $type;
        }));
    }

    /**
     * Checks if the given test already contains a result.
     */
    public function existsInTestCase(TestCase $test)
    {
        foreach ($this->testCaseTests as $testResult) {
            if (CTesting_PhpUnit_TestResult::makeDescription($test) === $testResult->description) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the printable test case name from the given `TestCase`.
     */
    public static function getPrintableTestCaseName(TestCase $test)
    {
        return $test instanceof CTesting_PhpUnit_HasPrintableTestCaseNameInterface
            ? $test->getPrintableTestCaseName()
            : get_class($test);
    }
}
