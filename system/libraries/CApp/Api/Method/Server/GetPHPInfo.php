<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api_Method_Server_GetPHPInfo extends CApp_Api_Method_Server {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;

        $data = CServer::phpInfo()->get();

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
