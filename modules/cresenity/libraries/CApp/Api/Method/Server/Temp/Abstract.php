<?php

abstract class CApp_Api_Method_Server_Temp_Abstract {
    /**
     * @var CApp_Api_Method_Server
     */
    protected $method;

    public function __construct($method) {
        $this->method = $method;
    }

    abstract public function execute();

    public function basePath() {
        return DOCROOT . 'temp/';
    }
}
