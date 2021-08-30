<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 6:19:18 PM
 */
class CServer_Memory_Info {
    /**
     * Free memory in bytes
     *
     * @var int
     */
    private $memFree = 0;

    /**
     * Total memory in bytes
     *
     * @var int
     */
    private $memTotal = 0;

    /**
     * Used memory in bytes
     *
     * @var int
     */
    private $memUsed = 0;

    /**
     * Used memory by applications in bytes
     *
     * @var int
     */
    private $memApplication = null;

    /**
     * Used memory for buffers in bytes
     *
     * @var int
     */
    private $memBuffer = null;

    /**
     * Used memory for cache in bytes
     *
     * @var int
     */
    private $memCache = null;

    /**
     * Array with swap devices
     *
     * @see DiskDevice
     *
     * @var array
     */
    private $swapDevices = [];

    /**
     * Return percent of used memory
     *
     * @see CServer_Memory_Info::memUsed
     * @see CServer_Memory_Info::memTotal
     *
     * @return int
     */
    public function getMemPercentUsed() {
        if ($this->memTotal > 0) {
            return round($this->memUsed / $this->memTotal * 100);
        } else {
            return 0;
        }
    }

    /**
     * Return percent of used memory for applications
     *
     * @see CServer_Memory_Info::memApplication
     * @see CServer_Memory_Info::memTotal
     *
     * @return int
     */
    public function getMemPercentApplication() {
        if ($this->memApplication !== null) {
            if (($this->memApplication > 0) && ($this->memTotal > 0)) {
                return round($this->memApplication / $this->memTotal * 100);
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    /**
     * Return percent of used memory for cache
     *
     * @see CServer_Memory_Info::memCache
     * @see CServer_Memory_Info::memTotal
     *
     * @return int
     */
    public function getMemPercentCache() {
        if ($this->memCache !== null) {
            if (($this->memCache > 0) && ($this->memTotal > 0)) {
                if (($this->memApplication !== null) && ($this->memApplication > 0)) {
                    return round(($this->memCache + $this->memApplication) / $this->memTotal * 100) - $this->getMemPercentApplication();
                } else {
                    return round($this->memCache / $this->memTotal * 100);
                }
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    /**
     * Return percent of used memory for buffer
     *
     * @see CServer_Memory_Info::memBuffer
     * @see CServer_Memory_Info::memTotal
     *
     * @return int
     */
    public function getMemPercentBuffer() {
        if ($this->memBuffer !== null) {
            if (($this->memBuffer > 0) && ($this->memTotal > 0)) {
                if (($this->memCache !== null) && ($this->memCache > 0)) {
                    if (($this->memApplication !== null) && ($this->memApplication > 0)) {
                        return round(($this->memBuffer + $this->memApplication + $this->memCache) / $this->memTotal * 100) - $this->getMemPercentApplication() - $this->getMemPercentCache();
                    } else {
                        return round(($this->memBuffer + $this->memCache) / $this->memTotal * 100) - $this->getMemPercentCache();
                    }
                } elseif (($this->memApplication !== null) && ($this->memApplication > 0)) {
                    return round(($this->memBuffer + $this->memApplication) / $this->memTotal * 100) - $this->getMemPercentApplication();
                } else {
                    return round($this->memBuffer / $this->memTotal * 100);
                }
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    /**
     * Returns total free swap space
     *
     * @see CServer_Memory_Info::swapDevices
     * @see DiskDevice::getFree()
     *
     * @return int
     */
    public function getSwapFree() {
        if (count($this->swapDevices) > 0) {
            $free = 0;
            foreach ($this->swapDevices as $dev) {
                $free += $dev->getFree();
            }

            return $free;
        }

        return null;
    }

    /**
     * Returns total swap space
     *
     * @see CServer_Memory_Info::swapDevices
     * @see DiskDevice::getTotal()
     *
     * @return int
     */
    public function getSwapTotal() {
        if (count($this->swapDevices) > 0) {
            $total = 0;
            foreach ($this->swapDevices as $dev) {
                $total += $dev->getTotal();
            }

            return $total;
        } else {
            return null;
        }
    }

    /**
     * Returns total used swap space
     *
     * @see CServer_Memory_Info::swapDevices
     * @see DiskDevice::getUsed()
     *
     * @return int
     */
    public function getSwapUsed() {
        if (count($this->swapDevices) > 0) {
            $used = 0;
            foreach ($this->swapDevices as $dev) {
                $used += $dev->getUsed();
            }

            return $used;
        } else {
            return null;
        }
    }

    /**
     * Return percent of total swap space used
     *
     * @see CServer_Memory_Info::getSwapUsed()
     * @see CServer_Memory_Info::getSwapTotal()
     *
     * @return int
     */
    public function getSwapPercentUsed() {
        if ($this->getSwapTotal() !== null) {
            if ($this->getSwapTotal() > 0) {
                return round($this->getSwapUsed() / $this->getSwapTotal() * 100);
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    /**
     * Returns $memApplication.
     *
     * @see CServer_Memory_Info::$memApplication
     *
     * @return int
     */
    public function getMemApplication() {
        return $this->memApplication;
    }

    /**
     * Sets $memApplication.
     *
     * @param int $memApplication application memory
     *
     * @see CServer_Memory_Info::$memApplication
     *
     * @return void
     */
    public function setMemApplication($memApplication) {
        $this->memApplication = $memApplication;
    }

    /**
     * Returns $memBuffer.
     *
     * @see CServer_Memory_Info::$memBuffer
     *
     * @return int
     */
    public function getMemBuffer() {
        return $this->memBuffer;
    }

    /**
     * Sets $memBuffer.
     *
     * @param int $memBuffer buffer memory
     *
     * @see CServer_Memory_Info::$memBuffer
     *
     * @return void
     */
    public function setMemBuffer($memBuffer) {
        $this->memBuffer = $memBuffer;
    }

    /**
     * Returns $memCache.
     *
     * @see CServer_Memory_Info::$memCache
     *
     * @return int
     */
    public function getMemCache() {
        return $this->memCache;
    }

    /**
     * Sets $memCache.
     *
     * @param int $memCache cache memory
     *
     * @see CServer_Memory_Info::$memCache
     *
     * @return void
     */
    public function setMemCache($memCache) {
        $this->memCache = $memCache;
    }

    /**
     * Returns $memFree.
     *
     * @see CServer_Memory_Info::$memFree
     *
     * @return int
     */
    public function getMemFree() {
        return $this->memFree;
    }

    /**
     * Sets $memFree.
     *
     * @param int $memFree free memory
     *
     * @see CServer_Memory_Info::$memFree
     *
     * @return void
     */
    public function setMemFree($memFree) {
        $this->memFree = $memFree;
    }

    /**
     * Returns $memTotal.
     *
     * @see CServer_Memory_Info::$memTotal
     *
     * @return int
     */
    public function getMemTotal() {
        return $this->memTotal;
    }

    /**
     * Sets $memTotal.
     *
     * @param int $memTotal total memory
     *
     * @see CServer_Memory_Info::$memTotal
     *
     * @return void
     */
    public function setMemTotal($memTotal) {
        $this->memTotal = $memTotal;
    }

    /**
     * Returns $memUsed.
     *
     * @see CServer_Memory_Info::$memUsed
     *
     * @return int
     */
    public function getMemUsed() {
        return $this->memUsed;
    }

    /**
     * Sets $memUsed.
     *
     * @param int $memUsed used memory
     *
     * @see CServer_Memory_Info::$memUsed
     *
     * @return void
     */
    public function setMemUsed($memUsed) {
        $this->memUsed = $memUsed;
    }

    /**
     * Returns $swapDevices.
     *
     * @see CServer_Memory_Info::$swapDevices
     *
     * @return array
     */
    public function getSwapDevices() {
        return $this->swapDevices;
    }

    /**
     * Sets $swapDevices.
     *
     * @param CServer_Device_Disk $swapDevices swap devices
     *
     * @see CServer_Memory_Info::$swapDevices
     * @see CServer_Device_Disk
     *
     * @return void
     */
    public function setSwapDevices($swapDevices) {
        array_push($this->swapDevices, $swapDevices);
    }
}
