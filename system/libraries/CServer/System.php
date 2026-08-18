<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_System {
    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var CServer_System_Info
     */
    protected $info;

    /**
     * @var CServer_System_OS
     */
    protected $os;

    /**
     * @param CServer_Server $server
     */
    public function __construct(CServer_Server $server) {
        $this->server = $server;
        $this->info = new CServer_System_Info();
        $osName = $server->getOS();
        $osClass = 'CServer_System_OS_' . $osName;
        $this->os = new $osClass($server, $this->info);
    }

    /**
     * @return CServer_Server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getHostname() {
        if (!$this->info->getHostname()) {
            $this->os->buildHostname();
        }

        return $this->info->getHostname();
    }

    /**
     * @return string
     */
    public function getIp() {
        if (!$this->info->getIp()) {
            $this->os->buildIp();
        }

        return $this->info->getIp();
    }

    /**
     * @return float
     */
    public function getUptime() {
        if (!$this->info->getUptime()) {
            $this->os->buildUptime();
        }

        return $this->info->getUptime();
    }

    /**
     * @return string
     */
    public function getKernel() {
        if (!$this->info->getKernel()) {
            $this->os->buildKernel();
        }

        return $this->info->getKernel();
    }

    /**
     * @return string
     */
    public function getDistribution() {
        if (!$this->info->getDistribution()) {
            $this->os->buildDistro();
        }

        return $this->info->getDistribution();
    }

    /**
     * @return string
     */
    public function getDistributionIcon() {
        if (!$this->info->getDistributionIcon()) {
            $this->os->buildDistro();
        }

        return $this->info->getDistributionIcon();
    }

    /**
     * @return int
     */
    public function getLastBoot() {
        $uptime = $this->getUptime();

        return time() - intval($uptime);
    }

    /**
     * @return int
     */
    public function getUsers() {
        if (!$this->info->getUsers()) {
            $this->os->buildUsers();
        }

        return $this->info->getUsers();
    }

    /**
     * @return array
     */
    public function getProcesses() {
        if (!$this->info->getProcesses()) {
            $this->os->buildProcesses();
        }

        return $this->info->getProcesses();
    }

    /**
     * @return string
     */
    public function getLoad() {
        if (!$this->info->getLoad()) {
            $this->os->buildLoadAvg();
        }

        return $this->info->getLoad();
    }

    /**
     * @return float
     */
    public function getLoadPercent() {
        if (!$this->info->getLoadPercent()) {
            $this->os->buildLoadAvg();
        }

        return $this->info->getLoadPercent();
    }

    /**
     * @return string
     */
    public function getMachine() {
        if (!$this->info->getMachine()) {
            $this->os->buildMachine();
        }

        return $this->info->getMachine();
    }

    /**
     * @return array
     */
    public function getCpus() {
        if (!$this->info->getCpus()) {
            $this->os->buildCpuInfo();
        }

        return $this->info->getCpus();
    }
}
