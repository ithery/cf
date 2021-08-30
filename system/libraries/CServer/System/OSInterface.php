<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 2:07:02 PM
 */
interface CServer_System_OSInterface {
    /**
     * Build the hostname information
     *
     * @return void
     */
    public function buildHostname();

    /**
     * Build the distro information
     *
     * @return void
     */
    public function buildDistro();

    /**
     * Build the ip information
     *
     * @return void
     */
    public function buildIp();

    /**
     * Build the kernel information
     *
     * @return void
     */
    public function buildKernel();

    /**
     * Build the machine information
     *
     * @return void
     */
    public function buildMachine();

    /**
     * Build the uptime information
     *
     * @return void
     */
    public function buildUptime();

    /**
     * Build the users information
     *
     * @return void
     */
    public function buildUsers();

    /**
     * Build the processes information
     *
     * @return void
     */
    public function buildProcesses();

    /**
     * Build the loadavg information
     *
     * @return void
     */
    public function buildLoadAvg();

    /**
     * Build the cpu information
     *
     * @return void
     */
    public function buildCpuInfo();
}
