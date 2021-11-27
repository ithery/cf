<?php

class CServer_Factory {
    /**
     * @return CServer_Device_Cpu
     */
    public static function createDeviceCpu() {
        return new CServer_Device_Cpu();
    }

    /**
     * @return CServer_Device_Disk
     */
    public static function createDeviceDisk() {
        return new CServer_Device_Disk();
    }
}
