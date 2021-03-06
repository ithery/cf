<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 1:45:37 PM
 */
class CServer_System extends CServer_Base {
    protected static $instance = [];

    /**
     * @var CServer_System_OS
     */
    protected $os;

    /**
     * @var CServer_System_Info
     */
    protected $info;

    public function __construct($sshConfig = null) {
        $os = CServer::getOS();
        $this->info = new CServer_System_Info();
        $osClass = 'CServer_System_OS_' . $os;
        $this->os = new $osClass($this, $this->info);
        $this->sshConfig = $sshConfig;
        $this->host = carr::get($sshConfig, 'host');
    }

    /**
     * @param array $sshConfig
     *
     * @return CServer_System
     */
    public static function instance(array $sshConfig = null) {
        if (!is_array(self::$instance)) {
            self::$instance = [];
        }
        $host = 'localhost';

        if ($sshConfig != null) {
            $host = carr::get($sshConfig, 'host');
        }
        if (!isset(self::$instance[$host])) {
            self::$instance[$host] = new CServer_System($sshConfig);
        }
        return self::$instance[$host];
    }

    public function getHostname() {
        if (!$this->info->getHostname()) {
            $this->os->buildHostname();
        }
        return $this->info->getHostname();
    }

    public function getIp() {
        if (!$this->info->getIp()) {
            $this->os->buildIp();
        }
        return $this->info->getIp();
    }

    public function getUptime() {
        if (!$this->info->getUptime()) {
            $this->os->buildUptime();
        }
        return $this->info->getUptime();
    }

    public function getKernel() {
        if (!$this->info->getKernel()) {
            $this->os->buildKernel();
        }
        return $this->info->getKernel();
    }

    public function getDistribution() {
        if (!$this->info->getDistribution()) {
            $this->os->buildDistro();
        }
        return $this->info->getDistribution();
    }

    public function getDistributionIcon() {
        if (!$this->info->getDistributionIcon()) {
            $this->os->buildDistro();
        }
        return $this->info->getDistributionIcon();
    }

    public function getLastBoot() {
        $uptime = $this->getUptime();
        return time() - intval($uptime);
    }

    public function getUsers() {
        if (!$this->info->getUsers()) {
            $this->os->buildUsers();
        }
        return $this->info->getUsers();
    }

    public function getProcesses() {
        if (!$this->info->getProcesses()) {
            $this->os->buildProcesses();
        }
        return $this->info->getProcesses();
    }

    public function getLoad() {
        if (!$this->info->getLoad()) {
            $this->os->buildLoadAvg();
        }
        return $this->info->getLoad();
    }

    public function getLoadPercent() {
        if (!$this->info->getLoadPercent()) {
            $this->os->buildLoadAvg();
        }
        return $this->info->getLoadPercent();
    }

    public function getMachine() {
        if (!$this->info->getMachine()) {
            $this->os->buildMachine();
        }
        return $this->info->getMachine();
    }

    public function getCpus() {
        if (!$this->info->getCpus()) {
            $this->os->buildCpuInfo();
        }
        return $this->info->getCpus();
    }
}
