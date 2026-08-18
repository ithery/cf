<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CServer_Memory_OSInterface {
    /**
     * Build the memory information.
     *
     * @return void
     */
    public function buildMemory();

    /**
     * Build the swap memory information.
     *
     * @return void
     */
    public function buildSwap();
}
