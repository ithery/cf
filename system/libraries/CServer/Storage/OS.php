<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
abstract class CServer_Storage_OS implements CServer_Storage_OSInterface {
    /**
     * @var CServer_Storage_Info
     */
    protected $info;

    protected $system;

    /**
     * @param CServer_Storage_Info $info
     */
    public function __construct(CServer_Storage $system, CServer_Storage_Info $info) {
        $this->info = $info;
        $this->system = $system;
    }

    public function createCommand() {
        return CServer::command($this->system->getSSHConfig());
    }
}
