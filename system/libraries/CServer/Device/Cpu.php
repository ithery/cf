<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 5:40:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Device_Cpu extends CServer_Device {

    /**
     * model of the cpu
     * @var String
     */
    private $model = "";

    /**
     * speed of the cpu in hertz
     * @var Integer
     */
    private $cpuSpeed = 0;

    /**
     * max speed of the cpu in hertz
     * @var Integer
     */
    private $cpuSpeedMax = 0;

    /**
     * min speed of the cpu in hertz
     * @var Integer
     */
    private $cpuSpeedMin = 0;

    /**
     * cache size in bytes, if available
     * @var Integer
     */
    private $cache = null;

    /**
     * virtualization, if available
     * @var String
     */
    private $virt = null;

    /**
     * busspeed in hertz, if available
     * @var Integer
     */
    private $busSpeed = null;

    /**
     * temperature of the cpu, if available
     * @var Integer
     */
    private $temp = null;

    /**
     * bogomips of the cpu, if available
     * @var Integer
     */
    private $bogomips = null;

    /**
     * current load in percent of the cpu, if available
     * @var Integer
     */
    private $load = null;

    /**
     * Returns $bogomips.
     * @see CServer_System_Device_Cpu::$bogomips
     * @return Integer
     */
    public function getBogomips() {
        return $this->bogomips;
    }

    /**
     * Sets $bogomips.
     * @param Integer $bogomips bogompis
     * @see CServer_System_Device_Cpu::$bogomips
     * @return Void
     */
    public function setBogomips($bogomips) {
        $this->bogomips = $bogomips;
    }

    /**
     * Returns $busSpeed.
     * @see CServer_System_Device_Cpu::$busSpeed
     * @return Integer
     */
    public function getBusSpeed() {
        return $this->busSpeed;
    }

    /**
     * Sets $busSpeed.
     * @param Integer $busSpeed busspeed
     * @see CServer_System_Device_Cpu::$busSpeed
     * @return Void
     */
    public function setBusSpeed($busSpeed) {
        $this->busSpeed = $busSpeed;
    }

    /**
     * Returns $cache.
     * @see CServer_System_Device_Cpu::$cache
     * @return Integer
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * Sets $cache.
     * @param Integer $cache cache size
     * @see CServer_System_Device_Cpu::$cache
     * @return Void
     */
    public function setCache($cache) {
        $this->cache = $cache;
    }

    /**
     * Returns $virt.
     * @see CServer_System_Device_Cpu::$virt
     * @return String
     */
    public function getVirt() {
        return $this->virt;
    }

    /**
     * Sets $virt.
     * @param string $virt
     * @see CServer_System_Device_Cpu::$virt
     * @return Void
     */
    public function setVirt($virt) {
        $this->virt = $virt;
    }

    /**
     * Returns $cpuSpeed.
     * @see CServer_System_Device_Cpu::$cpuSpeed
     * @return Integer
     */
    public function getCpuSpeed() {
        return $this->cpuSpeed;
    }

    /**
     * Returns $cpuSpeedMax.
     * @see CServer_System_Device_Cpu::$cpuSpeedMAx
     * @return Integer
     */
    public function getCpuSpeedMax() {
        return $this->cpuSpeedMax;
    }

    /**
     * Returns $cpuSpeedMin.
     * @see CServer_System_Device_Cpu::$cpuSpeedMin
     * @return Integer
     */
    public function getCpuSpeedMin() {
        return $this->cpuSpeedMin;
    }

    /**
     * Sets $cpuSpeed.
     * @param Integer $cpuSpeed cpuspeed
     * @see CServer_System_Device_Cpu::$cpuSpeed
     * @return Void
     */
    public function setCpuSpeed($cpuSpeed) {
        $this->cpuSpeed = $cpuSpeed;
    }

    /**
     * Sets $cpuSpeedMax.
     * @param Integer $cpuSpeedMax cpuspeedmax
     * @see CServer_System_Device_Cpu::$cpuSpeedMax
     * @return Void
     */
    public function setCpuSpeedMax($cpuSpeedMax) {
        $this->cpuSpeedMax = $cpuSpeedMax;
    }

    /**
     * Sets $cpuSpeedMin.
     * @param Integer $cpuSpeedMin cpuspeedmin
     * @see CServer_System_Device_Cpu::$cpuSpeedMin
     * @return Void
     */
    public function setCpuSpeedMin($cpuSpeedMin) {
        $this->cpuSpeedMin = $cpuSpeedMin;
    }

    /**
     * Returns $model.
     * @see CServer_System_Device_Cpu::$model
     * @return String
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Sets $model.
     * @param String $model cpumodel
     * @see CServer_System_Device_Cpu::$model
     * @return Void
     */
    public function setModel($model) {
        $this->model = $model;
    }

    /**
     * Returns $temp.
     * @see CServer_System_Device_Cpu::$temp
     * @return Integer
     */
    public function getTemp() {
        return $this->temp;
    }

    /**
     * Sets $temp.
     * @param Integer $temp temperature
     * @see CServer_System_Device_Cpu::$temp
     * @return Void
     */
    public function setTemp($temp) {
        $this->temp = $temp;
    }

    /**
     * Returns $load.
     * @see CServer_System_Device_Cpu::$load
     * @return Integer
     */
    public function getLoad() {
        return $this->load;
    }

    /**
     * Sets $load.
     * @param Integer $load load percent
     * @see CServer_System_Device_Cpu::$load
     *
     * @return Void
     */
    public function setLoad($load) {
        $this->load = $load;
    }

}
