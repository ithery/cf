<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CServer_Memory_OS implements CServer_Memory_OSInterface {
    /**
     * @var CServer_Memory_Info
     */
    protected $info;

    /**
     * @var CServer_Server
     */
    protected $server;

    /**
     * @param CServer_Server      $server
     * @param CServer_Memory_Info $info
     */
    public function __construct(CServer_Server $server, CServer_Memory_Info $info) {
        $this->server = $server;
        $this->info = $info;
    }
}
