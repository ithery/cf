<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 6:19:00 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CServer_Memory_OS implements CServer_Memory_OSInterface {

    /**
     * @var CServer_Memory_Info
     */
    protected $info;

    /**
     *
     * @var CServer_Memory
     */
    protected $memory;

    /**
     * 
     * @param CServer_Memory_Info $info
     */
    public function __construct(CServer_Memory $memory, CServer_Memory_Info $info) {
        $this->info = $info;
        $this->memory = $memory;
    }

    public function createCommand() {
        return CServer::command($this->memory->getSSHConfig());
    }

}
