<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api_Method_App_Config_Get extends CApp_Api_Method_App {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = carr::get($this->request(), 'domain');

        $data = [];

        try {
            $config = CConfig::instance('app');
            $config->addAppCode($this->appCode());
            $data=$config->getConfigData();
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
