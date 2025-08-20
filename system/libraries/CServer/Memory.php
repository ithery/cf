<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 1:45:25 PM
 */
class CServer_Memory extends CServer_Base {
    /**
     * @var CServer_Memory
     */
    protected static $instance = [];

    /**
     * @var CServer_Memory_OS
     */
    protected $os;

    /**
     * @var CServer_Memory_Info
     */
    protected $info;

    public function __construct(CRemote_SSH $ssh = null) {
        $os = CServer::getOS();
        $this->info = new CServer_Memory_Info();
        $osClass = 'CServer_Memory_OS_' . $os;
        $this->os = new $osClass($this, $this->info);
        $this->ssh = $ssh;
        $this->host = $ssh ? $ssh->getHost() : 'localhost';
    }

    /**
     * @param array|CRemote_SSH $sshConfig
     *
     * @return CServer_Memory
     */
    public static function instance($sshConfig = null) {
        if (!is_array(self::$instance)) {
            self::$instance = [];
        }
        $host = 'localhost';
        $ssh = null;
        if ($sshConfig != null) {
            $ssh = CServer_SSHRepository::instance()->getSSH($sshConfig);
            $host = $ssh->getHost();
        }
        if (!isset(self::$instance[$host])) {
            self::$instance[$host] = new CServer_Memory($ssh);
        }

        return self::$instance[$host];
    }

    public function getMemApplication() {
        if (!$this->info->getMemApplication()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemApplication();
    }

    public function getMemFree() {
        if (!$this->info->getMemFree()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemFree();
    }

    public function getMemBuffer() {
        if (!$this->info->getMemBuffer()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemBuffer();
    }

    public function getMemTotal() {
        if (!$this->info->getMemTotal()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemTotal();
    }

    public function getMemUsed() {
        if (!$this->info->getMemUsed()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemUsed();
    }

    public function getMemCache() {
        if (!$this->info->getMemCache()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemCache();
    }

    public function getMemPercentUsed() {
        if (!$this->info->getMemPercentUsed()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemPercentUsed();
    }

    public function getMemPercentBuffer() {
        if (!$this->info->getMemPercentBuffer()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemPercentBuffer();
    }

    public function getMemPercentApplication() {
        if (!$this->info->getMemPercentApplication()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemPercentApplication();
    }

    public function getMemPercentCache() {
        if (!$this->info->getMemPercentCache()) {
            $this->os->buildMemory();
        }

        return $this->info->getMemPercentCache();
    }

    public function getSwapDevices() {
        if (!$this->info->getSwapDevices()) {
            $this->os->buildSwap();
        }

        return $this->info->getSwapDevices();
    }

    public function getSwapFree() {
        if (!$this->info->getSwapFree()) {
            $this->os->buildSwap();
        }

        return $this->info->getSwapFree();
    }

    public function getSwapPercentUsed() {
        if (!$this->info->getSwapPercentUsed()) {
            $this->os->buildSwap();
        }

        return $this->info->getSwapPercentUsed();
    }

    public function getSwapTotal() {
        if (!$this->info->getSwapTotal()) {
            $this->os->buildSwap();
        }

        return $this->info->getSwapTotal();
    }

    public function getSwapUsed() {
        if (!$this->info->getSwapUsed()) {
            $this->os->buildSwap();
        }

        return $this->info->getSwapUsed();
    }
}
