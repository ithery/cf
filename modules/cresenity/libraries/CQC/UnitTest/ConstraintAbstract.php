<?php

/**
 * Description of ConstraintAbstract
 *
 * @author Hery
 */

/**
 * Abstract base class for constraints which can be applied to any value.
 */
abstract class CQC_UnitTest_ConstraintAbstract implements Countable, CQC_SelfDescribingInterface {

    /**
     * @var Exporter
     */
    private $exporter;

    /**
     * Evaluates the constraint for parameter $other
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     */
    public function evaluate($other,  $description = '',  $returnResult = false) {
        $success = false;

        if ($this->matches($other)) {
            $success = true;
        }

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            $this->fail($other, $description);
        }
    }

    /**
     * Counts the number of constraint elements.
     */
    public function count() {
        return 1;
    }

    protected function exporter() {
        if ($this->exporter === null) {
            $this->exporter = new CQC_Exporter;
        }

        return $this->exporter;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param mixed $other value or object to evaluate
     * @codeCoverageIgnore
     */
    protected function matches($other) {
        return false;
    }

    /**
     * Throws an exception for the given compared value and test description
     *
     * @param mixed             $other             evaluated value or object
     * @param string            $description       Additional information about the test
     * @param ComparisonFailure $comparisonFailure
     *
     * @throws ExpectationFailedException
     * @throws CQC_Exception_RecursionContextInvalidArgumentException
     *
     * @psalm-return never-return
     */
    protected function fail($other, $description, CComparator_Exception_ComparisonFailureException $comparisonFailure = null) {
        $failureDescription = \sprintf(
                'Failed asserting that %s.', $this->failureDescription($other)
        );

        $additionalFailureDescription = $this->additionalFailureDescription($other);

        if ($additionalFailureDescription) {
            $failureDescription .= "\n" . $additionalFailureDescription;
        }

        if (!empty($description)) {
            $failureDescription = $description . "\n" . $failureDescription;
        }

        throw new CQC_Exception_ExpectationFailedException(
        $failureDescription, $comparisonFailure
        );
    }

    /**
     * Return additional failure description where needed
     *
     * The function can be overridden to provide additional failure
     * information like a diff
     *
     * @param mixed $other evaluated value or object
     */
    protected function additionalFailureDescription($other) {
        return '';
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * To provide additional failure information additionalFailureDescription
     * can be used.
     *
     * @param mixed $other evaluated value or object
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function failureDescription($other) {
        return $this->exporter()->export($other) . ' ' . $this->toString();
    }

}
