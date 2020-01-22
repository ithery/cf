<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 15, 2019, 12:18:48 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CDaemon_TaskInterface {

    /**
     * Called on Construct or Init
     * @return void
     */
    public function setup();

    /**
     * Called on Destruct
     * @return void
     */
    public function teardown();

    /**
     * This is called after setup() returns
     * @return void
     */
    public function start();

    /**
     * Give your CDaemon_TaskInterface object a group name so the ProcessManager can identify and group processes. Or return Null
     * to just use the current __class__ name.
     * @return string
     */
    public function group();
}
