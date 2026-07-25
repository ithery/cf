<?php

abstract class CApp_Api_Method_Server_Temp_Abstract {
    /**
     * @var CApp_Api_Method_Server
     */
    protected $method;

    /**
     * @param CApp_Api_Method_Server $method
     */
    public function __construct($method) {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    abstract public function execute();

    /**
     * @return string
     */
    public function basePath() {
        return DOCROOT . 'temp/';
    }
}
