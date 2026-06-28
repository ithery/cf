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
     * @param CServer_Server $server
     */
    public function __construct(CServer_Server $server) {
        $this->server = $server;
        $this->info = new CServer_Storage_Info();
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
            $this->buildDiskDevices();
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

    /**
     * @return void
     */
    protected function buildDiskDevices() {
        $dfArgs = '';
        $hideFstypes = [];
        $config = $this->server->config();
        if (is_string($config->getHideFsTypes())) {
            if (preg_match(CServer::ARRAY_EXP, $config->getHideFsTypes())) {
                $hideFstypes = eval($config->getHideFsTypes());
            } else {
                $hideFstypes = [$config->getHideFsTypes()];
            }
        }
        foreach ($hideFstypes as $fstype) {
            $dfArgs .= '-x ' . $fstype . ' ';
        }
        $dfArgs = trim($dfArgs);
        $param = $dfArgs !== '' ? '-P ' . $dfArgs . ' 2>/dev/null' : '-P 2>/dev/null';
        $arrResult = $this->server->df($param);
        foreach ($arrResult as $dev) {
            $this->info->setDiskDevices($dev);
        }
    }
}
