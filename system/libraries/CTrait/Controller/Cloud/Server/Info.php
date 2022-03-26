<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 14, 2019, 10:20:08 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Cloud_Server_Info {

    public function info() {
        $errCode = 0;
        $errMessage = '';
        $app = CApp::instance();

        $app->title('Server Information');
        $cloudData = array();
        try {
            $cloudData = CApp_Cloud::instance()->api('Server/GetInfo');
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }

        $divRow = $app->addDiv()->addClass('row');
        if ($errCode == 0) {
            $loadAverageArray = carr::get($cloudData, 'load.average');
            $loadAveragePercent = carr::get($cloudData, 'load.averagePercent');
            $totalMemory = carr::get($cloudData, 'memory.total');
            $usedMemory = carr::get($cloudData, 'memory.used');
            $freeMemory = carr::get($cloudData, 'memory.free');
            $totalStorage = carr::get($cloudData, 'storage.total');
            $usedStorage = carr::get($cloudData, 'storage.used');
            $freeStorage = carr::get($cloudData, 'storage.free');
            //load avg
            $cardData = array();
            $cardData['title'] = 'Load Average';


            $cardData['properties'] = array();
            $cardData['properties'][] = array('label' => '01 Minute', 'value' => carr::get($loadAverageArray, 0));
            $cardData['properties'][] = array('label' => '05 Minute', 'value' => carr::get($loadAverageArray, 1));
            $cardData['properties'][] = array('label' => '15 Minute', 'value' => carr::get($loadAverageArray, 2));
            $cardData['chart'] = array();
            $cardData['chart']['percent'] = $loadAveragePercent;
            $cardData['chart']['color'] = '#d9534f';

            $divCol = $divRow->addDiv()->addClass('col-md-4');
            $divCol->addTemplate()->setTemplate('CApp/Card/Info')->setData($cardData);


            //memory
            $cardData = array();
            $cardData['title'] = 'Memory';

            $usedPercent = 0;
            if ($totalMemory > 0) {
                $usedPercent = ceil(($usedMemory * 100 ) / $totalMemory);
            }
            $cardData['properties'] = array();
            $cardData['properties'][] = array('label' => 'Total', 'value' => CHelper::formatter()->formatSize($totalMemory));
            $cardData['properties'][] = array('label' => 'Used', 'value' => CHelper::formatter()->formatSize($usedMemory));
            $cardData['properties'][] = array('label' => 'Free', 'value' => CHelper::formatter()->formatSize($freeMemory));
            $cardData['chart'] = array();
            $cardData['chart']['percent'] = $usedPercent;
            $cardData['chart']['color'] = '#d9534f';

            $divCol = $divRow->addDiv()->addClass('col-md-4');
            $divCol->addTemplate()->setTemplate('CApp/Card/Info')->setData($cardData);

            /* storage */
            $cardData = array();
            $cardData['title'] = 'Storage';

            $usedPercent = 0;
            if ($totalStorage > 0) {
                $usedPercent = ceil(($usedStorage * 100 ) / $totalStorage);
            }
            $cardData['properties'] = array();
            $cardData['properties'][] = array('label' => 'Total', 'value' => CHelper::formatter()->formatSize($totalStorage));
            $cardData['properties'][] = array('label' => 'Used', 'value' => CHelper::formatter()->formatSize($usedStorage));
            $cardData['properties'][] = array('label' => 'Free', 'value' => CHelper::formatter()->formatSize($freeStorage));
            $cardData['chart'] = array();
            $cardData['chart']['percent'] = $usedPercent;
            $cardData['chart']['color'] = '#d9534f';

            $divCol = $divRow->addDiv()->addClass('col-md-4');
            $divCol->addTemplate()->setTemplate('CApp/Card/Info')->setData($cardData);
        }

        if ($errCode == 0) {
            $tabList = $app->addTabList();
            $tabList->addTab()->setLabel('Storage')->setAjaxUrl($this->controllerUrl() . 'tabStorage')->setNoPadding();
        }

        if ($errCode > 0) {
            cmsg::add('error', $errMessage);
        }
        echo $app->render();
    }

    public function tabStorage() {
        $errCode = 0;
        $errMessage = '';
        $app = CApp::instance();

        $app->title('Server Storage Information');
        $cloudData = array();
        try {
            $cloudData = CApp_Cloud::instance()->api('Server/GetStorageInfo');
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }

        /* new row */
        $divRow = $app->addDiv()->addClass('row');

        $cardData = array();

        $cardData['title'] = 'Storage Information';
        $cardData['columns'] = array('Disk', 'Type', 'Mount Point', 'Usage', 'Free', 'Used', 'Size', 'INode (%)', 'Options');
        $diskDevices = carr::get($cloudData, 'disks');
        foreach ($diskDevices as $device) {
            /* @var $device CServer_Device_Disk */
            $freeDisk = carr::get($device, 'free');
            $usedDisk = carr::get($device, 'used');
            $totalDisk = carr::get($device, 'total');
            $usedPercent = 0;
            if ($totalDisk != 0) {
                $usedPercent = $usedDisk * 100 / $totalDisk;
                $usedPercent = min(ceil($usedPercent * 100) / 100, 100);
            }

            $htmlProgress = '<div class="progress" style="height: 6px;min-width:100px;"><div class="progress-bar" style="width: ' . $usedPercent . '%;"></div></div>' . $usedPercent . '%';
            $cardData['rows'][] = array(
                carr::get($device, 'name'),
                carr::get($device, 'fsType'),
                carr::get($device, 'mountPoint'),
                $htmlProgress,
                CHelper::formatter()->formatSize($freeDisk),
                CHelper::formatter()->formatSize($usedDisk),
                CHelper::formatter()->formatSize($totalDisk),
                carr::get($device, 'inodesUsedPercent') . '%',
                carr::get($device, 'options'),
            );
        }



        $divCol = $divRow->addDiv()->addClass('col-md-12');
        $divCol->addTemplate()->setTemplate('CApp/Card/Table')->setData($cardData);

        echo $app->render();
    }

}
