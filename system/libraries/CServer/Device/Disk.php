<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 6:26:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Device_Disk {

    /**
     * name of the disk device
     * @var String
     */
    private $name = "";

    /**
     * type of the filesystem on the disk device
     * @var String
     */
    private $fsType = "";

    /**
     * diskspace that is free in bytes
     * @var Integer
     */
    private $free = 0;

    /**
     * diskspace that is used in bytes
     * @var Integer
     */
    private $used = 0;

    /**
     * total diskspace
     * @var Integer
     */
    private $total = 0;

    /**
     * mount point of the disk device if available
     * @var String
     */
    private $mountPoint = null;

    /**
     * additional options of the device, like mount options
     * @var String
     */
    private $options = null;

    /**
     * inodes usage in percent if available
     * @var int
     */
    private $percentInodesUsed = null;

    /**
     * Returns PercentUsed calculated when function is called from internal values
     * @see CServer_Device_Disk::$total
     * @see CServer_Device_Disk::$used
     * @return Integer
     */
    public function getPercentUsed() {
        if ($this->total > 0) {
            return round($this->used / $this->total * 100);
        } else {
            return 0;
        }
    }

    /**
     * Returns $PercentInodesUsed.
     * @see CServer_Device_Disk::$PercentInodesUsed
     * @return Integer
     */
    public function getPercentInodesUsed() {
        return $this->percentInodesUsed;
    }

    /**
     * Sets $PercentInodesUsed.
     * @param Integer $percentInodesUsed inodes percent
     * @see CServer_Device_Disk::$PercentInodesUsed
     * @return Void
     */
    public function setPercentInodesUsed($percentInodesUsed) {
        $this->percentInodesUsed = $percentInodesUsed;
    }

    /**
     * Returns $free.
     * @see CServer_Device_Disk::$free
     * @return Integer
     */
    public function getFree() {
        return $this->free;
    }

    /**
     * Sets $free.
     * @param Integer $free free bytes
     * @see CServer_Device_Disk::$free
     * @return Void
     */
    public function setFree($free) {
        $this->free = $free;
    }

    /**
     * Returns $fsType.
     * @see CServer_Device_Disk::$fsType
     * @return String
     */
    public function getFsType() {
        return $this->fsType;
    }

    /**
     * Sets $fsType.
     * @param String $fsType filesystemtype
     * @see CServer_Device_Disk::$fsType
     * @return Void
     */
    public function setFsType($fsType) {
        $this->fsType = $fsType;
    }

    /**
     * Returns $mountPoint.
     * @see CServer_Device_Disk::$mountPoint
     * @return String
     */
    public function getMountPoint() {
        return $this->mountPoint;
    }

    /**
     * Sets $mountPoint.
     * @param String $mountPoint mountpoint
     * @see CServer_Device_Disk::$mountPoint
     * @return Void
     */
    public function setMountPoint($mountPoint) {
        $this->mountPoint = $mountPoint;
    }

    /**
     * Returns $name.
     * @see CServer_Device_Disk::$name
     * @return String
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets $name.
     * @param String $name device name
     * @see CServer_Device_Disk::$name
     * @return Void
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns $options.
     * @see CServer_Device_Disk::$options
     * @return String
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Sets $options.
     * @param String $options additional options
     * @see CServer_Device_Disk::$options
     * @return Void
     */
    public function setOptions($options) {
        $this->options = $options;
    }

    /**
     * Returns $total.
     * @see CServer_Device_Disk::$total
     * @return Integer
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * Sets $total.
     * @param Integer $total total bytes
     * @see CServer_Device_Disk::$total
     * @return Void
     */
    public function setTotal($total) {
        $this->total = $total;
    }

    /**
     * Returns $used.
     * @see CServer_Device_Disk::$used
     * @return Integer
     */
    public function getUsed() {
        return $this->used;
    }

    /**
     * Sets $used.
     * @param Integer $used used bytes
     * @see CServer_Device_Disk::$used
     * @return Void
     */
    public function setUsed($used) {
        $this->used = $used;
    }

}
