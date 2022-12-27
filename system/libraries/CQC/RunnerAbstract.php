<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 */
abstract class CQC_RunnerAbstract {
    protected $className;

    public function __construct($className) {
        $this->className = $className;
    }

    abstract public function run();
}
