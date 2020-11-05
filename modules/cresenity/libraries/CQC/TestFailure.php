<?php

/**
 * Description of TestFailure
 *
 * @author Hery
 */


final class CQC_TestFailure
{
    /**
     * @var null|Test
     */
    private $failedTest;

    /**
     * @var Throwable
     */
    private $thrownException;

    /**
     * @var string
     */
    private $testName;

    /**
     * Returns a description for an exception.
     */
    public static function exceptionToString(Throwable $e)
    {
        if ($e instanceof CQC_SelfDescribingInterface) {
            $buffer = $e->toString();

            if ($e instanceof CQC_Exception_ExpectationFailedException && $e->getComparisonFailure()) {
                $buffer .= $e->getComparisonFailure()->getDiff();
            }

            if ($e instanceof PHPTAssertionFailedError) {
                $buffer .= $e->getDiff();
            }

            if (!empty($buffer)) {
                $buffer = trim($buffer) . "\n";
            }

            return $buffer;
        }

        if ($e instanceof Error) {
            return $e->getMessage() . "\n";
        }

        if ($e instanceof ExceptionWrapper) {
            return $e->getClassName() . ': ' . $e->getMessage() . "\n";
        }

        return get_class($e) . ': ' . $e->getMessage() . "\n";
    }

    /**
     * Constructs a TestFailure with the given test and exception.
     */
    public function __construct(Test $failedTest, Throwable $t)
    {
        if ($failedTest instanceof CQC_SelfDescribingInterface) {
            $this->testName = $failedTest->toString();
        } else {
            $this->testName = get_class($failedTest);
        }

        if (!$failedTest instanceof CQC_UnitTestAbstract || !$failedTest->isInIsolation()) {
            $this->failedTest = $failedTest;
        }

        $this->thrownException = $t;
    }

    /**
     * Returns a short description of the failure.
     */
    public function toString()
    {
        return sprintf(
            '%s: %s',
            $this->testName,
            $this->thrownException->getMessage()
        );
    }

    /**
     * Returns a description for the thrown exception.
     */
    public function getExceptionAsString()
    {
        return self::exceptionToString($this->thrownException);
    }

    /**
     * Returns the name of the failing test (including data set, if any).
     */
    public function getTestName()
    {
        return $this->testName;
    }

    /**
     * Returns the failing test.
     *
     * Note: The test object is not set when the test is executed in process
     * isolation.
     *
     * @see Exception
     */
    public function failedTest()
    {
        return $this->failedTest;
    }

    /**
     * Gets the thrown exception.
     */
    public function thrownException()
    {
        return $this->thrownException;
    }

    /**
     * Returns the exception's message.
     */
    public function exceptionMessage()
    {
        return $this->thrownException()->getMessage();
    }

    /**
     * Returns true if the thrown exception
     * is of type AssertionFailedError.
     */
    public function isFailure()
    {
        return $this->thrownException() instanceof CQC_Exception_AssertionFailedError;
    }
}