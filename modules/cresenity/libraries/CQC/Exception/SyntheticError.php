<?php

/**
 * Description of SyntheticError
 *
 * @author Hery
 */
class CQC_Exception_SyntheticError extends CQC_Exception_AssertionFailedError {

    /**
     * The synthetic file.
     *
     * @var string
     */
    protected $syntheticFile = '';

    /**
     * The synthetic line number.
     *
     * @var int
     */
    protected $syntheticLine = 0;

    /**
     * The synthetic trace.
     *
     * @var array
     */
    protected $syntheticTrace = [];

    public function __construct($message, $code, $file, int $line, array $trace) {
        parent::__construct($message, $code);

        $this->syntheticFile = $file;
        $this->syntheticLine = $line;
        $this->syntheticTrace = $trace;
    }

    public function getSyntheticFile() {
        return $this->syntheticFile;
    }

    public function getSyntheticLine() {
        return $this->syntheticLine;
    }

    public function getSyntheticTrace() {
        return $this->syntheticTrace;
    }

}
