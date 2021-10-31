<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 6:19:08 PM
 */
interface CServer_Memory_OSInterface {
    /**
     * Build the memory information
     *
     * @return void
     */
    public function buildMemory();

    /**
     * Build the swap memory information
     *
     * @return void
     */
    public function buildSwap();
}
