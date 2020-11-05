<?php

/**
 * Description of PHPTAssertionFailedError
 *
 * @author Hery
 */
final class CQC_Exception_PHPTAssertionFailedError extends CQC_Exception_SyntheticError {

    /**
     * @var string
     */
    private $diff;

    public
            function __construct($message, $code, $file, $line, array $trace, $diff) {
        parent::__construct($message, $code, $file, $line, $trace);
        $this->diff = $diff;
    }

    public function getDiff() {
        return $this->diff;
    }

}
