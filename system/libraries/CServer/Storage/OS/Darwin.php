<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 7, 2019, 2:17:30 PM
 */
class CServer_Storage_OS_Darwin extends CServer_Storage_OS_Linux {
    /**
     * Filesystem information.
     *
     * @return void
     */
    public function buildDiskDevices() {
        $cmd = $this->createCommand();
        $df_args = '';
        $hideFstypes = [];
        if (is_string(CServer::config()->getHideFsTypes())) {
            if (preg_match(CServer::ARRAY_EXP, CServer::config()->getHideFsTypes())) {
                $hideFstypes = eval(CServer::config()->getHideFsTypes());
            } else {
                $hideFstypes = [CServer::config()->getHideFsTypes()];
            }
        }
        foreach ($hideFstypes as $Fstype) {
            $df_args .= "-x ${Fstype} ";
        }
        if ($df_args !== '') {
            $df_args = trim($df_args); //trim spaces
            $arrResult = $cmd->df("-P ${df_args} 2>/dev/null");
        } else {
            $arrResult = $cmd->df('-P 2>/dev/null');
        }
        foreach ($arrResult as $dev) {
            $this->info->setDiskDevices($dev);
        }
    }
}
