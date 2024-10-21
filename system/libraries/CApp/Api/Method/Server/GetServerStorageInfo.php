<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api_Method_Server_GetServerStorageInfo extends CApp_Api_Method_Server {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;

        $data = [];

        try {
            $serverStorage = CServer::storage();

            $dataStorage = [];
            $dataStorage['total'] = $serverStorage->getTotalSpace();
            $dataStorage['free'] = $serverStorage->getFreeSpace();
            $dataStorage['used'] = $dataStorage['total'] - $dataStorage['free'];

            $diskDevices = CServer::storage()->getDiskDevices();
            $dataDevices = [];

            foreach ($diskDevices as $device) {
                $dataDevice = [];

                /* @var $device CServer_Device_Disk */
                $freeDisk = $device->getFree();
                $usedDisk = $device->getUsed();
                $totalDisk = $device->getTotal();
                $usedPercent = $usedDisk * 100 / $totalDisk;
                $usedPercent = min(ceil($usedPercent * 100) / 100, 100);

                $dataDevice['free'] = $freeDisk;
                $dataDevice['used'] = $usedDisk;
                $dataDevice['total'] = $totalDisk;
                $dataDevice['usedPercent'] = $usedPercent;
                $dataDevice['name'] = $device->getName();
                $dataDevice['fsType'] = $device->getFsType();
                $dataDevice['mountPoint'] = $device->getMountPoint();
                $dataDevice['percentInodesUsed'] = $device->getPercentInodesUsed();
                $dataDevice['options'] = $device->getOptions();

                $dataDevices[] = $dataDevice;
            }
            $dataStorage['devices'] = $dataDevices;
            $data = $dataStorage;
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
