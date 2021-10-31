<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 6:26:12 PM
 */
class CServer_Device_Disk {
    /**
     * Name of the disk device
     *
     * @var string
     */
    private $name = '';

    /**
     * Type of the filesystem on the disk device
     *
     * @var string
     */
    private $fsType = '';

    /**
     * Diskspace that is free in bytes
     *
     * @var int
     */
    private $free = 0;

    /**
     * Diskspace that is used in bytes
     *
     * @var int
     */
    private $used = 0;

    /**
     * Total diskspace
     *
     * @var int
     */
    private $total = 0;

    /**
     * Mount point of the disk device if available
     *
     * @var string
     */
    private $mountPoint = null;

    /**
     * Additional options of the device, like mount options
     *
     * @var string
     */
    private $options = null;

    /**
     * Inodes usage in percent if available
     *
     * @var int
     */
    private $percentInodesUsed = null;

    /**
     * Returns PercentUsed calculated when function is called from internal values
     *
     * @see CServer_Device_Disk::$total
     * @see CServer_Device_Disk::$used
     *
     * @return int
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
     *
     * @see CServer_Device_Disk::$PercentInodesUsed
     *
     * @return int
     */
    public function getPercentInodesUsed() {
        return $this->percentInodesUsed;
    }

    /**
     * Sets $PercentInodesUsed.
     *
     * @param int $percentInodesUsed inodes percent
     *
     * @see CServer_Device_Disk::$PercentInodesUsed
     *
     * @return void
     */
    public function setPercentInodesUsed($percentInodesUsed) {
        $this->percentInodesUsed = $percentInodesUsed;
    }

    /**
     * Returns $free.
     *
     * @see CServer_Device_Disk::$free
     *
     * @return int
     */
    public function getFree() {
        return $this->free;
    }

    /**
     * Sets $free.
     *
     * @param int $free free bytes
     *
     * @see CServer_Device_Disk::$free
     *
     * @return void
     */
    public function setFree($free) {
        $this->free = $free;
    }

    /**
     * Returns $fsType.
     *
     * @see CServer_Device_Disk::$fsType
     *
     * @return string
     */
    public function getFsType() {
        return $this->fsType;
    }

    /**
     * Sets $fsType.
     *
     * @param string $fsType filesystemtype
     *
     * @see CServer_Device_Disk::$fsType
     *
     * @return void
     */
    public function setFsType($fsType) {
        $this->fsType = $fsType;
    }

    /**
     * Returns $mountPoint.
     *
     * @see CServer_Device_Disk::$mountPoint
     *
     * @return string
     */
    public function getMountPoint() {
        return $this->mountPoint;
    }

    /**
     * Sets $mountPoint.
     *
     * @param string $mountPoint mountpoint
     *
     * @see CServer_Device_Disk::$mountPoint
     *
     * @return void
     */
    public function setMountPoint($mountPoint) {
        $this->mountPoint = $mountPoint;
    }

    /**
     * Returns $name.
     *
     * @see CServer_Device_Disk::$name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets $name.
     *
     * @param string $name device name
     *
     * @see CServer_Device_Disk::$name
     *
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Returns $options.
     *
     * @see CServer_Device_Disk::$options
     *
     * @return string
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Sets $options.
     *
     * @param string $options additional options
     *
     * @see CServer_Device_Disk::$options
     *
     * @return void
     */
    public function setOptions($options) {
        $this->options = $options;
    }

    /**
     * Returns $total.
     *
     * @see CServer_Device_Disk::$total
     *
     * @return int
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * Sets $total.
     *
     * @param int $total total bytes
     *
     * @see CServer_Device_Disk::$total
     *
     * @return void
     */
    public function setTotal($total) {
        $this->total = $total;
    }

    /**
     * Returns $used.
     *
     * @see CServer_Device_Disk::$used
     *
     * @return int
     */
    public function getUsed() {
        return $this->used;
    }

    /**
     * Sets $used.
     *
     * @param int $used used bytes
     *
     * @see CServer_Device_Disk::$used
     *
     * @return void
     */
    public function setUsed($used) {
        $this->used = $used;
    }
}
