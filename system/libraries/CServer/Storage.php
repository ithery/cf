<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 13, 2018, 10:20:40 AM
 */
class CServer_Storage extends CServer_Base {
    const SHOW_MOUNT_OPTION = true;

    const SHOW_MOUNT_POINT = true;

    const SHOW_MOUNT_CREDENTIALS = true;

    const SHOW_INODES = true;

    protected static $instance = [];

    /**
     * @var CServer_Storage_OS
     */
    protected $os;

    /**
     * @var CServer_Storage_Info
     */
    protected $info;

    protected $freeSpace;

    protected $totalSpace;

    public function __construct(CRemote_SSH $ssh = null) {
        $os = CServer::getOS();
        $this->info = new CServer_Storage_Info();
        $osClass = 'CServer_Storage_OS_' . $os;
        $this->os = new $osClass($this, $this->info);
        $this->ssh = $ssh;
        $this->host = $ssh ? $ssh->getHost() : 'localhost';
    }

    /**
     * @param array|CRemote_SSH $sshConfig
     *
     * @return CServer_Storage
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
            self::$instance[$host] = new CServer_Storage($ssh);
        }

        return self::$instance[$host];
    }

    public function getDiskDevices() {
        if (!$this->info->getDiskDevices()) {
            $this->os->buildDiskDevices();
        }

        return $this->info->getDiskDevices();
    }

    /**
     * @return float
     */
    public function getFreeSpace() {
        if ($this->freeSpace == null) {
            $this->freeSpace = disk_free_space('/');
        }

        return $this->freeSpace;
    }

    public function getTotalSpace() {
        if ($this->totalSpace == null) {
            $this->totalSpace = @disk_total_space('/');
        }

        return $this->totalSpace;
    }
}
