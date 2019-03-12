<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 12, 2019, 3:24:36 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CDaemon_ServiceInterface {

    /**
     * The setup method will contain the one-time setup needs of the daemon.
     * It will be called as part of the built-in init() method.
     * Any exceptions thrown from setup() will be logged as Fatal Errors and result in the daemon shutting down.
     * @return void
     * @throws Exception
     */
    public function setup();

    /**
     * The execute method will contain the actual function of the daemon.
     * It can be called directly if needed but its intention is to be called every iteration by the ->run() method.
     * Any exceptions thrown from execute() will be logged as Fatal Errors and result in the daemon attempting to restart or shut down.
     *
     * @return void
     * @throws Exception
     */
    public function execute();
}
