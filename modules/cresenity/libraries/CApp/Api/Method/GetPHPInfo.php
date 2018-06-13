<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 14, 2018, 5:57:04 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api_Method_GetPHPInfo extends CApp_Api_Method {

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
