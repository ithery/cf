<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 12, 2019, 6:02:05 PM
 */
interface CDaemon_PluginInterface {
    /**
     * Called on Construct or Init
     *
     * @return void
     */
    public function setup();

    /**
     * Called on Destruct
     *
     * @return void
     */
    public function teardown();

    /**
     * This is called during object construction to validate any dependencies
     * NOTE: At a minimum you should ensure that if $errors is not empty that you pass it along as the return value.
     *
     * @return array Return array of error messages (Think stuff like "GD Library Extension Required" or "Cannot open /tmp for Writing") or an empty array
     */
    public function checkEnvironment(array $errors = []);
}
