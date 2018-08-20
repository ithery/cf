<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 10:20:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Storage extends CServer_Base {

    const SHOW_MOUNT_OPTION = true;
    const SHOW_MOUNT_POINT = true;
    const SHOW_MOUNT_CREDENTIALS = true;
    const SHOW_INODES = true;

    protected static $instance = array();

    /**
     *
     * @var CServer_Storage_OS
     */
    protected $os;

    /**
     *
     * @var CServer_Storage_Info
     */
    protected $info;
    protected $freeSpace;
    protected $totalSpace;

    public function __construct($sshConfig = null) {
        $os = CServer::getOS();
        $this->info = new CServer_Storage_Info();
        $osClass = 'CServer_Storage_OS_' . $os;
        $this->os = new $osClass($this, $this->info);
        $this->sshConfig = $sshConfig;
        $this->host = carr::get($sshConfig, 'host');
    }

    /**
     * 
     * @param array $sshConfig
     * @return CServer_Storage
     */
    public static function instance(array $sshConfig = null) {
        if (!is_array(self::$instance)) {
            self::$instance = array();
        }
        $host = 'localhost';

        if ($sshConfig != null) {
            $host = carr::get($sshConfig, 'host');
        }
        if (!isset(self::$instance[$host])) {
            self::$instance[$host] = new CServer_Storage($sshConfig);
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
     * 
     * @return float
     */
    public function getFreeSpace() {
        if ($this->freeSpace == null) {
            $this->freeSpace = disk_free_space(".");
        }
        return $this->freeSpace;
    }

    public function getTotalSpace() {
        if ($this->totalSpace == null) {
            $this->totalSpace = disk_total_space("/");
        }
        return $this->totalSpace;
    }

}
