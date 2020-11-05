<?php

/**
 * Description of ExpectationFailedException
 *
 * @author Hery
 */
final class CQC_Exception_ExpectationFailedException extends CQC_Exception_AssertionFailedError {

    /**
     * @var ComparisonFailure
     */
    protected $comparisonFailure;

    public function __construct($message, CComparator_Exception_ComparisonFailureException $comparisonFailure = null, Exception $previous = null) {
        $this->comparisonFailure = $comparisonFailure;

        parent::__construct($message, 0, $previous);
    }

    /**
     * 
     * @return CComparator_Exception_ComparisonFailureException|null
     */
    public function getComparisonFailure() {
        return $this->comparisonFailure;
    }

}
