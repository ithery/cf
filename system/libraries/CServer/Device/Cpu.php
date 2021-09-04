<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 5:40:10 PM
 */
class CServer_Device_Cpu extends CServer_Device {
    /**
     * Model of the cpu
     *
     * @var string
     */
    private $model = '';

    /**
     * Speed of the cpu in hertz
     *
     * @var int
     */
    private $cpuSpeed = 0;

    /**
     * Max speed of the cpu in hertz
     *
     * @var int
     */
    private $cpuSpeedMax = 0;

    /**
     * Min speed of the cpu in hertz
     *
     * @var int
     */
    private $cpuSpeedMin = 0;

    /**
     * Cache size in bytes, if available
     *
     * @var int
     */
    private $cache = null;

    /**
     * Virtualization, if available
     *
     * @var string
     */
    private $virt = null;

    /**
     * Busspeed in hertz, if available
     *
     * @var int
     */
    private $busSpeed = null;

    /**
     * Temperature of the cpu, if available
     *
     * @var int
     */
    private $temp = null;

    /**
     * Bogomips of the cpu, if available
     *
     * @var int
     */
    private $bogomips = null;

    /**
     * Current load in percent of the cpu, if available
     *
     * @var int
     */
    private $load = null;

    /**
     * Returns $bogomips.
     *
     * @see CServer_System_Device_Cpu::$bogomips
     *
     * @return int
     */
    public function getBogomips() {
        return $this->bogomips;
    }

    /**
     * Sets $bogomips.
     *
     * @param int $bogomips bogompis
     *
     * @see CServer_System_Device_Cpu::$bogomips
     *
     * @return void
     */
    public function setBogomips($bogomips) {
        $this->bogomips = $bogomips;
    }

    /**
     * Returns $busSpeed.
     *
     * @see CServer_System_Device_Cpu::$busSpeed
     *
     * @return int
     */
    public function getBusSpeed() {
        return $this->busSpeed;
    }

    /**
     * Sets $busSpeed.
     *
     * @param int $busSpeed busspeed
     *
     * @see CServer_System_Device_Cpu::$busSpeed
     *
     * @return void
     */
    public function setBusSpeed($busSpeed) {
        $this->busSpeed = $busSpeed;
    }

    /**
     * Returns $cache.
     *
     * @see CServer_System_Device_Cpu::$cache
     *
     * @return int
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * Sets $cache.
     *
     * @param int $cache cache size
     *
     * @see CServer_System_Device_Cpu::$cache
     *
     * @return void
     */
    public function setCache($cache) {
        $this->cache = $cache;
    }

    /**
     * Returns $virt.
     *
     * @see CServer_System_Device_Cpu::$virt
     *
     * @return string
     */
    public function getVirt() {
        return $this->virt;
    }

    /**
     * Sets $virt.
     *
     * @param string $virt
     *
     * @see CServer_System_Device_Cpu::$virt
     *
     * @return void
     */
    public function setVirt($virt) {
        $this->virt = $virt;
    }

    /**
     * Returns $cpuSpeed.
     *
     * @see CServer_System_Device_Cpu::$cpuSpeed
     *
     * @return int
     */
    public function getCpuSpeed() {
        return $this->cpuSpeed;
    }

    /**
     * Returns $cpuSpeedMax.
     *
     * @see CServer_System_Device_Cpu::$cpuSpeedMAx
     *
     * @return int
     */
    public function getCpuSpeedMax() {
        return $this->cpuSpeedMax;
    }

    /**
     * Returns $cpuSpeedMin.
     *
     * @see CServer_System_Device_Cpu::$cpuSpeedMin
     *
     * @return int
     */
    public function getCpuSpeedMin() {
        return $this->cpuSpeedMin;
    }

    /**
     * Sets $cpuSpeed.
     *
     * @param int $cpuSpeed cpuspeed
     *
     * @see CServer_System_Device_Cpu::$cpuSpeed
     *
     * @return void
     */
    public function setCpuSpeed($cpuSpeed) {
        $this->cpuSpeed = $cpuSpeed;
    }

    /**
     * Sets $cpuSpeedMax.
     *
     * @param int $cpuSpeedMax cpuspeedmax
     *
     * @see CServer_System_Device_Cpu::$cpuSpeedMax
     *
     * @return void
     */
    public function setCpuSpeedMax($cpuSpeedMax) {
        $this->cpuSpeedMax = $cpuSpeedMax;
    }

    /**
     * Sets $cpuSpeedMin.
     *
     * @param int $cpuSpeedMin cpuspeedmin
     *
     * @see CServer_System_Device_Cpu::$cpuSpeedMin
     *
     * @return void
     */
    public function setCpuSpeedMin($cpuSpeedMin) {
        $this->cpuSpeedMin = $cpuSpeedMin;
    }

    /**
     * Returns $model.
     *
     * @see CServer_System_Device_Cpu::$model
     *
     * @return string
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Sets $model.
     *
     * @param string $model cpumodel
     *
     * @see CServer_System_Device_Cpu::$model
     *
     * @return void
     */
    public function setModel($model) {
        $this->model = $model;
    }

    /**
     * Returns $temp.
     *
     * @see CServer_System_Device_Cpu::$temp
     *
     * @return int
     */
    public function getTemp() {
        return $this->temp;
    }

    /**
     * Sets $temp.
     *
     * @param int $temp temperature
     *
     * @see CServer_System_Device_Cpu::$temp
     *
     * @return void
     */
    public function setTemp($temp) {
        $this->temp = $temp;
    }

    /**
     * Returns $load.
     *
     * @see CServer_System_Device_Cpu::$load
     *
     * @return int
     */
    public function getLoad() {
        return $this->load;
    }

    /**
     * Sets $load.
     *
     * @param int $load load percent
     *
     * @see CServer_System_Device_Cpu::$load
     *
     * @return void
     */
    public function setLoad($load) {
        $this->load = $load;
    }
}
