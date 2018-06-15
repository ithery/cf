<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 2:07:02 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CServer_System_OSInterface {

    /**
     * build the hostname information
     * @return void
     */
    public function buildHostname();

    /**
     * build the distro information
     * @return void
     */
    public function buildDistro();

    /**
     * build the ip information
     * @return void
     */
    public function buildIp();

    /**
     * build the kernel information
     * @return void
     */
    public function buildKernel();

    /**
     * build the uptime information
     * @return void
     */
    public function buildUptime();

    /**
     * build the users information
     * @return void
     */
    public function buildUsers();

    /**
     * build the processes information
     * @return void
     */
    public function buildProcesses();
    
    /**
     * build the loadavg information
     * @return void
     */
    public function buildLoadAvg();
}
