<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 2:18:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_System_Info {

    /**
     * name of the host where CServer_System runs
     * @var String
     */
    private $hostname;

    /**
     * ip of the host where CServer_System runs
     * @var String
     */
    private $ip;

    /**
     * detailed Information about the kernel
     * @var String
     */
    private $kernel;

    /**
     * name of the distribution
     * @var String
     */
    private $distribution;

    /**
     * icon of the distribution (must be available in CServer_System)
     * @var String
     */
    private $distributionIcon;

    /**
     * detailed Information about the machine name
     * @var String
     */
    private $machine;

    /**
     * time in sec how long the system is running
     * @var Integer
     */
    private $uptime;

    /**
     * count of users that are currently logged in
     * @var Integer
     */
    private $users;

    /**
     * load of the system
     * @var String
     */
    private $load;

    /**
     * load of the system in percent (all cpus, if more than one)
     * @var Integer
     */
    private $loadPercent;

    /**
     * array of types of processes
     * @var array
     */
    private $processes;

    /**
     * array with cpu devices
     * @see CServer_System_Device_Cpu
     * @var array
     */
    private $cpus;

    /**
     * Returns $hostname.
     * @see CServer_System_Info::$hostname
     * @return String
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * Sets $hostname.
     * @param String $hostname hostname
     * @see CServer_System_Info::$hostname
     * @return $this;
     */
    public function setHostname($hostname) {
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * Returns $ip.
     * @see CServer_System_Info::$ip
     * @return String
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * Sets $ip.
     * @param String $ip IP
     * @see CServer_System_Info::$ip
     * @return $this
     */
    public function setIp($ip) {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Returns $kernel.
     * @see CServer_System_Info::$kernel
     * @return String
     */
    public function getKernel() {
        return $this->kernel;
    }

    /**
     * Sets $kernel.
     * @param String $kernel kernelname
     * @see CServer_System_Info::$kernel
     * @return $this
     */
    public function setKernel($kernel) {
        $this->kernel = $kernel;
        return $this;
    }

    /**
     * Returns $distribution.
     * @see CServer_System_Info::$distribution
     * @return String
     */
    public function getDistribution() {
        return $this->distribution;
    }

    /**
     * Sets $distribution.
     * @param String $distribution distributionname
     * @see CServer_System_Info::$distribution
     * @return Void
     */
    public function setDistribution($distribution) {
        $this->distribution = $distribution;
    }

    /**
     * Returns $distributionIcon.
     * @see CServer_System_Info::$distributionIcon
     * @return String
     */
    public function getDistributionIcon() {
        return $this->distributionIcon;
    }

    /**
     * Sets $distributionIcon.
     * @param String $distributionIcon distribution icon
     * @see CServer_System_Info::$distributionIcon
     * @return Void
     */
    public function setDistributionIcon($distributionIcon) {
        $this->distributionIcon = $distributionIcon;
    }

    /**
     * Returns $load.
     * @see CServer_System_Info::$load
     * @return String
     */
    public function getLoad() {
        return $this->load;
    }

    /**
     * Sets $load.
     * @param String $load current system load
     * @see CServer_System_Info::$load
     * @return Void
     */
    public function setLoad($load) {
        $this->load = $load;
    }

    /**
     * Returns $loadPercent.
     * @see CServer_System_Info::$loadPercent
     * @return Integer
     */
    public function getLoadPercent() {
        return $this->loadPercent;
    }

    /**
     * Sets $loadPercent.
     * @param Integer $loadPercent load percent
     * @see CServer_System_Info::$loadPercent
     * @return Void
     */
    public function setLoadPercent($loadPercent) {
        $this->loadPercent = $loadPercent;
    }

    /**
     * Returns $machine.
     * @see CServer_System_Info::$machine
     * @return String
     */
    public function getMachine() {
        return $this->machine;
    }

    /**
     * Sets $machine.
     * @param string $machine machine
     * @see CServer_System_Info::$machine
     * @return Void
     */
    public function setMachine($machine) {
        $this->machine = $machine;
    }

    /**
     * Returns $uptime.
     * @see CServer_System_Info::$uptime
     * @return Integer
     */
    public function getUptime() {
        return $this->uptime;
    }

    /**
     * Sets $uptime.
     * @param integer $uptime uptime
     * @see CServer_System_Info::$uptime
     * @return Void
     */
    public function setUptime($uptime) {
        $this->uptime = $uptime;
    }

    /**
     * Returns $users.
     *
     * @see CServer_System_Info::$users
     *
     * @return Integer
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Sets $users.
     * @param Integer $users user count
     * @see CServer_System_Info::$users
     * @return Void
     */
    public function setUsers($users) {
        $this->users = $users;
    }

    /**
     * Returns $_processes.
     * @see CServer_System_Info::$_processes
     * @return array
     */
    public function getProcesses() {
        return $this->processes;
    }

    /**
     * Sets $proceses.
     * @param $processes array of types of processes
     * @see CServer_System_Info::$processes
     * @return Void
     */
    public function setProcesses($processes) {
        $this->processes = $processes;
    }

    /**
     * Returns $cpus.
     * @see CServer_System_Info::$_cpus
     * @return array
     */
    public function getCpus() {
        return $this->cpus;
    }

    /**
     * Sets $_cpus.
     * @param CpuDevice $cpus cpu device
     *
     * @see System::$_cpus
     * @see CpuDevice
     *
     * @return Void
     */
    public function addCpus($cpus) {
        if (!is_array($this->cpus)) {
            $this->cpus = array();
        }
        array_push($this->cpus, $cpus);
    }

    /**
     * Sets $_cpus.
     * @param CpuDevice $cpus cpu device
     *
     * @see System::$_cpus
     * @see CpuDevice
     *
     * @return Void
     */
    public function setCpus($cpus) {
        $this->cpus = $cpus;
    }

}
