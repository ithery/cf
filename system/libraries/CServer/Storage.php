<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Storage {
    const SHOW_MOUNT_OPTION = true;

    const SHOW_MOUNT_POINT = true;

    const SHOW_MOUNT_CREDENTIALS = true;

    const SHOW_INODES = true;

    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @var CServer_Storage_Info
     */
    protected $info;

    /**
     * @var CServer_Storage_OS
     */
    protected $os;

    /**
     * @param CServer_Server $server
     */
    public function __construct(CServer_Server $server) {
        $this->server = $server;
        $this->info = new CServer_Storage_Info();
        $osName = $server->getOS();
        $osClass = 'CServer_Storage_OS_' . $osName;
        $this->os = new $osClass($server, $this->info);
    }

    /**
     * @return CServer_Server
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @return array
     */
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
        if ($this->server->isRemote()) {
            $output = '';
            $this->server->executeProgram('df', '-k / | tail -1', $output);
            $parts = preg_split('/\s+/', trim($output));
            if (isset($parts[3])) {
                return (float) $parts[3] * 1024;
            }

            return 0.0;
        }

        return (float) disk_free_space('/');
    }

    /**
     * @return float
     */
    public function getTotalSpace() {
        if ($this->server->isRemote()) {
            $output = '';
            $this->server->executeProgram('df', '-k / | tail -1', $output);
            $parts = preg_split('/\s+/', trim($output));
            if (isset($parts[1])) {
                return (float) $parts[1] * 1024;
            }

            return 0.0;
        }

        return (float) @disk_total_space('/');
    }
}
