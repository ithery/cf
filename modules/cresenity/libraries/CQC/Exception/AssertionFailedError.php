<?php

/**
 * Description of AssertionFailedError
 *
 * @author Hery
 */
class CQC_Exception_AssertionFailedError extends CQC_Exception implements CQC_SelfDescribingInterface {

    /**
     * Wrapper for getMessage() which is declared as final.
     */
    public function toString() {
        return $this->getMessage();
    }

}
