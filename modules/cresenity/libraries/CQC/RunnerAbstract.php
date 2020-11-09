<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Oct 7, 2020 
 * @license Ittron Global Teknologi
 */
abstract class CQC_RunnerAbstract {

    protected $className;    
    
    public function __construct($className) {
        $this->className = $className;
        
    }
    
    abstract public function run();
}
