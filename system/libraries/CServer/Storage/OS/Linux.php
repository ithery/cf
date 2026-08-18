<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer_Storage_OS_Linux extends CServer_Storage_OS {
    /**
     * @return void
     */
    public function buildDiskDevices() {
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
