<?php

use Mpdf\Tag\P;

class CApp_Api_Method_Server_Service_Beanstalkd_GetInfo extends CApp_Api_Method_Server {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $request = $this->request();
        $host = carr::get($request, 'host', 'localhost');
        $port = carr::get($request, 'port', 11300);
        $data = [];

        try {
            $beanstalkd = CServer::createBeanstalkd(['host' => $host, 'port' => $port]);
            $tubeStat = $beanstalkd->getTubesStats();
            $serverStat = $beanstalkd->getServerStats();
            $data['tubeStat'] = $tubeStat;
            $data['serverStat'] = $serverStat;
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
