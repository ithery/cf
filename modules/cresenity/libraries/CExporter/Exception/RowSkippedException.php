<?php

class CExporter_Exception_RowSkippedException extends Exception {
    /**
     * @var CExporter_Validator_Failure[]
     */
    private $failures;

    /**
     * @param Failure ...$failures
     */
    public function __construct(CExporter_Validator_Failure ...$failures) {
        $this->failures = $failures;

        parent::__construct();
    }

    /**
     * @return CExporter_Validator_Failure[]|CCollection
     */
    public function failures() {
        return new CCollection($this->failures);
    }

    /**
     * @return int[]
     */
    public function skippedRows() {
        return $this->failures()->map->row()->all();
    }
}
