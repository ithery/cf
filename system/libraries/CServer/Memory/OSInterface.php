<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 6:19:08 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CServer_Memory_OSInterface {

    /**
     * build the memory information
     * @return void
     */
    public function buildMemory();

    /**
     * build the swap memory information
     * @return void
     */
    public function buildSwap();
}
