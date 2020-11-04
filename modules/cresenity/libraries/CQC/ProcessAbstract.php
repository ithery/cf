<?php

/**
 * Description of ProcessAbstract
 *
 * @author Hery
 */
abstract class CQC_ProcessAbstract {

    protected $className;

    public function __construct($className) {
        $this->className = $className;
    }

    abstract public function run();
}
