<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 19, 2018, 3:49:17 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Storage_Info {

    /**
     * array with disk devices
     * @see CServer_Device_Disk
     * @var array
     */
    private $diskDevices = array();

    /**
     * Returns $diskDevices.
     *
     * @see CServer_Storage_Info::$diskDevices
     * @return array
     */
    public function getDiskDevices() {
        return $this->diskDevices;
    }

    /**
     * Sets $_diskDevices.
     *
     * @param CServer_Device_Disk $diskDevices disk device
     *
     * @see CServer_Storage_Info::$diskDevices
     * @see CServer_Device_Disk
     *
     * @return void
     */
    public function setDiskDevices(CServer_Device_Disk $diskDevices) {
        array_push($this->diskDevices, $diskDevices);
    }

}
