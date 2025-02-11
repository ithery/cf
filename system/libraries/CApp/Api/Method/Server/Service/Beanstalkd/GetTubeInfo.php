<?php

class CApp_Api_Method_Server_Service_Beanstalkd_GetTubeInfo extends CApp_Api_Method_Server {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $request = $this->request();
        $host = carr::get($request, 'host', 'localhost');
        $port = carr::get($request, 'port', 11300);
        $tube = carr::get($request, 'tube');

        $data = [];

        if (strlen($tube) == 0) {
            $errCode++;
            $errMessage = 'tube required';
        }
        if ($errCode == 0) {
            try {
                $beanstalkd = CServer::createBeanstalkd(['host' => $host, 'port' => $port]);
                $tubeStat = $beanstalkd->getRawTubeStats($tube);
                $nextReady = $beanstalkd->peekReady($tube);
                $nextBuried = $beanstalkd->peekBuried($tube);
                $nextDelayed = $beanstalkd->peekDelayed($tube);
                $data['stat'] = $tubeStat;

                $data['nextReady'] = $nextReady;
                $data['nextBuried'] = $nextBuried;
                $data['nextDelayed'] = $nextDelayed;
            } catch (Throwable $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
        }

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
