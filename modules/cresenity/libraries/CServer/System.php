<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 1:45:37 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_System {

    /**
     *
     * @var CServer_System
     */
    protected static $instance;

    /**
     *
     * @var CServer_System_OS
     */
    protected $os;

    /**
     *
     * @var CServer_System_Info
     */
    protected $info;

    public function __construct() {
        $os = CServer::getOS();
        $this->info = new CServer_System_Info();
        $osClass = 'CServer_System_OS_' . $os;
        $this->os = new $osClass($this->info);
    }

    public static function instance() {
        if (self::$instance == null) {
            return new CServer_System();
        }
        return self::$instance;
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

    public function getLoadPercent($forceEnabled = false) {
        if (!$this->info->getLoadPercent()) {
            $this->os->buildLoadAvg();
        }
        return $this->info->getLoadPercent();
    }

}
