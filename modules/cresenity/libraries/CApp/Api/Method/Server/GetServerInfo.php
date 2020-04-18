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


        $data = array();

        try {
            $serverStorage = CServer::storage();
            $serverSystem = CServer::system();
            $serverMemory = CServer::memory();
            $dataStorage = array();
            $dataStorage['total'] = $serverStorage->getTotalSpace();
            $dataStorage['free'] = $serverStorage->getFreeSpace();
            $dataStorage['used'] = $dataStorage['total'] - $dataStorage['free'];

            $dataSystem = array();
            $dataSystem['hostname'] = $serverSystem->getHostname();
            $dataSystem['distribution'] = $serverSystem->getDistribution();
            $dataSystem['distributionIcon'] = $serverSystem->getDistributionIcon();
            $dataSystem['ip'] = $serverSystem->getIp();
            $dataSystem['kernel'] = $serverSystem->getKernel();
            $dataSystem['lastBoot'] = $serverSystem->getLastBoot();
            $dataSystem['load'] = $serverSystem->getLoad();
            $dataSystem['loadPercent'] = $serverSystem->getLoadPercent();
            $dataSystem['processes'] = $serverSystem->getProcesses();
            $dataSystem['uptime'] = $serverSystem->getUptime();
            $dataSystem['users'] = $serverSystem->getUsers();

            $dataSystem['machine'] = $serverSystem->getMachine();
            $cpus = $serverSystem->getCpus();
            $cpusData = [];

            foreach ($cpus as $cpu) {
                $cpusData[] = [
                    'model' => $cpu->getModel(),
                    'speed' => $cpu->getCpuSpeed(),
                    'cache' => $cpu->getCache(),
                    'virtualization' => $cpu->getVirt(),
                ];
            }
            $dataSystem['cpus'] = $cpusData;
            $dataMemory = array();
            $dataMemory['physical'] = array();
            $dataMemory['physical']['free'] = $serverMemory->getMemFree();
            $dataMemory['physical']['total'] = $serverMemory->getMemTotal();
            $dataMemory['physical']['used'] = $serverMemory->getMemUsed();
            $dataMemory['physical']['cache'] = $serverMemory->getMemCache();
            $dataMemory['physical']['buffer'] = $serverMemory->getMemBuffer();
            $dataMemory['physical']['application'] = $serverMemory->getMemApplication();
            $dataMemory['physical']['applicationPercent'] = $serverMemory->getMemPercentApplication();
            $dataMemory['physical']['bufferPercent'] = $serverMemory->getMemPercentBuffer();
            $dataMemory['physical']['cachePercent'] = $serverMemory->getMemPercentCache();
            $dataMemory['physical']['usedPercent'] = $serverMemory->getMemPercentUsed();

            $dataMemory['swap'] = array();
            $dataMemory['swap']['free'] = $serverMemory->getSwapFree();
            $dataMemory['swap']['total'] = $serverMemory->getSwapTotal();
            $dataMemory['swap']['used'] = $serverMemory->getSwapUsed();
            $dataMemory['swap']['usedPercent'] = $serverMemory->getSwapPercentUsed();
            $dataMemory['swap']['devices'] = $serverMemory->getSwapDevices();


            $data['storage'] = $dataStorage;
            $data['system'] = $dataSystem;
            $data['memory'] = $dataMemory;
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
