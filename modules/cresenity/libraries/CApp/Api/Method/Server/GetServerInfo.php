<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 14, 2018, 4:40:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api_Method_Server_GetServerInfo extends CApp_Api_Method_Server {

    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $serverStorage = CServer::storage();
        $dataStorage = array();
        $dataStorage['total'] = $serverStorage->getTotalSpace();
        $dataStorage['free'] = $serverStorage->getFreeSpace();
        $dataStorage['used'] = $dataStorage['total'] - $dataStorage['free'];

        

        $data = array();
        $data['storage'] = $dataStorage;

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }

}
