<?php

/**
 * @codeCoverageIgnore
 */
trait CVendor_Firebase_Trait_ExceptionHasErrorsTrait {
    /** @var string[] */
    protected $errors = [];

    /**
     * @return string[]
     */
    public function errors() {
        return $this->errors;
    }
}
