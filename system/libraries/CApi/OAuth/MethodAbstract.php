<?php

abstract class CApi_OAuth_MethodAbstract extends CApi_MethodAbstract {
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return CApi_OAuth
     */
    protected function getOAuth() {
        return CApi::oauth($this->group);
    }
}
