<?php

trait CTrait_Controller_Application_Server_Info {
    public function info() {
        $app = CApp::instance();

        $app->title('Local Server');

        $actionContainer = $app->addDiv()->addClass('action-container mb-3 clearfix');
        if (c::hasTrait($this, CTrait_Controller_Application_Server_PhpInfo::class)) {
            $phpInfoAction = $actionContainer->addAction()->setIcon('fas fa-user-cog')->setLabel('PHP Info')->addClass('btn-warning float-right mr-2')->setLink($this->controllerUrl() . 'phpinfo');
        }

        $divRow = $app->addDiv()->addClass('row');

        //load avg
        $cardData = [];
        $cardData['title'] = 'Load Average';
        $loadAverage = CServer::system()->getLoad();
        $loadAverageArray = explode(' ', $loadAverage);
        $loadAveragePercent = min(ceil(CServer::system()->getLoadPercent(true)), 100);

        $cardData['properties'] = [];
        $cardData['properties'][] = ['label' => '01 Minute', 'value' => carr::get($loadAverageArray, 0)];
        $cardData['properties'][] = ['label' => '05 Minute', 'value' => carr::get($loadAverageArray, 1)];
        $cardData['properties'][] = ['label' => '15 Minute', 'value' => carr::get($loadAverageArray, 2)];
        $cardData['chart'] = [];
        $cardData['chart']['percent'] = $loadAveragePercent;
        $cardData['chart']['color'] = '#d9534f';

        $divCol = $divRow->addDiv()->addClass('col-md-4');
        $divCol->addTemplate()->setTemplate('CApp/Card/Info')->setData($cardData);

        //memory
        $cardData = [];
        $cardData['title'] = 'Memory';
        $freeMemory = CServer::memory()->getMemFree();
        $totalMemory = CServer::memory()->getMemTotal();
        $usedMemory = CServer::memory()->getMemUsed();
        $usedPercent = 0;
        if ($totalMemory > 0) {
            $usedPercent = ceil(($usedMemory * 100) / $totalMemory);
        }

        $cardData['properties'] = [];
        $cardData['properties'][] = ['label' => 'Total', 'value' => CHelper::formatter()->formatSize($totalMemory)];
        $cardData['properties'][] = ['label' => 'Used', 'value' => CHelper::formatter()->formatSize($usedMemory)];
        $cardData['properties'][] = ['label' => 'Free', 'value' => CHelper::formatter()->formatSize($freeMemory)];
        $cardData['chart'] = [];
        $cardData['chart']['percent'] = $usedPercent;
        $cardData['chart']['color'] = '#d9534f';

        $divCol = $divRow->addDiv()->addClass('col-md-4');
        $divCol->addTemplate()->setTemplate('CApp/Card/Info')->setData($cardData);

        //storage
        $cardData = [];
        $cardData['title'] = 'Storage';
        $freeStorage = CServer::storage()->getFreeSpace();
        $totalStorage = CServer::storage()->getTotalSpace();
        $usedStorage = $totalStorage - $freeStorage;
        $usedPercent = 0;
        if ($totalStorage != 0) {
            $usedPercent = ceil(($usedStorage * 100) / $totalStorage);
        }

        $cardData['properties'] = [];
        $cardData['properties'][] = ['label' => 'Total', 'value' => CHelper::formatter()->formatSize($totalStorage)];
        $cardData['properties'][] = ['label' => 'Used', 'value' => CHelper::formatter()->formatSize($usedStorage)];
        $cardData['properties'][] = ['label' => 'Free', 'value' => CHelper::formatter()->formatSize($freeStorage)];
        $cardData['chart'] = [];
        $cardData['chart']['percent'] = $usedPercent;
        $cardData['chart']['color'] = '#d9534f';

        $divCol = $divRow->addDiv()->addClass('col-md-4');
        $divCol->addTemplate()->setTemplate('CApp/Card/Info')->setData($cardData);

        $divRow = $app->addDiv()->addClass('row');
        $cardData = [];

        $cardData['title'] = 'Local Server Information';
        $distroImg = '<img src="' . CServer::system()->getDistributionIcon() . '" />';
        $cardData['columns'] = [];
        $cardData['rows'] = [];
        $cardData['rows'][] = ['Canonical Hostname', CServer::system()->getHostname()];
        $cardData['rows'][] = ['Listening IP', CServer::system()->getIp()];
        $cardData['rows'][] = ['Kernel Version', CServer::system()->getKernel()];
        $cardData['rows'][] = ['Distro', $distroImg . ' ' . CServer::system()->getDistribution()];
        $cardData['rows'][] = ['Uptime', CHelper::formatter()->formatTime(floor(CServer::system()->getUptime()))];
        $cardData['rows'][] = ['Last Boot', CHelper::formatter()->formatDatetime(floor(CServer::system()->getLastBoot()))];
        $userCount = CServer::system()->getUsers();
        if ($userCount == null) {
            $userCount = 0;
        }

        $cardData['rows'][] = ['Current User', $userCount];
        $processes = CServer::system()->getProcesses();
        $allProcess = carr::get($processes, '*', 0);
        $runningProcess = carr::get($processes, 'R', 0);
        $sleepProcess = carr::get($processes, 'S', 0);
        $waitProcess = carr::get($processes, 'W', 0);
        $stoppedProcess = carr::get($processes, 'T', 0);
        $zombieProcess = carr::get($processes, 'Z', 0);
        $otherProcess = $allProcess - ($sleepProcess + $waitProcess + $runningProcess);
        $processesText = sprintf('%d (%d running, %d sleep, %d waiting, %d other)', $allProcess, $runningProcess, $sleepProcess, $waitProcess, $otherProcess);
        $cardData['rows'][] = ['Processes', $processesText];
        $divCol = $divRow->addDiv()->addClass('col-md-6');
        $divCol->addTemplate()->setTemplate('CApp/Card/Table')->setData($cardData);

        //hardware
        $cardData = [];

        $cardData['title'] = 'Local Hardware Information';
        $distroImg = '<img src="' . CServer::system()->getDistributionIcon() . '" />';
        $cardData['columns'] = [];
        $cardData['rows'] = [];
        $cardData['rows'][] = ['Machine', CServer::system()->getMachine()];
        $cpus = CServer::system()->getCpus();
        $cardData['rows'][] = ['Total CPU', count($cpus)];

        $i = 0;
        foreach ($cpus as $cpu) {
            $i++;
            /* @var $cpu CServer_System_Device_Cpu */
            $cpuHtml = '';
            $cpuHtml .= '<div class="font-bold">' . $cpu->getModel() . '</div>';
            $cpuHtml .= '<div>CPU Speed: ' . round($cpu->getCpuSpeed() / 1000, 2) . ' GHz</div>';
            $cpuHtml .= '<div>Cache Size: ' . CHelper::formatter()->formatSize(round($cpu->getCache())) . '</div>';
            $cpuHtml .= '<div>Virtualization: ' . $cpu->getVirt() . '</div>';
            $cardData['rows'][] = ['CPU ' . $i . ' Info', $cpuHtml];
        }

        $divCol = $divRow->addDiv()->addClass('col-md-6');
        $divCol->addTemplate()->setTemplate('CApp/Card/Table')->setData($cardData);

        /* new row */
        $divRow = $app->addDiv()->addClass('row');

        $cardData = [];

        $cardData['title'] = 'Local Memory Information';
        $cardData['columns'] = ['Type', 'Usage', 'Free', 'Used', 'Size'];
        $memoryPercentUsed = CServer::memory()->getMemPercentUsed();
        $htmlProgress = '<div class="progress" style="height: 6px;min-width:100px;"><div class="progress-bar" style="width: ' . $memoryPercentUsed . '%;"></div></div>' . $memoryPercentUsed . '%';
        $cardData['rows'][] = [
            'Physical Memory',
            $htmlProgress,
            CHelper::formatter()->formatSize(CServer::memory()->getMemFree()),
            CHelper::formatter()->formatSize(CServer::memory()->getMemUsed()),
            CHelper::formatter()->formatSize(CServer::memory()->getMemTotal()),
        ];

        $swapPercentUsed = CServer::memory()->getSwapPercentUsed();
        $htmlProgress = '<div class="progress" style="height: 6px;min-width:100px;"><div class="progress-bar bg-warning" style="width: ' . $swapPercentUsed . '%;"></div></div>' . $swapPercentUsed . '%';
        $cardData['rows'][] = [
            'SWAP',
            $htmlProgress,
            CHelper::formatter()->formatSize(CServer::memory()->getSwapFree()),
            CHelper::formatter()->formatSize(CServer::memory()->getSwapUsed()),
            CHelper::formatter()->formatSize(CServer::memory()->getSwapTotal()),
        ];

        $divCol = $divRow->addDiv()->addClass('col-md-12');
        $divCol->addTemplate()->setTemplate('CApp/Card/Table')->setData($cardData);

        /* new row */
        $divRow = $app->addDiv()->addClass('row');

        $cardData = [];

        $cardData['title'] = 'Local Storage Information';
        $cardData['columns'] = ['Disk', 'Type', 'Mount Point', 'Usage', 'Free', 'Used', 'Size', 'INode (%)', 'Options'];
        $diskDevices = CServer::storage()->getDiskDevices();
        foreach ($diskDevices as $device) {
            /* @var $device CServer_Device_Disk */
            $freeDisk = $device->getFree();
            $usedDisk = $device->getUsed();
            $totalDisk = $device->getTotal();
            $usedPercent = $usedDisk * 100 / $totalDisk;
            $usedPercent = min(ceil($usedPercent * 100) / 100, 100);

            $htmlProgress = '<div class="progress" style="height: 6px;min-width:100px;"><div class="progress-bar" style="width: ' . $usedPercent . '%;"></div></div>' . $usedPercent . '%';
            $cardData['rows'][] = [
                $device->getName(),
                $device->getFsType(),
                $device->getMountPoint(),
                $htmlProgress,
                CHelper::formatter()->formatSize($freeDisk),
                CHelper::formatter()->formatSize($usedDisk),
                CHelper::formatter()->formatSize($totalDisk),
                $device->getPercentInodesUsed() . '%',
                $device->getOptions(),
            ];
        }

        $divCol = $divRow->addDiv()->addClass('col-md-12');
        $divCol->addTemplate()->setTemplate('CApp/Card/Table')->setData($cardData);

        $cardData = [];

        $cardData['title'] = 'Local PHP Information';
        $phpInfo = CServer::phpInfo()->get();
        $serverAPI = carr::get($phpInfo, 'phpinfo.Server API', '(unknown)');
        $serverBuildDate = carr::get($phpInfo, 'phpinfo.Build Date', '(unknown)');
        $phpIniPath = carr::get(carr::get($phpInfo, 'phpinfo'), 'Configuration File (php.ini) Path', '(unknown)');
        $cardData['rows'][] = ['OS', CServer::getOS()];
        $cardData['rows'][] = ['Load AVG', implode(' ', CServer::getLoadAvg())];
        $cardData['rows'][] = ['Server API', $serverAPI];
        $cardData['rows'][] = ['PHP INI Path', $phpIniPath];
        $cardData['rows'][] = ['PHP Version', CServer::phpInfo()->getPhpVersion()];
        $cardData['rows'][] = ['Build Date', $serverBuildDate];

        $divCol = $divRow->addDiv()->addClass('col-md-6');
        $divCol->addTemplate()->setTemplate('CApp/Card/Table')->setData($cardData);

        return $app;
    }
}
