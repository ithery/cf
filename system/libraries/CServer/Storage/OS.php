<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CServer_Storage_OS implements CServer_Storage_OSInterface {
    /**
     * @var CServer_Storage_Info
     */
    protected $info;

    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @param CServer_Server      $server
     * @param CServer_Storage_Info $info
     */
    public function __construct(CServer_Server $server, CServer_Storage_Info $info) {
        $this->server = $server;
        $this->info = $info;
    }
}
