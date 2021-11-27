<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 2:18:46 PM
 */
class CServer_System_Info {
    /**
     * Name of the host where CServer_System runs.
     *
     * @var string
     */
    private $hostname;

    /**
     * Ip of the host where CServer_System runs.
     *
     * @var string
     */
    private $ip;

    /**
     * Detailed Information about the kernel.
     *
     * @var string
     */
    private $kernel;

    /**
     * Name of the distribution.
     *
     * @var string
     */
    private $distribution;

    /**
     * Icon of the distribution (must be available in CServer_System).
     *
     * @var string
     */
    private $distributionIcon;

    /**
     * Detailed Information about the machine name.
     *
     * @var string
     */
    private $machine;

    /**
     * Time in sec how long the system is running.
     *
     * @var int
     */
    private $uptime;

    /**
     * Count of users that are currently logged in.
     *
     * @var int
     */
    private $users;

    /**
     * Load of the system.
     *
     * @var string
     */
    private $load;

    /**
     * Load of the system in percent (all cpus, if more than one).
     *
     * @var int
     */
    private $loadPercent;

    /**
     * Array of types of processes.
     *
     * @var array
     */
    private $processes;

    /**
     * Array with cpu devices.
     *
     * @see CServer_System_Device_Cpu
     *
     * @var array
     */
    private $cpus = [];

    /**
     * Returns $hostname.
     *
     * @see CServer_System_Info::$hostname
     *
     * @return string
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * Sets $hostname.
     *
     * @param string $hostname hostname
     *
     * @see CServer_System_Info::$hostname
     *
     * @return $this;
     */
    public function setHostname($hostname) {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Returns $ip.
     *
     * @see CServer_System_Info::$ip
     *
     * @return string
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * Sets $ip.
     *
     * @param string $ip IP
     *
     * @see CServer_System_Info::$ip
     *
     * @return $this
     */
    public function setIp($ip) {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Returns $kernel.
     *
     * @see CServer_System_Info::$kernel
     *
     * @return string
     */
    public function getKernel() {
        return $this->kernel;
    }

    /**
     * Sets $kernel.
     *
     * @param string $kernel kernelname
     *
     * @see CServer_System_Info::$kernel
     *
     * @return $this
     */
    public function setKernel($kernel) {
        $this->kernel = $kernel;

        return $this;
    }

    /**
     * Returns $distribution.
     *
     * @see CServer_System_Info::$distribution
     *
     * @return string
     */
    public function getDistribution() {
        return $this->distribution;
    }

    /**
     * Sets $distribution.
     *
     * @param string $distribution distributionname
     *
     * @see CServer_System_Info::$distribution
     *
     * @return void
     */
    public function setDistribution($distribution) {
        $this->distribution = $distribution;
    }

    /**
     * Returns $distributionIcon.
     *
     * @see CServer_System_Info::$distributionIcon
     *
     * @return string
     */
    public function getDistributionIcon() {
        return $this->distributionIcon;
    }

    /**
     * Sets $distributionIcon.
     *
     * @param string $distributionIcon distribution icon
     *
     * @see CServer_System_Info::$distributionIcon
     *
     * @return void
     */
    public function setDistributionIcon($distributionIcon) {
        $this->distributionIcon = $distributionIcon;
    }

    /**
     * Returns $load.
     *
     * @see CServer_System_Info::$load
     *
     * @return string
     */
    public function getLoad() {
        return $this->load;
    }

    /**
     * Sets $load.
     *
     * @param string $load current system load
     *
     * @see CServer_System_Info::$load
     *
     * @return void
     */
    public function setLoad($load) {
        $this->load = $load;
    }

    /**
     * Returns $loadPercent.
     *
     * @see CServer_System_Info::$loadPercent
     *
     * @return int
     */
    public function getLoadPercent() {
        return $this->loadPercent;
    }

    /**
     * Sets $loadPercent.
     *
     * @param int $loadPercent load percent
     *
     * @see CServer_System_Info::$loadPercent
     *
     * @return void
     */
    public function setLoadPercent($loadPercent) {
        $this->loadPercent = $loadPercent;
    }

    /**
     * Returns $machine.
     *
     * @see CServer_System_Info::$machine
     *
     * @return string
     */
    public function getMachine() {
        return $this->machine;
    }

    /**
     * Sets $machine.
     *
     * @param string $machine machine
     *
     * @see CServer_System_Info::$machine
     *
     * @return void
     */
    public function setMachine($machine) {
        $this->machine = $machine;
    }

    /**
     * Returns $uptime.
     *
     * @see CServer_System_Info::$uptime
     *
     * @return int
     */
    public function getUptime() {
        return $this->uptime;
    }

    /**
     * Sets $uptime.
     *
     * @param int $uptime uptime
     *
     * @see CServer_System_Info::$uptime
     *
     * @return void
     */
    public function setUptime($uptime) {
        $this->uptime = $uptime;
    }

    /**
     * Returns $users.
     *
     * @see CServer_System_Info::$users
     *
     * @return int
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Sets $users.
     *
     * @param int $users user count
     *
     * @see CServer_System_Info::$users
     *
     * @return void
     */
    public function setUsers($users) {
        $this->users = $users;
    }

    /**
     * Returns $_processes.
     *
     * @see CServer_System_Info::$_processes
     *
     * @return array
     */
    public function getProcesses() {
        return $this->processes;
    }

    /**
     * Sets $proceses.
     *
     * @param $processes array of types of processes
     *
     * @see CServer_System_Info::$processes
     *
     * @return void
     */
    public function setProcesses($processes) {
        $this->processes = $processes;
    }

    /**
     * Returns $cpus.
     *
     * @see CServer_System_Info::$_cpus
     *
     * @return array
     */
    public function getCpus() {
        return $this->cpus;
    }

    /**
     * Sets $_cpus.
     *
     * @param CpuDevice $cpus cpu device
     *
     * @see System::$_cpus
     * @see CpuDevice
     *
     * @return void
     */
    public function addCpus($cpus) {
        if (!is_array($this->cpus)) {
            $this->cpus = [];
        }
        array_push($this->cpus, $cpus);
    }

    /**
     * Sets $_cpus.
     *
     * @param CpuDevice $cpus cpu device
     *
     * @see System::$_cpus
     * @see CpuDevice
     *
     * @return void
     */
    public function setCpus($cpus) {
        $this->cpus = $cpus;
    }
}
