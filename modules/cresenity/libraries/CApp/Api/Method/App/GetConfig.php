<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * 
 */
class CApp_Api_Method_Server_GetConfig extends CApp_Api_Method_App {

    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;

        $data = array();

        try {
            $config = CConfig::instance();
            $data = $config->get();
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }

}
